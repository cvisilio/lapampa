<?php

header('Content-Type: application/json; charset=UTF-8');

require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos

if (!isset($con) || !$con) { echo json_encode(['ok'=>false,'error'=>'Sin conexión DB']); exit; }

mysqli_set_charset($con, 'utf8mb4');

$sistema_id = isset($_POST['sistema_id']) ? (int)$_POST['sistema_id'] : 0;
if ($sistema_id <= 0) { echo json_encode(['ok'=>false,'error'=>'Parámetro inválido']); exit; }

/* Detectar si la columna se llama id_grupo o id_gupo */
$col = 'id_grupo';
$chk = mysqli_query($con, "SHOW COLUMNS FROM rubros LIKE 'id_grupo'");
if (!$chk || mysqli_num_rows($chk) === 0) {
  $chk2 = mysqli_query($con, "SHOW COLUMNS FROM rubros LIKE 'id_gupo'");
  if ($chk2 && mysqli_num_rows($chk2) > 0) $col = 'id_gupo';
}

/* Query usando el nombre correcto de columna y devolviendo 'nombre' para el front */
$sql = "SELECT id, name FROM rubros WHERE {$col} = ? ORDER BY name";
$stmt = mysqli_prepare($con, $sql);
if (!$stmt) { error_log('rubros prepare: '.mysqli_error($con)); echo json_encode(['ok'=>false,'error'=>'Error preparando consulta']); exit; }

mysqli_stmt_bind_param($stmt, 'i', $sistema_id);
if (!mysqli_stmt_execute($stmt)) { error_log('rubros execute: '.mysqli_error($con)); echo json_encode(['ok'=>false,'error'=>'Error ejecutando consulta']); exit; }

$rubros = [];
if (function_exists('mysqli_stmt_get_result')) {
  $res = mysqli_stmt_get_result($stmt);
  while ($r = $res ? mysqli_fetch_assoc($res) : null) {
    $rubros[] = ['id' => (int)$r['id'], 'nombre' => $r['name']]; // el front usa 'nombre'
  }
} else {
  mysqli_stmt_bind_result($stmt, $id, $name);
  while (mysqli_stmt_fetch($stmt)) {
    $rubros[] = ['id' => (int)$id, 'nombre' => $name];
  }
}

echo json_encode(['ok'=>true,'rubros'=>$rubros], JSON_UNESCAPED_UNICODE);
?>