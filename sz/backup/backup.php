<?php
 if ($login->isUserLoggedIn() == true)
  { 		
	require_once ("config/db.php");
	
	require_once ("config/conexion.php");
	
	include ('function.php');
		
	if ($permisos_eliminar==1)
	{//Si cuenta por los permisos bien
	
	 backDb(DB_HOST, DB_USER, DB_PASS,DB_NAME);
		
		 
	exit;
	  
	  }  
	else
	 {
	    $aviso="Acceso denegado!";
		$msj="No cuentas con los permisos necesario para acceder a este módulo.";
		$classM="alert alert-error";
		$times="&times;";
	 }
      
}	