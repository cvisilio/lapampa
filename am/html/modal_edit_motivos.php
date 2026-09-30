<div id="editProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="edit_product" id="edit_product">
					<div class="modal-header">						
						<h4 class="modal-title">Editar Motivo</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						
                        <input type="hidden" name="edit_id" id="edit_id" >
						
                         <div class="form-group">
                        	 <label class="col-sm-2">Motivo</label>
                            <div class="col-sm-10">
							 <input type="text" name="edit_motivo" id="edit_motivo" class="form-control" required>   </div>
                            
                        </div>     
                    				
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-info" value="Guardar datos">
					</div>
				</form>
			</div>
		</div>
	</div>