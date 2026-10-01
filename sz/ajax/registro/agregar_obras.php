<?php
session_start();
$user_id=$_SESSION['user_id'];
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
  
	if (empty($_POST['nombre_obra'])){
			$errors[] = "Nombre está vacío.";
			
	} 
	elseif (empty($_POST['ministerio'])){
			$errors[] = "Ministerio está vacío.";
	}
	
	elseif (!empty($_POST['nombre_obra']) && !empty($_POST['ministerio'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
	  
	$nombre_obra = mysqli_real_escape_string($con,(strip_tags($_POST["nombre_obra"],ENT_QUOTES)));
	
	$expediente = mysqli_real_escape_string($con,(strip_tags($_POST["expediente"],ENT_QUOTES)));
	
	$estado=intval($_POST['estado']);
	
	$ministerio=intval($_POST['ministerio']);
	
	if(!empty($_POST['fecha_licitar']))
	 { $fecha_licitar=mysqli_real_escape_string($con,(strip_tags($_POST["fecha_licitar"],ENT_QUOTES)));
	
	list($dia,$mes,$anio)=explode("/",$fecha_licitar);
	$fecha_licitar="$anio-$mes-$dia";
	}
	else
	 $fecha_licitar=NULL;
	
	if(!empty($_POST['fecha_finalizada'])){ 
	$fecha_finalizada=mysqli_real_escape_string($con,(strip_tags($_POST["fecha_finalizada"],ENT_QUOTES)));
	list($dia,$mes,$anio)=explode("/",$fecha_finalizada);
	$fecha_finalizada="$anio-$mes-$dia";}
	else
	 $fecha_finalizada=NULL;
	
	$cantidad=mysqli_real_escape_string($con,(strip_tags($_POST["cantidad"],ENT_QUOTES)));
	
	//$cantidad_nuevos=mysqli_real_escape_string($con,(strip_tags($_POST["cantidad_nuevos"],ENT_QUOTES)));
	
	$localidad=mysqli_real_escape_string($con,(strip_tags($_POST["localidad"],ENT_QUOTES)));
	
	$localidad2=mysqli_real_escape_string($con,(strip_tags($_POST["localidad2"],ENT_QUOTES)));
	
	$empresa=mysqli_real_escape_string($con,(strip_tags($_POST["empresa"],ENT_QUOTES)));
	
	$porcentaje_avance=mysqli_real_escape_string($con,(strip_tags($_POST["porcentaje_avance"],ENT_QUOTES)));
	
	$presupuesto_oficial=mysqli_real_escape_string($con,(strip_tags($_POST["presupuesto_oficial"],ENT_QUOTES)));
	
	$mes_base=mysqli_real_escape_string($con,(strip_tags($_POST["mes_base"],ENT_QUOTES)));
	
	$monto_adjudicado=mysqli_real_escape_string($con,(strip_tags($_POST["monto_adjudicado"],ENT_QUOTES)));
	
	$monto_actual=mysqli_real_escape_string($con,(strip_tags($_POST["monto_actual"],ENT_QUOTES)));
	
	$partida_contable=mysqli_real_escape_string($con,(strip_tags($_POST["partida_contable"],ENT_QUOTES)));
	
	$cuenta=mysqli_real_escape_string($con,(strip_tags($_POST["cuenta"],ENT_QUOTES)));
	
	$subclase=mysqli_real_escape_string($con,(strip_tags($_POST["subclase"],ENT_QUOTES)));
	
	$observaciones=mysqli_real_escape_string($con,(strip_tags($_POST["observaciones"],ENT_QUOTES)));
	
	$finalidad_y_funcion=0;
		
	
	// UPDATE data into database  
    $sql = "INSERT INTO obras (expediente,estado,ministerio,fecha_a_licitar,fecha_finalizada,localidad,localidad2,empresa_adjudicataria_1,porcentaje_avance,presupuesto_oficial,mes_base,monto_adjudicado,monto_actual,partida_contable,cuenta,subclase,cantidad,observaciones,nombre_obra) VALUES ('".$expediente."','".$estado."','".$ministerio."','".$fecha_licitar."', '".$fecha_finalizada."', '".$localidad."', '".$localidad2."', '".$empresa."', '".$porcentaje_avance."', '".$presupuesto_oficial."', '".$mes_base."', '".$monto_adjudicado."', '".$monto_actual."', '".$partida_contable."', '".$cuenta."', '".$subclase."','".$cantidad."', '".$observaciones."', '".$nombre_obra."')";
  
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
     if ($query==1) {
        $messages[] = "La obra ha sido actualizado con éxito.";
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