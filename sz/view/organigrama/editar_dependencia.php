<?php

/* Connect To Database*/
 session_start();
 $user_id=$_SESSION['user_id'];

 require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
 require_once ("../../libraries/inventory.php");//Contiene funcion que conecta a la base de datos

if (isset($_POST['id']))
 {$id=$_POST['id'];}
 if (isset($_POST['codigo']))
 {$codigo= mysqli_real_escape_string($con,(strip_tags($_POST['codigo'],ENT_QUOTES)));
 }

 if (isset($_POST['nombre']))
 {$denominacion= mysqli_real_escape_string($con,(strip_tags($_POST['nombre'],ENT_QUOTES)));
 }
 if (isset($_POST['tipo_saldo']))
 {$tipo_saldo= mysqli_real_escape_string($con,(strip_tags($_POST['tipo_saldo'],ENT_QUOTES)));
 }
 if (isset($_POST['imputable']))
 {$imputable= mysqli_real_escape_string($con,(strip_tags($_POST['imputable'],ENT_QUOTES)));
 
 } 

if (!empty($id) and !empty($denominacion) and !empty($codigo))
{
 $id=intval($_POST['id']);
 $sql = "UPDATE ministerios SET codigo='".$codigo."',  denominacion='".$denominacion."', tipo_saldo='".$tipo_saldo."', imputable='".$imputable."' WHERE id='".$id."' ";
 
 $query=mysqli_query($con,$sql);
   if ($query)
        $messages[] = "La dependencia ha sido modificada con éxito.";
   else 
        $errors[] = "Lo sentimos, el registro falló. Por favor, regrese y vuelva a intentarlo.";
    }
else
 {
 $errors[] = "Revise los datos ingresados";
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
			 
			}	?>