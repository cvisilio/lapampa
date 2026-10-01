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
	$sql="select * from indicadores_gestion where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$indicador=$rw['nombre_indicador'];
	$numero=$rw['codigo_indicador'];
	$meta=$rw['id_meta'];
	$formula_calculo=$rw['formula_calculo'];
	$link1=$rw['link1'];
	$open_target=$rw['open_target1'];
	
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="indicador" class="col-sm-2 control-label">Indicador</label>
	<div class="col-sm-7">
		 <textarea id="indicador" cols="10" name="indicador" class="form-control"><?php echo $indicador;?></textarea>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
        
	</div>
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Indicador</label>
	<div class="col-sm-7">
        
     <input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Código de la Meta" value="<?php echo $numero;?>" required>
     
    
	</div>
 </div>
 
  <div class="form-group">
	   <label for="id_meta" class="col-sm-2 control-label">Meta</label>
          <div class="col-sm-7">
                      <select class="form-control" name="id_meta" id="id_meta" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select metas_gestion.* from metas_gestion order by metas_gestion.id");
							
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['codigo_meta'].": ".$rw['meta'];
								if ($meta==$id1){$selected1="selected";}else{$selected1="";}
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
	<label for="link1" class="col-sm-2 control-label">Link 1</label>
	<div class="col-sm-8">
        
     <input type="text" class="form-control" id="link1" name="link1" placeholder="Ingresa un link de referencia" value="<?php echo $link1;?>">
    
	</div>
   
   <div class="col-sm-2">
    <input type="checkbox" name="open_target1" id="open_target1" <?php if($open_target==1) echo "checked"; ?>><span> Interno</span>
   </div>
   
 </div>

 <div class="form-group">
	   <label for="id_imagen" class="col-sm-2 control-label">Imagen</label>
          <div class="col-sm-7">
             <div id="load_img">
               <img class="img-responsive" src="<?php echo $formula_calculo;?>" alt="Imagen">
             </div>
             
             <div class="col-sm-2">
                      <input type="file" name="imagefile" id="imagefile" onChange="upload_image(<?php echo $id; ?>);">
              </div>
             
          </div>
  </div>        
