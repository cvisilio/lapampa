<?php

	if (empty($_POST['name'])){
			$errors[] = "Nombre del Rubro está vacío.";
	} elseif (!empty($_POST['name'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $name = mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
	$status=intval($_POST['status']);
	$id=intval($_POST['id']);
	$muestra_copete=intval($_POST['muestra_copete']);
	$muestra_solo_a_ministerio=intval($_POST['muestra_solo_a_ministerio']);
	// UPDATE data into database
    $sql = "UPDATE rubros SET name='".$name."',status='".$status."',muestra_copete='".$muestra_copete."',muestra_solo_a_ministerio='".$muestra_solo_a_ministerio."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El rubro ha sido actualizado con éxito.";
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