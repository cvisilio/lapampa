<?php

 
	if (empty($_POST['link'])){
			$errors[] = "Nombre está vacío.";
	} elseif (!empty($_POST['link'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $link = $_POST["link"];
	$id_tabla=intval($_POST["id_tabla"]);
	$id_registro=intval($_POST["id_registro"]);
	$titulo= mysqli_real_escape_string($con,(strip_tags($_POST["titulo"],ENT_QUOTES)));
	
	$resumen=mysqli_real_escape_string($con,(strip_tags($_POST["resumen1"],ENT_QUOTES)));
	
	$id=intval($_POST["id"]);
	
	// UPDATE data into database
    $sql = "UPDATE links SET link='".$link."', titulo='".$titulo."',id_registro='".$id_registro."',id_tabla='".$id_tabla."',resumen='".$resumen."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El Link se ha sido actualizado con éxito.";
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