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
	$sql="select * from objetivos_gestion where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$objetivo=$rw['objetivo'];
	$copete=$rw['copete'];
	$descripcion=$rw['descripcion'];
	$numero=$rw['numero'];
	$status=$rw['status'];
	$color1=$rw['color1'];
	$color2=$rw['color2'];
	$ambito=$rw['ambito'];
	$icono=$rw['icono'];
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="objetivo" class="col-sm-2 control-label">Objetivo</label>
	<div class="col-sm-7">
		<input type="text" class="form-control" id="objetivo" name="objetivo" placeholder="Ingresa el Objetivo" value="<?php echo $objetivo;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="copete" class="col-sm-2 control-label">Copete</label>
	<div class="col-sm-7">
     <textarea id="copete" cols="10" name="copete" class="form-control"><?php echo $copete;?></textarea>
    
	</div>
 </div>
 
 <div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n</label>
	<div class="col-sm-7">
    <textarea id="descripcion" cols="10" name="descripcion" class="form-control" ><?php echo $descripcion;?></textarea>
    		
	</div>
 </div>
 
 <div class="form-group">
	<label for="color1" class="col-sm-2 control-label">Color 1</label>
	<div class="col-sm-3">
   <select class="form-control" name="color1" id="color1" required>
						<option value="">Selecciona</option>
						                          
                        
							<option value="red" <?php if ($color1=="red") echo "selected='selected'";?>>Rojo</option>
                            
                            <option value="darkred" <?php if ($color1=="darkred") echo "selected='selected'";?>>Bordo</option>
                           	<option value="orange" <?php if ($color1=="orange") echo "selected='selected'";?>>Naranja</option>     
                          	<option value="darkorange" <?php if ($color1=="darkorange") echo "selected='selected'";?>>Marr&oacute;n</option>
                            	<option value="blue" <?php if ($color1=="blue") echo "selected='selected'";?>>Azul</option>
                                <option value="lightblue" <?php if ($color1=="lightblue") echo "selected='selected'";?>>Celeste</option>
                                <option value="green" <?php if ($color1=="green") echo "selected='selected'";?>>Verde</option>
                                <option value="purple" <?php if ($color1=="purple") echo "selected='selected'";?>>Violeta</option>
                                
                                  
						  </select>
    		
	</div>
    
    <label for="color2" class="col-sm-2 control-label">Color 2</label>
	<div class="col-sm-3">
     
     <select class="form-control" name="color2" id="color2" required>
						<option value="">Selecciona</option>
						
							<option value="danger" <?php if ($color2=="danger") echo "selected='selected'";?>>Rojo</option>
                            <option value="warning" <?php if ($color2=="warning") echo "selected='selected'";?>>Amarillo</option>
                           	<option value="success" <?php if ($color2=="success") echo "selected='selected'";?>>Verde</option>     
                          	<option value="info" <?php if ($color2=="info") echo "selected='selected'";?>>Celeste</option>
                            	<option value="primary" <?php if ($color2=="primary") echo "selected='selected'";?>>Azul</option>  
						  </select>
    		
	</div>
    
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">N&uacute;mero</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Número" value="<?php echo $numero;?>" required>
	</div>
 
                    <label for="id_ambito" class="col-sm-2 control-label">Plan</label>

                    <div class="col-sm-3">
                      <select class="form-control" name="id_ambito" id="id_ambito" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from planes_gestion order by siglas");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['siglas'];
								if ($ambito==$id1){$selected1="selected";}else{$selected1="";}
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
	<label for="status" class="col-sm-2 control-label">Estado</label>
	<div class="col-sm-7">
		<select class="form-control" name="status" id="status">
			<option value="1" <?php if ($status==1){echo "selected";}?>>Activo</option>
			<option value="2" <?php if ($status==2){echo "selected";}?>>Inactivo</option>
		</select>
	</div>
</div>

<div class="form-group">
	   <label for="id_imagen" class="col-sm-2 control-label">Imagen</label>
          <div class="col-sm-7">
             <div id="load_img">
               <img class="img-responsive" src="<?php echo $icono;?>" alt="Imagen">
             </div>
             
             <div class="col-sm-2">
                      <input type="file" name="imagefile" id="imagefile" onChange="upload_image(<?php echo $id; ?>);">
              </div>
             
          </div>
  </div>        