<?php
// ver_localidad.php
header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION)) session_start();
require_once('Connections/conexionUsuarios.php');

// --- INPUT ---
$Idlocalidad = isset($_POST['id_localidad']) ? intval($_POST['id_localidad']) : 0;
if ($Idlocalidad <= 0) {
  echo '<div class="text-muted">Localidad inválida.</div>';
  exit;
}


// --- CONFIG PARTIDOS ---
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

$Partido[1]["imagen"]= "1.png";
$Partido[2]["imagen"]= "2.png";
$Partido[3]["imagen"]= "3.png";
$Partido[4]["imagen"]= "4.png";
$Partido[5]["imagen"]= "5.png";
$Partido[6]["imagen"]= "6.png";
$Partido[7]["imagen"]= "7.png";
$Partido[8]["imagen"]= "8.png";
$Partido[9]["imagen"]= "9.png";

$Partido[1]["color"]="#0066CC";
$Partido[2]["color"]="#CC0099";
$Partido[3]["color"]="#FF6666";
$Partido[4]["color"]="#FFCC00";
$Partido[5]["color"]="#FF0000";
$Partido[6]["color"]="#CCCCCC";
$Partido[7]["color"]="#CCCCCC";
$Partido[8]["color"]="#CCCCCC";
$Partido[9]["color"]="#CCCCCC";

// --- LOCALIDAD (nombre y cantidad de mesas) ---
$sqlLoc = "SELECT cantidad_mesas, localidad, id, partido 
           FROM localidades 
           WHERE id_loc_padron = $Idlocalidad
           LIMIT 1";
$rsLoc = mysqli_query($con, $sqlLoc);

/*
esto es para actualizar historial de intendencias, esta hecho desde el año 2015 hacia adelante
$sqlInsert = "
  INSERT INTO historial_intendencias (id_loc_gob_c, intendente, partido, anio)
  SELECT 
    l.id_loc_gob_c,
    l.intendente AS intendente,
    l.partido AS partido,
    2019 AS anio
  FROM partidos_2019 l where l.id_loc_gob_c >0
";
mysqli_query($con, $sqlInsert);
*/

 if (!$rsLoc || !($rowLoc = mysqli_fetch_assoc($rsLoc))) {
  echo '<div class="text-muted">No se encontró la localidad.</div>';
  exit;
 }
 $localidadNombre = $rowLoc['localidad'];
 $localidadNombre = utf8_encode($localidadNombre);

 $cantidadMesas   = (int)$rowLoc['cantidad_mesas'];
 $id_localidad   = (int)$rowLoc['id'];
 $partido_localidad= utf8_encode($rowLoc['partido']);

 switch ($partido_localidad)
  {
  case "PJ":
   $color_partido="#0066CC";
   break;
  case "JxC": 
   $color_partido="#FFCC00";
   break;
  case "JV": 
   $color_partido="#00913f";
   break;
  case "CO": 
   $color_partido="##00aae4";
   break;
   
  default:
   $color_partido="#cccccc";
   break;
  
  }  
 
 
 $color="#cccccc";

// --- AGREGADOS POR LOCALIDAD ---
$campo = "mesas.CodigoLocalidad";
$sqlAggr = "
SELECT 
  COUNT(*) as cantidadmesas,
  SUM(L1DP) as SumaL1DP, SUM(L1G) as SumaL1G,
  SUM(L2DP) as SumaL2DP, SUM(L2G) as SumaL2G,
  SUM(L3DP) as SumaL3DP, SUM(L3G) as SumaL3G,
  SUM(L4DP) as SumaL4DP, SUM(L4G) as SumaL4G,
  SUM(L5DP) as SumaL5DP, SUM(L5G) as SumaL5G,
  SUM(L6DP) as SumaL6DP, SUM(L6G) as SumaL6G,
  SUM(L7DP) as SumaL7DP, SUM(L7G) as SumaL7G,
  SUM(L8DP) as SumaL8DP, SUM(L8G) as SumaL8G,
  SUM(L9DP) as SumaL9DP, SUM(L9G) as SumaL9G
FROM mesas
WHERE $campo = $Idlocalidad AND Escrutada = 'S'
";
$rs = mysqli_query($con, $sqlAggr);
$rw = $rs ? mysqli_fetch_assoc($rs) : null;

$CantidadMesasEscrutadas = (int)($rw['cantidadmesas'] ?? 0);
$PorcentajeMesasEscrutadas = 0.0;
if ($cantidadMesas > 0 && $CantidadMesasEscrutadas > 0) {
  $PorcentajeMesasEscrutadas = min(100.0, ($CantidadMesasEscrutadas * 100.0) / $cantidadMesas);
}

// --- SUMAS DIPUTADOS (1..9) ---
$S = [];
for ($i=1; $i<=9; $i++){
  $S[$i] = (int)($rw["SumaL{$i}DP"] ?? 0);
}

