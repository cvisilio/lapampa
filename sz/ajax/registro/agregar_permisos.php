<?php

	/* Connect To Database*/
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	/* Inicio Validacion*/
	if (empty($_POST["nombres"])){
		$errors[] = "Nombres vacío";
	}elseif (!empty($_POST['nombres'])){
		$num=1;
		$sql="select * from modulos";
		$q=mysqli_query($con,$sql);
		$num_md=mysqli_num_rows($q);
		$num=1;
		$permisos_url="";
		while ($num<=$num_md){
			$perm="permisos_".$num;
			$view="view_".$num;
			$edit="edit_".$num;
			$del="del_".$num;
			$permisosfiles=@$_POST[$perm];
			$permisosview=@$_POST[$view];
			$permisosedit=@$_POST[$edit];
			$permisosdel=@$_POST[$del];
			if (empty($permisosview)){$permisosview=0;}
			if (empty($permisosedit)){$permisosedit=0;}
			if (empty($permisosdel)){$permisosdel=0;}
			$permisos_url.=$permisosfiles.",".$permisosview.",".$permisosedit.",".$permisosdel.";";
			$num++;
		}
		// escaping, additionally removing everything that could be (html/javascript-) code
		$nombres = mysqli_real_escape_string($con,(strip_tags($_POST['nombres'], ENT_QUOTES)));
		$date_added=date("Y-m-d H:i:s");
		// Guardo los datos
         $sql = "INSERT INTO user_group (name, permission, date_added) VALUES 
			('".$nombres."', '".$permisos_url."','".$date_added."');";
        $query_new_user_insert = mysqli_query($con,$sql);
		// if is added successfully
         if ($query_new_user_insert) {
            $messages[] = "Datos han sido registrados satisfactoriamente.";
          } else {
             $errors[] = "Lo sentimos, registro falló. Intente nuevamente. ".mysqli_error($con);
          }
	}
	
	if (isset($errors)){
		?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Error! </strong>
		<?php
			foreach ($errors as $error){
				echo $error;
			}	
		?>
		</div>	
		<?php	
	} 
	if (isset($messages)){
	?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong>Aviso! </strong>
	<?php
		foreach ($messages as $message){
			echo $message;
		}
	?>
		</div>	
	<?php
	}
										
?>	