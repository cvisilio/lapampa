<?php

declare(strict_types=1);

/**
 * Preview + confirm import de aportes (CSV / XLSX / XLS).
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');

include_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../classes/Login.php';
require_once __DIR__ . '/import_lib.php';

$login = new Login();
if ($login->isUserLoggedIn() !== true) {
    header('Location: ../login.php');
    exit;
}

function importar_redirect(string $status, string $why = '', ?string $token = null): void
{
    $q = 'import_status=' . rawurlencode($status);
    if ($why !== '') {
        $q .= '&why=' . rawurlencode($why);
    }
    if ($token !== null && $token !== '') {
        $q .= '&token=' . rawurlencode($token);
    }
    header('Location: ../importar.php?' . $q);
    exit;
}

$action = '';
if (isset($_POST['preview_data'])) {
    $action = 'preview';
} elseif (isset($_POST['import_data'])) {
    $action = 'import';
} elseif (isset($_POST['delete_last_import'])) {
    $action = 'delete_last';
} else {
    importar_redirect('error', 'Acción no válida');
}

/* ========== DELETE LAST IMPORT ========== */
if ($action === 'delete_last') {
    $colCheck = mysqli_query($con, "SHOW COLUMNS FROM transferencias LIKE 'numero_importacion'");
    if (!$colCheck || mysqli_num_rows($colCheck) === 0) {
        importar_redirect('error', 'La columna numero_importacion aún no existe en transferencias.');
    }

    $maxRes = mysqli_query($con, 'SELECT COALESCE(MAX(numero_importacion), 0) AS max_n FROM transferencias');
    if ($maxRes === false) {
        importar_redirect('error', 'No se pudo consultar numero_importacion: ' . mysqli_error($con));
    }
    $maxRow = mysqli_fetch_assoc($maxRes);
    $lastNum = (int) ($maxRow['max_n'] ?? 0);
    if ($lastNum <= 0) {
        importar_redirect('error', 'No hay importaciones para eliminar.');
    }

    $cntRes = mysqli_query(
        $con,
        'SELECT COUNT(*) AS c FROM transferencias WHERE numero_importacion = ' . $lastNum
    );
    $cnt = 0;
    if ($cntRes) {
        $cntRow = mysqli_fetch_assoc($cntRes);
        $cnt = (int) ($cntRow['c'] ?? 0);
    }

    $del = mysqli_query(
        $con,
        'DELETE FROM transferencias WHERE numero_importacion = ' . $lastNum
    );
    if ($del === false) {
        importar_redirect('error', 'No se pudo eliminar: ' . mysqli_error($con));
    }

    importar_redirect(
        'deleted_last',
        "Se eliminó la importación N° {$lastNum} ({$cnt} registros)."
    );
}

/* ========== PREVIEW ========== */
if ($action === 'preview') {
    if (empty($_FILES['file']['name']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        importar_redirect('no_file', 'No se recibió el archivo.');
    }

    $name = (string) $_FILES['file']['name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, ['csv', 'xlsx', 'xls'], true)) {
        importar_redirect('invalid_file', 'Subí un archivo CSV, XLSX o XLS.');
    }

    $destDir = __DIR__ . '/uploads';
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }
    $safe = preg_replace('/[^\w\.\-]+/', '_', basename($name)) ?: ('archivo.' . $ext);
    $path = $destDir . '/preview__' . date('Ymd_His') . '_' . $safe;
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
        importar_redirect('error', 'No se pudo guardar el archivo subido.');
    }

    try {
        $parsed = importar_parse_aportes_file($path, $ext, $con);
    } catch (Throwable $e) {
        @unlink($path);
        importar_redirect('error', 'No se pudo leer el archivo: ' . $e->getMessage());
    }

    $token = importar_save_preview_cache([
        'file_name' => $name,
        'file_path' => $path,
        'ext' => $ext,
        'parsed' => $parsed,
    ]);

    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['importar_preview_token'] = $token;
    }

    importar_redirect($parsed['can_import'] ? 'preview_ok' : 'preview_errors', '', $token);
}

/* ========== IMPORT CONFIRM ========== */
$token = isset($_POST['preview_token']) ? (string) $_POST['preview_token'] : '';
if ($token === '' && !empty($_SESSION['importar_preview_token'])) {
    $token = (string) $_SESSION['importar_preview_token'];
}
$cache = importar_load_preview_cache($token);
if ($cache === null || empty($cache['parsed']) || !is_array($cache['parsed'])) {
    importar_redirect('error', 'No hay vista previa válida. Analizá el archivo otra vez.');
}

$parsed = $cache['parsed'];
if (empty($parsed['can_import'])) {
    importar_redirect('preview_errors', 'Hay errores en la vista previa; corregí el archivo antes de importar.', $token);
}

$rows = $parsed['rows'] ?? [];
$okCount = 0;
$numeroImportacion = 1;

mysqli_begin_transaction($con);
try {
    // Asegurar columna (por si el servidor aún no la tiene)
    $colCheck = mysqli_query($con, "SHOW COLUMNS FROM transferencias LIKE 'numero_importacion'");
    if (!$colCheck || mysqli_num_rows($colCheck) === 0) {
        $alter = mysqli_query(
            $con,
            "ALTER TABLE transferencias
             ADD COLUMN numero_importacion INT NOT NULL DEFAULT 1
             AFTER observaciones"
        );
        if ($alter === false) {
            throw new RuntimeException('No se pudo crear numero_importacion: ' . mysqli_error($con));
        }
    }

    $maxRes = mysqli_query($con, 'SELECT COALESCE(MAX(numero_importacion), 0) AS max_n FROM transferencias');
    if ($maxRes === false) {
        throw new RuntimeException('No se pudo obtener numero_importacion: ' . mysqli_error($con));
    }
    $maxRow = mysqli_fetch_assoc($maxRes);
    $numeroImportacion = ((int) ($maxRow['max_n'] ?? 0)) + 1;

    $stmt = mysqli_prepare(
        $con,
        'INSERT INTO transferencias (id_localidad, monto, afectacion, numero_importacion) VALUES (?, ?, ?, ?)'
    );
    if ($stmt === false) {
        throw new RuntimeException('No se pudo preparar INSERT: ' . mysqli_error($con));
    }

    foreach ($rows as $row) {
        if (empty($row['ok'])) {
            continue;
        }
        $idLoc = (int) ($row['id_localidad'] ?? 0);
        $monto = (float) ($row['monto'] ?? 0);
        $idConc = (int) ($row['id_concepto'] ?? 0);
        if ($idLoc <= 0 || $idConc <= 0 || abs($monto) < 0.00001) {
            continue;
        }
        mysqli_stmt_bind_param($stmt, 'idii', $idLoc, $monto, $idConc, $numeroImportacion);
        if (!mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Error al insertar: ' . mysqli_stmt_error($stmt));
        }
        $okCount++;
    }
    mysqli_stmt_close($stmt);
    mysqli_commit($con);
} catch (Throwable $e) {
    mysqli_rollback($con);
    importar_redirect('error', $e->getMessage(), $token);
}

importar_clear_preview_cache($token);
if (session_status() === PHP_SESSION_ACTIVE) {
    unset($_SESSION['importar_preview_token']);
}
if (!empty($cache['file_path']) && is_file((string) $cache['file_path'])) {
    @unlink((string) $cache['file_path']);
}

importar_redirect(
    'success',
    "Se importaron {$okCount} transferencias (importación N° {$numeroImportacion})."
);
