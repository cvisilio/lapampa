<?php

/**
 * Punto de entrada: valida sesión, guarda el archivo y delega en import_pdf.php o import_excel.php.
 * Soporta PDF (con opción documento1_liberado), Excel .xlsx / .xls y CSV de extracto bancario.
 *
 * Requisitos: composer require smalot/pdfparser; phpoffice/phpspreadsheet (Excel).
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// true = muestra detalles en pantalla y NO redirige
define('DEBUG_IMPORT', false);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/pdf_import_matrix.php';
include_once("../conexion.php");
require_once("../classes/Login.php");

/* ====== Auth ====== */
$login = new Login();
if ($login->isUserLoggedIn() !== true) {
    header("Location: ../login.php");
    exit;
}

/* ====== Util ====== */
function back_to_index(string $status, string $why = ''): void {
    if (DEBUG_IMPORT) {
        echo "<h3>DEBUG IMPORT</h3>";
        echo "STATUS: <b>" . htmlspecialchars($status) . "</b><br>";
        if ($why !== '') {
            echo "WHY:<pre style='white-space:pre-wrap'>".htmlspecialchars($why)."</pre>";
        }
        exit;
    }
    header("Location: ../importar_pdf.php?import_status=" . urlencode($status) . "&why=" . urlencode($why));
    exit;
}

/**
 * Guarda un archivo subido. PDF, XLSX, XLS o CSV.
 *
 * @return array{0: ?string, 1: ?string, 2: ?string} ruta, extensión (pdf|xlsx|xls|csv), error
 */
function import_save_uploaded_file(string $fieldName, string $destDir, bool $onlyPdf): array {
    if (!isset($_FILES[$fieldName]) || !is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
        return [null, null, 'No subiste '.$fieldName];
    }
    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($_FILES[$fieldName]['tmp_name']);
    // finfo a veces devuelve "text/plain; charset=utf-8" y rompe in_array estricto
    $mimeBase = strtolower(trim(explode(';', (string) $mime, 2)[0]));

    if ($onlyPdf) {
        $mimeOk = in_array($mimeBase, ['application/pdf', 'application/x-pdf', 'application/octet-stream'], true);
        if (!$mimeOk && $ext === 'pdf') {
            $mimeOk = true;
        }
        if (!$mimeOk) {
            return [null, null, "Archivo $fieldName no parece PDF. MIME: $mime"];
        }
        if ($ext !== 'pdf') {
            return [null, null, 'documento1_liberado debe ser PDF.'];
        }
    } else {
        $pdfMime  = in_array($mimeBase, ['application/pdf', 'application/x-pdf', 'application/octet-stream'], true);
        $xlsxMime = in_array($mimeBase, ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream'], true);
        $xlsMime  = in_array($mimeBase, ['application/vnd.ms-excel', 'application/octet-stream'], true);
        // Los CSV suelen llegar como text/plain o application/octet-stream según el SO / navegador.
        $csvLikeMime = in_array($mimeBase, [
            'text/csv',
            'text/plain',
            'application/csv',
            'application/vnd.ms-excel',
            'application/octet-stream',
        ], true);

        $ok = false;
        if ($ext === 'pdf' && ($pdfMime || $mimeBase === 'application/octet-stream')) {
            $ok = true;
        } elseif ($ext === 'xlsx' && ($xlsxMime || $mimeBase === 'application/octet-stream')) {
            $ok = true;
        } elseif ($ext === 'xls' && ($xlsMime || $mimeBase === 'application/octet-stream')) {
            $ok = true;
        } elseif ($ext === 'csv' && ($csvLikeMime || $mimeBase === 'application/octet-stream')) {
            $ok = true;
        } elseif (in_array($ext, ['pdf', 'xlsx', 'xls', 'csv'], true)) {
            $ok = true;
        }

        // CSV con nombre .txt o sin extensión pero MIME de texto (muy habitual en Windows / PHP finfo).
        if (!$ok && $csvLikeMime && ($ext === 'txt' || $ext === '')) {
            $ext = 'csv';
            $ok = true;
        }

        if (!$ok) {
            return [null, null, "Formato no soportado (PDF, XLSX, XLS o CSV). MIME: $mime"];
        }
        if (!in_array($ext, ['pdf', 'xlsx', 'xls', 'csv'], true)) {
            return [null, null, "Extensi\xC3\xB3n no reconocida. Us\xC3\xA1 .csv, .xlsx, .xls o .pdf"];
        }
    }

    $name = basename($_FILES[$fieldName]['name']);
    $path = $destDir . '/' . $fieldName . '__' . preg_replace('/[^\w\.\-]+/', '_', $name);
    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $path)) {
        return [null, null, "move_uploaded_file fall\xC3\xB3 para $fieldName"];
    }

    return [$path, $ext, null];
}

/* ====== 1) Validación submit ====== */
if (!isset($_POST['import_data'])) {
    back_to_index('error', "No lleg\xC3\xB3 el submit import_data");
}

/* Transferencias recibidas: tildado = solo líneas con CUIT del titular */
$transferRecibidasSoloCuitLocalidad = isset($_POST['transfer_recibidas_solo_cuit_localidad'])
    && (string) $_POST['transfer_recibidas_solo_cuit_localidad'] === '1';

/* ====== 2) Archivos subidos ====== */
$hasOriginal = isset($_FILES['documento1'])
    && (int)($_FILES['documento1']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
    && is_uploaded_file($_FILES['documento1']['tmp_name']);

$hasLiberado = isset($_FILES['documento1_liberado'])
    && (int)($_FILES['documento1_liberado']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
    && is_uploaded_file($_FILES['documento1_liberado']['tmp_name']);

if (!$hasOriginal) {
    back_to_index('no_file', "Falta documento1 o el archivo no se subi\xC3\xB3 correctamente.");
}

/* ====== 3) Directorio destino ====== */
$destDir = __DIR__ . '/documentos_pdf';
if (!is_dir($destDir)) {
    @mkdir($destDir, 0755, true);
}

list($rutaOriginal, $extOriginal, $errO) = import_save_uploaded_file('documento1', $destDir, false);
if ($errO !== null) {
    back_to_index('error', $errO);
}

$rutaLiberado = null;
$extLiberado = null;
if ($hasLiberado) {
    list($rutaLiberado, $extLiberado, $errL) = import_save_uploaded_file('documento1_liberado', $destDir, true);
    if ($errL !== null) {
        back_to_index('error', $errL);
    }
}

/* ====== 4) Excel / CSV: mismo procesador (hoja o CSV de extracto) ====== */
if (in_array($extOriginal, ['xlsx', 'xls', 'csv'], true)) {
    if ($hasLiberado) {
        // Se ignora documento1_liberado si el principal es Excel/CSV
    }
    $ruta = $rutaOriginal;
    require __DIR__ . '/import_excel.php';
    exit;
}

/* ====== 5) PDF: priorizar liberado y delegar ====== */
if ($extOriginal !== 'pdf') {
    back_to_index('invalid_file', 'Extensi\xC3\xB3n no v\xC3\xA1lida.');
}

$ruta = $rutaLiberado ?: $rutaOriginal;
$usandoLiberado = (bool) $rutaLiberado;

if ($ruta === null || $ruta === '' || !is_readable($ruta)) {
    back_to_index('error', "No hay un archivo PDF guardado para procesar (ruta inv\xC3\xA1lida). Reintent\xC3\xA1 la subida.");
}

require __DIR__ . '/import_pdf.php';
exit;
