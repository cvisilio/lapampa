 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Funcionario</h4>
      </div>
      <div class="modal-body">
	  
    <div class="form-group">
	<label for="funcionario" class="col-sm-2 control-label">Funcionario</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="funcionario" name="funcionario" placeholder="Ingresa el Nombre del Funcionario" value="" required>
		
	</div>
 </div>

<div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripcion del Cargo</label>
	<div class="col-sm-10">
         <textarea id="descripcion" cols="10" name="descripcion" class="form-control"></textarea> 
   
     
    
	</div>
 </div> 

 <div class="form-group">
	<label for="telefono" class="col-sm-2 control-label">Telefono</label>
	<div class="col-sm-4">
		<input type="text" class="form-control" id="telefono" name="telefono" placeholder="Ingresa el Telefono" value="" required>
	</div>
    
   </div>
   
  
  
   <div class="form-group">  
            <label for="id_ministerio" class="col-sm-2 control-label">Ministerio</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_ministerio" id="id_ministerio" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from ministerios order by denominacion");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name= $rw['denominacion'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
	  </div> 
      
<div class="form-group">  
            <label for="id_cargo" class="col-sm-2 control-label">Cargo</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_cargo" id="id_cargo" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from cargos_poderes_estado order by cargo");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name= $rw['cargo'];
							
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