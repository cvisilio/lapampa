<?php
if (!isset($_SESSION)) { session_start(); }
require_once ("../../config/db.php");
require_once ("../../config/conexion.php");

$id = intval($_POST['id']);
$grupo = mysqli_real_escape_string($con, strip_tags($_POST["grupo"], ENT_QUOTES));
$id_sistema = intval($_POST['id_sistema_informacion']);

$sql = "UPDATE grupos_informacion SET grupo='$grupo', id_sistema_informacion=$id_sistema WHERE id=$id";

if (mysqli_query($con, $sql)) {
    echo '<div class="alert alert-success">Grupo actualizado.</div>';
} else {
    echo '<div class="alert alert-danger">Error al actualizar: ' . mysqli_error($con) . '</div>';
}
?>
