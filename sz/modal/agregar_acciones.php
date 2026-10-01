 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nueva Acci&oacute;n</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="indicador" class="col-sm-2 control-label">Acci&oacute;n</label>
	<div class="col-sm-10">
		<textarea id="accion" cols="10" name="accion" class="form-control"></textarea>
		
	</div>
 </div>


 <div class="form-group">
	 <label for="numero_accion" class="col-sm-2 control-label">N&uacute;mero</label>
	<div class="col-sm-5">
      <input type="text" class="form-control" id="numero_accion" name="numero_accion" placeholder="Ingresa numero acci&oacute;n" value="">
    </div>
    
	<label for="estado" class="col-sm-2 control-label">Estado</label>
	<div class="col-sm-3">
		<select class="form-control" name="estado" id="estado" required>
						<option value="">Selecciona</option>
						
							<option value="1"> Terminada </option>
                            <option value="2" selected="selected"> En Curso </option>
						
					  </select>
     
	</div>
  </div>
    
              <?php 
			$sql1="select * from acciones order by id desc";
			$query1=mysqli_query($con,$sql1);
			$rw1=mysqli_fetch_array($query1);
			$ultima_meta=$rw1['id_meta'];
			?>

                  <div class="form-group">
	   <label for="id_meta" class="col-sm-2 control-label">Meta</label>
          <div class="col-sm-10">
                      <select class="form-control" name="id_meta" id="id_meta">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select metas_gestion.* from metas_gestion order by metas_gestion.id");
							
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['codigo_meta'].": ".$rw['meta'];
								if ($ultima_meta==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                 </div>
    </div>

<div class="form-group">
 <label for="id_proyecto" class="col-sm-2 control-label">Proyecto</label>

                    <div class="col-sm-8">
                      <select class="form-control" name="id_proyecto" id="id_proyecto">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select proyectos_gestion.* from proyectos_gestion order by proyectos_gestion.id");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['codigo_proyecto'].": ".$rw['proyecto'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
            </div>
            
     <div class="form-group">
	   <label for="responsable" class="col-sm-2 control-label">Responsable</label>
          <div class="col-sm-10">
                      <select class="form-control" name="responsable" id="responsable">
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
	<label for="observaciones" class="col-sm-2 control-label">Observaciones</label>
	<div class="col-sm-10">
        
     <textarea id="observaciones" cols="10" name="observaciones" class="form-control"></textarea>
    
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