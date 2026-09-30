<?php
/**
 * Lee Plan_Estrategico_La_Pampa.xlsx (columnas estrategia, programa, componente, acción)
 * y actualiza gestion_gubernamental.sql con INSERTs vinculados (estrategia → programa → componente → acción).
 *
 * Uso: php tools/plan_excel_to_gestion_sql.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$xlsx = $root . '/Plan_Estrategico_La_Pampa.xlsx';
$sqlFile = $root . '/gestion_gubernamental.sql';

if (!is_readable($xlsx)) {
    fwrite(STDERR, "No se encuentra o no se puede leer: $xlsx\n");
    exit(1);
}

$rows = readXlsxFirstSheet($xlsx);
if (count($rows) < 2) {
    fwrite(STDERR, "El Excel no tiene filas de datos.\n");
    exit(1);
}

$header = array_shift($rows);
$colMap = mapColumns($header);
if (count($colMap) < 4) {
    fwrite(STDERR, 'Encabezados esperados: estrategia, programa, componente, accion (o acción). Encontrado: ' . json_encode($header, JSON_UNESCAPED_UNICODE) . "\n");
    exit(1);
}

$ts = date('Y-m-d H:i:s');
$plan = buildPlanHierarchy($rows, $colMap);
$sqlChunks = generateSql($plan, $ts);

if (!is_readable($sqlFile)) {
    fwrite(STDERR, "No se encuentra: $sqlFile\n");
    exit(1);
}

$original = file_get_contents($sqlFile);
if ($original === false) {
    fwrite(STDERR, "No se pudo leer $sqlFile\n");
    exit(1);
}

$patched = patchGestionSql($original, $sqlChunks);
if ($patched === null) {
    fwrite(STDERR, "No se pudo parchear el SQL (¿cambió el formato del dump?).\n");
    exit(1);
}

file_put_contents($sqlFile, $patched);
echo "OK: actualizado $sqlFile\n";
echo '  Estrategias: ' . count($plan['estrategias']) . ', Programas: ' . count($plan['programas'])
    . ', Componentes: ' . count($plan['componentes']) . ', Acciones: ' . count($plan['acciones']) . "\n";

// ─── XLSX (primera hoja, shared strings) ─────────────────────────────────────

function readXlsxFirstSheet(string $path): array
{
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('No se pudo abrir el .xlsx como ZIP');
    }
    $shared = [];
    if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
        $sx = $zip->getFromIndex($idx);
        $xml = @simplexml_load_string($sx);
        if ($xml && isset($xml->si)) {
            foreach ($xml->si as $si) {
                $shared[] = extractSiText($si);
            }
        }
    }
    $sheetPath = 'xl/worksheets/sheet1.xml';
    if ($zip->locateName($sheetPath) === false) {
        $rels = @simplexml_load_string($zip->getFromName('xl/workbook.xml'));
        if ($rels && isset($rels->sheets->sheet[0]['r:id'])) {
            $rid = (string) $rels->sheets->sheet[0]['r:id'];
            $wrels = @simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));
            if ($wrels) {
                foreach ($wrels->Relationship as $rel) {
                    if ((string) $rel['Id'] === $rid) {
                        $sheetPath = 'xl/' . ltrim((string) $rel['Target'], '/');
                        break;
                    }
                }
            }
        }
    }
    $sheetXml = $zip->getFromName($sheetPath);
    $zip->close();
    if ($sheetXml === false) {
        throw new RuntimeException('No se encontró la primera hoja en el xlsx');
    }
    $sheet = simplexml_load_string($sheetXml);
    if (!$sheet || !isset($sheet->sheetData)) {
        throw new RuntimeException('sheet1.xml inválido');
    }
    $sheet->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $rowNodes = $sheet->xpath('//m:sheetData/m:row') ?: [];

    $grid = [];
    foreach ($rowNodes as $row) {
        $rowIndex = (int) preg_replace('/\D/', '', (string) $row['r']);
        $cells = [];
        foreach ($row->c as $c) {
            $ref = (string) $c['r'];
            if (preg_match('/^([A-Z]+)/', $ref, $m)) {
                $colLetters = $m[1];
            } else {
                continue;
            }
            $colIndex = columnLettersToIndex($colLetters);
            $cells[$colIndex] = cellRawValue($c, $shared);
        }
        if ($cells !== []) {
            ksort($cells);
            $maxCol = max(array_keys($cells));
            $line = [];
            for ($i = 0; $i <= $maxCol; $i++) {
                $line[$i] = $cells[$i] ?? '';
            }
            $grid[] = $line;
        }
    }
    return $grid;
}

function extractSiText(SimpleXMLElement $si): string
{
    if (isset($si->t)) {
        return (string) $si->t;
    }
    $parts = [];
    if (isset($si->r)) {
        foreach ($si->r as $r) {
            if (isset($r->t)) {
                $parts[] = (string) $r->t;
            }
        }
    }
    return implode('', $parts);
}

function columnLettersToIndex(string $letters): int
{
    $n = 0;
    $len = strlen($letters);
    for ($i = 0; $i < $len; $i++) {
        $n = $n * 26 + (ord($letters[$i]) - 64);
    }
    return $n - 1;
}

function cellRawValue(SimpleXMLElement $c, array $shared): string
{
    $t = (string) $c['t'];
    $v = isset($c->v) ? (string) $c->v : '';
    if ($t === 's' && $v !== '') {
        $i = (int) $v;
        return $shared[$i] ?? '';
    }
    if ($t === 'inlineStr' && isset($c->is->t)) {
        return (string) $c->is->t;
    }
    return $v;
}

function mapColumns(array $headerRow): array
{
    $norm = static function (string $s): string {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $s);
        return preg_replace('/\s+/', ' ', $s) ?? $s;
    };
    $want = ['estrategia' => null, 'programa' => null, 'componente' => null, 'accion' => null];
    foreach ($headerRow as $i => $cell) {
        $k = $norm((string) $cell);
        if ($k === 'estrategia') {
            $want['estrategia'] = $i;
        } elseif ($k === 'programa') {
            $want['programa'] = $i;
        } elseif ($k === 'componente') {
            $want['componente'] = $i;
        } elseif ($k === 'accion' || $k === 'acción') {
            $want['accion'] = $i;
        }
    }
    return array_filter($want, static fn ($v) => $v !== null);
}

/**
 * @return array{estrategias: list, programas: list, componentes: list, acciones: list}
 */
