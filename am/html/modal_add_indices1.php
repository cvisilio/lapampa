<div id="addProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="add_product" id="add_product">
					<div class="modal-header">						
						<h4 class="modal-title">Agregar Indice</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						  <div class="form-group">
                       <label>Localidad</label>
                       
				    	<select class="form-control" name="localidad" id="localidad" required>
						 <option value="">Seleccione Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['localidad'];
								 ?>
						
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                       </div> 
                    
                    <div class="form-group">
                       <label>Ley (Origen)</label>
                       
				    	<select class="form-control" name="ley" id="ley">
                        <option value="">Seleccione Origen</option>
					        <?php 
							$sql=mysqli_query($con,"select disponibilidades_x_leyes.*, leyes.abreviatura from disponibilidades_x_leyes,leyes where disponibilidades_x_leyes.id_ley=leyes.id order by abreviatura");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['abreviatura'] . " (". $rw['anio'].")";
								 ?>
						
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
                            </select>
                       </div> 
                       
                     <div class="form-group">
                     <label>&Iacute;ndice</label>
						      <input type="text" name="indice" id="indice" class="form-control">
                     </div>     
                    
                    <div class="form-group"> 
                     <label>A&ntilde;o</label>
						   
						      <input type="number" name="anio" value="<?php echo date('Y');?>" id="anio" class="form-control" required>
                      </div>     	
                    				
                                    		
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" id="guardar_datos" class="btn btn-success" value="Guardar datos">
					</div>
				</form>
			</div>
		</div>
	</div>