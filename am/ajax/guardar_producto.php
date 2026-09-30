<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	if (empty($_POST['localidad'])){
		$errors[] = "Ingresa localidad.";
	} elseif (!empty($_POST['localidad'])){
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $localidad = intval($_POST["localidad"]);
	$monto = floatval($_POST["monto"]);
	$afectacion= intval($_POST["afectacion"]);
	$cuotas = intval($_POST["cuotas"]);
	$observaciones=mysqli_real_escape_string($con,(strip_tags($_POST["observaciones"],ENT_QUOTES)));
	$mes_inicio=intval($_POST["mes_inicio"]);
	$anio_inicio=intval($_POST["anio_inicio"]);
	
	if($cuotas >0)
	 $cuotas_1=$cuotas -1;
	else 
	 $cuotas_1=0;
	
	$dia=1;
	$fecha = $anio_inicio . '-'. $mes_inicio.'-'.$dia;
	$nuevafecha = strtotime ( '+'.$cuotas_1.' month' , strtotime ( $fecha ) ) ;
	$fecha_final_transferencia = date('Y-m-d' , $nuevafecha);
    
	// REGISTER data into database
    $sql = "INSERT INTO compromisos_am(id_localidad, monto, afectacion, cuotas, observaciones,mes,anio,fecha_final_transferencia) VALUES ('$localidad','$monto','$afectacion','$cuotas','$observaciones','$mes_inicio','$anio_inicio','$fecha_final_transferencia')";
    $query = mysqli_query($con,$sql);
    // if product has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido guardado con éxito.";
    } else {
        $errors[] = "Lo sentimos, el registro falló. Por favor, regrese y vuelva a intentarlo.";
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