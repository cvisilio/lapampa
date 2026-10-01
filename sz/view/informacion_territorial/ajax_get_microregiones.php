<?php
// ajax_get_microregiones.php (UTF-8 sin BOM)
header('Content-Type: application/json; charset=UTF-8');

require_once ("../../config/db.php");
require_once ("../../config/conexion.php");
if (!isset($con) || !$con) { echo json_encode(['ok'=>false,'error'=>'Sin conexión DB']); exit; }

mysqli_set_charset($con, 'utf8mb4');

/* Trae id de localidad y su región (1..10). Ajustá el nombre del campo si difiere */
$sql = "SELECT id_loc_padron, region_am 
          FROM localidades 
         WHERE region_am IS NOT NULL
           AND region_am BETWEEN 1 AND 10";
$res = mysqli_query($con, $sql);
if (!$res) {
  error_log('microregiones query: '.mysqli_error($con));
  echo json_encode(['ok'=>false,'error'=>'Error de consulta']); exit;
}

$map = [];      // id_localidad => region_am
$present = [];  // set de regiones presentes (para leyenda opcional)
while ($r = mysqli_fetch_assoc($res)) {
  $lid = (int)$r['id_loc_padron'];
  $reg = (int)$r['region_am'];
  $map[$lid] = $reg;
  $present[$reg] = true;
}

/* opcional: nombres de regiones si luego querés mostrar leyenda */
$legend = [];
for ($i=1;$i<=10;$i++){
  $legend[] = ['id'=>$i, 'name'=>"Microregión $i", 'present'=> isset($present[$i])];
}

echo json_encode(['ok'=>true, 'map'=>$map, 'legend'=>$legend], JSON_UNESCAPED_UNICODE);
