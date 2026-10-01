<?php
if (!isset($_SESSION)) { session_start(); }
 require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			
$rubro=intval($_POST['rubro']);
$micro=intval($_POST['microregion']);
$id_loc = isset($_POST['id_localidad']) && $_POST['id_localidad']!=='' ? intval($_POST['id_localidad']) : 'NULL';
$volumen = floatval($_POST['volumen']);
$color = isset($_POST['color'])? mysqli_real_escape_string($con,$_POST['color']) : '';

$col_grupo = "id_grupo";
$check_col = mysqli_query($con, "SHOW COLUMNS FROM rubros LIKE 'id_grupo'");
if (!$check_col || mysqli_num_rows($check_col) === 0) {
  $check_col_alt = mysqli_query($con, "SHOW COLUMNS FROM rubros LIKE 'id_gupo'");
  if ($check_col_alt && mysqli_num_rows($check_col_alt) > 0) {
    $col_grupo = "id_gupo";
  }
}
$check_rubro = mysqli_query($con, "SELECT id FROM rubros WHERE id=".$rubro." AND ".$col_grupo." > 0 LIMIT 1");
if (!$check_rubro || mysqli_num_rows($check_rubro) === 0) {
  echo '<div class="alert alert-danger">El rubro seleccionado no es válido para este ABM.</div>';
  exit;
}

$sql = "INSERT INTO volumenes_rubros_microregiones (rubro,microregion,id_localidad,volumen,color) VALUES ($rubro,$micro,$id_loc,$volumen,'$color')";
if (mysqli_query($con,$sql)){
  echo '<div class="alert alert-success">Registro agregado.</div>';
}else{
  echo '<div class="alert alert-danger">Error al guardar.</div>';
}
?>