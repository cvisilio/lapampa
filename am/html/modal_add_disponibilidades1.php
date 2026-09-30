<div id="addProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="add_product" id="add_product">
					<div class="modal-header">						
						<h4 class="modal-title">Agregar Disponibilidad</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						                        
                        <div class="form-group">
                        	 <label>Ley</label>
                      <select class="form-control" name="ley" id="ley" required>
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
                        
                            <div class="col-sm-7">
                              <div class="input-group">
						        <div class="input-group-addon">
							      <i class="fa fa-usd"></i>
						        </div>
							 <input type="text" name="monto" value="" id="monto" class="form-control" required>    
                              </div> <!-- input -->
                          </div> <!-- col-sm -->
                            
                             <label>A&ntilde;o</label>
						      <input type="number" value="" min="2020" max="2050" name="anio" id="anio" class="form-control">
                        </div>
                     
                     <div class="form-group">
                        	 <label>Partida</label>
                     		 <input type="text" name="partida" value="" id="partida" class="form-control" required>   </div>
                      
                    
                   			
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-success" value="Guardar datos">
					</div>
				</form>
			</div>
		</div>
	</div>