function buildPlanHierarchy(array $rows, array $colMap): array
{
    $ie = $colMap['estrategia'];
    $ip = $colMap['programa'];
    $ic = $colMap['componente'];
    $ia = $colMap['accion'];

    $carryE = $carryP = $carryC = '';

    $estrategias = [];
    $programas = [];
    $componentes = [];
    $acciones = [];

    $estKeyToId = [];
    $progKeyToId = [];
    $compKeyToId = [];
    $estDescToCodigo = [];

    $codigoUsado = ['estrategias' => [], 'programas' => [], 'componentes' => [], 'acciones' => []];

    $nextEst = $nextProg = $nextComp = $nextAcc = 1;
    $synEstSeq = 0;

    foreach ($rows as $row) {
        $e = trim((string) ($row[$ie] ?? ''));
        $p = trim((string) ($row[$ip] ?? ''));
        $c = trim((string) ($row[$ic] ?? ''));
        $a = trim((string) ($row[$ia] ?? ''));

        if ($e !== '') {
            $carryE = $e;
            $carryP = '';
            $carryC = '';
        }
        if ($p !== '') {
            $carryP = $p;
            $carryC = '';
        }
        if ($c !== '') {
            $carryC = $c;
        }

        if ($carryE === '' || $carryP === '' || $carryC === '' || $a === '') {
            continue;
        }

        [$ec, $ed] = parseCodigoDesc($carryE);
        [$pc, $pd] = parseCodigoDesc($carryP);
        [$cc, $cd] = parseCodigoDesc($carryC);
        [$ac, $ad] = parseCodigoDesc($a);

        if ($ec === null) {
            $dkey = $carryE;
            if (!isset($estDescToCodigo[$dkey])) {
                $synEstSeq++;
                $estDescToCodigo[$dkey] = 'S' . str_pad((string) $synEstSeq, 2, '0', STR_PAD_LEFT);
            }
            $ec = $estDescToCodigo[$dkey];
        }
        if ($ed === '') {
            $ed = $carryE;
        }

        $estKey = $ec;
        if (!isset($estKeyToId[$estKey])) {
            $id = $nextEst++;
            $estKeyToId[$estKey] = $id;
            $codE = uniquifyCodigoTabla($ec, 20, $codigoUsado['estrategias']);
            $estrategias[] = ['id' => $id, 'codigo' => $codE, 'descripcion' => $ed];
        }
        $estId = $estKeyToId[$estKey];

        if ($pc === null) {
            $pc = $ec . '.' . str_pad((string) count(array_filter($programas, static fn ($x) => $x['estrategia_id'] === $estId)) + 1, 2, '0', STR_PAD_LEFT);
        }
        if ($pd === '') {
            $pd = $carryP;
        }
        $progKey = $estKey . '|' . $pc;
        if (!isset($progKeyToId[$progKey])) {
            $id = $nextProg++;
            $progKeyToId[$progKey] = $id;
            $codP = uniquifyCodigoTabla($pc, 30, $codigoUsado['programas']);
            $programas[] = ['id' => $id, 'estrategia_id' => $estId, 'codigo' => $codP, 'descripcion' => $pd];
        }
        $progId = $progKeyToId[$progKey];

        if ($cc === null) {
            $cc = $pc . '.' . str_pad((string) count(array_filter($componentes, static fn ($x) => $x['programa_id'] === $progId)) + 1, 2, '0', STR_PAD_LEFT);
        }
        if ($cd === '') {
            $cd = $carryC;
        }
        $compKey = $progKey . '|' . $cc;
        if (!isset($compKeyToId[$compKey])) {
            $id = $nextComp++;
            $compKeyToId[$compKey] = $id;
            $codC = uniquifyCodigoTabla($cc, 40, $codigoUsado['componentes']);
            $componentes[] = ['id' => $id, 'programa_id' => $progId, 'codigo' => $codC, 'descripcion' => $cd];
        }
        $compId = $compKeyToId[$compKey];

        if ($ac === null) {
            $n = count(array_filter($acciones, static fn ($x) => $x['componente_id'] === $compId)) + 1;
            $ac = $cc . '.' . str_pad((string) $n, 2, '0', STR_PAD_LEFT);
        }
        if ($ad === '') {
            $ad = $a;
        }
        $codA = uniquifyCodigoTabla($ac, 50, $codigoUsado['acciones']);
        $acciones[] = [
            'id' => $nextAcc++,
            'componente_id' => $compId,
            'codigo' => $codA,
            'descripcion' => $ad,
        ];
    }

    return compact('estrategias', 'programas', 'componentes', 'acciones');
}

