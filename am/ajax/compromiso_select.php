<?php
session_start();
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	
	include '../classes/cart.php';
    $cart = new Cart;
	
	$rubro_id=intval($_POST['id']);
		 	
	$return_arr = array();
	
	$sql=mysqli_query($con,"select compromisos_am.cuotas,compromisos_am.monto,compromisos_am.afectacion,compromisos_am.id_localidad from compromisos_am where compromisos_am.id=$rubro_id"); 
	
	$fila=mysqli_fetch_array($sql);
    
	$valores['cuota']=0;
    $cuotas =$fila['cuotas'];
	$valores['objetivo']=$fila['afectacion'];
	
	$id_localidad=$fila['id_localidad'];
	
	if ($cuotas >0)
	 {
	 $valores['cuotas']=$fila['cuotas'];
	 $valores['monto']=round($fila['monto'] / $fila['cuotas'],0);		
	
	 }
	else
	 {
	 $valores['monto']=$fila['monto'];
     $valores['cuotas']="";
	 $cuotas=0;
	 }
	
	if($cuotas > 0)
	{ 
	$sql2=mysqli_query($con,"select transferencias.* from transferencias where transferencias.id_compromiso=$rubro_id and id_localidad=$id_localidad order by id desc limit 1"); 
	$fila2=mysqli_fetch_array($sql2);
	$ultima_transferencia=$fila2['cuotas'];
	$transferencias_hechas_a_compromiso= mysqli_num_rows($sql2); 
	$valores['cuota']=$ultima_transferencia+1;//$transferencias_hechas_a_compromiso + 1;
	
	}
	
	$valores['motivos']="";
	
	$sql3=mysqli_query($con,"select * from objetivos_motivos order by motivo");  	
	while ($fila3=mysqli_fetch_array($sql3)){
	 
	 if ($fila3['id']==$valores['objetivo']){$selected1="selected";}else{$selected1="";}
	 
     $valores['motivos'].="<option ".$selected1  ." value=" . $fila3['id'] . ">". $fila3['motivo'] . "</option>";		
		}
		 
	array_push($return_arr,$valores);
    echo json_encode($return_arr);

		
?>

