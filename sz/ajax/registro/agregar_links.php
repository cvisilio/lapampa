<?php

	if (empty($_POST['link'])){
			$errors[] = "link está vacío.";
		} elseif (!empty($_POST['link'])){
			require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
	 $link = $_POST["link"];
	 $id_tabla=intval($_POST["id_tabla"]);
	 $id_registro=intval($_POST["id_registro"]);
	 $titulo= mysqli_real_escape_string($con,(strip_tags($_POST["titulo"],ENT_QUOTES)));
	 $resumen=mysqli_real_escape_string($con,(strip_tags($_POST["resumen"],ENT_QUOTES)));
	 
			
			//Write register in to database
			
			$sql = "INSERT INTO links (link, id_tabla,id_registro,titulo,resumen) VALUES('".$link."','".$id_tabla."','".$id_registro."','".$titulo."','".$resumen."')";
			$query_new = mysqli_query($con,$sql);
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Link ha sido creado con éxito.";
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