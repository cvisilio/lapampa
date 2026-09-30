<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
        require_once ("../conexion.php");
     
  /* $dataArray = json_encode($_POST['seleccion1']); //Recibo el arreglo
  
  foreach ($dataArray as $valor) {
    $cadena. = $valor;
}
   */
 echo $_GET['seleccion1'];
  			
}
?>			