<?php 
 
require_once('Connections/conexionUsuarios.php');

 $return_arr = array();
 
 $mesa = mysqli_real_escape_string($con,(strip_tags($_POST['mesa'], ENT_QUOTES)));		
 
 $consulta22 = mysqli_query($con,"SELECT localidades.* FROM mesas, localidades where mesas.CodigoLocalidad=localidades.id_loc_padron and mesas.Mesa='$mesa'");
 
 $row=mysqli_fetch_array($consulta22);
 $cargo_elecciones=$row['cargo_elecciones']; 
 $CodigoLocalidad= $row['id_loc_padron'];
 //$localidad= $row['localidad'];

 $Listas['codigo_localidad']=$CodigoLocalidad;
 $Listas['cargo_elecciones']= $cargo_elecciones;
 //$Listas['localidad']= $localidad;

 array_push($return_arr,$Listas);
  
 echo json_encode($return_arr);
 

?>