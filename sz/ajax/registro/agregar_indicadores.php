<?php

	if (empty($_POST['titulo'])){
			$errors[] = "titulo está vacío.";
		} elseif (!empty($_POST['titulo'])){
			require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
            $titulo = mysqli_real_escape_string($con,(strip_tags($_POST["titulo"],ENT_QUOTES)));
	    	$descripcion =  $_POST["editor1"];
			$region=intval($_POST["region1"]);
			
			$indicador_gestion=intval($_POST["indicador_gestion"]);
			$id_accion=intval($_POST["id_accion"]);
				
			$sql = "INSERT INTO indicadores (titulo, descripcion, ambito, indicador_gestion, accion) VALUES('".$titulo."','".$descripcion."','".$region."','".$indicador_gestion."','".$id_accion."')"; 
			$query_new = mysqli_query($con,$sql);
			
			
			
			/*Obtengo ultimo id indicador para insertarlo en tabla indicadores provincias*/
			$id_indicador= 0;
			$sql=mysqli_query($con,"select id from indicadores order by id desc limit 0,1");
			$rw=mysqli_fetch_array($sql); 
			$id_indicador=$rw['id'];
			
			/**/
			
			$sql="select regiones_mapa.id from regiones_mapa where region='$region'";
			
            $query2=mysqli_query($con,$sql);
			
			while($rw = mysqli_fetch_array($query2)){
				$provincia=$rw['id'];
				$texto = mysqli_real_escape_string($con,(strip_tags($_POST[$provincia],ENT_QUOTES)));
				$color = mysqli_real_escape_string($con,(strip_tags($_POST["color_" .$provincia],ENT_QUOTES)));
				$link = mysqli_real_escape_string($con,(strip_tags($_POST["link_" .$provincia],ENT_QUOTES)));
				
				 $sql = "INSERT INTO indicadores_provincias (text, color, provincia, indicador,url) VALUES('".$texto."','".$color."','".$provincia."','".$id_indicador."','".$link."')"; 
				 $query = mysqli_query($con,$sql);
	         }
			
			
            // if has been added successfully
            if ($query_new) {
                $messages[] = "Indicador ha sido creado con éxito.";
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