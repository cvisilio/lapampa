 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Rubro</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
		<label for="name" class="col-sm-3 control-label">Nombre</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control"  id="name" name="name" placeholder="Ingresa el Rubro" required>
			 
		</div>
	  </div>
      
      <div class="form-group">
		<label for="muestra_copete" class="col-sm-3 control-label">Muestra Copete</label>
		<div class="col-sm-3">
		  
           <select class="form-control" name="muestra_copete" id="muestra_copete">
			 <option value="">Selecciona</option>
				 <option value="1" selected="selected"> Si</option>
                 <option value="2"> No</option>
			 </select>
			 
		</div>
        <label for="muestra_solo_a_ministerio" class="col-sm-3 control-label">Muestra Solo a Ministerio</label>
		<div class="col-sm-3">
		 <select class="form-control" name="muestra_solo_a_ministerio" id="muestra_solo_a_ministerio">
			 <option value="">Selecciona</option>
				 <option value="1" > Si</option>
                 <option value="2" selected="selected"> No</option>
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