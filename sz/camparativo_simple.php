<?php

require_once("config/db.php");
// load the login class
require_once("classes/Login.php");
// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();
	// ... ask if we are logged in here:
	if ($login->isUserLoggedIn() == true) 
	{	
		/* Connect To Database*/
		require_once ("config/conexion.php");
		require_once ("libraries/function_home.php");
		//Inicia Control de Permisos
		include("./config/permisos.php");
		$user_id = $_SESSION['user_id'];
		get_cadena($user_id);
		$modulo="Rubros";
		permisos($modulo,$cadena_permisos);
		//Finaliza Control de Permisos
		$title="Comparativo Indicadores";
		$skin=$color_sitio;
		$reports=1;
		$graficos=1;
		include('view/graficos_estadisticos/comparativo_simple.php');//Include file with the view
	}
	else
	{
		header("location: login.php");
		exit;		
	}
?>