<?php
// include the configs / constants for the database connection
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
		/* function_home*/
		require_once ("libraries/function_home.php");
		
		//Inicia Control de Permisos
		include("./config/permisos.php");
		
		$user_id = $_SESSION['user_id'];
		$ministerio_usuario=$_SESSION['ministerio_usuario'];
		$usuario_editor=$_SESSION['usuario_editor'];
		get_cadena($user_id);
		$modulo="Inicio";
		permisos($modulo,$cadena_permisos);
		//Finaliza Control de Permisos
		$title="Panel de control | Gesti&oacute;n Comunicacional";
		$skin=$color_sitio; /*| skin-blue | skin-black  | skin-purple | skin-yellow | skin-red | skin-green*/
	
		$home=1;
		if($usuario_editor==1)
		 include('view/home2.php');
		else
		 include('view/home.php');//Include file with the view
	}
	else
	{
		header("location: login.php");
		exit;		
	}
?>