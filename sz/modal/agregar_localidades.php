 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nueva Localidad</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
		<label for="name" class="col-sm-3 control-label">Nombre</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control"  id="name" name="name" placeholder="Ingresa Nombre" required>
			 
		</div>
	  </div>
      
  <div class="form-group">
	<label for="cantidad_habitantes" class="col-sm-3 control-label">Habitantes</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="cantidad_habitantes" name="cantidad_habitantes" placeholder="Ingresa Cantidad" value="" >
	</div>
    
    <label for="demanda_habitacional" class="col-sm-3 control-label">Demanda Casas</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="demanda_habitacional" name="demanda_habitacional" placeholder="Ingresa Cantidad" value="" >
		
	</div>
    
    
</div>

<div class="form-group">
	<label for="latitud" class="col-sm-3 control-label">Latitud</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="latitud" name="latitud" placeholder="Ingresa Latitud" value="" >
	</div>
    
    <label for="longitud" class="col-sm-3 control-label">Longitud</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="longitud" name="longitud" placeholder="Ingresa Longitud" value="" >
		
	</div>
    
    
</div>    
	  
      <div class="form-group">
		<label for="modulos" class="col-sm-3 control-label">M&oacute;dulos</label>
		<div class="col-sm-4">
		  <input type="text" class="form-control" id="modulos" name="modulos" placeholder="Ingresa los módulos" value="" required>
			 
		</div>
     
      <label for="id_loc_gob_c" class="col-sm-2 control-label">Id Gob.</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="id_loc_gob_c" name="id_loc_gob_c" placeholder="Id de referenicia en Provincia" value="">
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