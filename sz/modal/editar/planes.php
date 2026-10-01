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
	$sql="select * from planes_gestion where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$plan=$rw['plan'];
	$numero=$rw['codigo_plan'];
	$id_ambito=$rw['ambito'];
	$siglas=$rw['siglas'];
	$nombre_sub=$rw['nombre_sub'];
	$descripcion_plan=$rw['descripcion_plan'];
	$autoridad_aplicacion1=$rw['autoridad_aplicacion'];
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="plan" class="col-sm-2 control-label">Plan</label>
	<div class="col-sm-7">
		<input type="text" class="form-control" id="plan" name="plan" placeholder="Ingresa el Plan" value="<?php echo $plan;?>" required>
        
        
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Plan</label>
	<div class="col-sm-3">
        
     <input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Código del Plan" value="<?php echo $numero;?>" required>
         
	</div>
    
    <label for="siglas" class="col-sm-2 control-label">Siglas</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="siglas" name="siglas" placeholder="Ingresa las siglas" value="<?php echo $siglas;?>" required>
	</div>
    
 </div>
 
   <div class="form-group">
	<label for="descripcion_plan" class="col-sm-2 control-label">Descripci&oacute;n Plan</label>
	<div class="col-sm-10">
		 <textarea id="descripcion_plan" cols="10" name="descripcion_plan" class="form-control"><?php echo $descripcion_plan;?></textarea>
		
	</div>
   </div>
   
    <div class="form-group">
	<label for="nombre_sub" class="col-sm-2 control-label">Nombre Sub</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="nombre_sub" name="nombre_sub" placeholder="Ejemplo: Meta; Objetivo Espec&iacute;fico" value="<?php echo $nombre_sub;?>" required>
	</div>
   </div>
   
   <div class="form-group">  
   <label for="autoridad_aplicacion" class="col-sm-2 control-label">Autoridad Aplicacion</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="autoridad_aplicacion" id="autoridad_aplicacion" required>	
                      <option value="">Selecciona</option>					
						<?php 
							$sql=mysqli_query($con,"select * from ministerios");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['denominacion'];
							    if ($id==$autoridad_aplicacion1){$selected1="selected";}else{$selected1="";}
							?>
                            
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?> </option>
							<?php
							}
						?>
					  </select>
                    </div>
    </div>
            
 
  <div class="form-group">
	   <label for="id_ambito" class="col-sm-2 control-label">Ambito</label>
          <div class="col-sm-3">
                      <select class="form-control" name="id_ambito" id="id_ambito" required>
						<option value="">Selecciona</option>
						<option value="1" <?php if($id_ambito==1) echo "selected='selected'"; ?>>Nacional</option>
                        <option value="2" <?php if($id_ambito==2) echo "selected='selected'"; ?>>Provincial</option>
						
					  </select>
                    </div>

</div>

