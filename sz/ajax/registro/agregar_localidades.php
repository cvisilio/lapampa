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
            $name = mysqli_real_escape_string($con,(strip_tags($_POST["localidad"],ENT_QUOTES)));
			$cantidad_habitantes=mysqli_real_escape_string($con,(strip_tags($_POST["cantidad_habitantes"],ENT_QUOTES)));
			$demanda_habitacional=mysqli_real_escape_string($con,(strip_tags($_POST["demanda_habitacional"],ENT_QUOTES)));
            $latitud=mysqli_real_escape_string($con,(strip_tags($_POST["latitud"],ENT_QUOTES)));
            $longitud=mysqli_real_escape_string($con,(strip_tags($_POST["longitud"],ENT_QUOTES)));
            $modulos=mysqli_real_escape_string($con,(strip_tags($_POST["modulos"],ENT_QUOTES)));

            $id_loc_gob_c=intval($_POST['id_loc_gob_c']); // Id codigo de las localidades que le asigna provincia, esto es importante para poder relacionar las importaciones de distintos ministerios
           	//Write register in to database
			
			$sql = "INSERT INTO localidades (name, modulos,cantidad_habitantes,demanda_habitacional,latitud,longitud,id_loc_gob_c) VALUES('".$name."','".$modulos."','".$cantidad_habitantes."','".$demanda_habitacional."','".$latitud."','".$longitud."','".$id_loc_gob_c."')";
			$query_new = mysqli_query($con,$sql);
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Localidad ha sido creado con éxito.";
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