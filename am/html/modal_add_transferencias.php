<div class="modal fade" id="addProductModal"  tabindex="null" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form name="add_product" id="add_product">
					<div class="modal-header">						
						<h4 class="modal-title">Agregar Transferencia</h4>
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
							<label>Compromiso</label>
					<select class="form-control" name="id_compromiso" id="id_compromiso">
						<option value="">Seleccione Compromiso</option> 
						<?php /* // dejar deshabilitado tiene que elegir localidad primero 
							$sql=mysqli_query($con,"select compromisos_am.id,objetivos_motivos.motivo from compromisos_am, objetivos_motivos where compromisos_am.afectacion=objetivos_motivos.id order by id");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['motivo'];
								 ?>
							*/?>
							<option value="<?php // echo $id;?>"><?php // echo $name;?></option>
							<?php
							//}
						?>
					  </select>
						</div> 
                        
						<div class="form-group">
                        	 <label class="col-sm-2">Monto</label>
                            <div class="col-sm-5">
							 <input type="text" name="monto" value="" id="monto" class="form-control" required>                   </div>
                            
                             <label class="col-sm-2">Cuota</label>
						    <div class="col-sm-3">
						      <input type="number" value="" min="0" max="36" name="cuotas" id="cuotas" class="form-control">
                             </div> 
						
						</div>
                     
						<div class="form-group">
							<label>Afectacion</label>
							<select style="width: 100% !important;" class="form-control select2" name="afectacion" id="afectacion" required>
						
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
													
						<div class="form-group">
							
							<textarea id="observaciones" cols="14" name="observaciones" class="form-control" placeholder="Observaciones"></textarea>
						</div>					
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancelar">
						<input type="submit" class="btn btn-success" value="Guardar datos">
					</div>
				</form>
			</div>
		</div>
	</div>
    
 <script type="text/javascript">
   
   $(document).ready(function(){
   
    $("#localidad").change(function(){
    $.ajax({
      url:"./ajax/compromisos_localidad_select.php",
      type: "POST",
      data:"id="+$("#localidad").val(),
      success: function(opciones){
        $("#id_compromiso").html(opciones);
	    $("#id_compromiso").change();
		   //
         }
    })
  });
  
});

$("#id_compromiso").change(function(){
    document.getElementById('monto').value="";
	  var compromiso=$("#id_compromiso").val();
    $.ajax({
      url:"./ajax/compromiso_select.php",
      type: "POST",
      data:"id="+compromiso,
      success: function(opciones){
	     var datos =  $.parseJSON(opciones);
         document.getElementById('monto').value=datos[0].monto;
		 document.getElementById('cuotas').value=datos[0].cuota;
		// document.getElementById("afectacion").selectedIndex=datos[0].objetivo;
		 $("#afectacion").html(datos[0].motivos); 
		document.getElementById("afectacion").value=document.getElementById("afectacion").value; 
		$("#afectacion").change();  
         }
    })
  });
 

</script>     