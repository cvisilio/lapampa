<?php
/**
 * modulos/hitos.php
 * CRUD de hitos para proyectos (se usa desde proyectos.php vía include o AJAX)
 */

require_once '../includes/config.php';
requireLogin();

$db = getDB();

// ── Guardar hito ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_hito'])) {
    requireRol('admin', 'editor');

    $proyecto_id     = (int)($_POST['proyecto_id'] ?? 0);
    $titulo          = trim($_POST['titulo'] ?? '');
    $descripcion     = trim($_POST['descripcion'] ?? '');
    $fecha_prog      = $_POST['fecha_programada'] ?: null;
    $id_hito         = (int)($_POST['hito_id'] ?? 0);

    if (!$proyecto_id || !$titulo) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    if ($id_hito) {
        $db->prepare("UPDATE hitos SET titulo=?, descripcion=?, fecha_programada=? WHERE id=? AND proyecto_id=?")
           ->execute([$titulo, $descripcion, $fecha_prog, $id_hito, $proyecto_id]);
    } else {
        $db->prepare("INSERT INTO hitos (proyecto_id, titulo, descripcion, fecha_programada) VALUES (?,?,?,?)")
           ->execute([$proyecto_id, $titulo, $descripcion, $fecha_prog]);
    }

    // Si viene de AJAX
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['ok' => true]);
        exit;
    }

    header('Location: ../proyectos.php?id=' . $proyecto_id);
    exit;
}

// ── Completar hito ─────────────────────────────────────────────
if (isset($_GET['completar']) && $_GET['completar']) {
    requireRol('admin', 'editor');
    $hid = (int)$_GET['completar'];
    $db->prepare("UPDATE hitos SET estado='completado', fecha_real=CURDATE() WHERE id=?")->execute([$hid]);

    $pid = $db->query("SELECT proyecto_id FROM hitos WHERE id=$hid")->fetchColumn();
    header('Location: ../proyectos.php?id=' . $pid);
    exit;
}

// ── Eliminar hito ──────────────────────────────────────────────
if (isset($_GET['eliminar']) && $_GET['eliminar']) {
    requireRol('admin');
    $hid = (int)$_GET['eliminar'];
    $pid = $db->query("SELECT proyecto_id FROM hitos WHERE id=$hid")->fetchColumn();
    $db->prepare("DELETE FROM hitos WHERE id=?")->execute([$hid]);
    header('Location: ../proyectos.php?id=' . $pid);
    exit;
}

// ── Listado de hitos de un proyecto (respuesta JSON para AJAX) ──
if (isset($_GET['proyecto_id'])) {
    $pid   = (int)$_GET['proyecto_id'];
    $hitos = $db->query("SELECT * FROM hitos WHERE proyecto_id=$pid ORDER BY fecha_programada")->fetchAll();
    header('Content-Type: application/json');
    echo json_encode($hitos);
    exit;
}

http_response_code(400);
echo 'Parámetros inválidos';
