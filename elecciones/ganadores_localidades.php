<?php
if (!isset($_SESSION)) session_start();
require_once('Connections/conexionUsuarios.php');

$cacheFile = __DIR__.'/cache/ganadores_localidades.json';
$ttl = 30; // segundos

if (is_file($cacheFile) && (time() - filemtime($cacheFile) < $ttl)) {
    header('Content-Type: application/json; charset=UTF-8');
    readfile($cacheFile);
    exit;
}

header('Content-Type: application/json; charset=UTF-8');

// Mapa partido → color
$partidoColor = [
  1 => "#0066CC", // FRENTE DEFENDEMOS LA PAMPA
  2 => "#CC0099", // LLA
  3 => "#FF6666", // FIT-U
  4 => "#FFCC00", // Cambia La Pampa
  5 => "#FF0000", // MAS
  6 => "#CCCCCC", // Blancos
  7 => "#CCCCCC", // Nulos
  8 => "#CCCCCC", // Recurridos
  9 => "#CCCCCC"  // Impugnados
];

$sql = "
  SELECT 
    CodigoLocalidad,
    SUM(L1DP) AS L1, SUM(L2DP) AS L2, SUM(L3DP) AS L3,
    SUM(L4DP) AS L4, SUM(L5DP) AS L5, SUM(L6DP) AS L6,
    SUM(L7DP) AS L7, SUM(L8DP) AS L8, SUM(L9DP) AS L9
  FROM mesas
  WHERE Escrutada = 'S'
  GROUP BY CodigoLocalidad
";

$res = mysqli_query($con, $sql);
if (!$res) {
  echo json_encode(['error' => mysqli_error($con)], JSON_UNESCAPED_UNICODE);
  exit;
}

$out = [];
while ($row = mysqli_fetch_assoc($res)) {
  $idLoc = (int)$row['CodigoLocalidad'];

  // Armo array partido → votos
  $pv = [];
  $tot = 0;
  for ($i=1; $i<=9; $i++) {
    $val = isset($row['L'.$i]) ? (int)$row['L'.$i] : 0;
    $pv[] = ['partido'=>$i, 'votos'=>$val];
    $tot += $val;
  }

  // Ordeno por votos desc
  usort($pv, function($a,$b){ return $b['votos'] <=> $a['votos']; });

  $top1 = $pv[0];
  $top2 = $pv[1];

  $hayEmpateTop2 = ($top1['votos'] > 0 && $top1['votos'] === $top2['votos']);

  if ($hayEmpateTop2) {
    $ganador = 0; // sin ganador por empate
    $color   = '#FFFFFF'; // blanco para empate
    $pct     = ($tot > 0) ? round(($top1['votos'] * 100.0) / $tot, 1) : 0.0;
  } else {
    $ganador = $top1['partido'];
    $color   = $partidoColor[$ganador] ?? '#cfd4da';
    $pct     = ($tot > 0) ? round(($top1['votos'] * 100.0) / $tot, 1) : 0.0;
  }

  $out[$idLoc] = [
    'winner' => $ganador,
    'color'  => $color,
    'pct'    => $pct,
    'total'  => $tot
    // Si querés, podés exponer también:
    // 'tie' => $hayEmpateTop2,
    // 'leaders' => $hayEmpateTop2 ? [$top1['partido'], $top2['partido']] : [$ganador]
  ];
}

$json = json_encode($out, JSON_UNESCAPED_UNICODE);
file_put_contents($cacheFile, $json, LOCK_EX);
echo $json;
