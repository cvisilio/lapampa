<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	if (empty($_POST['edit_id'])){
		$errors[] = "ID está vacío.";
	} elseif (!empty($_POST['edit_id'])){
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
   
   // $localidad = intval($_POST["edit_localidad"]);
	
	$monto = floatval($_POST["edit_monto"]);
	$afectacion = intval($_POST["edit_afectacion"]);
	$cuotas = intval($_POST["edit_cuotas"]);
    $observaciones = mysqli_real_escape_string($con,(strip_tags($_POST["edit_observaciones"],ENT_QUOTES)));
	$id=intval($_POST['edit_id']);
	$mes_inicio=intval($_POST["edit_mes_inicio"]);
	$anio_inicio=intval($_POST["edit_anio_inicio"]);
	
	if($cuotas >0)
	 $cuotas_1=$cuotas -1;
	else 
	 $cuotas_1=0;
	
	$dia=1;
	$fecha = $anio_inicio . '-'. $mes_inicio.'-'.$dia;
	$nuevafecha = strtotime ( '+'.$cuotas_1.' month' , strtotime ($fecha)) ;
	$fecha_final_transferencia = date('Y-m-d' , $nuevafecha);
    
	
	// UPDATE data into database
	//id_localidad='".$localidad."',
    $sql = "UPDATE compromisos_am SET  monto='".$monto."', afectacion='".$afectacion."', cuotas='".$cuotas."',  observaciones='".$observaciones."', mes='".$mes_inicio."',anio='".$anio_inicio."',fecha_final_transferencia='".$fecha_final_transferencia."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if product has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido actualizado con éxito.";
    } else {
        $errors[] = "Lo sentimos, la actualización falló. Por favor, regrese y vuelva a intentarlo.";
    }
		
	} else 
	{
		$errors[] = "desconocido.";
	}
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