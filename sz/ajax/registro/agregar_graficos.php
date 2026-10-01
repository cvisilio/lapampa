<?php

	if (empty($_POST['name'])){
			$errors[] = "titulo está vacío.";
		} elseif (!empty($_POST['name'])){
			require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
            $titulo = mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
			$serie_agrupadora = mysqli_real_escape_string($con,(strip_tags($_POST["serie_agrupadora"],ENT_QUOTES))); 
			 
			$descripcion=mysqli_real_escape_string($con,(strip_tags($_POST["descripcion"],ENT_QUOTES)));
			$tipo=mysqli_real_escape_string($con,(strip_tags($_POST["tipo"],ENT_QUOTES)));
			
			$tipo_comparativo=mysqli_real_escape_string($con,(strip_tags($_POST["tipo_comparativo"],ENT_QUOTES)));
	
		
	        $ids_graficos_comparar=mysqli_real_escape_string($con,(strip_tags($_POST["ids_graficos_comparar"],ENT_QUOTES)));
			
			$indicador_gestion=intval($_POST['indicador_gestion']);
			
			$columna1="Nombre";
			$columna2="Valor";
	
			$sql = "INSERT INTO graficos_estadisticos (titulo, descripcion, tipo, serie_agrupadora,tipo_comparativo,ids_graficos_comparar,indicador_gestion) VALUES('".$titulo."','".$descripcion."','".$tipo."','".$serie_agrupadora."','".$tipo_comparativo."','".$ids_graficos_comparar."','".$indicador_gestion."')";
			$query_new = mysqli_query($con,$sql);
			
	 if($tipo_comparativo==""){ // si no elige tipo grafico comparativo			
			/*Obtengo ultimo id indicador para insertarlo en tabla valores_graficos_estadisticos*/
			$id_indicador= 0;
			$sql=mysqli_query($con,"select id_grafico from graficos_estadisticos order by id_grafico desc limit 0,1");
			$rw=mysqli_fetch_array($sql); 
			$id_grafico=$rw['id_grafico'];
			
			/**/
			$cantidad=10;						
			for($i=1; $i<=$cantidad; $i++){
				$caja_texto_nombre="nombre".$i;
				$caja_texto_valor="valor".$i;
				$nombre = mysqli_real_escape_string($con,(strip_tags($_POST[$caja_texto_nombre],ENT_QUOTES)));
				$valor = floatval($_POST[$caja_texto_valor]);
				if($valor <>""){
				 $sql = "INSERT INTO valores_graficos_estadisticos (nombre, valor, id_grafico) VALUES('".$nombre."','".$valor."','".$id_grafico."')"; 
				 $query = mysqli_query($con,$sql);
				 } // if valor
	         } // for
			 
	  }	 // if tipo_comparativo	 
			
			
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