// Totales: positivos y emitidos
$totalPositivos = ($S[1] + $S[2] + $S[3] + $S[4] + $S[5]);
$totalEmitidos  = array_sum($S);

// Completar PARTIDOS (guardamos los votos y el % sobre POSITIVOS solo para 1..5)
for ($i=1; $i<=9; $i++){
  $Partido[$i]["suma_diputado"] = $S[$i];
  if ($i <= 5) {
    $Partido[$i]["porcentaje_diputado"] = ($totalPositivos > 0 && $S[$i] > 0) ? ($S[$i]*100.0/$totalPositivos) : 0;
  } else {
    // para 6..9 no usamos barra; % se mostrará abajo sobre totalEmitidos
    $Partido[$i]["porcentaje_diputado"] = 0;
  }
}

// Ordenar SOLO las listas 1..5 por votos descendente
$orden = [1,2,3,4,5];
usort($orden, function($a,$b) use ($S){
  return $S[$b] <=> $S[$a];
});

// --- SALIDA HTML ---
?>
<div class="h4 mb-2">
  <?php 
   $nombre_localidad=htmlspecialchars($localidadNombre, ENT_QUOTES, 'UTF-8');
  echo $nombre_localidad; ?> · Resultados locales
</div>

<div class="row text-muted small mb-3">
  <div class="col-6">
    Mesas escrutadas: <?php echo number_format($CantidadMesasEscrutadas,0,',','.'); ?>
    (<?php echo number_format($PorcentajeMesasEscrutadas,1,',','.'); ?>%)
  </div>
  <div class="col-6 text-right">
    Actualización: <?php echo date('d/m/Y'); ?> | <?php echo date('H:i'); ?>
  </div>
</div>

<?php if ($totalEmitidos <= 0): ?>
  <div class="text-center text-muted my-4">Esperando resultados…</div>
<?php else: ?>
  <?php foreach ($orden as $i):
    $p      = $Partido[$i];
    $nombre = $p['nombre'] ?? '';
    $votos  = (int)($p['suma_diputado'] ?? 0);
    $pct    = (float)($p['porcentaje_diputado'] ?? 0); // % sobre positivos
    $color  = $p['color'] ?? '#999999';
  ?>
    <div class="media align-items-center mb-3">
      <div class="media-body">
        <div class="d-flex justify-content-between">
          <div class="font-weight-bold"><?php echo htmlspecialchars($nombre,ENT_QUOTES,'UTF-8'); ?></div>
          <div class="font-weight-bold"><?php echo number_format($pct,1,',','.'); ?>%</div>
        </div>
        <div class="progress" style="height:27px;">
          <div class="progress-bar" role="progressbar"
               style="width: <?php echo $pct; ?>%; background: <?php echo $color; ?>;"
               aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="text-muted small mt-1">
          <?php echo number_format($votos,0,',','.'); ?> votos
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?php
    // No afirmativos (sobre total emitidos)
    $blancos    = $S[6];
    $nulos      = $S[7];
    $recurridos = $S[8];
    $impugnados = $S[9];
    $observados = $recurridos + $impugnados;

    $fmtPct = function($n,$den){
      $v = ($den > 0) ? ($n * 100.0 / $den) : 0.0;
      return number_format($v, 1, ',', '.');
    };
  ?>

  <hr class="my-3">

  <div class="small text-muted">
    <div>Votos en blanco: <?php echo number_format($blancos,0,',','.'); ?> (<?php echo $fmtPct($blancos,$totalEmitidos); ?>%)</div>
    <div>Votos nulos: <?php echo number_format($nulos,0,',','.'); ?> (<?php echo $fmtPct($nulos,$totalEmitidos); ?>%)</div>
    <div>Votos observados (recurridos + impugnados): <?php echo number_format($observados,0,',','.'); ?> (<?php echo $fmtPct($observados,$totalEmitidos); ?>%)</div>
    <div class="mt-2">Total de votos emitidos: <?php echo number_format($totalEmitidos,0,',','.'); ?></div>
  
  <?php if($_SESSION['usuario_id']==1){ ?>  
    <div class="mt-2"><a href="#" data-toggle="modal" style="font-weight:bold; color:<?php echo $color_partido;?>" data-target="#myModal" onClick="informacion11('<?php echo $id_localidad;?>','<?php echo $nombre_localidad;?>');"><?php echo $nombre_localidad;?></a> <a href="#" data-toggle="modal" style="font-weight:bold; color:#666666" data-target="#myModal" onClick="informacion22('<?php echo $id_localidad;?>','<?php echo $nombre_localidad;?>');">&nbsp;<i class="fa fa-history" aria-hidden="true">Historial</i></a> </div>
     <?php } ?>
            
    <div class="mt-2"> 
      Actualización: <?php echo date('d/m/Y'); ?> | <?php echo date('H:i'); ?><br>
      Fuente: Partido Justicialista La Pampa
    </div>
  </div>
<?php endif; ?>
