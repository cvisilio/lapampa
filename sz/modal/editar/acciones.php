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
	$sql="select * from acciones where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$accion=$rw['nombre'];
	$meta=$rw['id_meta'];
	$proyecto=$rw['id_proyecto'];
	$estado=$rw['estado'];
	$observaciones=$rw['observaciones'];
	$responsable=$rw['responsable'];
	$numero_accion=$rw['numero_accion'];
	
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="indicador" class="col-sm-2 control-label">Acci&oacute;n</label>
	<div class="col-sm-10">
		 <textarea id="accion" cols="10" name="accion" class="form-control"><?php echo $accion;?></textarea>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
        
	</div>
 </div>
 
 <div class="form-group">
	 <label for="numero_accion" class="col-sm-2 control-label">N&uacute;mero</label>
	<div class="col-sm-5">
      <input type="text" class="form-control" id="numero_accion" name="numero_accion" placeholder="Ingresa numero acci&oacute;n" value="<?php echo $numero_accion;?>">
    </div>
    
    
    <label for="estado" class="col-sm-2 control-label">Estado</label>
	<div class="col-sm-3">
      
     <select class="form-control" name="estado" id="estado" required>
						<option value="">Selecciona</option>
						
							<option value="1" <?php if ($estado==1)echo ' selected="selected"';else 'selected=""'; ?>> Terminado </option>
                            <option value="2" <?php if ($estado==2)echo ' selected="selected"';else 'selected=""'; ?>> En Curso </option>
						
					  </select>    
     
    
	</div>
   </div> 
 
 <div class="form-group">
	   <label for="id_meta" class="col-sm-2 control-label">Meta</label>
          <div class="col-sm-9">
                      <select class="form-control" name="id_meta" id="id_meta">
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
	   <label for="id_meta" class="col-sm-2 control-label">Proyecto</label>
          <div class="col-sm-7">
                      <select class="form-control" name="id_proyecto" id="id_proyecto">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select proyectos_gestion.* from proyectos_gestion order by proyectos_gestion.id");
							
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['codigo_proyecto'].": ".$rw['proyecto'];
								if ($proyecto==$id1){$selected1="selected";}else{$selected1="";}
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
	   <label for="responsable" class="col-sm-2 control-label">Responsable</label>
          <div class="col-sm-10">
                      <select class="form-control" name="responsable" id="responsable">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select funcionarios.nombre,funcionarios.id,ministerios.denominacion from funcionarios,ministerios where funcionarios.ministerio=ministerios.id order by ministerios.denominacion, funcionarios.nombre");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['denominacion'] . ": ". $rw['nombre'];
								if ($responsable==$id1){$selected1="selected";}else{$selected1="";}
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

 