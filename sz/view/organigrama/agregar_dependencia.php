<?php

/* Connect To Database*/
 session_start();
 $user_id=$_SESSION['user_id'];

  require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
 require_once ("../../libraries/inventory.php");//Contiene funcion que conecta a la base de datos


if (isset($_POST['id']))
 {$id_cuenta_dependiente=$_POST['id'];}
 
 
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


if (isset($_GET['id']))//codigo elimina un elemento de la DB
{
 $id_tmp=intval($_GET['id']);
 $query = "select * FROM ministerios where id_cuenta_dependiente='$id_tmp'";
 $result = mysqli_query($con, $query);
 $resultado=mysqli_num_rows($result);
 
 $query = "select * FROM obras where ministerio='$id_tmp'";
 $result = mysqli_query($con, $query);
 $resultado2=mysqli_num_rows($result);
 
 if ($resultado==0 and $resultado2==0) 	
  {
  $query = "delete FROM ministerios where id='$id_tmp'";
  $result = mysqli_query($con, $query);
  $messages[] = "La dependecnia ha sido eliminada con éxito.";
 }
 else
  $errors[] = "No se puede eliminar una dependecnia que tiene subdependecnias asociadas";
}
else
{
if (!empty($denominacion) and !empty($codigo))
{

if (empty($id_cuenta_dependiente))
 $id_cuenta_dependiente=0;

$sql="INSERT INTO ministerios
	(id_cuenta_dependiente,denominacion,codigo,tipo_saldo,imputable) 
		VALUES ('$id_cuenta_dependiente', '$denominacion', '$codigo', '$tipo_saldo', '$imputable');";
 $query=mysqli_query($con,$sql);
   if ($query)
    $messages[] = "La dependecnia ha sido creada con éxito.";
   else 
    $errors[] = "Lo sentimos, el registro falló. Por favor, regrese y vuelva a intentarlo.";

 
}
else
 {
 $errors[] = "Ha ocurrido un error revise los datos ingresados";
 }

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