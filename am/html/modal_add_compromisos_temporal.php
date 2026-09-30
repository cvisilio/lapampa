<div class="modal fade bs-example-modal-lg" id="AgregarCompromisoTemporalModal" tabindex="null" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="add_product2" id="add_product2">
					<div class="modal-header">	
                   
						<h4 class="modal-title">Sumar Compromiso  </h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Localidad</label>
					<select class="form-control" name="localidad2" id="localidad2" disabled="disabled" required>
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
                      
                       <input type="hidden" name="id_localidad2" id="id_localidad2" >
                      
					  <input type="hidden" name="id_motivo2" id="id_motivo2" >	
                       
                    	<div class="form-group">
							<label>Compromiso</label>
							<select style="width: 100% !important;" afte class="form-control" name="id_compromiso" id="id_compromiso" required>
                           <option value="">Seleccione Compromiso</option> 
						
						<?php /* 
							$sql2=mysqli_query($con,"select compromisos_am.id,compromisos_am.cuotas,compromisos_am.monto,objetivos_motivos.motivo from compromisos_am,objetivos_motivos where compromisos_am.afectacion=objetivos_motivos.id  order by compromisos_am.id desc");                  
							while ($rw=mysqli_fetch_array($sql2)){
								$id=$rw['id'];
								$name=$rw['motivo'] . "(".$rw['monto'].")";
								 ?>
							
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						*/?>
					  </select>
						</div>
                        
                     
                       	<div class="form-group">
                            <div class="col-sm-6">
                              <div class="input-group">
						        <div class="input-group-addon">
							      <i class="fa fa-usd"></i>
						        </div>
							 <input type="text" name="monto2" value="" id="monto2" class="form-control" required>    
                              </div> <!-- input -->
                          </div> <!-- col-sm -->
                          
                        
                               <div class="col-sm-3">
                               <input type="number" value="" min="0" max="36" name="cuota_2" id="cuota_2" class="form-control"> 
                              </div>
                                 <div class="col-sm-3">
						          <label id="cantidad_cuotas"> </label> 
                                 </div>                   						      
                          </div> <!-- form-group --> 
                                               			
					</div>
                    <br />
                    
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-success" value="Agregar">
					</div>
				</form>
			</div>
		</div>
	</div>

<script> 

$("#id_compromiso").change(function(){
    document.getElementById('monto2').value="";
	$("#cantidad_cuotas").html("");
	  var compromiso=$("#id_compromiso").val();
    $.ajax({
      url:"./ajax/compromiso_select.php",
      type: "POST",
      data:"id="+compromiso,
      success: function(opciones){
	     var datos =  $.parseJSON(opciones);
         document.getElementById('monto2').value=datos[0].monto;
		 document.getElementById('cuota_2').value=datos[0].cuota;
		 document.getElementById('id_motivo2').value=datos[0].objetivo;
		 $("#cantidad_cuotas").html("Cuota de "+datos[0].cuotas); 
         }
    })
  });
</script> 