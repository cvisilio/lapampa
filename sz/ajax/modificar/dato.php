<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
	if (empty($_POST['editor1'])){
			$errors[] = "Dato está vacío.";
	} 
	elseif (empty($_POST['titulo'])){
			$errors[] = "Titulo está vacío.";
	}
	
	elseif (!empty($_POST['editor1']) && !empty($_POST['titulo'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
	$id=intval($_POST['product_id']);
    $dato ="";
	$dato = $_POST["editor1"];
	$titulo = mysqli_real_escape_string($con,(strip_tags($_POST["titulo"],ENT_QUOTES)));
	$rubro=intval($_POST['rubro']);
	$ministerio=intval($_POST['ministerio']);
	$tipo=1;//intval($_POST['tipo']);
	$ambito=intval($_POST['ambito']);
	$mapa=intval($_POST['mapa']);
	$mapa2=intval($_POST['mapa2']);
	$graficos= mysqli_real_escape_string($con,(strip_tags($_POST["graficos"],ENT_QUOTES)));
	$copete = $_POST["copete"];
	
	$status=mysqli_real_escape_string($con,(strip_tags($_POST["status"],ENT_QUOTES)));
	//$rubro=intval($_POST['rubro']);
	
	
	//, , , ambito,orden
	
	$id=intval($_POST['product_id']);
	// UPDATE data into database
    $sql = "UPDATE datos SET dato='".$dato."', mapa='".$mapa."', titulo='".$titulo."', copete='".$copete."', rubro='".$rubro."', ministerio='".$ministerio."', tipo='".$tipo."', ambito='".$ambito."', status='".$status."', graficos='".$graficos."', mapa2='".$mapa2."' WHERE id='".$id."'";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El dato ha sido actualizado con éxito.";
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