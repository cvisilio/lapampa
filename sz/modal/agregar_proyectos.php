 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Proyecto</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="proyecto" class="col-sm-2 control-label">Proyecto</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="proyecto" name="proyecto" placeholder="Ingresa el Proyecto" value="" required>
		
	</div>
 </div>

 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Proyecto</label>
	<div class="col-sm-4">
		<input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el N&uacute;mero" value="" required>
	</div>
    
   </div>
  
   <div class="form-group">  
            <label for="id_programa" class="col-sm-2 control-label">Programa</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_programa" id="id_programa" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from programas_gestion order by codigo_programa");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['codigo_programa'] . ": ". $rw['programa'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
	  </div> 
      
<div class="form-group">
	<label for="indicadores_gestion" class="col-sm-2 control-label">Indicadores Gesti&oacute;n</label>
	<div class="col-sm-4">
        
     <input type="text" class="form-control" id="indicadores_gestion" name="indicadores_gestion" placeholder="Ingrese codigo indicadores separados por coma" value="">
     
    
	</div>
 </div>

  <div class="form-group">
	   <label for="id_funcionario" class="col-sm-2 control-label">Funcionario</label>
          <div class="col-sm-10">
                      <select class="form-control" name="id_funcionario" id="id_funcionario">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select funcionarios.nombre,funcionarios.id,ministerios.denominacion from funcionarios,ministerios where funcionarios.ministerio=ministerios.id order by ministerios.denominacion, funcionarios.nombre");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['denominacion'] . ": ". $rw['nombre'];
								
							?>
							<option value="<?php echo $id1;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
          </div> 
          
      <div class="form-group">  
       <label for="estado" class="col-sm-2 control-label">Estado</label>
          <div class="col-sm-4">
                      <select class="form-control" name="estado" id="estado" required>
						<option value="">Selecciona</option>
						
							<option value="1"> Terminado </option>
                            <option value="2" selected="selected"> En Curso </option>
						
					  </select>
                    </div>                
    </div>         	 
	
  
  <div class="form-group">
	<label for="avance" class="col-sm-2 control-label">Avance</label>
	<div class="col-sm-3">
        
     <input type="text" class="form-control" id="avance" name="avance" placeholder="Ingrese el avance" value="" >
     
    
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