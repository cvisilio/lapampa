<?php 
    require_once ("conexion.php");
    require_once("classes/Login.php");

    $login = new Login();
	if ($login->isUserLoggedIn() == true) 
	{	
	 require_once ("conexion.php");
	 include("head.php");
	 include("abm/localidades.php");
	 ?>
   
 <?php                             		                            
	}
	else
	{
	 header("location: login.php");
	 exit;		
	}
?>
