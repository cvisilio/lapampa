<div id="editProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="edit_product" id="edit_product">
					<div class="modal-header">						
						<h4 class="modal-title">Editar Disponibilidad</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						
                        <input type="hidden" name="edit_id" id="edit_id" >
						
                          <div class="form-group">
                        	 <label>Ley</label>
                        <select class="form-control" name="edit_ley" id="edit_ley" required>
						<option value="">Seleccione Origen</option>
						<?php 
							$sql=mysqli_query($con,"select * from leyes order by abreviatura");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['abreviatura'];
								 ?>
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>   
                     </div>
                            
                       
                     <div class="form-group">
                         <label>Disponible</label>
                          
                              <div class="input-group">
						        <div class="input-group-addon">
							      <i class="fa fa-usd"></i>
						        </div>
							 <input type="text" name="edit_monto" value="" id="edit_monto" class="form-control" required>    
                              </div> <!-- input -->
                         
                     </div>
                        
                          <div class="form-group"> 
                        
                             <label>A&ntilde;o</label>
                          
						   
						      <input type="number" value="" min="2020" max="2050" name="edit_anio" id="edit_anio" class="form-control">
                           
                          </div>
                          
                            <div class="form-group">  
                          
                        	 <label>Partida</label>
                           
                             <input type="text" name="edit_partida" value="" id="edit_partida" class="form-control" required>   </div>
                                         				
					</div>
                    
                    
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-info" value="Guardar datos">
					</div>
				</form>
			</div>
		</div>
	</div>