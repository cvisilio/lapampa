<?php
header('Content-Type: application/json; charset=UTF-8');
require_once ("../../config/db.php");
require_once ("../../config/conexion.php");

$rubro_id     = isset($_POST['rubro_id']) ? (int)$_POST['rubro_id'] : 0;
$localidad_id = isset($_POST['localidad_id']) ? (int)$_POST['localidad_id'] : 0;

$out = ['ok'=>false, 'rows'=>[], 'by_micro'=>[], 'anio'=>null, 'unidad'=>null];

if ($rubro_id > 0) {
  // ⚠️ en tu tabla el campo se llama 'rubro' (id del rubro)
  $where  = " WHERE vrm.rubro = ? ";
  $types  = "i";
  $params = [$rubro_id];

  if ($localidad_id > 0) {
    $where   .= " AND vrm.id_localidad = ? ";
    $types   .= "i";
    $params[] = $localidad_id;
  }

  // Traemos volumen por micro + la unidad del rubro
  $sql = "
    SELECT
      vrm.microregion,
      vrm.volumen,
      vrm.id_localidad,
      um.nombre        AS unidad_nombre,
      um.unidad_medida AS unidad_simbolo
    FROM volumenes_rubros_microregiones vrm
    JOIN rubros rb
      ON rb.id = vrm.rubro
    LEFT JOIN unidades_medida um
      ON um.id = rb.unidad_medida
    $where
    ORDER BY vrm.volumen DESC
    LIMIT 500
  ";

  if ($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $rs = mysqli_stmt_get_result($stmt);

    $unidadSimbolo = null; // la misma para todo el rubro
    while ($r = mysqli_fetch_assoc($rs)) {
      if ($unidadSimbolo === null) {
        // preferimos el símbolo; si no, el nombre
        $unidadSimbolo = $r['unidad_simbolo'] ?: $r['unidad_nombre'];
      }
      $out['rows'][] = [
        'microregion' => (int)$r['microregion'],
        'volumen'     => (float)$r['volumen'],
        'unidad'      => $r['unidad_simbolo'] ?: $r['unidad_nombre'],
      ];
    }
    mysqli_stmt_close($stmt);

    if (!empty($out['rows'])) {
      // Agregamos mapa micro -> volumen (sumando por si hubiera varias filas)
      $agg = [];
      foreach ($out['rows'] as $row) {
        $m = (int)$row['microregion'];
        $v = (float)$row['volumen'];
        if (!isset($agg[$m])) $agg[$m] = 0.0;
        $agg[$m] += $v;
      }
      $out['by_micro'] = $agg;
      $out['unidad']   = $unidadSimbolo; // unidad global del rubro
      $out['ok']       = true;
    }
  }
}

echo json_encode($out);
