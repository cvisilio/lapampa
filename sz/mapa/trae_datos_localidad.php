<?php 

  $id_localidad=intval($_POST['id_localidad']);
 
 require_once ("../config/db.php");
 require_once ("../config/conexion.php");
 
 $query =  mysqli_query($con,"SELECT * FROM localidades where id='$id_localidad'");
 $num=mysqli_num_rows($query);
  if ($num==1){
    $rw=mysqli_fetch_array($query);
	$lista_ganadora=$rw['lista_ganadora'];
	
	if ($lista_ganadora==1){
	 $lbl_proximo_partido="Frejupa";
	}elseif ($lista_ganadora==2)  {
	 $lbl_proximo_partido="Camb.";
	}elseif ($lista_ganadora==4)  {
	 $lbl_proximo_partido="Com. Org.";
	}elseif ($lista_ganadora==10)  {
	 $lbl_proximo_partido="J.V.";
	}
	
	$cadena="<b>". utf8_encode($rw['localidad']) . "</b><br><br>"."Ganador Elecciones 2019:  " .   $lbl_proximo_partido;
  }
  else
  {
  $cadena="...";
  }
  echo $cadena; //$cadena;
?>