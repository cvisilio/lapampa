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
	$sql="select * from funcionarios where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$funcionario=$rw['nombre'];
	$telefono=$rw['telefono'];
	$ministerio=$rw['ministerio'];
	$descripcion=$rw['descripcion_cargo'];
	$cargo=$rw['id_cargo'];
	$posicion=$rw['posicion'];
	$lista=$rw['lista']; // partido politico
	$eleccion=$rw['eleccion'];

	}
	}	
	else {exit;}
?>
  <div class="form-group">
	<label for="funcionario" class="col-sm-2 control-label">Funcionario</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="funcionario" name="funcionario" placeholder="Ingresa el Nombre del Funcionario" value="<?php echo $funcionario;?>" required>
		
        <input type="hidden" value="<?php echo $id;?>" name="id" id="id">
            
	</div>
 </div>

<div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripcion del Cargo</label>
	<div class="col-sm-8">
    <textarea id="descripcion" cols="10" name="descripcion" class="form-control"><?php echo $descripcion;?></textarea>    
   	</div>
 </div> 

 <div class="form-group">
	<label for="telefono" class="col-sm-2 control-label">Telefono</label>
	<div class="col-sm-4">
		<input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ingresa el Telefono" value="<?php echo $telefono;?>">
	</div>
    
   </div>
   
      
   <div class="form-group">  
            <label for="lista" class="col-sm-2 control-label">Lista Partido</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="lista" id="lista">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from listas_elecciones order by eleccion desc");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id_lista'];
								$name= $rw['abreviatura_provincial'] . " (". $rw['numero_lista_provincial'] . ")";
								if ($lista==$id){$selected1="selected";}else{$selected1="";}
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
            <label for="eleccion" class="col-sm-2 control-label">Nro Elecci&oacute;n</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="eleccion" id="eleccion">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from elecciones");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id_eleccion'];
								$name= $rw['tipo'];
								if ($eleccion==$id){$selected1="selected";}else{$selected1="";}
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
            <label for="id_ministerio" class="col-sm-2 control-label">Ministerio</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_ministerio" id="id_ministerio" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from ministerios order by denominacion");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name= $rw['denominacion'];
								if ($ministerio==$id){$selected1="selected";}else{$selected1="";}
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
            <label for="id_cargo" class="col-sm-2 control-label">Cargo</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_cargo" id="id_cargo" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from cargos_poderes_estado order by cargo");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name= $rw['cargo'];
								if ($cargo==$id){$selected1="selected";}else{$selected1="";}
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
	<label for="posicion" class="col-sm-2 control-label">Posicion</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="posicion" name="posicion" placeholder="Ingresa la Posición" value="<?php echo $posicion;?>">
	</div>
    
   </div> 
