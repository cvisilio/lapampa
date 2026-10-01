<?php
	date_default_timezone_set("America/Santiago");
	/* Connect To Database*/
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	if (empty($_POST["nombres"])){
		$errors[] = "Nombres vacío";
	}elseif (!empty($_POST['nombres'])){
		$id=base64_decode($_POST["user_group_id"]);
		$user_group_id=intval($id);
		$num=1;
		$sql="select * from modulos";
		$q=mysqli_query($con,$sql);
		$num_md=mysqli_num_rows($q);
		$num=0;
		$permisos_url="";
		while ($num<$num_md){
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
		$permisos_url;
		// escaping, additionally removing everything that could be (html/javascript-) code
		$nombres = mysqli_real_escape_string($con,(strip_tags($_POST['nombres'], ENT_QUOTES)));
		$date_added=date("Y-m-d H:i:s");
		// update data into database
         $sql = "UPDATE user_group SET name='".$nombres."', permission='".$permisos_url."' 
		WHERE user_group_id='".$user_group_id."';";
        $query_new_user_insert = mysqli_query($con,$sql);
        // if user has been added successfully
         if ($query_new_user_insert) {
            $messages[] = "Grupo de usuario actualizado satisfactoriamente.";
          } else {
            $errors[] = "Lo sentimos, actualización falló. Intente nuevamente. ".mysqli_error($con);
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