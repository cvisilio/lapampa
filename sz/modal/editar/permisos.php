<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: facturacionlp.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	

	/* Connect To Database*/
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from user_group where user_group_id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$name=$rw['name'];
	$cadena=$rw['permission'];
	$archivos=substr_count($cadena,";");
	}
	}	
	else {echo "<script>location.replace('../../permisos.php')</script>";}	
?>
      <div class="form-group  ">
		<label for="nombres" class="col-sm-4 control-label">Nivel Administrador</label>
		<div class="col-sm-8">
		  <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo $name;?>" required>
		  <input type="hidden" id="user_group_id" name="user_group_id" value="<?php echo base64_encode($id)?>">	
		</div>
	  </div>

	  <table class="table table-hover table-nomargin">
		<thead>
		<tr>
			<th >Módulo</th>
			<th ><input name="Todos" type="checkbox" value="1" id="all_ver2" class="check_ver"/> Visualizar</th>
			<th ><input name="Todos" type="checkbox" value="1" id="all_mod2" class="check_mod"/> Editar</th>
			<th ><input name="Todos" type="checkbox" value="1" id="all_del2" class="check_del"/> Eliminar</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$num=0;
		while ($num<$archivos) {
			$parte=explode(";",$cadena);
			$permisos=$parte[$num];
			list($url,$view,$edit,$del)=explode(",",$permisos);
			if ($view==1){$ck1="checked";}else {$ck1="";}
			if ($edit==1){$ck2="checked";}else {$ck2="";}
			if ($del==1){$ck3="checked";}else {$ck3="";}
		?>
		<tr>
			<td>
				<?php echo $url;?>
				<input type='hidden' name='permisos_<?php echo $num;?>' value='<?php echo $url;?>'>
			</td>
			<td><input  type ='checkbox' name='view_<?php echo $num;?>' value='1' class='ck' <?php echo $ck1;?>></td>
			<td><input  type ='checkbox' name='edit_<?php echo $num;?>'  value='1' class='ck1' <?php echo $ck2;?>></td>
			<td><input  type ='checkbox' name='del_<?php echo $num;?>'  value='1'  class='ck2' <?php echo $ck3;?>></td>
		</tr>
		<?php		
			$num++;
		}	
		?>
		</tbody>
	</table>