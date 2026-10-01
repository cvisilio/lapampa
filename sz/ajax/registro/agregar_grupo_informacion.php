<?php
if (!isset($_SESSION)) { session_start(); }
require_once ("../../config/db.php");
require_once ("../../config/conexion.php");

$grupo = mysqli_real_escape_string($con, strip_tags($_POST["grupo"], ENT_QUOTES));
$id_sistema = intval($_POST['id_sistema_informacion']);

$sql = "INSERT INTO grupos_informacion (grupo, id_sistema_informacion) 
        VALUES ('$grupo', $id_sistema)";

if (mysqli_query($con, $sql)) {
    echo '<div class="alert alert-success">Grupo agregado.</div>';
} else {
    echo '<div class="alert alert-danger">Error al guardar: ' . mysqli_error($con) . '</div>';
}
?>
