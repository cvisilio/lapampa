<?php

// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}	
	if (empty($_POST['proyecto'])){
			$errors[] = "Programa está vacío.";
		} elseif (!empty($_POST['proyecto'])){
			require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
    $proyecto = mysqli_real_escape_string($con,(strip_tags($_POST["proyecto"],ENT_QUOTES)));
	$programa=intval($_POST["id_programa"]);
	$funcionario=intval($_POST["id_funcionario"]);
	$estado=intval($_POST["estado"]);
	$avance=floatval($_POST["avance"]);
	$numero= mysqli_real_escape_string($con,(strip_tags($_POST["numero"],ENT_QUOTES)));
	$indicadores_gestion= mysqli_real_escape_string($con,(strip_tags($_POST["indicadores_gestion"],ENT_QUOTES)));
			
			//Write register in to database
			
			$sql = "INSERT INTO proyectos_gestion (proyecto, id_programa,codigo_proyecto,indicadores_gestion,id_funcionario,estado,avance) VALUES('".$proyecto."','".$programa."','".$numero."','".$indicadores_gestion."','".$funcionario."','".$estado."','".$avance."')";
			$query_new = mysqli_query($con,$sql);
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Proyecto ha sido creada con éxito.";
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