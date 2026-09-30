<div id="addProductModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="add_product" id="add_product">
					<div class="modal-header">						
						<h4 class="modal-title">Agregar Compromiso</h4>
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
                            
                         <label class="col-sm-2">Cuotas</label>
						    <div class="col-sm-3">
						      <input type="number" value="" min="0" max="36" name="cuotas" id="cuotas" class="form-control">
                              <!--onChange="if (this.value !='') colocar_anio_mes(this.value);" -->
                             </div> 
                        </div>     
						
                        <div><label>Afectacion</label> </div>
                      
						<div class="form-group">
							
                           <div class="col-sm-12">
							<select style="width: 100% !important;" class="form-control select2" name="afectacion" id="afectacion" required>
						<option value="">Seleccione Afectaci&oacute;n</option>
						<?php 
							$sql=mysqli_query($con,"select * from objetivos_motivos order by motivo");                  
							while ($rw=mysqli_fetch_array($sql)){
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
                       </div>
                      <br  />  
					
                    
                    <div class="form-group">
                        	 <label class="col-sm-2">Mes pago</label>
                            <div class="col-sm-3">
                            <?php 
							$mes=intval(date('m'));
							$anio=date('Y');
							$anio_min=$anio-2;
							$anio_max=$anio+2;
							if ($mes ==12)
							 {
							 $mes=1;
							 $anio=$anio+1;
							 }
							else
							 $mes =$mes+1;
							?>
							 <input type="number" value="<?php echo $mes;?>" name="mes_inicio" id="mes_inicio" class="form-control" min="1" max="12" required>                   </div>
                            
                             <label class="col-sm-3">A&ntilde;o pago</label>
						    <div class="col-sm-4">
						      <input type="number" value="<?php echo $anio;?>" name="anio_inicio" id="anio_inicio" class="form-control" min="<?php echo $anio_min?>" max="<?php echo $anio_max?>" required>
                             </div> 
                        </div>     	
						
						<div class="form-group">
							
							<textarea id="observaciones" cols="10" name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
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

 
 
   
  <script>
 function colocar_anio_mes(cuotas1){
   var fecha = new Date();
   document.getElementById('mes_inicio').value=fecha.getMonth() +2;
   document.getElementById('anio_inicio').value=fecha.getFullYear();
  }
           
</script>