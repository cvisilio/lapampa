<?php

require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
require_once ("../../libraries/inventory.php");//Contiene funcion que conecta a la base de datos

if (isset($_POST['id'])){
 
 if (isset($_POST['extraParams'])){
  $lista1= $_POST['extraParams'];
  } 

 $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['id'], ENT_QUOTES)));	
	
$return_arr = array();
/* Si la conexión a la base de datos , ejecuta instrucción SQL. */


	$fetch = mysqli_query($con,"SELECT * FROM users where user_id =$q");
	
	$row_cnt = mysqli_num_rows($fetch);
	
	if  ($row_cnt > 0)
	 {
	    $row=mysqli_fetch_array($fetch);
		$product_id=$row['user_id'];
		$row_array['product_id']=$row['user_id'];
		$row_array['descripcion']=$row['fullname'];
			
		array_push($return_arr,$row_array);
    }



/* Codifica el resultado del array en JSON. */
echo json_encode($return_arr);

}
?>