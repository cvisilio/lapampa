<?php
 if (!isset($_SESSION)) {
  session_start();
}


include('Connections/conexionUsuarios.php');

 
 //Propcedimiento que se hace una vez cargado todo, para fijar la cantidad de mesas de cada localidad, de esta manera se evita sobrecargar de consultas a la base de datos



for ($i = 1; $i <= 94; $i++) {
 $Idlocalidad = $i;
 $registros="select * from mesas WHERE CodigoLocalidad='$Idlocalidad'" ;
 $consulta = mysqli_query($con,$registros);
 $CantidadMesas = mysqli_num_rows($consulta);  

 if($CantidadMesas>0)
  {
  $actualizar2 = "UPDATE localidades SET cantidad_mesas=".$CantidadMesas. "  WHERE id_loc_padron=".$Idlocalidad;
  $actualizar = mysqli_query($con,$actualizar2);
 echo "Localidad : ". $Idlocalidad. " =" . $CantidadMesas . "<br />";
  }
}