function parseCodigoDesc(string $s): array
{
    $s = trim(preg_replace('/\s+/u', ' ', $s) ?? $s);
    if ($s === '') {
        return [null, ''];
    }
    if (preg_match('/^(\d+(?:\.\d+)*)\s*[.\-–—]\s*(.+)$/u', $s, $m)) {
        return [trim($m[1]), trim($m[2])];
    }
    if (preg_match('/^(\d+(?:\.\d+)*)$/u', $s, $m)) {
        return [trim($m[1]), $s];
    }
    return [null, $s];
}

/**
 * Garantiza unicidad de `codigo` por tabla (índice UNIQUE en el dump).
 *
 * @param array<string, true> $usados
 */
function uniquifyCodigoTabla(string $base, int $maxLen, array &$usados): string
{
    $base = trim($base);
    if ($base === '') {
        $base = 'X';
    }
    $c = mb_substr($base, 0, $maxLen);
    if (!isset($usados[$c])) {
        $usados[$c] = true;

        return $c;
    }
    for ($i = 2; $i < 10000; $i++) {
        $suffix = '-' . $i;
        $room = $maxLen - mb_strlen($suffix);
        if ($room < 1) {
            $room = 1;
        }
        $try = mb_substr(mb_substr($base, 0, $room) . $suffix, 0, $maxLen);
        if (!isset($usados[$try])) {
            $usados[$try] = true;

            return $try;
        }
    }

    $fallback = mb_substr('U' . str_replace('.', '', uniqid('', true)), 0, $maxLen);
    $usados[$fallback] = true;

    return $fallback;
}

