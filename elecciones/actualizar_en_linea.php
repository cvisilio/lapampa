<?php 
 include('Connections/conexionUsuarios.php'); 

 $QueModifica = mysqli_real_escape_string($con,(strip_tags($_GET['QueModifica'], ENT_QUOTES)));
 $Mesa=intval($_GET['Mesa']);
 $cantidad_actualizar=intval($_GET['cantidad_actualizar']);

 $sel_actualizar1="UPDATE mesas SET ".$QueModifica ."='".$cantidad_actualizar ." WHERE Mesa='".$Mesa."'";
//$sel_actualizar1="UPDATE mesas SET Escrutada='S' WHERE Mesa='".$Mesa."'";

 $query = mysqli_query($con,$sel_actualizar1);
 

 echo $cantidad_actualizar;

?>