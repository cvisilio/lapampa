<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	if (empty($_POST['edit_localidad'])){
		$errors[] = "Localidad está vacía.";
	} elseif (!empty($_POST['edit_localidad'])){
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
   
   // $localidad = intval($_POST["edit_localidad"]);
	
	$localidad = intval($_POST["edit_localidad"]);
	$ley = intval($_POST["edit_ley"]);
	$indice = floatval($_POST["edit_indice"]);
	$anio = intval($_POST["edit_anio"]);
	
	$id=intval($_POST['edit_id']);	
	// UPDATE data into database
	//id_localidad='".$localidad."',
    $sql = "UPDATE indices SET  id_localidad='".$localidad."', id_disponible='".$ley."', indice='".$indice."', anio='".$anio."' WHERE id='".$id."' ";
    $query = mysqli_query($con,$sql);
    // if product has been added successfully
    if ($query) {
        $messages[] = "El registro ha sido actualizado con éxito.";
    } else {
        $errors[] = "Lo sentimos, la actualización falló. Por favor, regrese y vuelva a intentarlo.";
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
}			
?>			