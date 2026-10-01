<?php

/* Connect To Database*/
 session_start();
 $user_id=$_SESSION['user_id'];

  require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
 require_once ("../../libraries/inventory.php");//Contiene funcion que conecta a la base de datos


if (isset($_POST['id']))
 $id_cuenta_origen=intval($_POST['id']);
else
 $errors[] = "Origen está vacío"; 
 

if (isset($_POST['id2']))
 $id_cuenta_destino=intval($_POST['id2']);
else
 $errors[] = "Destino está vacío"; 

 $sql = "UPDATE ministerios SET id_cuenta_dependiente='".$id_cuenta_destino."' WHERE id='".$id_cuenta_origen."'";
  

   $query=mysqli_query($con,$sql);
   if ($query)
    $messages[] = "La dependecnia ha sido pasada con éxito.";
   else 
    $errors[] = "Lo sentimos, el registro falló. Por favor, regrese y vuelva a intentarlo.";


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