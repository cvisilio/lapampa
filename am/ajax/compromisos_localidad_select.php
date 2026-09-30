<?php
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	
	
	$rubro_id=intval($_POST['id']);
	
	$opciones="";
	$sql=mysqli_query($con,"select compromisos_am.id,objetivos_motivos.motivo,compromisos_am.monto from compromisos_am, objetivos_motivos where compromisos_am.afectacion=objetivos_motivos.id and compromisos_am.id_localidad='$rubro_id'"); 
	
	 $opciones="<option value=''>Seleccione </option>";
	 $primero=0;
	while ($fila=mysqli_fetch_array($sql)){
	 if($primero==0)
	  $selected=" selected='selected'";
	 else
	  $selected="";
	    
     $opciones.="<option value=" . $fila['id'] . $selected .">". $fila['motivo']. " ($".$fila['monto'] .")". "</option>";
	 	$primero=1;	
		}
		

	echo $opciones;
		
?>

