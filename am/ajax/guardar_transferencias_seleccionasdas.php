<?php

 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	  
    include '../classes/cart.php';
    $cart = new Cart;
	$cartItems = $cart->contents();
	
	include '../classes/cart_compromisos.php';
	
	$cart_compromisos = new Cart_compromisos;
	$cartItemsCompromisos = $cart_compromisos->contents();
		 
	require_once ("../conexion.php");
			 
	foreach ($cartItemsCompromisos as $item) {
     $id_en_array=$item["id_compromiso"]; //id_compromisos es localidad (jjjjj)
   	 if($id_en_array)
	    {
	    $localidad =$id_en_array; //id_compromisos es localidad (jjjjj)
		
		$monto =floatval($item["price"]);
		$afectacion= intval($item["id_motivo"]);
		$cuotas = intval($item["qty"]);
		$id_compromiso = intval($item["id"]);
						
		$sql = "INSERT INTO transferencias(id_localidad, id_compromiso, monto, afectacion, cuotas) VALUES ('$localidad','$id_compromiso','$monto','$afectacion','$cuotas')";
    $query = mysqli_query($con,$sql);
	     
		if ($query) {
			$messages[] = "Las transferencias han sido guardadas con éxito";
			$indice=md5($id_compromiso);		
	        //$eliminar = $cart_compromisos->remove($indice); 
		} else {
			$errors[] = "Lo sentimos, el registro falló. Por favor, regrese y vuelva a intentarlo";
		}
	   
	 
	   } //if  if($id_en_array)
	  } // for each compromisos 
	   
	foreach ($cartItems as $item) {
     $id_en_array=$item["id_localidad"];
     if($id_en_array)
	  {
	    $localidad =intval($item["id_localidad"]); 
		$monto =floatval($item["price"]);
		$afectacion= intval($item["id_motivo"]);
		$cuotas = intval($item["qty"]);
		$id_compromiso = intval($item["id_compromiso"]);
		
				
	 $sql = "INSERT INTO transferencias(id_localidad, id_compromiso, monto, afectacion, cuotas) VALUES ('$localidad','$id_compromiso','$monto','$afectacion','$cuotas')";
    $query = mysqli_query($con,$sql);
	
	
   
		if ($query) {
		   $messages[] = "Las transferencias han sido guardadas con éxito.";
		
		   $id_localidad_id_Motivo=$localidad."-".$afectacion;
		   $indice2=md5($id_localidad_id_Motivo);		
		 //  $eliminar = $cart->remove($indice2); 
		} else {
			$errors[] = "Lo sentimos, el registro falló. Por favor, regrese y vuelva a intentarlo.";
		}
	  
	 
	  } //if  if($id_en_array)
	  } // for each Item 
	  
	 	 
	  if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
							echo $error;
						}
						?>
			</div>
			<?php
		}
	else
	 {
	
	// include 'pdf_transferencias_seleccionasdas.php';
	 
	 
	//  session_start();
	//  unset($_SESSION['cart_contents']);
	//  unset($_SESSION['cart_compromisos']);
	 }	
	if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}

}				
?>			