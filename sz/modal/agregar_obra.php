 
<form class="form-horizontal" method="post" id="new_register" name="new_register">
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nueva Obra</h4>
      </div>
      <div class="modal-body">
	  
          <div class="form-group">
                        <label for="expediente" class="col-sm-3 control-label">Expediente</label>
                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="expediente"  name="expediente" >
                        </div>
                        
                        <label for="cantidad" class="col-sm-3 control-label">Cantidad</label>
                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="cantidad"  name="cantidad"  value="">
                        
                        
                      </div>
                      </div>
         
                    <div class="form-group">
                        <label for="nombre_obra" class="col-sm-3 control-label">Nombre de la Obra</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="nombre_obra"  name="nombre_obra" required>
                        </div>
                      </div>
                      
					  <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Estado</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="estado" id="estado" required>
						<option value="">Selecciona Estado</option>
						<?php 
							$sql=mysqli_query($con,"select * from estados_obras");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id_estado'];
								$name=$rw['estado'];
							
							
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                        </div>
                       
                        <label for="ministerio" class="col-sm-3 control-label">Ministerio</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="ministerio" id="ministerio" required>
						<option value="">Selecciona Ministerio</option>
						<?php
							$sql1=mysqli_query($con,"select * from ministerios order by denominacion");
							while ($rw1=mysqli_fetch_array($sql1)){
							    $id=$rw1['id'];
								
								$cadena= $rw1['modulos'];
								$modulos = explode(",", $cadena);
							   if (in_array('12', $modulos)) 
							    {
								?>
								<option value="<?php echo $id;?>"><?php echo $rw1['denominacion'];?></option>	
								<?php
							  } //if
							 } //while
							?>
					  </select>
                        </div> 
                        
                     
                     </div>
                     
                   <div class="form-group">
                      <label for="fecha_licitar" class="col-sm-2 control-label">Licitada</label>
                    <div class="col-sm-4">
                   	         <div class="input-group">
                                            <input type="text" class="form-control datepicker" name="fecha_licitar"  value="<?php echo date("d/m/Y")?>">

                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                    </div>
                    </div>
                  
                    <label for="fecha_finalizada" class="col-sm-2 control-label">Finalizada</label>
                      <div class="col-sm-4">
                   	  <div class="input-group">
                                            <input type="text" class="form-control datepicker" name="fecha_finalizada"  value="<?php echo date("d/m/Y")?>" >

                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                    </div>
                    </div>           
                   </div>      
                     
                    <div class="form-group">
                    <label for="localidad" class="col-sm-3 control-label">Localidad</label>

                    <div class="col-sm-3">
                      <select class="form-control" name="localidad" id="localidad" required>
						<option value="">Seleccione Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=utf8_encode($rw['localidad']);
								 ?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div> 
                    
                     <label for="localidad2" class="col-sm-3 control-label">Localidad 2</label>

                    <div class="col-sm-3">
                      <select class="form-control" name="localidad2" id="localidad2">
						<option value="">Seleccione Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=utf8_encode($rw['localidad']);
								 ?>
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>      
                         
                   </div>  
                      
					<div class="form-group">
                    <label for="empresa" class="col-sm-3 control-label">Empresa</label>

                    <div class="col-sm-4">
                      <select class="form-control" name="empresa" id="empresa">
						<option value="">Selecciona Empresa</option>
						
					  </select>
                    </div>
                    
                    <label for="porcentaje_avance" class="col-sm-3 control-label">Avance</label>

                    <div class="col-sm-2">
                      <input type="text" class="form-control" id="porcentaje_avance" name="porcentaje_avance" value="" >
                    </div>       
                          
                   </div>
                 
                 <div class="form-group">
                        <label for="presupuesto_oficial" class="col-sm-3 control-label">Presupuesto Oficial</label>
                        <div class="col-sm-4">
                          <div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-usd"></i>
						  </div>
						  <input type="text" class="form-control" id="presupuesto_oficial" name="presupuesto_oficial" pattern="\d+(\.\d{2})?">
						</div>
                        </div>
                         <label for="mes_base" class="col-sm-3 control-label">Mes Base</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="mes_base"  name="mes_base" >
                        </div>
                      </div> 
                 
                   <div class="form-group">
                    <label for="monto_adjudicado" class="col-sm-3 control-label">Monto Adjudicado</label>

                    <div class="col-sm-3">
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-usd"></i>
						  </div>
						  <input type="text" class="form-control" id="monto_adjudicado" name="monto_adjudicado" pattern="\d+(\.\d{2})?">
						</div>
                    </div>
                    
                    <label for="monto_actual" class="col-sm-3 control-label">Monto Actual</label>
                         <div class="col-sm-3">
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-usd"></i>
						  </div>
						  <input type="text" class="form-control" id="monto_actual" name="monto_actual" pattern="\d+(\.\d{2})?">
						</div>
                    </div>
                   </div> 
                    
                   <div class="form-group">
                        <label for="partida_contable" class="col-sm-3 control-label">Part. Contable</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="partida_contable"  name="partida_contable" >
                        </div>
                         <label for="cuenta" class="col-sm-2 control-label">Cuenta</label>
                        <div class="col-sm-1">
                          <input type="text" class="form-control" id="cuenta"  name="cuenta" >
                        </div>
                          <label for="subclase" class="col-sm-2 control-label">SubClase</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="subclase"  name="subclase" >
                        </div>
                      </div>     
                       
			  <div class="form-group">
                        <label for="observaciones" class="col-sm-3 control-label">Observaciones</label>
                        <div class="col-sm-9">
                        <textarea  id="observaciones" name="observaciones"></textarea>
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