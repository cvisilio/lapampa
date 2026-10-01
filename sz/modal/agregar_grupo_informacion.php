<?php $sis_rs = mysqli_query($con, "SELECT id, nombre FROM sistemas_informacion ORDER BY nombre"); ?>
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
<form id="new_register">
<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Nuevo Grupo</h4></div>
<div class="modal-body">
<div class="form-group"><label>Grupo (número)</label><input type="text" name="grupo" class="form-control" required></div>
<div class="form-group"><label>Sistema</label>
<select name="id_sistema_informacion" class="form-control" required>
<option value="">-- Seleccionar --</option>
<?php while($s=mysqli_fetch_assoc($sis_rs)){ echo '<option value="'.$s['id'].'">'.htmlspecialchars($s['nombre']).'</option>'; } ?>
</select>
</div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button><button type="submit" class="btn btn-primary">Guardar</button></div>
</form>
</div>
</div>
</div>