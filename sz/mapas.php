<?php

require_once("config/db.php");
require_once("classes/Login.php");
$login = new Login();
	// ... ask if we are logged in here:
	if ($login->isUserLoggedIn() == true) 
	{	
		/* Connect To Database*/
		require_once ("config/conexion.php");
		//Inicia Control de Permisos
		include("./config/permisos.php");
		$user_id = $_SESSION['user_id'];
		get_cadena($user_id);
		$modulo="Rubros";
		permisos($modulo,$cadena_permisos);
		//Finaliza Control de Permisos
		$title="Indicadores en Mapa";
		$skin=$color_sitio;
		$reports=1;
		$mapas=1;
		include('mapa/tests/argentina.php');//Include file with the view
	}
	else
	{
		header("location: login.php");
		exit;		
	}
?>