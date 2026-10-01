<?php
	
				/* Connect To Database*/
				require_once ("../config/db.php");
				require_once ("../config/conexion.php");
				if (isset($_FILES["imagefile"])){
	            
				$id=intval($_REQUEST['id']);
				$tabla=mysqli_real_escape_string($con,(strip_tags($_REQUEST['tabla'], ENT_QUOTES)));
				$carpeta=mysqli_real_escape_string($con,(strip_tags($_REQUEST['carpeta'], ENT_QUOTES))); 
				$titulo=mysqli_real_escape_string($con,(strip_tags($_REQUEST['titulo'], ENT_QUOTES))); 
				
				$target_dir="../hemeroteca/".$carpeta."/";
				$target_file = $target_dir . basename($_FILES["imagefile"]["name"]);
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				$imageFileZise=$_FILES["imagefile"]["size"];
				
					
				
				/* Inicio Validacion*/
				// Allow certain file formats
				if(($imageFileType != "txt" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "pdf" ) and $imageFileZise>0) {
				$errors[]= "<p>Lo sentimos, sólo se permiten archivos TXT , DOC, DOCX y PDF.</p>";
				} else if ($imageFileZise > 1048576) {//1048576 byte=1MB
				$errors[]= "<p>Lo sentimos, pero el archivo es demasiado grande. Selecciona logo de menos de 1MB</p>";
				}  else
			{
				
				
				
				/* Fin Validacion*/
				if ($imageFileZise>0){
				move_uploaded_file($_FILES["imagefile"]["tmp_name"], $target_file);
				$archivo=basename($_FILES["imagefile"]["name"]);
				$ruta="hemeroteca/".$carpeta."/".$imagen;
				
				}	else { $logo_update="";}
				    $sql ="INSERT INTO archivos (registro_id,tabla,archivo,titulo) VALUES('".$id."','".$tabla."','".$ruta."','".$titulo."')";
				    
					$query_new_insert = mysqli_query($con,$sql);

                   
                    if ($query_new_insert) {
                        ?>
						<img class="img-responsive" src="img/<?php echo $carpeta ?>/<?php echo $imagen;?>" alt="<?php echo $target_file; ?>">
						<?php
                    } else {
                        $errors[] = "Lo sentimos, actualización falló. Intente nuevamente. ".mysqli_error($con);
                    }
		
				
				}
				
				}	
				
				
				
		
	?>
	
	<?php 
										if (isset($errors)){
											?>
										<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">&times;</button>
											<strong>Error! </strong>
											<?php
											foreach ($errors as $error){
													echo $error;
												}
											?>
										</div>	
											<?php
										}
									?>
									<?php 
										if (isset($messages)){
											?>
										<div class="alert alert-success">
											<button type="button" class="close" data-dismiss="alert">&times;</button>
											<strong>Aviso! </strong>
											<?php
											foreach ($messages as $message){
													echo $message;
												}
											?>
										</div>	
											<?php
										}
									?>