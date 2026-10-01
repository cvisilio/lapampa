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
	
	$sql="select * from entidades where Id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$name=$rw['Establecimiento'];
	$status=$rw['status'];
	
	 $count=mysqli_query($con,"select count(*) AS num_mesas, MAX(Mesa) as Maximo, MIN(Mesa) as Minimo from mesas where CodigoEscuela='".$id."'" );
	$rw_count=mysqli_fetch_array($count);
	$Desde=$rw_count['Minimo'];
	$Hasta=$rw_count['Maximo'];
	$num_mesas=$rw_count['num_mesas'];
	
	
	}
	}	
	else {exit;}
?>
<div class="form-group">
	<label for="name" class="col-sm-3 control-label">Nombre</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="name" name="name" placeholder="" value="<?php echo $name;?>" readonly="readonly" >
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
     
	</div>
</div>
<div class="form-group">
	<label for="status" class="col-sm-3 control-label">Estado</label>
	<div class="col-sm-6">
     <?php if ($status==1) 
	          $estado="Activo";
	       else 
	          $estado="Inactivo";?>
		<input type="text" class="form-control" id="status" name="status" placeholder="" value="<?php echo $estado;?>" readonly="readonly" >
	</div>
</div>

<div class="form-group">
	<label for="name" class="col-sm-3 control-label">Desde</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" maxlength="3" id="desde" name="desde" placeholder="" value="<?php echo $Desde;?>" > 
		
	</div>
    <label for="name" class="col-sm-2 control-label">Hasta</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" maxlength="3" id="hasta" name="hasta" placeholder="" value="<?php echo $Hasta;?>" >
		
	</div>
</div>
