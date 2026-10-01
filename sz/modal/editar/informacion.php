<?php
// view/informacion_territorial/modal/editar/informacion.php
if (!isset($_SESSION)) { session_start(); }
 require_once ("../../config/db.php");
 require_once ("../../config/conexion.php");


$id = 0;
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
} elseif (isset($_REQUEST['id'])) {
  $id = intval($_REQUEST['id']);
}

$sql = "SELECT * FROM sistemas_informacion WHERE id = $id LIMIT 1";
$rs  = mysqli_query($con, $sql);
if (!$rs || mysqli_num_rows($rs) == 0) {
  echo '<div class="alert alert-danger">Registro no encontrado.</div>';
  exit;
}
$row = mysqli_fetch_assoc($rs);

// Helpers para evitar notices
$nombre        = isset($row['nombre']) ? $row['nombre'] : '';
$descripcion   = isset($row['descripcion']) ? $row['descripcion'] : '';
$por_micro     = isset($row['por_microregion']) ? (int)$row['por_microregion'] : 0;
?>
          <div class="form-group">
            <label class="col-sm-2 control-label">Nombre</label>
            <div class="col-sm-8">
            <input type="text"
                   class="form-control"
                   name="nombre"
                   required
                   maxlength="40"
                   value="<?php echo htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8'); ?>">
          	
            <input type="hidden" value="<?php echo $id;?>" name="id" id="id">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label">Descripción</label>
            <div class="col-sm-8">
            <input type="text"
                   class="form-control"
                   name="descripcion"
                   maxlength="40"
                   value="<?php echo htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8'); ?>">
          </div></div>

          <div class="form-group">
            <label class="col-sm-2 control-label">¿Por microregión?</label>
             <div class="col-sm-8">
            <select class="form-control" name="por_microregion">
              <option value="0" <?php echo ($por_micro==0 ? 'selected' : ''); ?>>No</option>
              <option value="1" <?php echo ($por_micro==1 ? 'selected' : ''); ?>>Sí</option>
            </select>
          </div>  </div>