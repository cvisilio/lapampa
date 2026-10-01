<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
	if (empty($_POST['name'])){
			$errors[] = "Nombre de la localidad está vacío.";
	} elseif (!empty($_POST['name'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $name = mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
	$modulos=mysqli_real_escape_string($con,(strip_tags($_POST["modulos"],ENT_QUOTES)));
	
	$cantidad_habitantes=mysqli_real_escape_string($con,(strip_tags($_POST["cantidad_habitantes"],ENT_QUOTES)));
	$demanda_habitacional=mysqli_real_escape_string($con,(strip_tags($_POST["demanda_habitacional"],ENT_QUOTES)));
    $latitud=mysqli_real_escape_string($con,(strip_tags($_POST["latitud"],ENT_QUOTES)));
    $longitud=mysqli_real_escape_string($con,(strip_tags($_POST["longitud"],ENT_QUOTES)));
	
	$id=intval($_POST['id']);
	
	$lista_ganadora=intval($_POST['lista_ganadora']);
	
	$seccion_padron=intval($_POST['seccion_padron']);
	
	$circuito_default=mysqli_real_escape_string($con,(strip_tags($_POST["circuito_default"],ENT_QUOTES)));
	
	$id_loc_padron=intval($_POST['id_loc_padron']);
	$id_loc_gob_c=intval($_POST['id_loc_gob_c']); // Id codigo de las localidades que le asigna provincia, esto es importante para poder relacionar las importaciones de distintos ministerios
	$cargo_elecciones=$_POST["cargo_elecciones"];
	
	// UPDATE data into database localidad='".$name."',  id_loc_gob_c='".$id_loc_gob_c."',,cargo_elecciones='".$cargo_elecciones."', modulos='".$modulos."',
	
	//cargo_elecciones='".$cargo_elecciones."',
	//,  lista_ganadora='".$lista_ganadora."'
	
    $sql = "UPDATE localidades SET  cantidad_habitantes='".$cantidad_habitantes."',  demanda_habitacional='".$demanda_habitacional."',  latitud='".$latitud."',  longitud='".$longitud."', seccion_padron='".$seccion_padron."', circuito_default='".$circuito_default."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
	
	if (isset($_POST['actualiza_default']) && $_POST['actualiza_default'] == TRUE)
	{ 
	 $sql = "UPDATE entidades SET circuito='".$circuito_default."' WHERE CodigoLocalidad='".$id_loc_padron."'";
    $query = mysqli_query($con,$sql);
    }
    // if user has been added successfully
    if ($query) {
        $messages[] = "La Localidad ha sido actualizado con éxito.";
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