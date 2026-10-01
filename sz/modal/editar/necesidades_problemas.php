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
	$sql="select * from necesidades_problemas where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$nombre=$rw['nombre'];
	$descripcion=$rw['descripcion'];
	$tipo=$rw['tipo'];
	$id_localidad=$rw['localidad'];
	$avance=$rw['avance'];
	$resuelto=$rw['resuelto'];
	$programa=$rw['id_programa'];
	$funcionario=$rw['id_funcionario'];
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="nombre" class="col-sm-2 control-label">Nombre</label>
	<div class="col-sm-7">
		<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el Nombre" value="<?php echo $nombre;?>" required>
        
        
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripcion</label>
	<div class="col-sm-7">
       <textarea id="descripcion" cols="10" name="descripcion" class="form-control" ><?php echo $descripcion;?></textarea>  
       
    
	</div>
 </div>
 
  <div class="form-group">
	   <label for="id_localidad" class="col-sm-2 control-label">Localidad</label>
          <div class="col-sm-8">
                      <select class="form-control" name="id_localidad" id="id_localidad">
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
	   <label for="tipo" class="col-sm-2 control-label">Tipo</label>
          <div class="col-sm-4">
                      <select class="form-control" name="tipo" id="tipo" required>
						<option value="">Selecciona</option>
						
							<option value="1" <?php if ($tipo==1)echo ' selected="selected"';else 'selected=""'; ?>> Necesidades </option>
                            <option value="2" <?php if ($tipo==2)echo ' selected="selected"';else 'selected=""'; ?>> Problemas </option>
						
					  </select>
                    </div>
      
      <label for="avance" class="col-sm-2 control-label">Avance</label>
          <div class="col-sm-2"> 
     <input type="text" class="form-control" id="avance" name="avance" placeholder="Ingresa el Avance" value="<?php echo $avance;?>" required>  
      </div>
   
                 
    </div>
  
  <div class="form-group">  
    <label for="resuelto" class="col-sm-2 control-label">Resuelto</label>
          <div class="col-sm-3">
                      <select class="form-control" name="resuelto" id="resuelto" required>
						<option value="">Selecciona</option>
						
							<option value="1" <?php if ($resuelto==1)echo ' selected="selected"';else 'selected=""'; ?>> Si </option>
                            <option value="2" <?php if ($resuelto==2)echo ' selected="selected"';else 'selected=""'; ?>> No </option>
						
					  </select>
    
                    </div>      
   </div>

<div class="form-group">
	   <label for="id_programa" class="col-sm-2 control-label">Programa</label>
     <div class="col-sm-8">   
        <select id="id_programa" name="id_programa" class="form-control">
 <option value="">Todos los Programas </option>
							<?php
							$sql1=mysqli_query($con,"select * from programas_gestion order by codigo_programa");
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id'];
							  $name=$rw1['codigo_programa'] . ": ". $rw1['programa'];
							  if ($programa==$id){$selected1="selected";}else{$selected1="";}
								?>
								<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>	
								<?php 
							}
							?>
		</select>
  </div>
  
 </div>      
 
  <div class="form-group">
	   <label for="id_funcionario" class="col-sm-2 control-label">Funcionario</label>
          <div class="col-sm-10">
                      <select class="form-control" name="id_funcionario" id="id_funcionario">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select funcionarios.nombre,funcionarios.id,ministerios.denominacion from funcionarios,ministerios where funcionarios.ministerio=ministerios.id order by ministerios.denominacion, funcionarios.nombre");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['denominacion'] . ": ". $rw['nombre'];
								if ($funcionario==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
          </div>    
       