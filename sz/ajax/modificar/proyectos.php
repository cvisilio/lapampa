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
			$errors[] = "Nombre está vacío.";
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
	$id=intval($_POST["id"]);
	$indicadores_gestion= mysqli_real_escape_string($con,(strip_tags($_POST["indicadores_gestion"],ENT_QUOTES)));
	
	 $seleccion =mysqli_real_escape_string($con,(strip_tags($_POST["opciones_agregadas2"],ENT_QUOTES)));
	   $seleccionados= explode(",",$seleccion);// convierto el string a un array.
       
	  $sql1 = "delete from localidades_proyectos WHERE id_proyecto='".$id."' ";
      $query1 = mysqli_query($con,$sql1);
	   
	for ($i=0;$i<count($seleccionados);$i++) { 
	 $id_localidad=$seleccionados[$i];
     $sql2 = "INSERT INTO localidades_proyectos (id_proyecto, id_localidad) VALUES('".$id."','".$id_localidad."')";
	$query_new1 = mysqli_query($con,$sql2);  
    } 
	
	// UPDATE data into database
    $sql = "UPDATE proyectos_gestion SET proyecto='".$proyecto."', codigo_proyecto='".$numero."',id_programa='".$programa."',indicadores_gestion='".$indicadores_gestion."',id_funcionario='".$funcionario."',estado='".$estado."',avance='".$avance."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
    if ($query) {
        $messages[] = "El Proyecto se ha sido actualizado con éxito." . $seleccionados;
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