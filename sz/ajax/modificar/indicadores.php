<?php
function color1($n)
{

$string_color=array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");


$div_uno=intval($n % 16);
if($div_uno <0 or $div_uno>15)
 $div_uno=1;
 
$uno=$string_color[$div_uno];
$div_dos=intval($n / 16);
if($div_dos <0 or $div_dos>15)
 $div_dos=1;
$dos=$string_color[$div_dos];



return $uno . $dos;
}

 //https://html-color-codes.info/codigos-de-colores-hexadecimales/

 if (empty($_POST['titulo'])){
			$errors[] = "Titulo está vacío.";
	} elseif (!empty($_POST['titulo'])){
	require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $titulo = mysqli_real_escape_string($con,(strip_tags($_POST["titulo"],ENT_QUOTES)));
	
	$descripcion =  $_POST["editor1"];
	  
	if (isset($_POST["degradado"]))
	 $degradado=mysqli_real_escape_string($con,(strip_tags($_POST["degradado"],ENT_QUOTES)));
	else
	 $degradado=false;
	 
	$color_degradado = mysqli_real_escape_string($con,(strip_tags($_POST["color_degradado"],ENT_QUOTES)));
	
	$region=intval($_POST["region"]);
	
	$multiplicador_radio_circulo=intval($_POST["multiplicador_radio_circulo"]);
			
	$color1=substr($color_degradado,0,3); // obtener los primeros caracteres de la cadena de color
	$color2=substr($color_degradado,-2); // obtener los ultimos caracteres de la cadena de color
    // *luego se invoca la funcion (línea 102, color1($icolor)) para que nos devuelva los caracteres del medio que irían cambiando para formar el degradado*	

	
	if($degradado==true){ //obtener maximo y minimo para normalizar DECIMAL(10, 4)
	
	 $sql="select regiones_mapa.id from regiones_mapa where region='$region'";
	 $query2=mysqli_query($con,$sql);
	 $maximo=0;
	 $minimo=0;
 	while($rw = mysqli_fetch_array($query2)){
	 $provincia=trim($rw['id']);
	 $texto = mysqli_real_escape_string($con,(strip_tags($_POST[$provincia],ENT_QUOTES)));
	  if(is_numeric($texto))
	  {
	   $valor=intval($texto);
	   
	   if($valor>$maximo)
	    $maximo=$valor;
	   
	   if($valor<$minimo or $minimo==0)
	    $minimo=$valor;

	  }
	  
	}	 // while
	 
	
	
	} // if degradado==true
	
	$indicador_gestion=intval($_POST["indicador_gestion"]);
	$id_accion=intval($_POST["id_accion"]);
	 
	$id=intval($_POST['id']);
	
		
	// UPDATE data into database
    $sql = "UPDATE indicadores SET titulo='".$titulo."',  descripcion='".$descripcion."',  multiplicador_radio_circulo='".$multiplicador_radio_circulo."', indicador_gestion='".$indicador_gestion."', accion='".$id_accion."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if user has been added successfully
	
	$sql="select regiones_mapa.id from regiones_mapa where region='$region'";
	$query2=mysqli_query($con,$sql);
 	while($rw = mysqli_fetch_array($query2)){
	    $provincia=trim($rw['id']);
		
		$texto = mysqli_real_escape_string($con,(strip_tags($_POST[$provincia],ENT_QUOTES)));
		$link = mysqli_real_escape_string($con,(strip_tags($_POST["link_" .$provincia],ENT_QUOTES)));
		 if(is_numeric($texto) && ($degradado==true)) 
		 {
		 //normaliza entre unos límites definidos (valor-mínimo_valor) / (Máximo_valor - mínimo_valor)
		 $texto_valor=intval($texto);
         $porcentaje_valor=intval(($texto_valor-$minimo)/($maximo-$minimo)*100); //normalizacion estándar y luego se aplica el porcentaje que representa en 255
         $icolor=($porcentaje_valor*255) /100;
         $icolor=255-$icolor; 
         $color=$color1 . color1($icolor) . $color2;
 
		 $color = "#" . mysqli_real_escape_string($con,(strip_tags($color,ENT_QUOTES)));
		 }
		 else
		 {
		 $color = mysqli_real_escape_string($con,(strip_tags($_POST["color_" .$provincia],ENT_QUOTES)));
		 }
		 
		
		 
	  	 $sql = "UPDATE indicadores_provincias SET text='".$texto."',  color='".$color."',  url='".$link."' WHERE indicador='".$id."' and provincia='".$provincia."'";
         $query = mysqli_query($con,$sql);
	}
	
    if ($query) {
        $messages[] = "El indicador ha sido actualizado con éxito.";
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
						<strong>¡Bien hecho! <?php echo $maximo; ?></strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
?>			