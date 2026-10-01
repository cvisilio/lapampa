<!-- Modal -->
 <form class="form-horizontal" method="post" id="guardar_usuario" name="guardar_usuario">
<div class="modal fade" id="usuario_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Usuario</h4>
      </div>
      <div class="modal-body">
     
     
     	  
      <div class="form-group">
		<label for="fullname" class="col-sm-3 control-label">Nombre completo</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Ingresa el nombre completo del usuario" required>
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="user_name" class="col-sm-3 control-label">Usuario</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Ingresa el usuario" pattern="[a-zA-Z0-9]{4,64}" required>
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="user_email" class="col-sm-3 control-label">Email</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" id="user_email" name="user_email" placeholder="example@gmail.com" required>
		</div>
	  </div>

	  <div class="form-group">
		<label for="user_group_id" class="col-sm-3 control-label">Grupo de permisos</label>
		<div class="col-sm-6">
			<select class="form-control" name="user_group_id" id="user_group_id">
				<?php
				$sql_grupos="select * from user_group";
				$query_grupos=mysqli_query($con,$sql_grupos);
				while ($rw_grupos=mysqli_fetch_array($query_grupos)){
					?>
					<option value="<?php echo $rw_grupos['user_group_id'];?>"><?php echo $rw_grupos['name'];?></option>	
					<?php
				}
				?>
			</select> 
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="status" class="col-sm-3 control-label">Estado</label>
		<div class="col-sm-6">
		  <select class="form-control" name="status" id="status">
			<option value="1">Activo</option>
			<option value="2">Inactivo</option>
		  </select>
		</div>
	  </div>

	  <div class="form-group">
		<label for="fullname" class="col-sm-3 control-label">Contraseña</label>
		<div class="col-sm-6">
		  <input type="password" class="form-control" id="user_password_new" name="user_password_new" placeholder="******" pattern=".{6,}" required>
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="fullname" class="col-sm-3 control-label">Repite contraseña</label>
		<div class="col-sm-6">
		  <input type="password" class="form-control" id="user_password_repeat" name="user_password_repeat" placeholder="******" pattern=".{6,}"  required>
		</div>
	  </div>
 
  <div class="form-group">
  <label for="ministerio" class="col-sm-3 control-label">Ministerio</label>

                    <div class="col-sm-6">
                      <select class="form-control" name="ministerio" id="ministerio">
                      	<option value="">Seleccione Ministerio </option>					
						<?php 
							$sql=mysqli_query($con,"select * from ministerios");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['denominacion'];
							   
							?>
                            
							<option value="<?php echo $id;?>"><?php echo $name;?> </option>
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
