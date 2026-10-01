<?php
// view/informacion_territorial/ajax_get_localidades.php (UTF-8 sin BOM)
header('Content-Type: application/json; charset=UTF-8');

require_once("../../config/db.php");
require_once("../../config/conexion.php");

if (!isset($con) || !$con) {
    echo json_encode(['ok' => false, 'error' => 'Sin conexión DB']);
    exit;
}

mysqli_set_charset($con, 'utf8mb4');

// tipo_localidad: 1 = Localidades, 2 = Comisiones de Fomento
$tipo_localidad = isset($_GET['tipo_localidad']) ? (int)$_GET['tipo_localidad'] : 0;

if ($tipo_localidad !== 2 && $tipo_localidad !== 3) {
    echo json_encode(['ok' => false, 'error' => 'Parámetro tipo_localidad inválido']);
    exit;
}

// Acá solo necesitamos los IDs para pintar/filtrar en el mapa
$sql = "SELECT id_loc_padron
          FROM localidades
         WHERE es_localidad = $tipo_localidad";

$res = mysqli_query($con, $sql);

if (!$res) {
    $err = mysqli_error($con);
    error_log('ajax_get_localidades query error: ' . $err . ' | SQL: ' . $sql);
    echo json_encode(['ok' => false, 'error' => 'Error de consulta: ' . $err]);
    exit;
}

$listado = [];
while ($r = mysqli_fetch_assoc($res)) {
    $listado[] = [
        'id' => (int)$r['id_loc_padron']
    ];
}

echo json_encode([
    'ok'      => true,
    'tipo'    => $tipo_localidad,
    'listado' => $listado
], JSON_UNESCAPED_UNICODE);
