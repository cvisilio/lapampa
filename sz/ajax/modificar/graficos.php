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
			$errors[] = "Titulo está vacío";
	} elseif (!empty($_POST['name'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $titulo = mysqli_real_escape_string($con,(strip_tags($_POST["name"],ENT_QUOTES)));
	$descripcion=mysqli_real_escape_string($con,(strip_tags($_POST["descripcion"],ENT_QUOTES)));
	
	$serie_agrupadora = mysqli_real_escape_string($con,(strip_tags($_POST["serie_agrupadora"],ENT_QUOTES))); 
	
	$estado=mysqli_real_escape_string($con,(strip_tags($_POST["status"],ENT_QUOTES)));
	$tipo=mysqli_real_escape_string($con,(strip_tags($_POST["tipo"],ENT_QUOTES)));
	
	$tipo_comparativo=mysqli_real_escape_string($con,(strip_tags($_POST["tipo_comparativo"],ENT_QUOTES)));
	
		
	$ids_graficos_comparar=mysqli_real_escape_string($con,(strip_tags($_POST["ids_graficos_comparar"],ENT_QUOTES)));
	
	$columna1="Nombre";
	$columna2="Valor";
		
	$id_grafico=intval($_POST['id']);
	$indicador_gestion=intval($_POST['indicador_gestion']);
	
	// UPDATE data into database
    $sql = "UPDATE graficos_estadisticos SET titulo='".$titulo."',  descripcion='".$descripcion."',  tipo='".$tipo."',  estado='".$estado."',  columna1='".$columna1."',  columna2='".$columna2."', serie_agrupadora='".$serie_agrupadora."', tipo_comparativo='".$tipo_comparativo."', ids_graficos_comparar='".$ids_graficos_comparar."', indicador_gestion='".$indicador_gestion."' WHERE id_grafico='".$id_grafico."'";
    $query = mysqli_query($con,$sql);

  if($tipo_comparativo==""){ // si no elige tipo grafico comparativo	
	$sql1=mysqli_query($con,"select * from valores_graficos_estadisticos where  id_grafico='$id_grafico'");
 $i=1;

  while ($rw=mysqli_fetch_array($sql1)){
			
			    $id=$rw['id'];			
				$caja_texto_nombre="nombre_".$id;
				$caja_texto_valor="valor_".$id;
			 	
				$nombre = mysqli_real_escape_string($con,(strip_tags($_POST[$caja_texto_nombre],ENT_QUOTES)));
				$valor =floatval($_POST[$caja_texto_valor]);
				
				if($valor <>""){
				 $sql = "UPDATE valores_graficos_estadisticos set nombre='".$nombre."', valor='".$valor."' WHERE id='".$id."'";
				 $query = mysqli_query($con,$sql);
				 } // if valor
			
	         } // while
	 
	} // if comparativo
	
    // if user has been added successfully
    if ($query) {
        $messages[] = "La Localidad ha sido actualizado con éxito.";
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