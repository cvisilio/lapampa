 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nueva Entidad</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
		<label for="name" class="col-sm-3 control-label">Nombre</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control"  id="name" name="name" placeholder="Ingresa el Nombre" required>
			 
		</div>
	  </div>
     
    <div class="form-group">
	<label for="domicilio" class="col-sm-3 control-label">Domicilio</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="domicilio" name="domicilio" placeholder="Ingresa el Domicilio" value="" required>
		
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
								$id=$rw['id'];
								$name=$rw['name'];
							?>
							<option value="<?php echo $id;?>"><?php echo utf8_encode($name);?></option>
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
							$sql=mysqli_query($con,"select * from localidades where status=1 order by localidad");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id_loc_padron'];
								$name=$rw['localidad'];
							?>
							<option value="<?php echo $id;?>"><?php echo utf8_encode($name);?></option>
							<?php
							}
						?>
					  </select>
                    </div>
	  </div> 
	 
	  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" id="guardar_datos" class="btn btn-primary">Registrar</button>
      </div>
    </div>
  </div>
</div>
</form>