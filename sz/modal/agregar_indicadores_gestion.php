 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Indicador Gesti&oacute;n</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="indicador" class="col-sm-2 control-label">Indicador</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="indicador" name="indicador" placeholder="Ingresa el Indicador" value="" required>
		
	</div>
 </div>

 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Indicador</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Número" value="" required>
	</div>
            <label for="id_meta" class="col-sm-2 control-label">Meta</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_meta" id="id_meta" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select metas_gestion.* from metas_gestion order by metas_gestion.id");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['codigo_meta'].": ".$rw['meta'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
	  </div> 


<div class="form-group">
	<label for="link1" class="col-sm-2 control-label">Link 1</label>
	<div class="col-sm-8">
        
     <input type="text" class="form-control" id="link1" name="link1" placeholder="Ingresa un link de referencia" value="">
    
	</div>
 
  <div class="col-sm-2">
    <input type="checkbox" name="open_target1" id="open_target1"  value="1"><span> Interno</span>
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