/** @param array{estrategias: list, programas: list, componentes: list, acciones: list} $plan */
function generateSql(array $plan, string $ts): array
{
    $q = static function (?string $s): string {
        if ($s === null || $s === '') {
            return "''";
        }
        return "'" . str_replace(["\\", "'"], ["\\\\", "''"], $s) . "'";
    };

    $linesE = [];
    foreach ($plan['estrategias'] as $r) {
        $linesE[] = sprintf('(%d, %s, %s, NULL, NULL, \'media\', \'pendiente\', NULL, NULL, NULL, \'%s\', \'%s\')', $r['id'], $q($r['codigo']), $q($r['descripcion']), $ts, $ts);
    }
    $sqlE = "INSERT INTO `estrategias` (`id`, `codigo`, `descripcion`, `organismo_responsable_id`, `funcionario_responsable_id`, `prioridad`, `estado`, `fecha_inicio`, `fecha_fin_estimada`, `observaciones`, `created_at`, `updated_at`) VALUES\n" . implode(",\n", $linesE) . ";\n";

    $linesP = [];
    foreach ($plan['programas'] as $r) {
        $linesP[] = sprintf('(%d, %d, %s, %s, NULL, NULL, \'pendiente\', NULL, NULL, NULL, NULL, \'%s\', \'%s\')', $r['id'], $r['estrategia_id'], $q($r['codigo']), $q($r['descripcion']), $ts, $ts);
    }
    $sqlP = "INSERT INTO `programas` (`id`, `estrategia_id`, `codigo`, `descripcion`, `organismo_responsable_id`, `funcionario_responsable_id`, `estado`, `presupuesto_estimado`, `fecha_inicio`, `fecha_fin_estimada`, `observaciones`, `created_at`, `updated_at`) VALUES\n" . implode(",\n", $linesP) . ";\n";

    $linesC = [];
    foreach ($plan['componentes'] as $r) {
        $linesC[] = sprintf('(%d, %d, %s, %s, NULL, NULL, \'pendiente\', NULL, NULL, NULL, NULL, \'%s\', \'%s\')', $r['id'], $r['programa_id'], $q($r['codigo']), $q($r['descripcion']), $ts, $ts);
    }
    $sqlC = "INSERT INTO `componentes` (`id`, `programa_id`, `codigo`, `descripcion`, `organismo_responsable_id`, `funcionario_responsable_id`, `estado`, `presupuesto_estimado`, `fecha_inicio`, `fecha_fin_estimada`, `observaciones`, `created_at`, `updated_at`) VALUES\n" . implode(",\n", $linesC) . ";\n";

    $linesA = [];
    foreach ($plan['acciones'] as $r) {
        $linesA[] = sprintf('(%d, %d, %s, %s, NULL, NULL, \'pendiente\', \'media\', NULL, 0.00, NULL, NULL, NULL, 0, NULL, \'%s\', \'%s\')', $r['id'], $r['componente_id'], $q($r['codigo']), $q($r['descripcion']), $ts, $ts);
    }
    $sqlA = "INSERT INTO `acciones` (`id`, `componente_id`, `codigo`, `descripcion`, `organismo_responsable_id`, `funcionario_responsable_id`, `estado`, `prioridad`, `presupuesto_estimado`, `presupuesto_ejecutado`, `fecha_inicio`, `fecha_fin_estimada`, `fecha_fin_real`, `porcentaje_avance`, `observaciones`, `created_at`, `updated_at`) VALUES\n" . implode(",\n", $linesA) . ";\n";

    $maxE = max(array_column($plan['estrategias'], 'id'));
    $maxP = max(array_column($plan['programas'], 'id'));
    $maxCo = max(array_column($plan['componentes'], 'id'));
    $maxA = max(array_column($plan['acciones'], 'id'));

    return [
        'estrategias' => $sqlE,
        'programas' => $sqlP,
        'componentes' => $sqlC,
        'acciones' => $sqlA,
        'auto_inc' => [
            'estrategias' => $maxE + 1,
            'programas' => $maxP + 1,
            'componentes' => $maxCo + 1,
            'acciones' => $maxA + 1,
        ],
    ];
}

