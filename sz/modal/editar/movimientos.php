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
	$sql="select * from movimientos_localidades where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$name=$rw['detalle'];
	$id_localidad=$rw['id_localidad'];
	$status=$rw['estado_movimiento'];
	$valor=$rw['valor'];
	}
	}	
	else {exit;}
?>
<div class="form-group">
	<label for="detalle" class="col-sm-3 control-label">Detalle</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="detalle" name="detalle" placeholder="Ingresa el Detalle" value="<?php echo $name;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
</div>

  <div class="form-group">
                    <label for="id_rubro" class="col-sm-3 control-label">Localidad</label>

                    <div class="col-sm-6">
                      <select class="form-control" name="id_localidad" id="id_localidad" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=utf8_encode($rw['localidad']);
								if ($id_localidad==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>

</div>
<div class="form-group">
	<label for="status" class="col-sm-3 control-label">Estado</label>
	<div class="col-sm-6">
		<select class="form-control" name="status" id="status">
			<option value="0" <?php if ($status==0){echo "selected";}?>>En Curso</option>
			<option value="1" <?php if ($status==1){echo "selected";}?>>AnteProyecto</option>
		</select>
	</div>
</div>

 <div class="form-group">
		<label for="valor" class="col-sm-3 control-label">Monto</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" value="<?php echo $valor;?>"  id="valor" name="valor" placeholder="Ingresa el Monto" required>
			 
		</div>
	  </div>