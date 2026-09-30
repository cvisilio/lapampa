<?php
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	
	include '../classes/cart_compromisos.php';
    $cart_compromisos = new Cart_compromisos;
			
	$id_compromiso=intval($_POST['id_compromiso']);
	$indice=md5($id_compromiso);		
	$eliminar = $cart_compromisos->remove($indice); 
	
	$cartItemsCompromisos = $cart_compromisos->contents(); 
	 $total=0;
     foreach ($cartItemsCompromisos as $item) {
     $total+=$item["price"];
   	 }
	 		 
	echo "Total a transferir: $". number_format($total,0,",",".");

		
?>