function patchGestionSql(string $content, array $chunks): ?string
{
    // Reemplazar bloque INSERT estrategias (hasta el primer ");" que cierra el VALUES)
    $reEst = '/--\R-- Volcado de datos para la tabla `estrategias`\R--\R\RINSERT INTO `estrategias`[\s\S]*?\);\R\R/s';
    if (!preg_match($reEst, $content)) {
        return null;
    }
    $introEst = "--\n-- Volcado de datos para la tabla `estrategias`\n--\n\n";
    $content = preg_replace($reEst, $introEst . $chunks['estrategias'] . "\n", $content, 1);

    // Sustituir (o insertar) volcados programas + componentes + acciones: desde comentario programas hasta antes de estructura `proyectos`
    $rePca = '/--\R-- Volcado de datos para la tabla `programas`[\s\S]*?(?=\R--\R-- Estructura de tabla para la tabla `proyectos`)/s';
    $block = "--\n-- Volcado de datos para la tabla `programas`\n--\n\n" . $chunks['programas'] . "\n"
        . "--\n-- Volcado de datos para la tabla `componentes`\n--\n\n" . $chunks['componentes'] . "\n"
        . "--\n-- Volcado de datos para la tabla `acciones`\n--\n\n" . $chunks['acciones'] . "\n";

    if (preg_match($rePca, $content)) {
        $content = preg_replace($rePca, $block, $content, 1);
    } else {
        $anchor = ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n-- --------------------------------------------------------\n\n--\n-- Estructura de tabla para la tabla `proyectos`";
        $pos = strpos($content, $anchor);
        if ($pos === false) {
            return null;
        }
        $insertAt = $pos + strlen(") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n");
        $content = substr($content, 0, $insertAt) . $block . substr($content, $insertAt);
    }

    // AUTO_INCREMENT
    $ai = $chunks['auto_inc'];
    $content = preg_replace(
        '/(ALTER TABLE `estrategias`\R\s+MODIFY `id` int\(11\) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=)\d+/',
        '${1}' . $ai['estrategias'],
        $content,
        1
    );
    $content = preg_replace(
        '/(ALTER TABLE `programas`\R\s+MODIFY `id` int\(11\) NOT NULL AUTO_INCREMENT)(, AUTO_INCREMENT=\d+)?;/',
        '$1, AUTO_INCREMENT=' . $ai['programas'] . ';',
        $content,
        1
    );
    $content = preg_replace(
        '/(ALTER TABLE `componentes`\R\s+MODIFY `id` int\(11\) NOT NULL AUTO_INCREMENT)(, AUTO_INCREMENT=\d+)?;/',
        '$1, AUTO_INCREMENT=' . $ai['componentes'] . ';',
        $content,
        1
    );
    $content = preg_replace(
        '/(ALTER TABLE `acciones`\R\s+MODIFY `id` int\(11\) NOT NULL AUTO_INCREMENT)(, AUTO_INCREMENT=\d+)?;/',
        '$1, AUTO_INCREMENT=' . $ai['acciones'] . ';',
        $content,
        1
    );

    return $content;
}
