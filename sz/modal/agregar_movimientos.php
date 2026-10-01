 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Compromiso</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
		<label for="name" class="col-sm-3 control-label">Detalle</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" value=""  id="detalle" name="detalle" placeholder="Ingresa el Detalle" required>
			 
		</div>
	  </div>


<div class="form-group">
                    <label for="id_localidad" class="col-sm-3 control-label">Localidad</label>

                    <div class="col-sm-6">
                      <select class="form-control" name="id_localidad" id="id_localidad" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=utf8_encode($rw['localidad']);
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
	  </div>
      
      <div class="form-group">
		<label for="valor" class="col-sm-3 control-label">Monto</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" value=""  id="valor" name="valor" placeholder="Ingresa el Monto" required>
			 
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