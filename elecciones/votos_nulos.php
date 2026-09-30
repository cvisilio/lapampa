<?php
// listado_recurridos_mesas.php
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION)) session_start();
require_once('Connections/conexionUsuarios.php');

// --- CONSULTA PRINCIPAL ---
$sql = "
  SELECT
    l.id AS id_localidad,
    l.localidad AS nombre_localidad,
    m.Mesa AS nro_mesa,
    m.L7DP AS recurridos
  FROM localidades l
  LEFT JOIN mesas m
         ON m.CodigoLocalidad = l.id_loc_padron
  WHERE m.L7DP > 0
  ORDER BY l.localidad ASC, m.Mesa ASC
";

$rs = mysqli_query($con, $sql);
if (!$rs) {
  echo '<div class="text-danger">Error en la consulta: '.htmlspecialchars(mysqli_error($con), ENT_QUOTES, 'UTF-8').'</div>';
  exit;
}

// Agrupar por localidad y acumular totales
$datos = [];
$granTotalRecurridos = 0;
$granTotalMesasConRecurridos = 0;

while ($row = mysqli_fetch_assoc($rs)) {
  $loc = utf8_encode($row['nombre_localidad']);
  $mesa = (int)$row['nro_mesa'];
  $rec  = (int)$row['recurridos'];

  $datos[$loc][] = [
    'mesa'       => $mesa,
    'recurridos' => $rec
  ];

  $granTotalRecurridos += $rec;
  $granTotalMesasConRecurridos++;
}
?>

<style>
  .table-sticky thead th {
    position: sticky; top: 0; z-index: 2;
    background: #e9ecef; /* gris claro */
    color: #000;         /* texto negro */
  }
  .table-tight td, .table-tight th { padding: .6rem .7rem; }
  .num { text-align: right; white-space: nowrap; }
  .muted { color: #6c757d; }
  .card-localidad { border-radius: .75rem; overflow: hidden; }
  .card-localidad .card-header { background: #f8f9fa; margin-top: 2.5rem; }
  .localidad-nombre {
    font-size: 1.6rem;
    font-weight: 700;
    color: #212529;
  }
  .btn-export {
    background-color: #198754;
    color: white;
    border: none;
    border-radius: .4rem;
    padding: .6rem 1.2rem;
    font-size: 0.95rem;
  }
  .btn-export:hover {
    background-color: #157347;
    color: #fff;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="h4 mb-0">Votos Nulos por localidad y mesa</div>
  <br />
  <button class="btn-export" onclick="exportarExcel()"> Exportar a Excel</button>
</div>
<div class="small text-muted mb-3">
  Actualizaci&oacute;n: <?php echo date('d/m/Y'); ?> | <?php echo date('H:i'); ?>
</div>

<div id="tablaExportar">
<?php if (empty($datos)): ?>
  <div class="text-center text-muted">No hay votos nulos registrados.</div>
<?php else: ?>
  <?php foreach ($datos as $localidad => $mesas): ?>
    <?php
      $totalRecurridosLoc = array_sum(array_column($mesas, 'recurridos'));
      $cantidadMesasLoc   = count($mesas);
    ?>
    <div class="card card-localidad mb-4 shadow-sm">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
        <div class="localidad-nombre mb-1">
          <?php echo htmlspecialchars($localidad, ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <div class="muted small">
          Mesas con nulos: <strong><?php echo $cantidadMesasLoc; ?></strong>
          &nbsp;|&nbsp; Total nulos: <strong><?php echo number_format($totalRecurridosLoc, 0, ',', '.'); ?></strong>
          <?php if ($granTotalRecurridos > 0): ?>
            &nbsp;(<strong><?php echo number_format($totalRecurridosLoc * 100 / $granTotalRecurridos, 1, ',', '.'); ?>%</strong> del total de los nulos)
          <?php endif; ?>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover mb-0 table-tight table-sticky">
            <thead>
              <tr class="text-center">
                <th style="width: 30%">Mesa</th>
                <th style="width: 70%">Votos nulos</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mesas as $m): ?>
                <tr>
                  <td class="text-center"><?php echo $m['mesa']; ?></td>
                  <td class="num font-weight-bold"><?php echo number_format($m['recurridos'], 0, ',', '.'); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <th class="text-right">Subtotal localidad</th>
                <th class="num"><?php echo number_format($totalRecurridosLoc, 0, ',', '.'); ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <!-- Resumen general -->
  <div class="card border-0 mt-5">
    <div class="table-responsive">
      <table class="table table-bordered w-auto ml-auto table-tight">
        <tbody>
          <tr>
            <th class="text-right">Total mesas con nulos</th>
            <td class="num"><?php echo number_format($granTotalMesasConRecurridos, 0, ',', '.'); ?></td>
          </tr>
          <tr>
            <th class="text-right">Total general de votos nulos</th>
            <td class="num font-weight-bold"><?php echo number_format($granTotalRecurridos, 0, ',', '.'); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="small muted">
      Fuente: Partido Justicialista La Pampa
    </div>
  </div>
<?php endif; ?>
</div>

<script>
// Exportar el contenido del div #tablaExportar a Excel
function exportarExcel() {
  const tabla = document.getElementById("tablaExportar").outerHTML;
  const blob = new Blob([tabla], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "votos_nulos.xls";
  a.click();
  URL.revokeObjectURL(url);
}
</script>
