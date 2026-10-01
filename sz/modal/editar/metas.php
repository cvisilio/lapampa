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
	$sql="select * from metas_gestion where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$meta=$rw['meta'];
	$numero=$rw['codigo_meta'];
	$objetivo=$rw['id_objetivo'];
	$rubro=$rw['rubro'];
	$observaciones=$rw['observaciones'];
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="meta" class="col-sm-2 control-label">Meta</label>
	<div class="col-sm-10">
		 <textarea id="meta" cols="10" name="meta" class="form-control"><?php echo $meta;?></textarea>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Meta</label>
	<div class="col-sm-7">
        
     <input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Código de la Meta" value="<?php echo $numero;?>" required>
     
    
	</div>
 </div>
 
  <div class="form-group">
	   <label for="id_objetivo" class="col-sm-2 control-label">Objetivo</label>
          <div class="col-sm-10">
                      <select class="form-control" name="id_objetivo" id="id_objetivo" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from objetivos_gestion where status=1 order by ambito,numero");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['numero'] . ": ". $rw['objetivo'];
								if ($objetivo==$id1){$selected1="selected";}else{$selected1="";}
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
	<label for="observaciones" class="col-sm-2 control-label">Observaciones</label>
	<div class="col-sm-10">
        
   
     <textarea id="observaciones" cols="10" name="observaciones" class="form-control"><?php echo $observaciones;?></textarea>
    
	</div>
      
 </div>
