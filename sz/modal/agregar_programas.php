 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Programa</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="programa" class="col-sm-2 control-label">Programa</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="programa" name="programa" placeholder="Ingresa el programa" value="" required>
		
	</div>
 </div>

 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Programa</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el N&uacute;mero" value="" required>
	</div>
   </div>
   
   <div class="form-group">     
          
            <label for="id_plan" class="col-sm-2 control-label"> Plan </label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_plan" id="id_plan" required>
						<option value="">Selecciona</option>
                        
						<?php 
							$sql=mysqli_query($con,"select * from planes_gestion order by codigo_plan");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['codigo_plan'] . ": ". $rw['plan'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
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