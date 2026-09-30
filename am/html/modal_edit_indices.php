<div id="editProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="edit_product" id="edit_product">
					<div class="modal-header">						
						<h4 class="modal-title">Editar &Iacute;ndice</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						
                        <input type="hidden" name="edit_id" id="edit_id" >
						
                        <div class="form-group">
                       <label>Localidad</label>
				    	<select class="form-control" name="edit_localidad" id="edit_localidad" required>
						
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
				    	<select class="form-control" name="edit_ley" id="edit_ley">
                        
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
						      <input type="text" name="edit_indice" id="edit_indice" class="form-control">
                     </div>     
                    
                    <div class="form-group"> 
                     <label>A&ntilde;o</label>
						   
						      <input type="number" name="edit_anio" id="edit_anio" class="form-control" required>
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