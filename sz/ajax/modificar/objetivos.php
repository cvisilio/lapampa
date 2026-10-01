<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
	if (empty($_POST['objetivo'])){
			$errors[] = "Nombre está vacío.";
	} elseif (!empty($_POST['objetivo'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $objetivo = mysqli_real_escape_string($con,(strip_tags($_POST["objetivo"],ENT_QUOTES)));
	$copete = mysqli_real_escape_string($con,(strip_tags($_POST["copete"],ENT_QUOTES)));
	$descripcion = mysqli_real_escape_string($con,(strip_tags($_POST["descripcion"],ENT_QUOTES)));
	$color1= mysqli_real_escape_string($con,(strip_tags($_POST["color1"],ENT_QUOTES)));
    $color2 = mysqli_real_escape_string($con,(strip_tags($_POST["color2"],ENT_QUOTES)));
	$ambito=intval($_POST["id_ambito"]);
	$numero=intval($_POST["numero"]);
	$id=intval($_POST["id"]);
	$status=intval($_POST["status"]);
	
	// UPDATE data into database
    $sql = "UPDATE objetivos_gestion SET objetivo='".$objetivo."', copete='".$copete."',descripcion='".$descripcion."',color1='".$color1."',color2='".$color2."',ambito='".$ambito."',numero='".$numero."', status='".$status."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El Objetivo se ha sido actualizado con éxito.";
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