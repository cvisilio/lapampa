<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
	if (empty($_POST['desde'])){
			$errors[] = "El campo desde está vacío.";
	} 
	elseif (empty($_POST['hasta'])){
			$errors[] = "El campo hasta está vacío.";
	}
	elseif (!empty($_POST['desde']) && !empty($_POST['hasta'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    
	$id=intval($_POST['id']);
	$desde=intval($_POST["desde"]);
	$_POST['desde']="";
	$hasta=intval($_POST["hasta"]);
	$_POST['hasta']="";
	
	// UPDATE data into database
	
	$fecha_modificado=date("Y-m-d H:i:s");	
	// UPDATE data into database //Establecimiento='".$name."',
    $sql1 = "UPDATE entidades SET ultima_modificacion='".$fecha_modificado."' WHERE Id='".$id."'";
    $query1 = mysqli_query($con,$sql1); 
	
	$sql="select * from entidades where Id='$id'";
	$query=mysqli_query($con,$sql);
	$rw=mysqli_fetch_array($query);
	$Establecimiento=$rw['Establecimiento'];
	$CodigoLocalidad=$rw['CodigoLocalidad'];
	$CodigoEscuela= $id;
	
		
	for ($i=$desde;$i<=$hasta;$i++)
	 {
	 //Mesa. CodigoEscuela, Establecimiento, CodigoLocalidad
	 $sql = "UPDATE mesas SET CodigoLocalidad='".$CodigoLocalidad."',  CodigoEscuela='".$CodigoEscuela."' WHERE Id='".$i."' ";
    $query = mysqli_query($con,$sql);
	} // end for
     
    if ($query) {
        $messages[] = "Las mesas han sido actualizadas con éxito.";
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