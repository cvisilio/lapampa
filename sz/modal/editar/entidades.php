<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: facturacionlp.com.ar
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
	 $id_rubro=$rw['id_rubro'];
	 $id_localidad=$rw['CodigoLocalidad'];
	 $status=$rw['status'];
	 $Telefono_Referencia=$rw['Telefono_Referencia'];
	 $circuito=$rw['circuito'];
	 $domicilio=$rw['DomicilioEstablecimiento'];
	 
	}
	}	
	else {exit;}
?>
<div class="form-group">
	<label for="name" class="col-sm-3 control-label">Nombre</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="name" name="name" placeholder="Ingresa el Nombre" value="<?php echo $name;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
</div>

<div class="form-group">
	<label for="domicilio" class="col-sm-3 control-label">Domicilio</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="domicilio" name="domicilio" placeholder="Ingresa el Domicilio" value="<?php echo $domicilio;?>" required>
		
	</div>
</div>


  <div class="form-group">
                    <label for="id_rubro" class="col-sm-3 control-label">Rubro</label>

                    <div class="col-sm-6">
                      <select class="form-control" name="id_rubro" id="id_rubro" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from rubros where modulos in(19) and status=1 order by name");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['name'];
								if ($id_rubro==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo utf8_encode($name);?></option>
							<?php
							}
						?>
					  </select>
                    </div>

</div>



 <div class="form-group">
                    <label for="CodigoLocalidad" class="col-sm-3 control-label">Localidad</label>

                    <div class="col-sm-6">
                      <select class="form-control" name="CodigoLocalidad" id="CodigoLocalidad" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");

							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id_loc_padron'];
								$name=$rw['localidad'];
								if ($id_localidad==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo utf8_encode($name);?></option>
							<?php
							}
						?>
					  </select>
                    </div>

</div>


<div class="form-group">
	<label for="status" class="col-sm-3 control-label">Estado</label>
	<div class="col-sm-6">
		<select class="form-control" name="status" id="status">
			<option value="1" <?php if ($status==1){echo "selected";}?>>Activo</option>
			<option value="2" <?php if ($status==2){echo "selected";}?>>Inactivo</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label for="telefono_referencia" class="col-sm-3 control-label">Tel&eacute;fono Contacto</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="telefono_referencia" name="telefono_referencia" placeholder="Ingresa el Teléfono" value="<?php echo $Telefono_Referencia;?>" required>
		
	</div>
</div>

<div class="form-group">
	<label for="circuito" class="col-sm-3 control-label">Circuito</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="circuito" name="circuito" placeholder="Ingresa el Circuito" value="<?php echo $circuito;?>" required>
		
	</div>
</div>