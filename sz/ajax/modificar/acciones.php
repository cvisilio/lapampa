<?php
// checking for minimum PHP version
	if (empty($_POST['accion'])){
			$errors[] = "Acci&oacute;n está vacía.";
	} elseif (!empty($_POST['accion'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
     $accion = mysqli_real_escape_string($con,(strip_tags($_POST["accion"],ENT_QUOTES)));
	 $meta=intval($_POST["id_meta"]);
	 $estado=intval($_POST["estado"]);
	 $proyecto= mysqli_real_escape_string($con,(strip_tags($_POST["id_proyecto"],ENT_QUOTES)));
	 $observaciones= mysqli_real_escape_string($con,(strip_tags($_POST["observaciones"],ENT_QUOTES)));
	 $responsable= intval($_POST["responsable"]);
	 
	 $numero_accion= mysqli_real_escape_string($con,(strip_tags($_POST["numero_accion"],ENT_QUOTES))); 		
			
	 $id=intval($_POST["id"]); 
	
	// UPDATE data into database $
    $sql = "UPDATE acciones SET nombre='".$accion."', id_meta='".$meta."',id_proyecto='".$proyecto."',observaciones='".$observaciones."',estado='".$estado."',responsable='".$responsable."',numero_accion='".$numero_accion."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "La acci&oacute;n ha sido actualizado con éxito.";
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
?>			