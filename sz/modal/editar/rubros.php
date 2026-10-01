<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	/* Connect To Database*/
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from rubros where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$name=$rw['name'];
	$status=$rw['status'];
	$muestra_copete=$rw['muestra_copete'];
	$muestra_solo_a_ministerio=$rw['muestra_solo_a_ministerio'];
	}
	}	
	else {exit;}
?>
<div class="form-group">
	<label for="name" class="col-sm-3 control-label">Nombre</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="name"  name="name" placeholder="Ingresa el Rubro" value="<?php echo $name;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
</div>
<div class="form-group">
	<label for="status" class="col-sm-3 control-label">Estado</label>
	<div class="col-sm-6">
		<select class="form-control" name="status" id="status" >
			<option value="1" <?php if ($status==1){echo "selected";}?>>Activo</option>
			<option value="2" <?php if ($status==2){echo "selected";}?>>Inactivo</option>
		</select>
	</div>
</div>

<div class="form-group">
		<label for="muestra_copete" class="col-sm-3 control-label">Muestra Copete</label>
		<div class="col-sm-3">
		            
           <select class="form-control" name="muestra_copete" id="muestra_copete">
			 <option value="">Selecciona</option>
				 <option value="1" <?php if ($muestra_copete==1){echo "selected";}?>>Si</option>
			     <option value="2" <?php if ($muestra_copete==2){echo "selected";}?>>No</option>
			 </select>
			 
		</div>
        
        <label for="muestra_solo_a_ministerio" class="col-sm-3 control-label">Muestra Solo a Ministerio</label>
		<div class="col-sm-3">
		 <select class="form-control" name="muestra_solo_a_ministerio" id="muestra_solo_a_ministerio">
			 <option value="">Selecciona</option>
				 	 <option value="1" <?php if ($muestra_solo_a_ministerio==1){echo "selected";}?>>Si</option>			     <option value="2" <?php if ($muestra_solo_a_ministerio==2){echo "selected";}?>>No</option>
			 </select>
			 
		</div>
        
	  </div>