<div class="modal fade bs-example-modal-lg" id="AgregarTransferenciaTemporalModal" tabindex="null" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="add_product" id="add_product">
					<div class="modal-header">						
						<h4 class="modal-title">Sumar Transferencia</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Localidad</label>
					<select class="form-control" name="localidad" id="localidad" disabled="disabled" required>
						<option value="" >Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['localidad'];
								 ?>
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                       </div>
                      
                       <input type="hidden" name="id_localidad1" id="id_localidad1" >
                      
						
                       
                    	<div class="form-group">
							<label>Afectacion</label>
							<select style="width: 100% !important;" class="form-control select2" name="afectacion" id="afectacion" required>
                           
						
						<?php 
							$sql2=mysqli_query($con,"select * from objetivos_motivos order by motivo");                  
							while ($rw=mysqli_fetch_array($sql2)){
								$id=$rw['id'];
								$name=$rw['motivo'];
								 ?>
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
						</div>
                       
                       	<div class="form-group">
                        	 <label class="col-sm-2">Monto</label>
                           <div class="col-sm-6">
                              <div class="input-group">
						        <div class="input-group-addon">
							      <i class="fa fa-usd"></i>
						        </div>
							 <input type="text" name="monto" value="" id="monto" class="form-control" required>    
                              </div> <!-- input -->
                          </div> <!-- col-sm -->
                         </div>     
						
                        <br />
                        			
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-success" value="Agregar">
					</div>
				</form>
			</div>
		</div>
	</div>
    
