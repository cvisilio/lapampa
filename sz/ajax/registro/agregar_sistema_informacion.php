<?php
if (!isset($_SESSION)) { session_start(); }
 require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			
$nombre = mysqli_real_escape_string($con, $_POST['nombre']);
$descripcion = isset($_POST['descripcion'])? mysqli_real_escape_string($con, $_POST['descripcion']) : '';
$por_microregion = isset($_POST['por_microregion'])? intval($_POST['por_microregion']) : 0;


$sql = "INSERT INTO sistemas_informacion (nombre, descripcion, por_microregion) VALUES ('$nombre','$descripcion',$por_microregion)";
if (mysqli_query($con,$sql)){
echo '<div class="alert alert-success">Registro agregado correctamente.</div>';
}else{
echo '<div class="alert alert-danger">Error al guardar.</div>';
}
?>