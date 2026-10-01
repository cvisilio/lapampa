<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
	if (empty($_POST['id_localidad'])){
			$errors[] = "Error en localidad";
			
	} 
	if (empty($_POST['user_id'])){
			$errors[] = "Error en Usuario";
			
	} 
	elseif (!empty($_POST['id_localidad']) && !empty($_POST['user_id'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $anotacion = mysqli_real_escape_string($con,(strip_tags($_POST["anotacion"],ENT_QUOTES)));
	$recordatorio=mysqli_real_escape_string($con,(strip_tags($_POST["recordatorio"],ENT_QUOTES)));
			
	$id_localidad=intval($_POST['id_localidad']);
	$user_id1=mysqli_real_escape_string($con,(strip_tags($_POST["user_id"],ENT_QUOTES)));
	
    $sql = "UPDATE anotaciones SET recordatorio='".$recordatorio."',  anotacion='".$anotacion."',  usuarios='".$user_id1."' WHERE id_localidad='".$id_localidad."' and usuarios='".$user_id1."'";
    $query = mysqli_query($con,$sql);
	
	
    // if user has been added successfully
    if ($query) {
        $messages[] = "La Anotacion ha sido actualizada con éxito.";
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