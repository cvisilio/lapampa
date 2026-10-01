 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Link</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="link" class="col-sm-2 control-label">link a archivo</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" id="link" name="link" placeholder="Ingresa el link a archivo" value="" required>
		
	</div>
 </div>

 <div class="form-group">
	<label for="titulo" class="col-sm-2 control-label">Titulo</label>
	<div class="col-sm-10">
		<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ingresa el titulo" value="" required>
	</div>
   </div>
   
    <div class="form-group">
                    <label for="resumen" class="col-sm-2 control-label">Resumen</label>
                    <div class="col-sm-10">
				      <textarea class="form-control" name="resumen" style="width: 100%;" id="resumen"></textarea>
                    </div>
                  </div>
   
       
    <div class="form-group">     
          
            <label for="id_tabla" class="col-sm-2 control-label"> ¿donde aparecera el link? </label>

                    <div class="col-sm-6">
                      <select class="form-control" name="id_tabla" id="id_tabla" required>
						<option value="">Selecciona</option>
                        
						<?php 
							$sql=mysqli_query($con,"select * from tablas order by tabla");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name= $rw['tabla'];
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
               <label for="id_registro" class="col-sm-2 control-label">Id Registro</label>
	<div class="col-sm-2">
     <input type="text" class="form-control" id="id_registro" name="id_registro" placeholder="Ingresa el registro de la tabla (ejemplo podria parecer de acuerdo a alguna seleccion de un select)" value="" >
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