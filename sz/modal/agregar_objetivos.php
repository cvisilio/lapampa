 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Objetivo</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="objetivo" class="col-sm-2 control-label">Objetivo</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="objetivo" name="objetivo" placeholder="Ingresa el Objetivo" value="" required>
		
	</div>
 </div>
 
 <div class="form-group">
	<label for="copete" class="col-sm-2 control-label">Copete</label>
	<div class="col-sm-8">
     <textarea id="copete" cols="10" name="copete" class="form-control"></textarea>
    
	</div>
 </div>
 
 <div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n</label>
	<div class="col-sm-8">
    <textarea id="descripcion" cols="10" name="descripcion" class="form-control" ></textarea>
    		
	</div>
 </div>
 
 <div class="form-group">
	<label for="color1" class="col-sm-2 control-label">Color 1</label>
	<div class="col-sm-3">
   <select class="form-control" name="color1" id="color1" required>
						<option value="">Selecciona</option>
						
							<option value="red">Rojo</option>
                            <option value="darkred">Bordo</option>
                           	<option value="orange">Naranja</option>     
                          	<option value="darkorange">Marr&oacute;n</option>
                            <option value="blue">Azul</option>
                            <option value="lightblue">Celeste</option>
                            <option value="green">Verde</option>
                            <option value="purple">Violeta</option>      
						  </select>
    		
	</div>
    
    <label for="color2" class="col-sm-2 control-label">Color 2</label>
	<div class="col-sm-3">
    <select class="form-control" name="color2" id="color2" required>
						<option value="">Selecciona</option>
						
							<option value="danger">Rojo</option>
                            <option value="warning">Amarillo</option>
                           	<option value="success">Verde</option>     
                          	<option value="info">Celeste</option>
                            	<option value="primary">Azul</option>  
						  </select>
    		
	</div>
    
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">N&uacute;mero</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Número" value="" required>
	</div>
    
    
                    <label for="id_ambito" class="col-sm-2 control-label">Plan</label>

                    <div class="col-sm-3">
                      <select class="form-control" name="id_ambito" id="id_ambito" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from planes_gestion order by siglas");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['siglas'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
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