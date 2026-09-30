<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	if (empty($_POST['delete_id'])){
		$errors[] = "Id vacío.";
	} elseif (!empty($_POST['delete_id'])){
	require_once ("../conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
    $id=intval($_POST['delete_id']);
	
	$query_validate=mysqli_query($con,"select monto from transferencias where id_compromiso ='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==0)
	 {
      if ($query = mysqli_query($con,"DELETE FROM compromisos_am WHERE id='$id'")) {
        $messages[] = "El registro se eliminado con éxito.";
      } 
	 else {
        $errors[] = "Lo sentimos, la eliminación falló. Por favor, regrese y vuelva a intentarlo.";
       }
	 }  
   else
    {
	$errors[]="Error al eliminar. El Compromiso tiene transferencias";
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