 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nueva/o Necesidad/Problema</h4>
      </div>
      <div class="modal-body">
	  
    <div class="form-group">
	<label for="nombre" class="col-sm-2 control-label">Nombre</label>
	<div class="col-sm-7">
		<input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el Nombre" value="" required>
            
		</div>
 </div>
 
 <div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripcion</label>
	<div class="col-sm-7">
       <textarea id="descripcion" cols="10" name="descripcion" class="form-control" ></textarea>  
       
    
	</div>
 </div>
 
  <div class="form-group">
	   <label for="id_localidad" class="col-sm-2 control-label">Localidad</label>
          <div class="col-sm-8">
                      <select class="form-control" name="id_localidad" id="id_localidad">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=utf8_encode($rw['localidad']);
								
							?>
							<option value="<?php echo $id1;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>

</div>

 <div class="form-group">
	   <label for="tipo" class="col-sm-2 control-label">Tipo</label>
          <div class="col-sm-4">
                      <select class="form-control" name="tipo" id="tipo" required>
						<option value="">Selecciona</option>
						
							<option value="1"> Problemas </option>
                            <option value="2"> Necesidades </option>
						
					  </select>
                    </div>
      
      <label for="avance" class="col-sm-2 control-label">Avance</label>
          <div class="col-sm-2"> 
     <input type="text" class="form-control" id="avance" name="avance" placeholder="Ingresa el Avance" value="" required>  
      </div>
   
   </div>
   
    <div class="form-group">  
       <label for="resuelto" class="col-sm-2 control-label">Resuelto</label>
          <div class="col-sm-4">
                      <select class="form-control" name="resuelto" id="resuelto" required>
						<option value="">Selecciona</option>
						
							<option value="1"> Si </option>
                            <option value="2" selected="selected"> No </option>
						
					  </select>
                    </div>                
    </div>

 <div class="form-group">
	   <label for="id_programa" class="col-sm-2 control-label">Programa</label>
     <div class="col-sm-8">   
        <select id='id_programa' name="id_programa" class='form-control'>
 <option value="">Todos los Programas </option>
							<?php
							$sql1=mysqli_query($con,"select * from programas_gestion order by codigo_programa");
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id'];
							  $name=$rw1['codigo_programa'] . ": ". $rw1['programa'];
							 
								?>
								<option value="<?php echo $id;?>"><?php echo $name;?></option>	
								<?php 
							}
							?>
		</select>
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
	 
	  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" id="guardar_datos" class="btn btn-primary">Registrar</button>
      </div>
    </div>
  </div>
</div>
</form>