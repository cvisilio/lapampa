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
			$errors[] = "Nombre de la Entidad está vacío.";
}  elseif (empty($_POST['id_rubro'])) {
            $errors[] = "Ingresa el Rubro";					
} 
 elseif (empty($_POST['CodigoLocalidad'])) {
            $errors[] = "Ingresa la Localidad";		
		} 
		
elseif (!empty($_POST['name']) && !empty($_POST['id_rubro']) && !empty($_POST['CodigoLocalidad'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $name =mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
	$domicilio =mysqli_real_escape_string($con,(strip_tags($_POST["domicilio"],ENT_QUOTES)));
	$status=intval($_POST['status']);
	$id_rubro=intval($_POST['id_rubro']);
	$id=intval($_POST['id']);
	$CodigoLocalidad=intval($_POST['CodigoLocalidad']);
	$Telefono_Referencia=mysqli_real_escape_string($con,(strip_tags($_POST["telefono_referencia"],ENT_QUOTES)));
	
    $circuito=mysqli_real_escape_string($con,(strip_tags($_POST["circuito"],ENT_QUOTES)));

	$fecha_modificado=date("Y-m-d H:i:s");	
	// UPDATE data into database //
    $sql = "UPDATE entidades SET status='".$status."',  id_rubro='".$id_rubro."',CodigoLocalidad='".$CodigoLocalidad."',Telefono_Referencia='".$Telefono_Referencia."',circuito='".$circuito."',ultima_modificacion='".$fecha_modificado."',Establecimiento='".$name."',DomicilioEstablecimiento='".$domicilio."' WHERE Id='".$id."'";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "La Entidad ha sido actualizado con éxito.";
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