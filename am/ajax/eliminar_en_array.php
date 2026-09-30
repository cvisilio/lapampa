<?php
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	
	$id_localidad=intval($_POST['id_localidad']);
	
	include '../classes/cart.php';
    $cart = new Cart;
	$cartItems = $cart->contents();
	
	include '../classes/cart_compromisos.php';
	
    $cart_compromisos = new Cart_compromisos;
	$cartItemsCompromisos = $cart_compromisos->contents();
	foreach ($cartItemsCompromisos as $item) {
     $id_en_array=$item["id_compromiso"]; //id_compromisos es localidad (jjjjj)
   	 if($id_en_array==$id_localidad)
	    {
	 	$indice=md5($item["id"]);		
	    $eliminar = $cart_compromisos->remove($indice); 
	   }
	  } 
	   
	foreach ($cartItems as $item) {
     $id_en_array=$item["id_localidad"];
   	 if($id_en_array==$id_localidad)
	  {
	  $id_motivo=$item["id_motivo"];
	  $id_localidad_id_Motivo=$id_localidad."-".$id_motivo;
	  $indice2=md5($id_localidad_id_Motivo);		
	  $eliminar = $cart->remove($indice2); 
	  }
	 } 
	
		 
	echo "";

		
?>

