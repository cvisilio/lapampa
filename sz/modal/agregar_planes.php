 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Plan</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="plan" class="col-sm-2 control-label">Plan</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="plan" name="plan" placeholder="Ingresa el Plan" value="" required>
		
	</div>
 </div>

 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Plan</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el N&uacute;mero" value="" required>
	</div>
    <label for="siglas" class="col-sm-2 control-label">Siglas</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="siglas" name="siglas" placeholder="Ingresa las siglas" value="" required>
	</div>
   </div>
  
     <div class="form-group">
	<label for="descripcion_plan" class="col-sm-2 control-label">Descripci&oacute;n Plan</label>
	<div class="col-sm-10">
		 <textarea id="descripcion_plan" cols="10" name="descripcion_plan" class="form-control"></textarea>
		
	</div>
   </div>
  
   <div class="form-group">
	<label for="nombre_sub" class="col-sm-2 control-label">Nombre Sub</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="nombre_sub" name="nombre_sub" placeholder="Ejemplo: Meta; Objetivo Espec&iacute;fico" value="" required>
	</div>
   </div>
 
  <div class="form-group">  
   <label for="autoridad_aplicacion" class="col-sm-2 control-label">Autoridad Aplicacion</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="autoridad_aplicacion" id="autoridad_aplicacion" required>	
                      <option value="">Selecciona</option>					
						<?php 
							$sql=mysqli_query($con,"select * from ministerios");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['denominacion'];
							   
							?>
                            
							<option value="<?php echo $id;?>"><?php echo $name;?> </option>
							<?php
							}
						?>
					  </select>
                    </div>
    </div>
            
   <div class="form-group">
	   <label for="id_ambito" class="col-sm-2 control-label">Ambito</label>
          <div class="col-sm-3">
                      <select class="form-control" name="id_ambito" id="id_ambito" required>
						<option value="">Selecciona</option>
						<option value="1" >Nacional</option>
                        <option value="2" >Provincial</option>
						
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