<?php
if (!isset($_SESSION)) { session_start(); }

// --- Bootstrap & jQuery (v3, mismo que us�s) ---
?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<?php
// ----- AUTH -----
$MM_authorizedUsers = "1,2,3,4";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
  $isValid = false;
  if (!empty($UserName)) {
    $arrUsers  = explode(",", $strUsers);
    $arrGroups = explode(",", $strGroups);
    if (in_array($UserName, $arrUsers))  $isValid = true;
    if (in_array($UserGroup, $arrGroups)) $isValid = true;
    if ($strUsers == "" && false) $isValid = true;
  }
  return $isValid;
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_qsChar  = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: " . $MM_restrictGoTo);
  exit;
}

// ----- DATA -----
require_once('Connections/conexionUsuarios.php');

// Total de mesas del padr�n (ajustable)
$TOTAL_PADRON = 915;

$query_MesasFaltantes = "
  SELECT mesas.*, entidades.Establecimiento, localidades.Localidad
  FROM mesas
  JOIN entidades   ON mesas.CodigoEscuela = entidades.Id
  JOIN localidades ON mesas.CodigoLocalidad = localidades.id_loc_padron
  WHERE Escrutada = 'N'
  ORDER BY mesas.Id
";

$sql = mysqli_query($con, $query_MesasFaltantes);
$totalRows_MesasFaltantes = ($sql) ? mysqli_num_rows($sql) : 0;

$PorcentajeTotal = $totalRows_MesasFaltantes > 0
  ? round(($totalRows_MesasFaltantes * 100) / $TOTAL_PADRON, 2)
  : 0;

$procesadas = max($TOTAL_PADRON - $totalRows_MesasFaltantes, 0);

// Helper para color de barra seg�n % faltante
function progressClass($p) {
  if ($p >= 60) return "progress-bar-danger";
  if ($p >= 30) return "progress-bar-warning";
  if ($p >  0)  return "progress-bar-info";
  return "progress-bar-success";
}
$barClass = progressClass($PorcentajeTotal);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mesas Faltantes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
  body { background:#f7f9fc; }
  .page-header { margin:28px 0 18px; border:none; }

  /* Helpers flex para B3 */
  .flex { display:flex; }
  .flex-wrap { flex-wrap:wrap; }
  .items-center { align-items:center; }
  .justify-between { justify-content:space-between; }
  .gap-8 { gap:8px; }
  .gap-12 { gap:12px; }
  .gap-16 { gap:16px; }

  /* Encabezado */
  .header-bar { padding:0; }
  .title { margin:0; line-height:1.2; font-weight:600; }
  .badge-big { font-size:14px; background:#003366; }
  .kpi { font-size:16px; color:#607d8b; margin-top:10px; }
  .kpi strong { color:#263238; }
  .actions { margin-top:10px; }
  .search-box { max-width: 420px; min-width: 260px; }

  /* Progreso */
  .progress { height: 30px; border-radius: 10px; box-shadow: inset 0 2px 3px rgba(0,0,0,0.18); margin-top:16px; }
  .progress-bar { 
    font-size: 14px; 
    font-weight: 700; 
    line-height: 30px; 
    text-shadow: 0 0 3px rgba(0,0,0,0.35);
  }

  /* Responsive tweaks */
  @media (max-width: 991px){
    .title { font-size:20px; }
    .actions { width:100%; text-align:left; }
    .search-box { width:100%; max-width:none; }
  }
  @media (min-width: 992px){
    .title { font-size:22px; }
  }
</style>

</head>
<body>

<div class="container">
  <div class="page-header header-bar">
    <div class="flex flex-wrap items-center justify-between gap-12">
      <!-- Izquierda: título + KPIs -->
      <div>
        <h3 class="title">
          Mesas Faltantes
          <span class="badge badge-big"><?php echo $totalRows_MesasFaltantes; ?> / <?php echo $TOTAL_PADRON; ?></span>
        </h3>
        <div class="kpi">
          <strong><?php echo number_format($PorcentajeTotal,2,',','.'); ?>%</strong> faltantes ·
          <strong><?php echo $procesadas; ?></strong> escrutadas
        </div>
      </div>

      <!-- Derecha: acciones -->
      <div class="actions flex items-center gap-8">
        <a href="cargarmesas.php" class="btn btn-default">
          <span class="glyphicon glyphicon-menu-left"></span> Volver
        </a>
        <div class="input-group search-box">
          <input id="search" type="text" class="form-control" placeholder="Buscar por mesa, escuela o localidad...">
          <span class="input-group-btn">
            <button class="btn btn-primary" type="button" onclick="$('#search').val('').trigger('keyup')">
              <span class="glyphicon glyphicon-remove"></span>
            </button>
          </span>
        </div>
      </div>
    </div>

    <!-- Progreso -->
    <div class="progress">
      <div class="progress-bar <?php echo $barClass; ?>" role="progressbar"
          aria-valuenow="<?php echo $PorcentajeTotal; ?>" aria-valuemin="0" aria-valuemax="100"
          style="width: <?php echo $PorcentajeTotal; ?>%;">
        <?php echo number_format($PorcentajeTotal,2,',','.'); ?>%
      </div>
    </div>
  </div>
  
   
  <?php if (!$sql): ?>
    <div class="alert alert-danger">
      <strong>Error:</strong> No se pudo ejecutar la consulta.
    </div>
  <?php elseif ($totalRows_MesasFaltantes == 0): ?>
    <div class="alert alert-success">
      <span class="glyphicon glyphicon-ok"></span>
      �Todo listo! No hay mesas faltantes por escrutar.
    </div>
  <?php endif; ?>

  <div class="panel panel-default panel-clean">
    <div class="panel-heading">
      <strong>Detalle de mesas faltantes</strong>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th style="width:140px;">Mesa</th>
            <th>Escuela</th>
            <th>Localidad</th>
          </tr>
        </thead>
        <tbody id="tabla-rows">
          <?php if ($sql): ?>
            <?php while($row = mysqli_fetch_assoc($sql)) { ?>
              <tr>
                <td><span class="label label-primary" style="font-size:12px;"><?php echo htmlspecialchars($row['Mesa']); ?></span></td>
                <td><?php echo htmlspecialchars($row['Establecimiento']); ?></td>
                <td><?php echo htmlspecialchars(utf8_encode($row['Localidad'])); ?></td>
              </tr>
            <?php } ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  
</div>

<script>
// Filtro en vivo por texto
$(function(){
  $('#search').on('keyup', function(){
    var q = $(this).val().toLowerCase().trim();
    $('#tabla-rows tr').each(function(){
      var rowText = $(this).text().toLowerCase();
      $(this).toggle(rowText.indexOf(q) !== -1);
    });
  });
});
</script>
</body>
</html>
