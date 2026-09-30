<?php
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	
	include '../classes/cart_compromisos.php';
    $cart_compromisos = new Cart_compromisos;
	
	$id_compromiso=intval($_POST['id_compromiso']);
	$id_localidad=intval($_POST['id_localidad']);
	$id_motivo=intval($_POST['id_motivo']);
	$monto=intval($_POST['monto']);
	$cuotas=intval($_POST['cuotas']);
	
	
	if (isset($_GET['id_compromiso'])){$id_compromiso=intval($_GET['id_compromiso']);}
	if (isset($_GET['id_localidad2'])){$id_localidad=intval($_GET['id_localidad2']);}
	if (isset($_GET['monto2'])){$monto=intval($_GET['monto2']);}
	if (isset($_GET['cuota_2'])){$cuota=intval($_GET['cuota_2']);}
	if (isset($_GET['id_motivo2'])){$id_motivo=intval($_GET['id_motivo2']);}
	
	if($cuotas > 0) // cuando viene de tildar compromiso en listado compromisos, se calcula la cuota correspondient desde acá
	{ 
	$sql2=mysqli_query($con,"select transferencias.* from transferencias where transferencias.id_compromiso=$id_compromiso and id_localidad=$id_localidad"); 
	$fila2=mysqli_fetch_array($sql2);
	$transferencias_hechas_a_compromiso= mysqli_num_rows($sql2); 
	$cuota=$transferencias_hechas_a_compromiso + 1;
	
	}
		
	 $itemData = array(
            'id' => $id_compromiso,
            'id_compromiso' => $id_localidad,
            'price' => $monto,
			'id_motivo' => $id_motivo,
            'qty' => $cuota
        );
       
	$insertItem = $cart_compromisos->insert($itemData); 
	 
	$cartItemsCompromisos = $cart_compromisos->contents();  		 
	$total=0;
     foreach ($cartItemsCompromisos as $item) {
     $total+=$item["price"];
   	 }
	 		 
	echo "Total a transferir: $". number_format($total,0,",",".");

		
?>