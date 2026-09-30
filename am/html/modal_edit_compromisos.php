<div id="editProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="edit_product" id="edit_product">
					<div class="modal-header">						
						<h4 class="modal-title">Editar Compromiso</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Localidad</label>
							 <select disabled class="form-control" name="edit_localidad" id="edit_localidad" required>
						<option  value="">Seleccione Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['localidad'];
								//if ($id==$localidad1){$selected1="selected";}else{$selected1="";} 
								 ?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
                        
                        <input type="hidden" name="edit_id" id="edit_id" >
					</div>
						
                         <div class="form-group">
                        	
                           <div class="col-sm-7">
                              <div class="input-group">
                              
						        <div class="input-group-addon">
							      <i class="fa fa-usd"></i>
						        </div>
							 <input type="text" name="edit_monto" value="" id="edit_monto" class="form-control" required>    
                              </div> <!-- input -->
                          </div> <!-- col-sm -->
                            
                             <label class="col-sm-2">Cuotas</label>
						    <div class="col-sm-3">
						      <input type="number" min="0" max="36" name="edit_cuotas" id="edit_cuotas" class="form-control">
                             </div> 
                             
                        </div>     
                        
						<div class="form-group">
							<label>Afectacion </label>
							
                            <select class="form-control" required name="edit_afectacion" id="edit_afectacion">
						<option value="">Seleccione Afectaci&oacute;n</option>
						<?php 
							$sql=mysqli_query($con,"select * from objetivos_motivos order by motivo");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['motivo'];
								//if ($id==$localidad1){$selected1="selected";}else{$selected1="";} 
								 ?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
						</div>
                      
                      <?php 
					  $anio=date('Y');
							$anio_min=$anio-2;
							$anio_max=$anio+2;
					  ?>
                      
                      <div class="form-group">
                        	 <label class="col-sm-2">Mes inicio</label>
                            <div class="col-sm-3">
							 <input type="number" value="" name="edit_mes_inicio" id="edit_mes_inicio" class="form-control" min="1" max="12" required>  </div>
                            
                             <label class="col-sm-2">Anio inicio</label>
						    <div class="col-sm-5">
						      <input type="number" value="" name="edit_anio_inicio" id="edit_anio_inicio" class="form-control" min="<?php echo $anio_min?>" max="<?php echo $anio_max?>"required>
                             </div> 
                        </div>     	  
                        
						
						<div class="form-group">
												
                             <textarea id="edit_observaciones" cols="12" name="edit_observaciones" class="form-control" placeholder="Observaciones"></textarea>
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