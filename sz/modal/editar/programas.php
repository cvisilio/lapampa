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
	$sql="select * from programas_gestion where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$programa=$rw['programa'];
	$numero=$rw['codigo_programa'];
	$plan=$rw['id_plan'];
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="programa" class="col-sm-2 control-label">programa</label>
	<div class="col-sm-7">
		<input type="text" class="form-control" id="programa" name="programa" placeholder="Ingresa el programa" value="<?php echo $programa;?>" required>
        
        
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo programa</label>
	<div class="col-sm-3">
        
     <input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el N&uacute;mero del programa" value="<?php echo $numero;?>" required>
     
    
	</div>
   </div> 

  <div class="form-group">
 
	   <label for="id_plan" class="col-sm-2 control-label">Plan</label>
          <div class="col-sm-8">
                      <select class="form-control" name="id_plan" id="id_plan" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from planes_gestion order by codigo_plan");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['codigo_plan'] . ": ". $rw['plan'];
								if ($plan==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
                    
     </div>                
