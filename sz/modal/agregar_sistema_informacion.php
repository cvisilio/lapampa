<div class="modal fade" id="modal_register" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
<form id="new_register">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title">Nuevo Sistema de Información</h4>
</div>
<div class="modal-body">
<div class="form-group">
<label class="col-sm-2 control-label">Nombre</label>
<input type="text" class="form-control" name="nombre" required maxlength="40">
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Descripción</label>
<input type="text" class="form-control" name="descripcion" maxlength="40">
</div>
<div class="form-group">
<label class="col-sm-2 control-label">¿Por microregión?</label>
<select class="form-control" name="por_microregion">
<option value="0">No</option>
<option value="1">Sí</option>
</select>
</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
<button type="submit" id="guardar_datos" class="btn btn-primary">Guardar</button>
</div>
</form>
</div>
</div>
</div>