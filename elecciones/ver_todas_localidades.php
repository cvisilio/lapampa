<style>
/* ======== ESTILOS TABLA ELECCIONES ======== */
.table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 8px;
  font-family: "Segoe UI", Arial, sans-serif;
  font-size: 0.9rem;
}

.table th,
.table td {
  border: 1px solid #dee2e6;
  padding: 6px 10px;
  vertical-align: middle;
}

.table thead th {
  background-color: #f8f9fa;
  font-weight: bold;
  text-align: left;
  border-bottom: 2px solid #ccc;
}

.table-striped tbody tr:nth-child(odd) {
  background-color: #f9f9f9;
}

.table-striped tbody tr:hover {
  background-color: #f1f1f1;
}

/* ======== ESTILOS BOTÓN EXPORTAR ======== */
.btn {
  display: inline-block;
  font-weight: 500;
  text-align: center;
  padding: 6px 12px;
  border: 1px solid transparent;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-success {
  color: #fff;
  background-color: #198754;
  border-color: #198754;
}

.btn-success:hover {
  background-color: #157347;
}

/* ======== CONTENEDOR LOCALIDAD ======== */
.card {
  border: 1px solid #ddd;
  border-radius: 6px;
  margin-bottom: 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.card-body {
  padding: 16px;
}

h3 {
  margin-top: 0;
  margin-bottom: 6px;
  font-size: 1.2rem;
}

p {
  margin: 0 0 8px 0;
  color: #555;
}
</style>

<?php
header('Content-Type: text/html; charset=UTF-8');
if (!isset($_SESSION)) session_start();
require_once('Connections/conexionUsuarios.php');

/* ------------------- CONFIG PARTIDOS ------------------- */
$Partido = [];
$Partido[1]["nombre"]="FRENTE DEFENDEMOS LA PAMPA";
$Partido[2]["nombre"]="ALIANZA LA LIBERTAD AVANZA";
$Partido[3]["nombre"]="FRENTE DE IZQUIERDA Y DE TRABAJADORES-UNIDAD";
$Partido[4]["nombre"]="CAMBIA LA PAMPA";
$Partido[5]["nombre"]="MOVIMIENTO AL SOCIALISMO";
$Partido[6]["nombre"]="Votos Blancos";
$Partido[7]["nombre"]="Votos Nulos";
$Partido[8]["nombre"]="Votos Recurridos";
$Partido[9]["nombre"]="Votos Impugnados";

$Partido[1]["color"]="#0066CC";
$Partido[2]["color"]="#CC0099";
$Partido[3]["color"]="#FF6666";
$Partido[4]["color"]="#FFCC00";
$Partido[5]["color"]="#FF0000";
$Partido[6]["color"]="#CCCCCC";
$Partido[7]["color"]="#CCCCCC";
$Partido[8]["color"]="#CCCCCC";
$Partido[9]["color"]="#CCCCCC";

/* ------------------- LOCALIDADES ------------------- */
$sqlLoc = "
  SELECT id, id_loc_padron, localidad, cantidad_mesas, partido
  FROM localidades
  WHERE id_loc_padron > 0
  ORDER BY localidad ASC
";
$rsLoc = mysqli_query($con, $sqlLoc);

$localidades = [];
while ($row = mysqli_fetch_assoc($rsLoc)) {
    $id_loc_padron = (int)$row['id_loc_padron'];
    $localidades[$id_loc_padron] = [
        'id'             => (int)$row['id'],
        'id_loc_padron'  => $id_loc_padron,
        'localidad'      => utf8_encode($row['localidad']),
        'cantidad_mesas' => (int)$row['cantidad_mesas'],
        'partido_loc'    => utf8_encode($row['partido']),
    ];
}

if (empty($localidades)) {
    echo "<div class='text-muted'>No hay localidades cargadas.</div>";
    exit;
}

/* ------------------- AGREGADOS ------------------- */
$sqlAggr = "
SELECT 
  mesas.CodigoLocalidad AS id_loc_padron,
  COUNT(*) as cantidadmesas,
  SUM(IFNULL(L1DP,0)) as SumaL1DP, SUM(IFNULL(L1G,0)) as SumaL1G,
  SUM(IFNULL(L2DP,0)) as SumaL2DP, SUM(IFNULL(L2G,0)) as SumaL2G,
  SUM(IFNULL(L3DP,0)) as SumaL3DP, SUM(IFNULL(L3G,0)) as SumaL3G,
  SUM(IFNULL(L4DP,0)) as SumaL4DP, SUM(IFNULL(L4G,0)) as SumaL4G,
  SUM(IFNULL(L5DP,0)) as SumaL5DP, SUM(IFNULL(L5G,0)) as SumaL5G,
  SUM(IFNULL(L6DP,0)) as SumaL6DP, SUM(IFNULL(L6G,0)) as SumaL6G,
  SUM(IFNULL(L7DP,0)) as SumaL7DP, SUM(IFNULL(L7G,0)) as SumaL7G,
  SUM(IFNULL(L8DP,0)) as SumaL8DP, SUM(IFNULL(L8G,0)) as SumaL8G,
  SUM(IFNULL(L9DP,0)) as SumaL9DP, SUM(IFNULL(L9G,0)) as SumaL9G
FROM mesas
WHERE Escrutada = 'S'
GROUP BY mesas.CodigoLocalidad
";
$rsAggr = mysqli_query($con, $sqlAggr);

$agregados = [];
while ($rw = mysqli_fetch_assoc($rsAggr)) {
    $id_loc_padron = (int)$rw['id_loc_padron'];
    $agregados[$id_loc_padron] = $rw;
}

/* ------------------- COLOR LOCALIDAD ------------------- */
function color_partido_localidad($p) {
    switch ($p) {
        case "PJ":  return "#0066CC";
        case "JxC": return "#FFCC00";
        case "JV":  return "#00913f";
        case "CO":  return "#00aae4";
        default:    return "#cccccc";
    }
}

?>
<h2>Resultados x localidad legislativas 2025</h2>
<button class="btn btn-success btn-sm mb-3" onclick="exportarTodo()">
  <i class="fa fa-file-excel-o"></i> Exportar TODO a Excel
</button>

<?php
/* ======================== MOSTRAR ======================== */
foreach ($localidades as $id_loc_padron => $loc) {

    $localidadNombre  = $loc['localidad'];
    $cantidadMesas    = $loc['cantidad_mesas'];
    $partido_localidad= $loc['partido_loc'];
    $color_partido    = color_partido_localidad($partido_localidad);

    $rw = isset($agregados[$id_loc_padron]) ? $agregados[$id_loc_padron] : null;

    $CantidadMesasEscrutadas = $rw ? (int)$rw['cantidadmesas'] : 0;
    if ($CantidadMesasEscrutadas == 0) continue; // no mostrar si no tiene mesas escrutadas

    $PorcentajeMesasEscrutadas = 0.0;
    if ($cantidadMesas > 0 && $CantidadMesasEscrutadas > 0) {
        $PorcentajeMesasEscrutadas = min(100.0, ($CantidadMesasEscrutadas * 100.0) / $cantidadMesas);
    }

    // SUMAS DIPUTADOS
    $S = [];
    for ($i=1; $i<=9; $i++){
        $key = $rw ? ("SumaL{$i}DP") : null;
        $S[$i] = ($rw && isset($rw[$key])) ? (int)$rw[$key] : 0;
    }

    $totalPositivos = ($S[1] + $S[2] + $S[3] + $S[4] + $S[5]);
    $totalEmitidos  = array_sum($S);

    $PartidoLocal = $Partido;
    for ($i=1; $i<=9; $i++){
        $PartidoLocal[$i]["suma_diputado"] = $S[$i];
        if ($i <= 5) {
            $PartidoLocal[$i]["porcentaje_diputado"] =
                ($totalPositivos > 0 && $S[$i] > 0) ? ($S[$i]*100.0/$totalPositivos) : 0;
        } else {
            $PartidoLocal[$i]["porcentaje_diputado"] = 0;
        }
    }

    $orden = [1,2,3,4,5];
    usort($orden, function($a,$b) use ($S){
        return $S[$b] <=> $S[$a];
    });
    ?>
    <div class="card mb-4" style="border:1px solid #ddd;">
      <div class="card-body">
        <h3 style="margin-bottom:10px;"><?php echo htmlspecialchars($localidadNombre, ENT_QUOTES, 'UTF-8'); ?></h3>
        <p style="margin-bottom:5px;">
          Mesas: <?php echo $CantidadMesasEscrutadas . " / " . $cantidadMesas; ?>
          (<?php echo number_format($PorcentajeMesasEscrutadas,1,',','.'); ?>%)
        </p>

        <table class="table table-sm table-striped" style="font-size:0.9rem;">
          <thead>
            <tr>
              <th>Partido / Lista</th>
              <th style="text-align:right;">Votos</th>
              <th style="text-align:right;">%</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orden as $i):
                $p      = $PartidoLocal[$i];
                $nombre = $p['nombre'] ?? '';
                $votos  = (int)($p['suma_diputado'] ?? 0);
                $pct    = (float)($p['porcentaje_diputado'] ?? 0);
            ?>
            <tr>
              <td><?php echo htmlspecialchars($nombre,ENT_QUOTES,'UTF-8'); ?></td>
              <td style="text-align:right;"><?php echo number_format($votos,0,',','.'); ?></td>
              <td style="text-align:right;"><?php echo number_format($pct,1,',','.'); ?>%</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
    <?php
}
?>

<script>
function exportarTodo() {
  const cards = document.querySelectorAll('.card');
  if (!cards.length) return;

  let csv = [];
  // Encabezado
  csv.push('"Localidad","Mesas escrutadas","Partido / Lista","Votos","%"');

  cards.forEach(card => {
    const nombreLocalidad = card.querySelector('h3') ? card.querySelector('h3').innerText.trim() : '';
    const parrafoMesas = card.querySelector('p');
    let mesasTexto = '';
    if (parrafoMesas) {
      // Ej: "Mesas: 5 / 7 (71,4%)"
      mesasTexto = parrafoMesas.innerText.replace(/"/g, '""').trim();
    }

    const tabla = card.querySelector('table');
    if (!tabla) return;

    const filas = tabla.querySelectorAll('tbody tr');
    filas.forEach(tr => {
      const cols = tr.querySelectorAll('td');
      if (cols.length < 3) return;

      const partido = cols[0].innerText.replace(/"/g, '""').trim();
      const votos   = cols[1].innerText.replace(/"/g, '""').trim();
      const pct     = cols[2].innerText.replace(/"/g, '""').trim();

      const locCSV   = '"' + nombreLocalidad.replace(/"/g, '""') + '"';
      const mesasCSV = '"' + mesasTexto + '"';
      const partCSV  = '"' + partido + '"';
      const votosCSV = '"' + votos + '"';
      const pctCSV   = '"' + pct + '"';

      csv.push([locCSV, mesasCSV, partCSV, votosCSV, pctCSV].join(','));
    });
  });

  const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = 'resultados_localidades.csv';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
</script>
