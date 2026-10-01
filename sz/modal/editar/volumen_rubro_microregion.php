<?php
 require_once ("../../config/db.php");
 require_once ("../../config/conexion.php");
$id=intval($_GET['id']);
$r=mysqli_query($con,"SELECT * FROM volumenes_rubros_microregiones WHERE id=$id");
$v=mysqli_fetch_assoc($r);
$col_grupo = "id_grupo";
$check_col = mysqli_query($con, "SHOW COLUMNS FROM rubros LIKE 'id_grupo'");
if (!$check_col || mysqli_num_rows($check_col) === 0) {
  $check_col_alt = mysqli_query($con, "SHOW COLUMNS FROM rubros LIKE 'id_gupo'");
  if ($check_col_alt && mysqli_num_rows($check_col_alt) > 0) {
    $col_grupo = "id_gupo";
  }
}
$rubros_sql = "SELECT r.id, r.name, um.unidad_medida
               FROM rubros r
               LEFT JOIN unidades_medida um ON um.id = r.unidad_medida
               WHERE r.".$col_grupo." > 0
               ORDER BY r.name";
$rubros_rs = mysqli_query($con, $rubros_sql);
$localidades_rs = mysqli_query($con, "SELECT id, localidad FROM localidades WHERE es_localidad_o_comision_fomento = 1 ORDER BY localidad");
$id_localidad_actual = isset($v['id_localidad']) && $v['id_localidad'] !== null ? (int)$v['id_localidad'] : 0;
?>
<div class="modal fade" id="modal_update_vol" tabindex="-1" role="dialog" style="display:block;">
<div class="modal-dialog" role="document">
<div class="modal-content">
<form id="update_register_vol">
<input type="hidden" name="id" value="<?php echo $v['id'];?>">
<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Editar Volumen</h4></div>
<div class="modal-body">
<div class="form-group">
  <label>Rubro</label>
  <select class="form-control" name="rubro" required>
    <option value="">Seleccionar...</option>
    <?php while ($rb = mysqli_fetch_assoc($rubros_rs)) { ?>
      <option value="<?php echo (int)$rb['id']; ?>" <?php if ((int)$v['rubro'] === (int)$rb['id']) { echo "selected"; } ?>>
        <?php echo htmlspecialchars($rb['name']." (".($rb['unidad_medida'] ? $rb['unidad_medida'] : "sin unidad").")"); ?>
      </option>
    <?php } ?>
  </select>
</div>
<div class="form-group"><label>Microregión</label><input type="number" class="form-control" name="microregion" value="<?php echo (int)$v['microregion'];?>" required></div>
<div class="form-group">
  <label>Localidad (opcional)</label>
  <select class="form-control" name="id_localidad">
    <option value="">Seleccionar...</option>
    <?php while ($loc = mysqli_fetch_assoc($localidades_rs)) { ?>
      <option value="<?php echo (int)$loc['id']; ?>" <?php if ($id_localidad_actual > 0 && $id_localidad_actual === (int)$loc['id']) { echo "selected"; } ?>>
        <?php echo htmlspecialchars($loc['localidad']); ?>
      </option>
    <?php } ?>
  </select>
</div>
<div class="form-group"><label>Volumen</label><input type="number" step="any" class="form-control" name="volumen" value="<?php echo (float)$v['volumen'];?>" required></div>
<div class="form-group"><label>Color</label><input type="text" maxlength="32" class="form-control" name="color" value="<?php echo htmlspecialchars($v['color'] ?? '');?>"></div>
</div>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Cerrar</button><button class="btn btn-primary" id="actualizar_vol" type="submit">Actualizar</button></div>
</form>
</div>
</div>
</div>