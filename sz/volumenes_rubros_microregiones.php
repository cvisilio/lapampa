<?php

require_once("config/db.php");
require_once("classes/Login.php");

$login = new Login();
if ($login->isUserLoggedIn() == true)
{
  require_once ("config/conexion.php");
  include("./config/permisos.php");
  $user_id = $_SESSION['user_id'];
  get_cadena($user_id);
  $modulo = "Rubros";
  permisos($modulo, $cadena_permisos);

  $title = "ABM Volumenes por Rubro y Microregion";
  $skin = $color_sitio;
  $informacion_territorial = 1;
  $sistema_informacion = 1;
  $volumenes_rubros_microregiones = 1;

  include('view/informacion_territorial/volumenes_rubros_microregiones.php');
}
else
{
  header("location: login.php");
  exit;
}
?>
