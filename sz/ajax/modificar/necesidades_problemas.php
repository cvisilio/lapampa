<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
	if (empty($_POST['nombre'])){
			$errors[] = "Nombre está vacío.";
	} elseif (!empty($_POST['nombre'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
	$descripcion = mysqli_real_escape_string($con,(strip_tags($_POST["descripcion"],ENT_QUOTES)));
	$programa=intval($_POST["id_programa"]);
	$avance=floatval($_POST["avance"]);
	$resuelto=intval($_POST["resuelto"]);
	$localidad=intval($_POST["id_localidad"]);
	$tipo= intval($_POST["tipo"]);
	$id=intval($_POST["id"]);
	$funcionario=intval($_POST["id_funcionario"]);
	// UPDATE data into database
    $sql = "UPDATE necesidades_problemas SET nombre='".$nombre."', tipo='".$tipo."',id_programa='".$programa."',localidad='".$localidad."',avance='".$avance."',resuelto='".$resuelto."',descripcion='".$descripcion."',id_funcionario='".$funcionario."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "Se ha sido actualizado con éxito.";
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