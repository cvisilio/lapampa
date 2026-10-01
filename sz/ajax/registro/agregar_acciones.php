<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}	
	if (empty($_POST['accion'])){
			$errors[] = "Accion está vacía.";
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
			//Write register in to database
			
			$sql = "INSERT INTO acciones (nombre, id_meta,id_proyecto,estado,responsable,observaciones,numero_accion) VALUES('".$accion."','".$meta."','".$proyecto."','".$estado."','".$responsable."','".$observaciones."','".$numero_accion."')";
			$query_new = mysqli_query($con,$sql);
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Accion ha sido creado con éxito.";
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