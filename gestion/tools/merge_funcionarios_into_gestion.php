<?php
/**
 * Copia el bloque CREATE + INSERT de funcionarios.sql dentro de gestion_gubernamental.sql.
 * Uso: php tools/merge_funcionarios_into_gestion.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$fragPath = $root . '/funcionarios.sql';
$gestPath = $root . '/gestion_gubernamental.sql';

$frag = str_replace("\r\n", "\n", (string) file_get_contents($fragPath));
$gest = str_replace("\r\n", "\n", (string) file_get_contents($gestPath));
if ($frag === '' || $gest === '') {
    fwrite(STDERR, "Archivos vacíos o no legibles.\n");
    exit(1);
}

// ── Extraer núcleo desde funcionarios.sql (hasta antes de ALTER TABLE funcionarios) ──
$fl = explode("\n", $frag);
$coreLines = [];
$capture = false;
foreach ($fl as $line) {
    if (str_contains($line, 'Estructura de tabla para la tabla `funcionarios`')) {
        $capture = true;
    }
    if (!$capture) {
        continue;
    }
    if (preg_match('/^ALTER TABLE `funcionarios`/', $line)) {
        break;
    }
    $coreLines[] = $line;
}
$core = rtrim(implode("\n", $coreLines));

if ($core === '' || !str_contains($core, 'CREATE TABLE `funcionarios`')) {
    fwrite(STDERR, "funcionarios.sql: no hay bloque estructura/volcado reconocible.\n");
    exit(1);
}

// Cabecera como en el dump principal
if (!str_contains(substr($core, 0, 120), '--------------------------------------------------------')) {
    $core = "-- --------------------------------------------------------\n\n--\n" . $core;
}

// ── Reemplazar en gestion: desde separador antes de funcionarios hasta antes de hitos ──
$gl = explode("\n", $gest);
$idxF = $idxH = null;
foreach ($gl as $i => $line) {
    if (str_contains($line, 'Estructura de tabla para la tabla `funcionarios`')) {
        $idxF = $i;
    }
    if ($idxF !== null && $i > $idxF && str_contains($line, 'Estructura de tabla para la tabla `hitos`')) {
        $idxH = $i;
        break;
    }
}
if ($idxF === null || $idxH === null) {
    fwrite(STDERR, "gestion_gubernamental.sql: no se hallaron secciones funcionarios/hitos.\n");
    exit(1);
}

$start = $idxF;
for ($j = $idxF - 1; $j >= 0; $j--) {
    if (str_contains($gl[$j], '-- --------------------------------------------------------')) {
        $start = $j;
        break;
    }
}

$before = implode("\n", array_slice($gl, 0, $start));

// Tras el volcado de funcionarios el dump trae separador + línea "--" + "Estructura hitos"; no repetir bloques
$after = implode("\n", array_slice($gl, $idxH));

$newGest = $before . "\n" . $core . "\n\n-- --------------------------------------------------------\n\n--\n" . $after;

preg_match_all('/\((\d+),/', $core, $m);
$maxId = $m[1] !== [] ? max(array_map('intval', $m[1])) : 0;
$aiFrag = null;
if (preg_match('/AUTO_INCREMENT=(\d+)/', $frag, $am)) {
    $aiFrag = (int) $am[1];
}
$autoInc = max($maxId + 1, $aiFrag ?? 0, 6145);

$newGest = preg_replace(
    '/(ALTER TABLE `funcionarios`\s*\n\s+MODIFY `id` int\(11\) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=)\d+/',
    '${1}' . $autoInc,
    $newGest,
    1
);

file_put_contents($gestPath, $newGest);
$nIds = count($m[1]);
echo "OK: gestion_gubernamental.sql ← datos de funcionarios.sql\n";
echo "  valores con id en INSERT: $nIds, max id: $maxId, AUTO_INCREMENT: $autoInc\n";
