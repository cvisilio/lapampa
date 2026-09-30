<?php
require_once 'includes/config.php';
require_once 'includes/xlsx.php';
require_once 'includes/spreadsheet.php';
require_once 'includes/localidades_match.php';
requireRol('admin', 'editor');

$db = getDB();
$page_title = 'Importar datos';
$breadcrumb = [['label' => 'Importar datos']];

$resultado = null;

function ensureAportesTable(PDO $db): void
{
    $createSql = "
        CREATE TABLE IF NOT EXISTS `aportes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `id_localidad` int(11) NOT NULL,
          `organismo` smallint(6) NOT NULL DEFAULT 0,
          `FecPag` date NOT NULL,
          `ExpedN` int(11) DEFAULT NULL,
          `ExpedA` smallint(6) DEFAULT NULL,
          `NombeJur` varchar(255) DEFAULT NULL,
          `NroChe` int(11) DEFAULT NULL,
          `importe` decimal(15,2) NOT NULL,
          `DescCpto` varchar(50) NOT NULL DEFAULT '',
          `DescSubCpto` varchar(50) NOT NULL DEFAULT '',
          `numero_importacion` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uk_aporte_pago` (`FecPag`,`ExpedN`,`NroChe`,`importe`),
          KEY `idx_aportes_localidad` (`id_localidad`),
          KEY `idx_aportes_importacion` (`numero_importacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $db->exec($createSql);

    $columnas = $db->query('SHOW COLUMNS FROM aportes')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('FecPag', $columnas, true)) {
        $count = (int) $db->query('SELECT COUNT(*) FROM aportes')->fetchColumn();
        if ($count > 0) {
            throw new RuntimeException(
                'La tabla aportes tiene un formato antiguo con datos. Ejecutá InformesXLocalidad/aportes.sql en MySQL antes de importar.'
            );
        }
        $db->exec('DROP TABLE aportes');
        $db->exec($createSql);
        $columnas = $db->query('SHOW COLUMNS FROM aportes')->fetchAll(PDO::FETCH_COLUMN);
    }

    $alteraciones = [
        'ExpedA' => 'ADD COLUMN `ExpedA` smallint(6) DEFAULT NULL AFTER `ExpedN`',
        'NombeJur' => 'ADD COLUMN `NombeJur` varchar(255) DEFAULT NULL AFTER `ExpedA`',
        'numero_importacion' => 'ADD COLUMN `numero_importacion` int(11) NOT NULL DEFAULT 0',
    ];
    foreach ($alteraciones as $columna => $sql) {
        if (!in_array($columna, $columnas, true)) {
            $db->exec("ALTER TABLE aportes {$sql}");
        }
    }

    $tipoImporte = $db->query("SHOW COLUMNS FROM aportes LIKE 'importe'")->fetch(PDO::FETCH_ASSOC);
    if ($tipoImporte && !str_contains((string) ($tipoImporte['Type'] ?? ''), 'decimal')) {
        $db->exec('ALTER TABLE aportes MODIFY `importe` decimal(15,2) NOT NULL');
    }

    $indice = $db->query("SHOW INDEX FROM aportes WHERE Key_name = 'uk_aporte_pago'")->fetch();
    if (!$indice) {
        try {
            $db->exec('ALTER TABLE aportes ADD UNIQUE KEY `uk_aporte_pago` (`FecPag`,`ExpedN`,`NroChe`,`importe`)');
        } catch (PDOException) {
            // Puede fallar si ya hay duplicados en datos existentes.
        }
    }
}

function ensureDeudaIssTable(PDO $db): void
{
    $db->exec("
        CREATE TABLE IF NOT EXISTS `deuda_iss` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `anio` smallint(6) NOT NULL,
          `mes` tinyint(4) NOT NULL,
          `id_localidad` int(11) NOT NULL,
          `id_concepto` int(11) NOT NULL,
          `importe` decimal(15,2) NOT NULL,
          `numero_importacion` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uk_deuda_iss_periodo` (`anio`,`mes`,`id_localidad`,`id_concepto`),
          KEY `idx_deuda_iss_localidad` (`id_localidad`),
          KEY `idx_deuda_iss_concepto` (`id_concepto`),
          KEY `idx_deuda_iss_importacion` (`numero_importacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

function ensureAgentesTable(PDO $db): void
{
    $createSql = "
        CREATE TABLE IF NOT EXISTS `agentes` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `id_dependencia` int(11) NOT NULL,
          `mes` tinyint(4) NOT NULL,
          `anio` smallint(6) NOT NULL,
          `total_empleados` int(11) NOT NULL DEFAULT 0,
          `total_remuneracion` decimal(15,2) NOT NULL DEFAULT 0.00,
          `total_aportes` decimal(15,2) NOT NULL DEFAULT 0.00,
          `total_asignacion_familiar` decimal(15,2) NOT NULL DEFAULT 0.00,
          `numero_importacion` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uk_agentes_periodo` (`id_dependencia`,`mes`,`anio`),
          KEY `idx_agentes_importacion` (`numero_importacion`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $db->exec($createSql);

    $columnas = $db->query('SHOW COLUMNS FROM agentes')->fetchAll(PDO::FETCH_COLUMN);
    $alteraciones = [
        'total_remuneracion' => 'ADD COLUMN `total_remuneracion` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `total_empleados`',
        'total_aportes' => 'ADD COLUMN `total_aportes` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `total_remuneracion`',
        'total_asignacion_familiar' => 'ADD COLUMN `total_asignacion_familiar` decimal(15,2) NOT NULL DEFAULT 0.00 AFTER `total_aportes`',
    ];
    foreach ($alteraciones as $columna => $sql) {
        if (!in_array($columna, $columnas, true)) {
            $db->exec("ALTER TABLE agentes {$sql}");
        }
    }
}

ensureAportesTable($db);
ensureDeudaIssTable($db);
ensureAgentesTable($db);

function normalizarTextoImport(string $texto): string
{
    $texto = mb_strtoupper(trim($texto), 'UTF-8');
    $texto = str_replace(
        ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
        ['A', 'E', 'I', 'O', 'U', 'U', 'N'],
        $texto
    );
    return preg_replace('/\s+/', ' ', $texto) ?? $texto;
}

function parseImporteRetencion(mixed $valor): float
{
    if ($valor === null || $valor === '') {
        return 0.0;
    }
    if (is_numeric($valor)) {
        return (float) $valor;
    }

    $texto = trim((string) $valor);
    $texto = str_replace(['$', ' '], '', $texto);
    if (preg_match('/,\d{1,2}$/', $texto)) {
        $texto = str_replace('.', '', $texto);
        $texto = str_replace(',', '.', $texto);
    } else {
        $texto = str_replace(',', '', $texto);
    }

    return is_numeric($texto) ? (float) $texto : 0.0;
}

function parseMesRetencion(mixed $valor): ?int
{
    if ($valor === null || $valor === '') {
        return null;
    }

    if (is_numeric($valor)) {
        $num = (float) $valor;
        if ($num >= 1 && $num <= 12 && floor($num) == $num) {
            return (int) $num;
        }
        if ($num > 40000 && $num < 70000) {
            $unix = (int) round(($num - 25569) * 86400);
            $mes = (int) date('n', $unix);
            return ($mes >= 1 && $mes <= 12) ? $mes : null;
        }
    }

    $texto = trim((string) $valor);
    if (preg_match('/^(\d{1,2})[\/\-](\d{4})$/', $texto, $m)) {
        $mes = (int) $m[1];
        return ($mes >= 1 && $mes <= 12) ? $mes : null;
    }
    if (preg_match('/^(\d{4})[\/\-](\d{1,2})/', $texto, $m)) {
        $mes = (int) $m[2];
        return ($mes >= 1 && $mes <= 12) ? $mes : null;
    }

    $meses = [
        'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
        'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
        'septiembre' => 9, 'setiembre' => 9, 'octubre' => 10,
        'noviembre' => 11, 'diciembre' => 12,
    ];
    $lower = mb_strtolower($texto, 'UTF-8');
    foreach ($meses as $nombre => $numero) {
        if (str_contains($lower, $nombre)) {
            return $numero;
        }
    }

    return null;
}

function mapRetencionesColumns(array $headerRow): array
{
    $map = [];
    foreach ($headerRow as $i => $cell) {
        $k = normalizarTextoImport((string) $cell);
        $k = str_replace(['-', '_'], ' ', $k);
        $k = preg_replace('/\s+/', ' ', $k) ?? $k;

        if (str_contains($k, 'LOCALIDAD')) {
            $map['localidad'] = $i;
        } elseif ((str_contains($k, 'FECHA') && str_contains($k, 'MES')) || $k === 'MES') {
            $map['mes'] = $i;
        } elseif (str_contains($k, 'IMPORTE') && str_contains($k, 'LIQ')) {
            $map['importe_liquidacion'] = $i;
        } elseif (str_contains($k, ' ISS') || $k === 'ISS' || str_contains($k, 'SUMA DE ISS')) {
            $map['iss'] = $i;
        } elseif (str_contains($k, 'ANTCOP') || str_contains($k, 'ANT COP')) {
            $map['antcop'] = $i;
        } elseif (str_contains($k, 'PMOSBLP') || str_contains($k, 'PMOS BLP')) {
            $map['pmosblp'] = $i;
        } elseif (str_contains($k, 'OPYPYM') || str_contains($k, 'OP Y PYM') || str_contains($k, 'OP YPYM')) {
            $map['opypym'] = $i;
        } elseif (str_contains($k, 'TOTRETEN') || str_contains($k, 'TOT RETEN')) {
            if (!isset($map['total_retenido'])) {
                $map['total_retenido'] = $i;
            }
        }
    }

    return $map;
}

function detectRetencionesHeaderRow(array $rows): int
{
    $maxScan = min(count($rows), 15);
    $bestIndex = -1;
    $bestScore = -1;

    for ($i = 0; $i < $maxScan; $i++) {
        $cols = mapRetencionesColumns($rows[$i]);
        $required = ['localidad', 'mes', 'importe_liquidacion', 'total_retenido'];
        $score = count(array_intersect($required, array_keys($cols)));
        foreach (['iss', 'antcop', 'pmosblp', 'opypym'] as $optional) {
            if (isset($cols[$optional])) {
                $score += 0.25;
            }
        }
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestIndex = $i;
        }
    }

    if ($bestIndex < 0 || $bestScore < 4) {
        throw new RuntimeException(
            'No se encontró la fila de encabezados. Se esperan columnas como Localidad, Fecha - Mes, Suma de Importe Liq y Suma de TotReten (normalmente fila 3).'
        );
    }

    return $bestIndex;
}

function esFilaRetencionIgnorable(string $nombreLocalidad): bool
{
    $norm = normalizarTextoImport($nombreLocalidad);
    if ($norm === '') {
        return true;
    }
    return in_array($norm, ['TOTAL', 'TOTALES', 'SUBTOTAL', 'SUB TOTAL'], true)
        || str_starts_with($norm, 'TOTAL ');
}

function buscarLocalidadIdSimilar(PDO $db, string $nombreExcel, array &$cache, float $minScore = 75): ?array
{
    $match = buscarLocalidadPorNombre($db, $nombreExcel, $cache, $minScore);
    if (!$match) {
        return null;
    }
    return [
        'id' => $match['id'],
        'nombre' => $match['nombre'],
        'score' => $match['score'],
    ];
}

function obtenerProximoNumeroImportacion(PDO $db, string $tabla = 'retenciones'): int
{
    if (!in_array($tabla, ['retenciones', 'deuda_iss', 'distribucion_semanal', 'agentes', 'aportes'], true)) {
        throw new InvalidArgumentException('Tabla de importación no permitida.');
    }

    return (int) $db->query("SELECT COALESCE(MAX(numero_importacion), 0) + 1 FROM {$tabla}")->fetchColumn();
}

function importarRetencionesDesdeExcel(PDO $db, string $path, int $anio): array
{
    $rows = readXlsxFirstSheet($path);
    if (count($rows) < 4) {
        throw new RuntimeException('El Excel no contiene filas de datos.');
    }

    $headerIndex = detectRetencionesHeaderRow($rows);
    $header = $rows[$headerIndex];
    $rows = array_slice($rows, $headerIndex + 1);
    $cols = mapRetencionesColumns($header);
    $required = ['localidad', 'mes', 'importe_liquidacion', 'total_retenido'];
    $missing = array_diff($required, array_keys($cols));
    if ($missing !== []) {
        throw new RuntimeException(
            'Faltan columnas en la fila de encabezados (fila ' . ($headerIndex + 1) . '): ' . implode(', ', $missing)
        );
    }

    $numeroImportacion = obtenerProximoNumeroImportacion($db, 'retenciones');

    $stmtFind = $db->prepare('
        SELECT id FROM retenciones
        WHERE id_localidad = ? AND anio = ? AND mes = ?
        LIMIT 1
    ');
    $stmtInsert = $db->prepare('
        INSERT INTO retenciones (
            id_localidad, anio, mes, importe_liquidacion, iss, antcop, pmosblp, opypym,
            otros, total_retenido, suma_neto, numero_importacion
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    ');
    $stmtUpdate = $db->prepare('
        UPDATE retenciones
        SET importe_liquidacion = ?, iss = ?, antcop = ?, pmosblp = ?, opypym = ?,
            otros = ?, total_retenido = ?, suma_neto = ?, numero_importacion = ?
        WHERE id = ?
    ');

    $localidadesCache = [];
    $insertados = 0;
    $actualizados = 0;
    $omitidos = 0;
    $sinLocalidad = [];
    $matchesDudosos = [];

    foreach ($rows as $row) {
        $nombreLocalidad = trim((string) ($row[$cols['localidad']] ?? ''));
        if (esFilaRetencionIgnorable($nombreLocalidad)) {
            $omitidos++;
            continue;
        }

        $mes = parseMesRetencion($row[$cols['mes']] ?? null);
        if (!$mes) {
            $omitidos++;
            continue;
        }

        $match = buscarLocalidadIdSimilar($db, $nombreLocalidad, $localidadesCache);
        if (!$match) {
            $sinLocalidad[$nombreLocalidad] = true;
            $omitidos++;
            continue;
        }
        if ($match['score'] < 90) {
            $matchesDudosos[] = "{$nombreLocalidad} → {$match['nombre']} ({$match['score']}%)";
        }

        $importe = parseImporteRetencion($row[$cols['importe_liquidacion']] ?? 0);
        $iss = isset($cols['iss']) ? parseImporteRetencion($row[$cols['iss']] ?? 0) : 0.0;
        $antcop = isset($cols['antcop']) ? parseImporteRetencion($row[$cols['antcop']] ?? 0) : 0.0;
        $pmosblp = isset($cols['pmosblp']) ? parseImporteRetencion($row[$cols['pmosblp']] ?? 0) : 0.0;
        $opypym = isset($cols['opypym']) ? parseImporteRetencion($row[$cols['opypym']] ?? 0) : 0.0;
        $totalRetenido = parseImporteRetencion($row[$cols['total_retenido']] ?? 0);
        $otros = max(0, $totalRetenido - ($iss + $antcop + $pmosblp + $opypym));
        $sumaNeto = $importe - $totalRetenido;

        $stmtFind->execute([$match['id'], $anio, $mes]);
        $existenteId = $stmtFind->fetchColumn();

        if ($existenteId) {
            $stmtUpdate->execute([
                $importe, $iss, $antcop, $pmosblp, $opypym,
                $otros, $totalRetenido, $sumaNeto, $numeroImportacion, (int) $existenteId,
            ]);
            $actualizados++;
        } else {
            $stmtInsert->execute([
                $match['id'], $anio, $mes, $importe, $iss, $antcop, $pmosblp, $opypym,
                $otros, $totalRetenido, $sumaNeto, $numeroImportacion,
            ]);
            $insertados++;
        }
    }

    return [
        'numero_importacion' => $numeroImportacion,
        'insertados' => $insertados,
        'actualizados' => $actualizados,
        'omitidos' => $omitidos,
        'sin_localidad' => array_keys($sinLocalidad),
        'matches_dudosos' => $matchesDudosos,
    ];
}

function parseEnteroImportacion(mixed $valor): ?int
{
    if ($valor === null || $valor === '') {
        return null;
    }
    if (is_numeric($valor)) {
        return (int) $valor;
    }
    $texto = trim((string) $valor);
    return is_numeric($texto) ? (int) $texto : null;
}

function mapDeudaIssColumns(array $headerRow): array
{
    $map = [];
    foreach ($headerRow as $i => $cell) {
        $k = normalizarTextoImport((string) $cell);
        $k = str_replace(['-', '_', '.'], ' ', $k);
        $k = preg_replace('/\s+/', ' ', $k) ?? $k;

        if ($k === 'ANIO' || $k === 'ANO' || str_contains($k, 'ANIO') || str_contains($k, 'ANO')) {
            $map['anio'] = $i;
        } elseif ($k === 'MES' || str_contains($k, 'MES')) {
            $map['mes'] = $i;
        } elseif (str_contains($k, 'LOCALIDAD')) {
            $map['localidad'] = $i;
        } elseif ($k === 'CONCEPTO' || str_contains($k, 'CONCEPTO')) {
            $map['concepto'] = $i;
        } elseif (
            str_contains($k, 'RETENER')
            && (str_contains($k, 'IMPORTE') || str_contains($k, 'IMPRTE') || str_contains($k, 'IMPT'))
        ) {
            $map['importe'] = $i;
        }
    }

    return $map;
}

function detectDeudaIssHeaderRow(array $rows): int
{
    $maxScan = min(count($rows), 30);
    $bestIndex = -1;
    $bestScore = -1;

    for ($i = 0; $i < $maxScan; $i++) {
        $cols = mapDeudaIssColumns($rows[$i]);
        $required = ['anio', 'mes', 'localidad', 'concepto', 'importe'];
        $score = count(array_intersect($required, array_keys($cols)));
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestIndex = $i;
        }
    }

    if ($bestIndex < 0 || $bestScore < 5) {
        throw new RuntimeException(
            'No se encontró la fila de encabezados. Se esperan Anio, Mes, Localidad, Concepto e Imprte a Retener.'
        );
    }

    return $bestIndex;
}

function buscarConceptoDeudaIss(PDO $db, mixed $valor, array &$cache): ?array
{
    if ($cache === []) {
        $rows = $db->query('SELECT id, concepto FROM conceptos ORDER BY concepto')->fetchAll();
        foreach ($rows as $row) {
            $nombre = (string) $row['concepto'];
            $cache['by_id'][(int) $row['id']] = $row;
            $cache['by_norm'][normalizarTextoImport($nombre)] = $row;
        }
    }

    $texto = trim((string) $valor);
    if ($texto === '') {
        return null;
    }

    if (is_numeric($texto)) {
        $id = (int) $texto;
        if (isset($cache['by_id'][$id])) {
            $row = $cache['by_id'][$id];
            return ['id' => (int) $row['id'], 'concepto' => $row['concepto'], 'score' => 100];
        }
    }

    $needle = normalizarTextoImport($texto);
    if (isset($cache['by_norm'][$needle])) {
        $row = $cache['by_norm'][$needle];
        return ['id' => (int) $row['id'], 'concepto' => $row['concepto'], 'score' => 100];
    }

    $best = null;
    $bestScore = 0.0;
    foreach ($cache['by_norm'] ?? [] as $norm => $row) {
        similar_text($needle, $norm, $score);
        if ($score > $bestScore) {
            $bestScore = $score;
            $best = $row;
        }
    }

    if ($best && $bestScore >= 82) {
        return ['id' => (int) $best['id'], 'concepto' => $best['concepto'], 'score' => round($bestScore, 1)];
    }

    return null;
}

function importarDeudaIssDesdeExcel(PDO $db, string $path, ?string $originalName = null): array
{
    $rows = readSpreadsheetFirstSheet($path, $originalName);
    if (count($rows) < 2) {
        throw new RuntimeException('El Excel no contiene filas de datos.');
    }

    $headerIndex = detectDeudaIssHeaderRow($rows);
    $cols = mapDeudaIssColumns($rows[$headerIndex]);
    $rows = array_slice($rows, $headerIndex + 1);

    $numeroImportacion = obtenerProximoNumeroImportacion($db, 'deuda_iss');

    $stmtFind = $db->prepare('
        SELECT id FROM deuda_iss
        WHERE anio = ? AND mes = ? AND id_localidad = ? AND id_concepto = ?
        LIMIT 1
    ');
    $stmtInsert = $db->prepare('
        INSERT INTO deuda_iss (anio, mes, id_localidad, id_concepto, importe, numero_importacion)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmtUpdate = $db->prepare('
        UPDATE deuda_iss
        SET importe = ?, numero_importacion = ?
        WHERE id = ?
    ');

    $localidadesCache = [];
    $conceptosCache = [];
    $insertados = 0;
    $actualizados = 0;
    $omitidos = 0;
    $sinLocalidad = [];
    $sinConcepto = [];
    $matchesDudosos = [];

    foreach ($rows as $row) {
        $anio = parseEnteroImportacion($row[$cols['anio']] ?? null);
        $mes = parseEnteroImportacion($row[$cols['mes']] ?? null);
        $nombreLocalidad = trim((string) ($row[$cols['localidad']] ?? ''));
        $conceptoRaw = $row[$cols['concepto']] ?? null;
        $importe = round(parseImporteRetencion($row[$cols['importe']] ?? 0), 2);

        if (!$anio || !$mes || $mes < 1 || $mes > 12 || $nombreLocalidad === '' || $importe == 0.0) {
            $omitidos++;
            continue;
        }

        $normLocalidad = normalizarTextoImport($nombreLocalidad);
        if (in_array($normLocalidad, ['TOTAL', 'TOTALES', 'SUBTOTAL', 'SUB TOTAL'], true) || str_starts_with($normLocalidad, 'TOTAL ')) {
            $omitidos++;
            continue;
        }

        $matchLocalidad = buscarLocalidadIdSimilar($db, $nombreLocalidad, $localidadesCache, 85);
        if (!$matchLocalidad) {
            $sinLocalidad[$nombreLocalidad] = true;
            $omitidos++;
            continue;
        }
        if ($matchLocalidad['score'] < 95) {
            $matchesDudosos[] = "{$nombreLocalidad} → {$matchLocalidad['nombre']} ({$matchLocalidad['score']}%)";
        }

        $matchConcepto = buscarConceptoDeudaIss($db, $conceptoRaw, $conceptosCache);
        if (!$matchConcepto) {
            $sinConcepto[trim((string) $conceptoRaw)] = true;
            $omitidos++;
            continue;
        }
        if ($matchConcepto['score'] < 95) {
            $matchesDudosos[] = trim((string) $conceptoRaw) . " → {$matchConcepto['concepto']} ({$matchConcepto['score']}%)";
        }

        $stmtFind->execute([$anio, $mes, $matchLocalidad['id'], $matchConcepto['id']]);
        $existenteId = $stmtFind->fetchColumn();

        if ($existenteId) {
            $stmtUpdate->execute([$importe, $numeroImportacion, (int) $existenteId]);
            $actualizados++;
        } else {
            $stmtInsert->execute([$anio, $mes, $matchLocalidad['id'], $matchConcepto['id'], $importe, $numeroImportacion]);
            $insertados++;
        }
    }

    return [
        'numero_importacion' => $numeroImportacion,
        'insertados' => $insertados,
        'actualizados' => $actualizados,
        'omitidos' => $omitidos,
        'sin_localidad' => array_keys($sinLocalidad),
        'sin_concepto' => array_values(array_filter(array_keys($sinConcepto), static fn(string $v): bool => $v !== '')),
        'matches_dudosos' => $matchesDudosos,
    ];
}

function mapDistribucionSemanalColumns(array $headerRow): array
{
    $map = [];
    foreach ($headerRow as $i => $cell) {
        $k = normalizarTextoImport((string) $cell);
        $k = str_replace(['-', '_'], ' ', $k);
        $k = preg_replace('/\s+/', ' ', $k) ?? $k;

        if (str_contains($k, 'FECHA') && str_contains($k, 'DISTRIB')) {
            $map['fecha'] = $i;
        } elseif ($k === 'SUBTOTAL' || str_starts_with($k, 'SUBTOTAL')) {
            $map['subtotal'] = $i;
        }
    }

    if (!isset($map['fecha'], $map['subtotal'])) {
        throw new RuntimeException('No se encontraron las columnas Fecha Distrib. y SUBTOTAL.');
    }

    $map['localidad'] = $map['fecha'] - 1;
    if ($map['localidad'] < 0) {
        $map['localidad'] = 1;
    }

    return $map;
}

function detectDistribucionSemanalHeaderRow(array $rows): int
{
    $maxScan = min(count($rows), 40);
    for ($i = 0; $i < $maxScan; $i++) {
        try {
            mapDistribucionSemanalColumns($rows[$i]);
            return $i;
        } catch (RuntimeException) {
            continue;
        }
    }

    throw new RuntimeException(
        'No se encontró la fila de encabezados con Fecha Distrib. y SUBTOTAL.'
    );
}

function parseFechaDistribucion(mixed $valor): ?string
{
    if ($valor === null || $valor === '') {
        return null;
    }

    if (is_numeric($valor)) {
        $num = (float) $valor;
        if ($num > 40000 && $num < 70000) {
            $unix = (int) round(($num - 25569) * 86400);

            return date('Y-m-d', $unix);
        }
    }

    $texto = trim(str_replace('\\/', '/', (string) $valor));
    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/', $texto, $m)) {
        $dia = (int) $m[1];
        $mes = (int) $m[2];
        $anio = (int) $m[3];
        if ($anio < 100) {
            $anio += 2000;
        }
        if (checkdate($mes, $dia, $anio)) {
            return sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
        }
    }

    return null;
}

function esFilaDistribucionSemanalIgnorable(string $nombreLocalidad, ?string $fecha, float $importe): bool
{
    $norm = normalizarTextoImport($nombreLocalidad);
    if ($norm === '') {
        return true;
    }
    if (in_array($norm, ['TOTAL', 'TOTALES', 'SUBTOTAL', 'SUB TOTAL', 'MUNICIPIO'], true)) {
        return true;
    }
    if (str_starts_with($norm, 'TOTAL ')) {
        return true;
    }
    if ($fecha === null || $importe <= 0) {
        return true;
    }

    return false;
}

function importarDistribucionSemanalDesdeExcel(PDO $db, string $path, ?string $originalName = null): array
{
    $rows = readSpreadsheetFirstSheet($path, $originalName);
    if (count($rows) < 2) {
        throw new RuntimeException('El Excel no contiene filas de datos.');
    }

    $headerIndex = detectDistribucionSemanalHeaderRow($rows);
    $cols = mapDistribucionSemanalColumns($rows[$headerIndex]);
    $rows = array_slice($rows, $headerIndex + 1);

    $numeroImportacion = obtenerProximoNumeroImportacion($db, 'distribucion_semanal');

    $stmtFind = $db->prepare('
        SELECT id FROM distribucion_semanal
        WHERE id_loclidad = ? AND fecha = ?
        LIMIT 1
    ');
    $stmtInsert = $db->prepare('
        INSERT INTO distribucion_semanal (id_loclidad, fecha, importe, numero_importacion)
        VALUES (?, ?, ?, ?)
    ');
    $stmtUpdate = $db->prepare('
        UPDATE distribucion_semanal
        SET importe = ?, numero_importacion = ?
        WHERE id = ?
    ');

    $localidadesCache = [];
    $insertados = 0;
    $actualizados = 0;
    $omitidos = 0;
    $sinLocalidad = [];
    $matchesDudosos = [];

    foreach ($rows as $row) {
        $nombreLocalidad = trim((string) ($row[$cols['localidad']] ?? ''));
        $fecha = parseFechaDistribucion($row[$cols['fecha']] ?? null);
        $importe = parseImporteRetencion($row[$cols['subtotal']] ?? 0);

        if (esFilaDistribucionSemanalIgnorable($nombreLocalidad, $fecha, $importe)) {
            $omitidos++;
            continue;
        }

        $match = buscarLocalidadIdSimilar($db, $nombreLocalidad, $localidadesCache, 85);
        if (!$match) {
            $sinLocalidad[$nombreLocalidad] = true;
            $omitidos++;
            continue;
        }
        if ($match['score'] < 95) {
            $matchesDudosos[] = "{$nombreLocalidad} → {$match['nombre']} ({$match['score']}%)";
        }

        $stmtFind->execute([$match['id'], $fecha]);
        $existenteId = $stmtFind->fetchColumn();

        if ($existenteId) {
            $stmtUpdate->execute([$importe, $numeroImportacion, (int) $existenteId]);
            $actualizados++;
        } else {
            $stmtInsert->execute([$match['id'], $fecha, $importe, $numeroImportacion]);
            $insertados++;
        }
    }

    return [
        'numero_importacion' => $numeroImportacion,
        'insertados' => $insertados,
        'actualizados' => $actualizados,
        'omitidos' => $omitidos,
        'sin_localidad' => array_keys($sinLocalidad),
        'matches_dudosos' => $matchesDudosos,
    ];
}

function mapAgentesColumns(array $headerRow): array
{
    $map = [];
    foreach ($headerRow as $i => $cell) {
        $k = normalizarTextoImport((string) $cell);
        $k = str_replace(['-', '_', '.', '/', '\\'], ' ', $k);
        $k = preg_replace('/\s+/', ' ', $k) ?? $k;

        if (str_contains($k, 'AGENTE') && (str_contains($k, 'N') || str_contains($k, 'NUMERO') || str_contains($k, 'NRO'))) {
            $map['ageret'] = $i;
        } elseif ($k === 'N AGENTE' || $k === 'NO AGENTE') {
            $map['ageret'] = $i;
        } elseif (str_contains($k, 'PERIODO')) {
            $map['periodo'] = $i;
        } elseif (str_contains($k, 'CANT') && str_contains($k, 'EMPLEAD')) {
            $map['total_empleados'] = $i;
        } elseif (str_contains($k, 'ASIG') && str_contains($k, 'FAMILIAR')) {
            $map['total_asignacion_familiar'] = $i;
        } elseif ((str_contains($k, 'REMUNER') || str_contains($k, 'REMUNERACION'))
            && str_contains($k, 'C APORTE')) {
            $map['total_remuneracion'] = $i;
        } elseif ((str_contains($k, 'REMUNER') || str_contains($k, 'REMUNERACION'))
            && (str_contains($k, 'S APORTE') || str_contains($k, 'SIN APORTE'))) {
            $map['total_aportes'] = $i;
        }
    }

    return $map;
}

function detectAgentesHeaderRow(array $rows): int
{
    $maxScan = min(count($rows), 20);
    for ($i = 0; $i < $maxScan; $i++) {
        $cols = mapAgentesColumns($rows[$i]);
        if (isset($cols['ageret'], $cols['periodo'], $cols['total_empleados'])) {
            return $i;
        }
    }

    throw new RuntimeException(
        'No se encontró la fila de encabezados con N° AGENTE, PERIODO y CANT. EMPLEADOS.'
    );
}

function parsePeriodoAgentes(mixed $valor): ?array
{
    if ($valor === null || $valor === '') {
        return null;
    }

    if (is_numeric($valor)) {
        $num = (float) $valor;
        if ($num > 40000 && $num < 70000) {
            $unix = (int) round(($num - 25569) * 86400);
            $mes = (int) date('n', $unix);
            $anio = (int) date('Y', $unix);

            return ($mes >= 1 && $mes <= 12) ? ['mes' => $mes, 'anio' => $anio] : null;
        }
    }

    $texto = trim(str_replace('\\/', '/', (string) $valor));
    if (preg_match('/^(\d{1,2})[\/\-](\d{4})$/', $texto, $m)) {
        $mes = (int) $m[1];
        $anio = (int) $m[2];

        return ($mes >= 1 && $mes <= 12) ? ['mes' => $mes, 'anio' => $anio] : null;
    }
    if (preg_match('/^(\d{4})[\/\-](\d{1,2})$/', $texto, $m)) {
        $mes = (int) $m[2];
        $anio = (int) $m[1];

        return ($mes >= 1 && $mes <= 12) ? ['mes' => $mes, 'anio' => $anio] : null;
    }

    return null;
}

function buscarLocalidadIdPorAgeRet(PDO $db, mixed $ageret, array &$cache): ?array
{
    $codigo = parseEnteroImportacion($ageret);
    if ($codigo === null || $codigo <= 0) {
        return null;
    }

    if ($cache === []) {
        $rows = $db->query('
            SELECT id, localidad, AgeRet
            FROM localidades
            WHERE AgeRet IS NOT NULL AND AgeRet != ""
        ')->fetchAll();
        foreach ($rows as $row) {
            $key = (int) $row['AgeRet'];
            $cache[$key] = $row;
        }
    }

    if (!isset($cache[$codigo])) {
        return null;
    }

    return [
        'id' => (int) $cache[$codigo]['id'],
        'nombre' => $cache[$codigo]['localidad'],
        'ageret' => $codigo,
    ];
}

function importarAgentesDesdeExcel(PDO $db, string $path, ?string $originalName = null): array
{
    $rows = readSpreadsheetFirstSheet($path, $originalName);
    if (count($rows) < 2) {
        throw new RuntimeException('El Excel no contiene filas de datos.');
    }

    $headerIndex = detectAgentesHeaderRow($rows);
    $cols = mapAgentesColumns($rows[$headerIndex]);
    $rows = array_slice($rows, $headerIndex + 1);

    $numeroImportacion = obtenerProximoNumeroImportacion($db, 'agentes');

    $stmtFind = $db->prepare('
        SELECT id FROM agentes
        WHERE id_dependencia = ? AND mes = ? AND anio = ?
        LIMIT 1
    ');
    $stmtInsert = $db->prepare('
        INSERT INTO agentes (
            id_dependencia, mes, anio, total_empleados,
            total_remuneracion, total_aportes, total_asignacion_familiar,
            numero_importacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmtUpdate = $db->prepare('
        UPDATE agentes
        SET total_empleados = ?, total_remuneracion = ?, total_aportes = ?,
            total_asignacion_familiar = ?, numero_importacion = ?
        WHERE id = ?
    ');

    $localidadesCache = [];
    $insertados = 0;
    $actualizados = 0;
    $omitidos = 0;
    $sinLocalidad = [];
    $periodoImportado = null;

    foreach ($rows as $row) {
        $ageretRaw = $row[$cols['ageret']] ?? null;
        $periodo = parsePeriodoAgentes($row[$cols['periodo']] ?? null);
        $totalEmpleados = parseEnteroImportacion($row[$cols['total_empleados']] ?? null);
        $totalRemuneracion = isset($cols['total_remuneracion'])
            ? round(parseImporteRetencion($row[$cols['total_remuneracion']] ?? 0), 2)
            : 0.0;
        $totalAportes = isset($cols['total_aportes'])
            ? round(parseImporteRetencion($row[$cols['total_aportes']] ?? 0), 2)
            : 0.0;
        $totalAsignacionFamiliar = isset($cols['total_asignacion_familiar'])
            ? round(parseImporteRetencion($row[$cols['total_asignacion_familiar']] ?? 0), 2)
            : 0.0;

        if ($periodo === null || $totalEmpleados === null || $totalEmpleados < 0) {
            $omitidos++;
            continue;
        }

        $match = buscarLocalidadIdPorAgeRet($db, $ageretRaw, $localidadesCache);
        if (!$match) {
            $codigo = parseEnteroImportacion($ageretRaw);
            $sinLocalidad[(string) ($codigo ?? trim((string) $ageretRaw))] = true;
            $omitidos++;
            continue;
        }

        if ($periodoImportado === null) {
            $periodoImportado = $periodo;
        }

        $stmtFind->execute([$match['id'], $periodo['mes'], $periodo['anio']]);
        $existenteId = $stmtFind->fetchColumn();

        if ($existenteId) {
            $stmtUpdate->execute([
                $totalEmpleados,
                $totalRemuneracion,
                $totalAportes,
                $totalAsignacionFamiliar,
                $numeroImportacion,
                (int) $existenteId,
            ]);
            $actualizados++;
        } else {
            $stmtInsert->execute([
                $match['id'],
                $periodo['mes'],
                $periodo['anio'],
                $totalEmpleados,
                $totalRemuneracion,
                $totalAportes,
                $totalAsignacionFamiliar,
                $numeroImportacion,
            ]);
            $insertados++;
        }
    }

    return [
        'numero_importacion' => $numeroImportacion,
        'insertados' => $insertados,
        'actualizados' => $actualizados,
        'omitidos' => $omitidos,
        'sin_localidad' => array_keys($sinLocalidad),
        'periodo' => $periodoImportado,
    ];
}

function truncarTextoImport(string $texto, int $max): string
{
    $texto = trim($texto);
    if ($texto === '') {
        return '';
    }

    return mb_substr($texto, 0, $max, 'UTF-8');
}

function mapAportesColumns(array $headerRow): array
{
    $map = [];
    foreach ($headerRow as $i => $cell) {
        $k = normalizarTextoImport((string) $cell);
        $k = str_replace(['-', '_'], ' ', $k);
        $k = preg_replace('/\s+/', ' ', $k) ?? $k;

        if (str_contains($k, 'LOCALIDAD')) {
            $map['localidad'] = $i;
        } elseif ($k === 'FECPAG' || (str_contains($k, 'FEC') && str_contains($k, 'PAG'))) {
            $map['fecpag'] = $i;
        } elseif ($k === 'EXPEDN' || str_contains($k, 'EXPED N')) {
            $map['expedn'] = $i;
        } elseif ($k === 'EXPEDA' || str_contains($k, 'EXPED A')) {
            $map['expeda'] = $i;
        } elseif (str_contains($k, 'NOMBEJUR') || str_contains($k, 'NOMBRE JUR')) {
            $map['nombejur'] = $i;
        } elseif ($k === 'NROCHE' || str_contains($k, 'NRO CHE')) {
            $map['nroche'] = $i;
        } elseif ($k === 'IMPORTE') {
            $map['importe'] = $i;
        } elseif (str_contains($k, 'DESCCPTO') && !str_contains($k, 'SUB')) {
            $map['desccpto'] = $i;
        } elseif (str_contains($k, 'DESCSUBCPTO') || str_contains($k, 'DESC SUBCPTO')) {
            $map['descsubcpto'] = $i;
        }
    }

    return $map;
}

function detectAportesHeaderRow(array $rows): int
{
    $maxScan = min(count($rows), 20);
    $bestIndex = -1;
    $bestScore = -1;

    for ($i = 0; $i < $maxScan; $i++) {
        $cols = mapAportesColumns($rows[$i]);
        $required = ['localidad', 'fecpag', 'expedn', 'nroche', 'importe'];
        $score = count(array_intersect($required, array_keys($cols)));
        if (isset($cols['expeda'], $cols['nombejur'])) {
            $score += 0.5;
        }
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestIndex = $i;
        }
    }

    if ($bestIndex < 0 || $bestScore < 5) {
        throw new RuntimeException(
            'No se encontró la fila de encabezados. Se esperan columnas Localidad, FecPag, ExpedN, ExpedA, NombeJur, NroChe, Importe, DescCpto y DescSubCpto.'
        );
    }

    return $bestIndex;
}

function obtenerAgeRetLocalidad(PDO $db, int $idLocalidad, array &$cache): int
{
    if (!isset($cache[$idLocalidad])) {
        $stmt = $db->prepare('SELECT COALESCE(AgeRet, 0) FROM localidades WHERE id = ? LIMIT 1');
        $stmt->execute([$idLocalidad]);
        $cache[$idLocalidad] = (int) $stmt->fetchColumn();
    }

    return $cache[$idLocalidad];
}

function importarAportesDesdeExcel(PDO $db, string $path, ?string $originalName = null): array
{
    $rows = readSpreadsheetFirstSheet($path, $originalName);
    if (count($rows) < 2) {
        throw new RuntimeException('El Excel no contiene filas de datos.');
    }

    $headerIndex = detectAportesHeaderRow($rows);
    $cols = mapAportesColumns($rows[$headerIndex]);
    $required = ['localidad', 'fecpag', 'expedn', 'nroche', 'importe'];
    $missing = array_diff($required, array_keys($cols));
    if ($missing !== []) {
        throw new RuntimeException(
            'Faltan columnas en la fila de encabezados (fila ' . ($headerIndex + 1) . '): ' . implode(', ', $missing)
        );
    }

    $rows = array_slice($rows, $headerIndex + 1);
    $numeroImportacion = obtenerProximoNumeroImportacion($db, 'aportes');

    $stmtFind = $db->prepare('
        SELECT id FROM aportes
        WHERE FecPag = ? AND ExpedN = ? AND NroChe = ? AND ROUND(importe, 2) = ?
        LIMIT 1
    ');
    $stmtInsert = $db->prepare('
        INSERT INTO aportes (
            id_localidad, organismo, FecPag, ExpedN, ExpedA, NombeJur, NroChe,
            importe, DescCpto, DescSubCpto, numero_importacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $localidadesCache = [];
    $ageretCache = [];
    $insertados = 0;
    $duplicados = 0;
    $omitidos = 0;
    $sinLocalidad = [];
    $matchesDudosos = [];

    foreach ($rows as $row) {
        $nombreLocalidad = trim((string) ($row[$cols['localidad']] ?? ''));
        $fecPag = parseFechaDistribucion($row[$cols['fecpag']] ?? null);
        $expedN = parseEnteroImportacion($row[$cols['expedn']] ?? null);
        $expedA = isset($cols['expeda']) ? parseEnteroImportacion($row[$cols['expeda']] ?? null) : null;
        $nombeJur = isset($cols['nombejur'])
            ? truncarTextoImport((string) ($row[$cols['nombejur']] ?? ''), 255)
            : null;
        $nroChe = parseEnteroImportacion($row[$cols['nroche']] ?? null);
        $importe = round(parseImporteRetencion($row[$cols['importe']] ?? 0), 2);
        $descCpto = isset($cols['desccpto'])
            ? truncarTextoImport((string) ($row[$cols['desccpto']] ?? ''), 50)
            : '';
        $descSubCpto = isset($cols['descsubcpto'])
            ? truncarTextoImport((string) ($row[$cols['descsubcpto']] ?? ''), 50)
            : '';

        if ($nombreLocalidad === '' || $fecPag === null || $expedN === null || $nroChe === null || $importe <= 0) {
            $omitidos++;
            continue;
        }

        $norm = normalizarTextoImport($nombreLocalidad);
        if (in_array($norm, ['TOTAL', 'TOTALES', 'SUBTOTAL', 'SUB TOTAL'], true) || str_starts_with($norm, 'TOTAL ')) {
            $omitidos++;
            continue;
        }

        $match = buscarLocalidadIdSimilar($db, $nombreLocalidad, $localidadesCache, 85);
        if (!$match) {
            $sinLocalidad[$nombreLocalidad] = true;
            $omitidos++;
            continue;
        }
        if ($match['score'] < 95) {
            $matchesDudosos[] = "{$nombreLocalidad} → {$match['nombre']} ({$match['score']}%)";
        }

        $stmtFind->execute([$fecPag, $expedN, $nroChe, $importe]);
        if ($stmtFind->fetchColumn()) {
            $duplicados++;
            continue;
        }

        $organismo = obtenerAgeRetLocalidad($db, $match['id'], $ageretCache);
        try {
            $stmtInsert->execute([
                $match['id'],
                $organismo,
                $fecPag,
                $expedN,
                $expedA,
                $nombeJur !== '' ? $nombeJur : null,
                $nroChe,
                $importe,
                $descCpto,
                $descSubCpto,
                $numeroImportacion,
            ]);
            $insertados++;
        } catch (PDOException $e) {
            if ((string) $e->getCode() === '23000') {
                $duplicados++;
                continue;
            }
            throw $e;
        }
    }

    return [
        'numero_importacion' => $numeroImportacion,
        'insertados' => $insertados,
        'duplicados' => $duplicados,
        'omitidos' => $omitidos,
        'sin_localidad' => array_keys($sinLocalidad),
        'matches_dudosos' => $matchesDudosos,
    ];
}

// ─── IMPORTAR PLAN ESTRATÉGICO (desde el Excel procesado) ───────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_plan'])) {
    try {
        $forzar = !empty($_POST['forzar']) && ($_SESSION['rol'] ?? '') === 'admin';
        // Verificar si ya hay datos
        $count = $db->query("SELECT COUNT(*) FROM estrategias")->fetchColumn();
        if ($count > 0 && !$forzar) {
            $resultado = ['tipo'=>'warning','msg'=>"Ya existen $count estrategias. Marcá 'Forzar reimportación' para sobrescribir."];
        } else {
            if ($forzar) {
                // Limpiar en orden
                $db->exec("SET FOREIGN_KEY_CHECKS = 0");
                $db->exec("TRUNCATE TABLE novedades");
                $db->exec("TRUNCATE TABLE hitos");
                $db->exec("TRUNCATE TABLE proyectos");
                $db->exec("TRUNCATE TABLE acciones");
                $db->exec("TRUNCATE TABLE componentes");
                $db->exec("TRUNCATE TABLE programas");
                $db->exec("TRUNCATE TABLE estrategias");
                $db->exec("SET FOREIGN_KEY_CHECKS = 1");
            }

            // Datos del plan extraídos del Excel anterior
            $plan_data = getPlanData();

            $stmt_est  = $db->prepare("INSERT IGNORE INTO estrategias (codigo, descripcion) VALUES (?,?)");
            $stmt_prog = $db->prepare("INSERT IGNORE INTO programas (estrategia_id, codigo, descripcion) VALUES (?,?,?)");
            $stmt_comp = $db->prepare("INSERT IGNORE INTO componentes (programa_id, codigo, descripcion) VALUES (?,?,?)");
            $stmt_acc  = $db->prepare("INSERT IGNORE INTO acciones (componente_id, codigo, descripcion) VALUES (?,?,?)");

            $c_est = $c_prog = $c_comp = $c_acc = 0;
            $est_map = $prog_map = $comp_map = [];

            foreach ($plan_data as $row) {
                [$est_cod, $est_desc, $prog_cod, $prog_desc, $comp_cod, $comp_desc, $acc_cod, $acc_desc] = $row;

                // Estrategia
                if (!isset($est_map[$est_cod])) {
                    $stmt_est->execute([$est_cod, $est_desc]);
                    $est_map[$est_cod] = $db->lastInsertId() ?: $db->query("SELECT id FROM estrategias WHERE codigo='$est_cod'")->fetchColumn();
                    if ($db->lastInsertId()) $c_est++;
                }

                // Programa
                $prog_key = $prog_cod;
                if (!isset($prog_map[$prog_key])) {
                    $stmt_prog->execute([$est_map[$est_cod], $prog_cod, $prog_desc]);
                    $prog_map[$prog_key] = $db->lastInsertId() ?: $db->query("SELECT id FROM programas WHERE codigo='$prog_cod'")->fetchColumn();
                    if ($db->lastInsertId()) $c_prog++;
                }

                // Componente
                $comp_key = $comp_cod;
                if (!isset($comp_map[$comp_key])) {
                    $stmt_comp->execute([$prog_map[$prog_key], $comp_cod, $comp_desc]);
                    $comp_map[$comp_key] = $db->lastInsertId() ?: $db->query("SELECT id FROM componentes WHERE codigo='$comp_cod'")->fetchColumn();
                    if ($db->lastInsertId()) $c_comp++;
                }

                // Acción
                $stmt_acc->execute([$comp_map[$comp_key], $acc_cod, $acc_desc]);
                if ($db->lastInsertId()) $c_acc++;
            }

            $resultado = ['tipo'=>'success','msg'=>"¡Importación completada! Estrategias: $c_est · Programas: $c_prog · Componentes: $c_comp · Acciones: $c_acc"];
        }
    } catch (Exception $e) {
        $resultado = ['tipo'=>'error','msg'=>'Error en importación: '.$e->getMessage()];
    }
}

// ─── IMPORTAR FUNCIONARIOS ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_funcionarios'])) {
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0) {
        $tmp = $_FILES['archivo']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));

        if ($ext !== 'csv') {
            $resultado = ['tipo'=>'error','msg'=>'Solo se acepta CSV. Exportá el Excel como CSV primero.'];
        } else {
            $handle = fopen($tmp, 'r');
            $header = fgetcsv($handle); // skip header

            $stmt = $db->prepare("
                INSERT INTO funcionarios (nombre_apellido, documento, cargo, organismo_id, situacion, activo)
                VALUES (?, ?, ?, (SELECT id FROM organismos WHERE sigla = ? LIMIT 1), ?, ?)
                ON DUPLICATE KEY UPDATE cargo=VALUES(cargo), situacion=VALUES(situacion)
            ");

            $c = 0; $err = 0;
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 4) continue;
                $nombre = trim($row[0]);
                $doc    = preg_replace('/[^0-9]/', '', $row[1] ?? '');
                $cargo  = trim($row[2] ?? '');
                $org    = trim($row[3] ?? '');
                $sit    = trim($row[4] ?? 'ANUAL');
                $activo = $sit === 'ANUAL' ? 1 : 0;

                if (!$nombre || !$org) continue;
                try {
                    $stmt->execute([$nombre, $doc, $cargo, $org, in_array($sit,['ANUAL','CESE'])?$sit:'OTRO', $activo]);
                    $c++;
                } catch (Exception $e) {
                    $err++;
                }
            }
            fclose($handle);
            $resultado = ['tipo'=>'success','msg'=>"Importación CSV: $c funcionarios procesados. Errores: $err"];
        }
    }
}

// ─── ELIMINAR IMPORTACIÓN DE RETENCIONES ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_importacion_retenciones'])) {
    try {
        $numero = (int) ($_POST['numero_importacion'] ?? 0);
        if ($numero <= 0) {
            throw new RuntimeException('Número de importación inválido.');
        }

        $stmt = $db->prepare('DELETE FROM retenciones WHERE numero_importacion = ?');
        $stmt->execute([$numero]);
        $eliminados = $stmt->rowCount();

        if ($eliminados === 0) {
            throw new RuntimeException("No hay registros con la importación #{$numero}.");
        }

        $resultado = ['tipo' => 'success', 'msg' => "Importación #{$numero} eliminada: {$eliminados} registro(s)."];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al eliminar importación: ' . $e->getMessage()];
    }
}

// ─── IMPORTAR RETENCIONES (Excel) ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_retenciones'])) {
    try {
        if (!isset($_FILES['archivo_retenciones']) || $_FILES['archivo_retenciones']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se recibió el archivo Excel o hubo un error al subirlo.');
        }

        $ext = strtolower(pathinfo($_FILES['archivo_retenciones']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'xlsx') {
            throw new RuntimeException('Solo se acepta formato .xlsx');
        }

        $anio = (int) date('Y');
        $stats = importarRetencionesDesdeExcel($db, $_FILES['archivo_retenciones']['tmp_name'], $anio);

        $msg = "Importación #{$stats['numero_importacion']} completada (año {$anio}): {$stats['insertados']} nuevo(s), {$stats['actualizados']} actualizado(s).";
        if ($stats['omitidos'] > 0) {
            $msg .= " Omitidas: {$stats['omitidos']}.";
        }
        if (!empty($stats['sin_localidad'])) {
            $muestra = array_slice($stats['sin_localidad'], 0, 8);
            $msg .= ' Sin localidad coincidente: ' . implode(', ', $muestra);
            if (count($stats['sin_localidad']) > 8) {
                $msg .= '...';
            }
        }
        if (!empty($stats['matches_dudosos'])) {
            $muestra = array_slice($stats['matches_dudosos'], 0, 5);
            $msg .= ' Coincidencias aproximadas: ' . implode(' | ', $muestra);
        }

        $resultado = ['tipo' => 'success', 'msg' => $msg];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al importar retenciones: ' . $e->getMessage()];
    }
}

// ─── ELIMINAR IMPORTACIÓN DEUDA ISS ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_importacion_deuda_iss'])) {
    try {
        $numero = (int) ($_POST['numero_importacion_deuda_iss'] ?? 0);
        if ($numero <= 0) {
            throw new RuntimeException('Número de importación inválido.');
        }

        $stmt = $db->prepare('DELETE FROM deuda_iss WHERE numero_importacion = ?');
        $stmt->execute([$numero]);
        $eliminados = $stmt->rowCount();

        if ($eliminados === 0) {
            throw new RuntimeException("No hay registros con la importación #{$numero}.");
        }

        $resultado = ['tipo' => 'success', 'msg' => "Importación Deuda ISS #{$numero} eliminada: {$eliminados} registro(s)."];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al eliminar importación Deuda ISS: ' . $e->getMessage()];
    }
}

// ─── IMPORTAR DEUDA ISS X MUNICIPIOS (Excel) ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_deuda_iss'])) {
    try {
        if (!isset($_FILES['archivo_deuda_iss']) || $_FILES['archivo_deuda_iss']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se recibió el archivo Excel o hubo un error al subirlo.');
        }

        $ext = strtolower(pathinfo($_FILES['archivo_deuda_iss']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'], true)) {
            throw new RuntimeException('Solo se acepta formato .xls o .xlsx');
        }

        $stats = importarDeudaIssDesdeExcel(
            $db,
            $_FILES['archivo_deuda_iss']['tmp_name'],
            $_FILES['archivo_deuda_iss']['name'] ?? null
        );

        $msg = "Importación Deuda ISS #{$stats['numero_importacion']} completada: {$stats['insertados']} nuevo(s), {$stats['actualizados']} actualizado(s).";
        if ($stats['omitidos'] > 0) {
            $msg .= " Omitidas: {$stats['omitidos']}.";
        }
        if (!empty($stats['sin_localidad'])) {
            $lista = implode(', ', array_slice($stats['sin_localidad'], 0, 8));
            $msg .= ' Sin localidad: ' . $lista;
            if (count($stats['sin_localidad']) > 8) {
                $msg .= '… (+' . (count($stats['sin_localidad']) - 8) . ' más)';
            }
        }
        if (!empty($stats['sin_concepto'])) {
            $lista = implode(', ', array_slice($stats['sin_concepto'], 0, 8));
            $msg .= ' Sin concepto: ' . $lista;
            if (count($stats['sin_concepto']) > 8) {
                $msg .= '… (+' . (count($stats['sin_concepto']) - 8) . ' más)';
            }
        }
        if (!empty($stats['matches_dudosos'])) {
            $msg .= ' Coincidencias dudosas: ' . implode(' | ', array_slice($stats['matches_dudosos'], 0, 3));
        }

        $resultado = ['tipo' => 'success', 'msg' => $msg];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al importar Deuda ISS: ' . $e->getMessage()];
    }
}

// ─── ELIMINAR IMPORTACIÓN DISTRIBUCIÓN SEMANAL ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_importacion_distribucion'])) {
    try {
        $numero = (int) ($_POST['numero_importacion_distribucion'] ?? 0);
        if ($numero <= 0) {
            throw new RuntimeException('Número de importación inválido.');
        }

        $stmt = $db->prepare('DELETE FROM distribucion_semanal WHERE numero_importacion = ?');
        $stmt->execute([$numero]);
        $eliminados = $stmt->rowCount();

        if ($eliminados === 0) {
            throw new RuntimeException("No hay registros con la importación #{$numero}.");
        }

        $resultado = ['tipo' => 'success', 'msg' => "Importación distribución semanal #{$numero} eliminada: {$eliminados} registro(s)."];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al eliminar importación: ' . $e->getMessage()];
    }
}

// ─── IMPORTAR DISTRIBUCIÓN SEMANAL (Excel) ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_distribucion_semanal'])) {
    try {
        if (!isset($_FILES['archivo_distribucion']) || $_FILES['archivo_distribucion']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se recibió el archivo Excel o hubo un error al subirlo.');
        }

        $ext = strtolower(pathinfo($_FILES['archivo_distribucion']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'], true)) {
            throw new RuntimeException('Solo se acepta formato .xls o .xlsx');
        }

        $stats = importarDistribucionSemanalDesdeExcel(
            $db,
            $_FILES['archivo_distribucion']['tmp_name'],
            $_FILES['archivo_distribucion']['name'] ?? null
        );

        $msg = "Importación #{$stats['numero_importacion']} completada: {$stats['insertados']} nuevo(s), {$stats['actualizados']} actualizado(s).";
        if ($stats['omitidos'] > 0) {
            $msg .= " Omitidas: {$stats['omitidos']}.";
        }
        if (!empty($stats['sin_localidad'])) {
            $lista = implode(', ', array_slice($stats['sin_localidad'], 0, 8));
            $msg .= ' Sin localidad: ' . $lista;
            if (count($stats['sin_localidad']) > 8) {
                $msg .= '… (+' . (count($stats['sin_localidad']) - 8) . ' más)';
            }
        }
        if (!empty($stats['matches_dudosos'])) {
            $msg .= ' Coincidencias dudosas: ' . implode(' | ', array_slice($stats['matches_dudosos'], 0, 3));
        }

        $resultado = ['tipo' => 'success', 'msg' => $msg];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al importar distribución semanal: ' . $e->getMessage()];
    }
}

// ─── ELIMINAR IMPORTACIÓN AGENTES ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_importacion_agentes'])) {
    try {
        $numero = (int) ($_POST['numero_importacion_agentes'] ?? 0);
        if ($numero <= 0) {
            throw new RuntimeException('Número de importación inválido.');
        }

        $stmt = $db->prepare('DELETE FROM agentes WHERE numero_importacion = ?');
        $stmt->execute([$numero]);
        $eliminados = $stmt->rowCount();

        if ($eliminados === 0) {
            throw new RuntimeException("No hay registros con la importación #{$numero}.");
        }

        $resultado = ['tipo' => 'success', 'msg' => "Importación agentes #{$numero} eliminada: {$eliminados} registro(s)."];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al eliminar importación: ' . $e->getMessage()];
    }
}

// ─── IMPORTAR AGENTES / EMPLEADOS (Excel) ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_agentes'])) {
    try {
        if (!isset($_FILES['archivo_agentes']) || $_FILES['archivo_agentes']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se recibió el archivo Excel o hubo un error al subirlo.');
        }

        $ext = strtolower(pathinfo($_FILES['archivo_agentes']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'], true)) {
            throw new RuntimeException('Solo se acepta formato .xls o .xlsx');
        }

        $stats = importarAgentesDesdeExcel(
            $db,
            $_FILES['archivo_agentes']['tmp_name'],
            $_FILES['archivo_agentes']['name'] ?? null
        );

        $periodoTxt = '';
        if (!empty($stats['periodo'])) {
            $periodoTxt = ' Período: ' . $stats['periodo']['mes'] . '/' . $stats['periodo']['anio'] . '.';
        }

        $msg = "Importación #{$stats['numero_importacion']} completada: {$stats['insertados']} nuevo(s), {$stats['actualizados']} actualizado(s).{$periodoTxt}";
        if ($stats['omitidos'] > 0) {
            $msg .= " Omitidas: {$stats['omitidos']}.";
        }
        if (!empty($stats['sin_localidad'])) {
            $lista = implode(', ', array_slice($stats['sin_localidad'], 0, 10));
            $msg .= ' Sin localidad (N° AGENTE): ' . $lista;
            if (count($stats['sin_localidad']) > 10) {
                $msg .= '… (+' . (count($stats['sin_localidad']) - 10) . ' más)';
            }
        }

        $resultado = ['tipo' => 'success', 'msg' => $msg];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al importar empleados: ' . $e->getMessage()];
    }
}

// ─── ELIMINAR IMPORTACIÓN APORTES ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_importacion_aportes'])) {
    try {
        $numero = (int) ($_POST['numero_importacion_aportes'] ?? 0);
        if ($numero <= 0) {
            throw new RuntimeException('Número de importación inválido.');
        }

        $stmt = $db->prepare('DELETE FROM aportes WHERE numero_importacion = ?');
        $stmt->execute([$numero]);
        $eliminados = $stmt->rowCount();

        if ($eliminados === 0) {
            throw new RuntimeException("No hay registros con la importación #{$numero}.");
        }

        $resultado = ['tipo' => 'success', 'msg' => "Importación aportes #{$numero} eliminada: {$eliminados} registro(s)."];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al eliminar importación: ' . $e->getMessage()];
    }
}

// ─── IMPORTAR DETALLE DE PAGOS / APORTES (Excel) ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importar_aportes'])) {
    try {
        if (!isset($_FILES['archivo_aportes']) || $_FILES['archivo_aportes']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se recibió el archivo Excel o hubo un error al subirlo.');
        }

        $ext = strtolower(pathinfo($_FILES['archivo_aportes']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'], true)) {
            throw new RuntimeException('Solo se acepta formato .xls o .xlsx');
        }

        $stats = importarAportesDesdeExcel(
            $db,
            $_FILES['archivo_aportes']['tmp_name'],
            $_FILES['archivo_aportes']['name'] ?? null
        );

        $msg = "Importación #{$stats['numero_importacion']} completada: {$stats['insertados']} nuevo(s).";
        if ($stats['duplicados'] > 0) {
            $msg .= " Duplicados omitidos: {$stats['duplicados']}.";
        }
        if ($stats['omitidos'] > 0) {
            $msg .= " Filas omitidas: {$stats['omitidos']}.";
        }
        if (!empty($stats['sin_localidad'])) {
            $lista = implode(', ', array_slice($stats['sin_localidad'], 0, 10));
            $msg .= ' Sin localidad: ' . $lista;
            if (count($stats['sin_localidad']) > 10) {
                $msg .= '… (+' . (count($stats['sin_localidad']) - 10) . ' más)';
            }
        }

        $resultado = ['tipo' => 'success', 'msg' => $msg];
    } catch (Throwable $e) {
        $resultado = ['tipo' => 'error', 'msg' => 'Error al importar detalle de pagos: ' . $e->getMessage()];
    }
}

include 'includes/header.php';

// ─── DATOS DEL PLAN (inline para no depender de archivos externos) ──────────
function getPlanData(): array {
    // Retorna una muestra representativa — el admin puede ampliarla
    // o importar el SQL completo directamente en MySQL
    return [
        ['1.01','Gestión sustentable del bosque nativo, la fauna, las reservas naturales, el ambiente, las especies nativas y la biodiversidad',
         '1.01.01','Conservación y manejo sostenible de la fauna silvestre y preservación de la biodiversidad.',
         '1.01.01.01','Aplicación de la Ley Nacional 22421 y Ley Provincial de Fauna N° 1194.',
         '1.01.01.01.01','Fortalecer los operativos de control y fiscalización del cumplimiento de la normativa de fauna silvestre en el territorio provincial.'],
        ['1.01','Gestión sustentable del bosque nativo, la fauna, las reservas naturales, el ambiente, las especies nativas y la biodiversidad',
         '1.01.01','Conservación y manejo sostenible de la fauna silvestre y preservación de la biodiversidad.',
         '1.01.01.01','Aplicación de la Ley Nacional 22421 y Ley Provincial de Fauna N° 1194.',
         '1.01.01.01.02','Incentivar el cumplimiento voluntario de la normativa de fauna mediante acciones de información y sensibilización dirigidas a actores vinculados.'],
        ['1.01','Gestión sustentable del bosque nativo, la fauna, las reservas naturales, el ambiente, las especies nativas y la biodiversidad',
         '1.01.01','Conservación y manejo sostenible de la fauna silvestre y preservación de la biodiversidad.',
         '1.01.01.02','Capacitaciones para actores cinegéticos',
         '1.01.01.02.01','Diseñar e implementar capacitaciones en aspectos normativos, éticos, de seguridad y conservación.'],
        ['1.02','Desarrollar y promocionar la integración comercial y productiva en la provincia y la región.',
         '1.02.01','Promoción comercial a nivel nacional',
         '1.02.01.01','Centro de Negocios en Neuquén',
         '1.02.01.01.01','Asistencia técnica y apoyo logístico a empresas que realicen acciones comerciales en Neuquén.'],
        ['1.03','Desarrollar y difundir las oportunidades de inversión en La Pampa',
         '1.03.01','Promoción de Inversiones',
         '1.03.01.01','Atención al Inversor: Informes sectoriales y de coyuntura',
         '1.03.01.01.01','Elaborar informes sectoriales, territoriales y de coyuntura económica para potenciales inversores.'],
        ['2.01','Promover sistemas de producción agropecuarios de alta tecnología y sustentables',
         '2.01.01','Inocuidad alimentaria',
         '2.01.01.01','Habilitación y control de establecimientos elaboradores de productos de origen animal.',
         '2.01.01.01.01','Asesorar y acompañar técnicamente a productores para habilitar o readecuar sus plantas.'],
        ['3.01','Impulsar la transición energética y priorizar el aprovechamiento de fuentes renovables',
         '3.01.01','Abastecimiento y desarrollo energético',
         '3.01.01.01','Desarrollo de infraestructura de transporte y distribución de energía.',
         '3.01.01.01.01','Fortalecer el Programa de redes eficientes (art. 49).'],
        ['4.01','Desarrollar instrumentos financieros y garantías acorde a los sectores',
         '4.01.01','Financiamiento estratégico orientado a sectores productivos',
         '4.01.01.01','Garantías Públicas para PyMES pampeanas',
         '4.01.01.01.01','Articular con bancos, SGR y entidades financieras para ampliar la utilización de las garantías del FoGaPam.'],
    ];
    // NOTA: Para importar las 368 filas completas, usar el archivo SQL adjunto:
    // mysql -u root gestion_gubernamental < plan_estrategico_data.sql
}
?>

<div class="space-y-6">
<div>
    <h1 class="text-2xl font-bold text-gray-900">Importar datos</h1>
    <p class="text-gray-500 text-sm">Carga del plan estratégico, funcionarios, retenciones, deuda ISS, distribución, empleados y detalle de pagos</p>
</div>

<?php if ($resultado): ?>
<div class="p-4 rounded-lg <?= $resultado['tipo']==='success'?'bg-green-50 border border-green-200 text-green-800':($resultado['tipo']==='warning'?'bg-yellow-50 border border-yellow-200 text-yellow-800':'bg-red-50 border border-red-200 text-red-800') ?>">
    <i class="fas <?= $resultado['tipo']==='success'?'fa-check-circle':($resultado['tipo']==='warning'?'fa-exclamation-triangle':'fa-times-circle') ?> mr-2"></i>
    <?= h($resultado['msg']) ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Importar plan estratégico -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chess text-blue-600"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800">Plan Estratégico</h2>
                <p class="text-xs text-gray-500">Estrategias, programas, componentes y acciones</p>
            </div>
        </div>

        <?php
        $counts = $db->query("
            SELECT
                (SELECT COUNT(*) FROM estrategias) as est,
                (SELECT COUNT(*) FROM programas) as prog,
                (SELECT COUNT(*) FROM componentes) as comp,
                (SELECT COUNT(*) FROM acciones) as acc
        ")->fetch();
        ?>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
            <?php foreach(['Estrat.'=>$counts['est'],'Program.'=>$counts['prog'],'Comp.'=>$counts['comp'],'Acciones'=>$counts['acc']] as $k=>$v): ?>
            <div class="text-center bg-gray-50 rounded-lg p-2">
                <p class="text-lg font-bold text-gray-800"><?= $v ?></p>
                <p class="text-xs text-gray-400"><?= $k ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="importar_plan" value="1">
            <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
            <div class="flex items-center gap-2 mb-3">
                <input type="checkbox" name="forzar" id="forzar" value="1" class="rounded">
                <label for="forzar" class="text-sm text-gray-600">Forzar reimportación (borra datos existentes)</label>
            </div>
            <?php endif; ?>
            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-import mr-2"></i> Importar plan estratégico
            </button>
        </form>

        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
            <i class="fas fa-info-circle mr-1"></i>
            Para importar las <strong>368 acciones completas</strong>, ejecutá directamente en MySQL:<br>
            <code class="bg-amber-100 px-1 rounded block mt-1">mysql -u root gestion_gubernamental &lt; plan_estrategico_data.sql</code>
        </div>
    </div>

    <!-- Importar funcionarios CSV -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-green-600"></i>
            </div>
            <div>
                <h2 class="font-bold text-gray-800">Funcionarios</h2>
                <p class="text-xs text-gray-500">Importar desde CSV (exportado del Excel)</p>
            </div>
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Actualmente: <strong><?= $db->query("SELECT COUNT(*) FROM funcionarios")->fetchColumn() ?></strong> funcionarios en la base.
        </p>

        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
            <p class="font-semibold mb-1">Formato esperado del CSV:</p>
            <code>Apellido y Nombre, DNI, Cargo, Organismo, Situacion</code><br>
            <span class="text-gray-400">Situacion: ANUAL o CESE</span>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="importar_funcionarios" value="1">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo CSV</label>
                <input type="file" name="archivo" accept=".csv" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700">
            </div>
            <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i> Importar funcionarios
            </button>
        </form>

        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
            <i class="fas fa-lightbulb mr-1"></i>
            También podés usar el SQL pre-generado:<br>
            <code class="bg-blue-100 px-1 rounded block mt-1">mysql -u root gestion_gubernamental &lt; funcionarios_data.sql</code>
        </div>
    </div>

    <!-- Importar retenciones Excel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-excel text-purple-600"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">Retenciones</h2>
                    <p class="text-xs text-gray-500">Importar desde Excel (.xlsx)</p>
                </div>
            </div>
            <button type="button"
                    onclick="abrirAyudaImportacion('Ayuda: importar retenciones', 'Referencia para exportar el Excel de Coparticipación - Sección 1', 'InformesXLocalidad/ayuda/Coparticipacion_Seccion_1.png', 'Ayuda para importar retenciones desde Coparticipación Sección 1')"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100 text-xs font-semibold transition-colors"
                    title="Ver ayuda de importación de retenciones">
                <i class="fas fa-question-circle"></i>
                Ayuda
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Actualmente: <strong><?= $db->query("SELECT COUNT(*) FROM retenciones")->fetchColumn() ?></strong> registros en la base.
            <?php
            $ultimaImportacion = (int) $db->query('SELECT COALESCE(MAX(numero_importacion), 0) FROM retenciones')->fetchColumn();
            if ($ultimaImportacion > 0):
            ?>
            · Última importación: <strong>#<?= $ultimaImportacion ?></strong>
            <?php endif; ?>
        </p>

        <?php
        $importacionesRetenciones = $db->query("
            SELECT numero_importacion, COUNT(*) AS registros, MIN(anio) AS anio_desde, MAX(anio) AS anio_hasta
            FROM retenciones
            WHERE numero_importacion IS NOT NULL
            GROUP BY numero_importacion
            ORDER BY numero_importacion DESC
        ")->fetchAll();
        ?>

        <?php if (!empty($importacionesRetenciones)): ?>
        <div class="mb-4 border border-gray-100 rounded-lg overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Importaciones cargadas
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($importacionesRetenciones as $imp): ?>
                <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <span class="font-medium text-gray-800">Importación #<?= (int) $imp['numero_importacion'] ?></span>
                        <span class="text-gray-500 text-xs ml-2"><?= (int) $imp['registros'] ?> registro(s)</span>
                        <?php if ($imp['anio_desde']): ?>
                        <span class="text-gray-400 text-xs ml-1">· año <?= h($imp['anio_desde']) ?><?= $imp['anio_hasta'] != $imp['anio_desde'] ? '–' . h($imp['anio_hasta']) : '' ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('¿Eliminar toda la importación #<?= (int) $imp['numero_importacion'] ?>?')">
                        <input type="hidden" name="eliminar_importacion_retenciones" value="1">
                        <input type="hidden" name="numero_importacion" value="<?= (int) $imp['numero_importacion'] ?>">
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 space-y-1">
            <p class="font-semibold">Columnas esperadas:</p>
            <code class="block leading-relaxed">Localidad, Fecha - Mes, Suma de Importe Liq, Suma de ISS, Suma de AntCop, Suma de PmosBLP, Suma de OPyPym, Suma de TotReten</code>
            <p class="text-gray-400 mt-2">Los encabezados pueden estar en la fila 3 del Excel. Si ya existe la misma localidad para el mismo mes y año, se actualiza con el último valor importado. Cada importación recibe un número correlativo (<strong>numero_importacion</strong>) para poder eliminarla completa. El año se toma del momento de importación (<?= date('Y') ?>).</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="importar_retenciones" value="1">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel</label>
                <input type="file" name="archivo_retenciones" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-purple-50 file:text-purple-700">
            </div>
            <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i> Importar retenciones
            </button>
        </form>
    </div>

    <!-- Importar Deuda ISS por municipios -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-cyan-600"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">Deuda ISS X Municipios</h2>
                    <p class="text-xs text-gray-500">Importar desde Excel (.xls / .xlsx)</p>
                </div>
            </div>
            <button type="button"
                    onclick="abrirAyudaImportacion('Ayuda: Deuda ISS X Municipios', 'Referencia para exportar el Excel de ISS Deuda por Municipios', 'InformesXLocalidad/ayuda/Deuda_ISS_X_Municipios_Seccion_2_y_3.png', 'Ayuda para importar Deuda ISS X Municipios')"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-cyan-200 text-cyan-700 bg-cyan-50 hover:bg-cyan-100 text-xs font-semibold transition-colors"
                    title="Ver ayuda de importación de Deuda ISS">
                <i class="fas fa-question-circle"></i>
                Ayuda
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Actualmente: <strong><?= $db->query('SELECT COUNT(*) FROM deuda_iss')->fetchColumn() ?></strong> registros en la base.
            <?php
            $ultimaImportacionDeudaIss = (int) $db->query('SELECT COALESCE(MAX(numero_importacion), 0) FROM deuda_iss')->fetchColumn();
            if ($ultimaImportacionDeudaIss > 0):
            ?>
            · Última importación: <strong>#<?= $ultimaImportacionDeudaIss ?></strong>
            <?php endif; ?>
        </p>

        <?php
        $importacionesDeudaIss = $db->query("
            SELECT numero_importacion, COUNT(*) AS registros, MIN(anio) AS anio_desde, MAX(anio) AS anio_hasta
            FROM deuda_iss
            WHERE numero_importacion IS NOT NULL
            GROUP BY numero_importacion
            ORDER BY numero_importacion DESC
        ")->fetchAll();
        ?>

        <?php if (!empty($importacionesDeudaIss)): ?>
        <div class="mb-4 border border-gray-100 rounded-lg overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Importaciones Deuda ISS cargadas
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($importacionesDeudaIss as $imp): ?>
                <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <span class="font-medium text-gray-800">Importación #<?= (int) $imp['numero_importacion'] ?></span>
                        <span class="text-gray-500 text-xs ml-2"><?= (int) $imp['registros'] ?> registro(s)</span>
                        <?php if ($imp['anio_desde']): ?>
                        <span class="text-gray-400 text-xs ml-1">· año <?= h($imp['anio_desde']) ?><?= $imp['anio_hasta'] != $imp['anio_desde'] ? '–' . h($imp['anio_hasta']) : '' ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('¿Eliminar toda la importación Deuda ISS #<?= (int) $imp['numero_importacion'] ?>?')">
                        <input type="hidden" name="eliminar_importacion_deuda_iss" value="1">
                        <input type="hidden" name="numero_importacion_deuda_iss" value="<?= (int) $imp['numero_importacion'] ?>">
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 space-y-1">
            <p class="font-semibold">Columnas esperadas:</p>
            <code class="block leading-relaxed">Anio, Mes, Localidad, Concepto, Imprte a Retener</code>
            <p><strong>Localidad</strong> se vincula con <strong>localidades</strong> → <strong>id_localidad</strong>.</p>
            <p><strong>Concepto</strong> se vincula con <strong>conceptos</strong> → <strong>id_concepto</strong>.</p>
            <p class="text-gray-400 mt-2">Si ya existe el mismo registro por <strong>año + mes + localidad + concepto</strong>, se actualiza el importe y queda asociado a la última importación.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="importar_deuda_iss" value="1">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel Deuda ISS</label>
                <input type="file" name="archivo_deuda_iss" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-cyan-50 file:text-cyan-700">
            </div>
            <button type="submit" class="w-full bg-cyan-700 hover:bg-cyan-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i> Importar Deuda ISS
            </button>
        </form>
    </div>

    <!-- Importar distribución semanal Excel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week text-amber-600"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">Distribución semanal</h2>
                    <p class="text-xs text-gray-500">Importar desde Excel (.xls / .xlsx)</p>
                </div>
            </div>
            <button type="button"
                    onclick="abrirAyudaImportacion('Ayuda: Distribución semanal', 'Referencia para exportar la proyección semanal - Sección 2', 'InformesXLocalidad/ayuda/Proyeccion_Semanal_Seccion_2.png', 'Ayuda para importar Distribución semanal')"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100 text-xs font-semibold transition-colors"
                    title="Ver ayuda de importación de distribución semanal">
                <i class="fas fa-question-circle"></i>
                Ayuda
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Actualmente: <strong><?= $db->query('SELECT COUNT(*) FROM distribucion_semanal')->fetchColumn() ?></strong> registros en la base.
            <?php
            $ultimaImportacionDist = (int) $db->query('SELECT COALESCE(MAX(numero_importacion), 0) FROM distribucion_semanal')->fetchColumn();
            if ($ultimaImportacionDist > 0):
            ?>
            · Última importación: <strong>#<?= $ultimaImportacionDist ?></strong>
            <?php endif; ?>
        </p>

        <?php
        $importacionesDistribucion = $db->query("
            SELECT numero_importacion, COUNT(*) AS registros, MIN(fecha) AS fecha_desde, MAX(fecha) AS fecha_hasta
            FROM distribucion_semanal
            WHERE numero_importacion IS NOT NULL
            GROUP BY numero_importacion
            ORDER BY numero_importacion DESC
        ")->fetchAll();
        ?>

        <?php if (!empty($importacionesDistribucion)): ?>
        <div class="mb-4 border border-gray-100 rounded-lg overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Importaciones cargadas
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($importacionesDistribucion as $imp): ?>
                <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <span class="font-medium text-gray-800">Importación #<?= (int) $imp['numero_importacion'] ?></span>
                        <span class="text-gray-500 text-xs ml-2"><?= (int) $imp['registros'] ?> registro(s)</span>
                        <?php if ($imp['fecha_desde']): ?>
                        <span class="text-gray-400 text-xs ml-1">· <?= h($imp['fecha_desde']) ?><?= $imp['fecha_hasta'] !== $imp['fecha_desde'] ? ' – ' . h($imp['fecha_hasta']) : '' ?></span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('¿Eliminar toda la importación #<?= (int) $imp['numero_importacion'] ?>?')">
                        <input type="hidden" name="eliminar_importacion_distribucion" value="1">
                        <input type="hidden" name="numero_importacion_distribucion" value="<?= (int) $imp['numero_importacion'] ?>">
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 space-y-1">
            <p class="font-semibold">Formato esperado (Listado índices por municipio):</p>
            <p>Fila de encabezados con <strong>Fecha Distrib.</strong> y <strong>SUBTOTAL</strong>.</p>
            <p>Columna anterior a la fecha: nombre de localidad (sin encabezado). Se vincula con la tabla <strong>localidades</strong>.</p>
            <p class="text-gray-400 mt-2">Si ya existe la misma localidad para la misma fecha, se actualiza el importe. Cada importación recibe un <strong>numero_importacion</strong> para poder eliminarla completa.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="importar_distribucion_semanal" value="1">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel</label>
                <input type="file" name="archivo_distribucion" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-amber-50 file:text-amber-700">
            </div>
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i> Importar distribución semanal
            </button>
        </form>
    </div>

    <!-- Importar listado de empleados (agentes) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-group text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">Listado de empleados</h2>
                    <p class="text-xs text-gray-500">Importar aportes / agentes (.xlsx)</p>
                </div>
            </div>
            <button type="button"
                    onclick="abrirAyudaImportacion('Ayuda: Listado de empleados', 'Referencia para exportar el listado de empleados provisto por ISS - Sección 3', 'InformesXLocalidad/ayuda/Empleados_provisto_por_ISS_Seccion_3.png', 'Ayuda para importar listado de empleados provisto por ISS')"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 text-xs font-semibold transition-colors"
                    title="Ver ayuda de importación de empleados">
                <i class="fas fa-question-circle"></i>
                Ayuda
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Actualmente: <strong><?= $db->query('SELECT COUNT(*) FROM agentes')->fetchColumn() ?></strong> registros en la base.
            <?php
            $ultimaImportacionAgentes = (int) $db->query('SELECT COALESCE(MAX(numero_importacion), 0) FROM agentes')->fetchColumn();
            if ($ultimaImportacionAgentes > 0):
            ?>
            · Última importación: <strong>#<?= $ultimaImportacionAgentes ?></strong>
            <?php endif; ?>
        </p>

        <?php
        $importacionesAgentes = $db->query("
            SELECT numero_importacion, COUNT(*) AS registros, MIN(mes) AS mes_desde, MAX(mes) AS mes_hasta, MIN(anio) AS anio_desde, MAX(anio) AS anio_hasta
            FROM agentes
            WHERE numero_importacion IS NOT NULL
            GROUP BY numero_importacion
            ORDER BY numero_importacion DESC
        ")->fetchAll();
        ?>

        <?php if (!empty($importacionesAgentes)): ?>
        <div class="mb-4 border border-gray-100 rounded-lg overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Importaciones cargadas
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($importacionesAgentes as $imp): ?>
                <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <span class="font-medium text-gray-800">Importación #<?= (int) $imp['numero_importacion'] ?></span>
                        <span class="text-gray-500 text-xs ml-2"><?= (int) $imp['registros'] ?> registro(s)</span>
                        <?php if ($imp['anio_desde']): ?>
                        <span class="text-gray-400 text-xs ml-1">
                            · <?= sprintf('%02d', (int) $imp['mes_desde']) ?>/<?= (int) $imp['anio_desde'] ?>
                            <?php if ($imp['anio_hasta'] != $imp['anio_desde'] || $imp['mes_hasta'] != $imp['mes_desde']): ?>
                            – <?= sprintf('%02d', (int) $imp['mes_hasta']) ?>/<?= (int) $imp['anio_hasta'] ?>
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('¿Eliminar toda la importación #<?= (int) $imp['numero_importacion'] ?>?')">
                        <input type="hidden" name="eliminar_importacion_agentes" value="1">
                        <input type="hidden" name="numero_importacion_agentes" value="<?= (int) $imp['numero_importacion'] ?>">
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 space-y-1">
            <p class="font-semibold">Formato esperado (listado de empleados):</p>
            <code class="block leading-relaxed">N° AGENTE, PERIODO, CANT. EMPLEADOS, REMUNERACION C/APORTE, REMUNERACION S/APORTE, ASIG. FAMILIAR</code>
            <p><strong>N° AGENTE</strong> se vincula con <strong>AgeRet</strong> en localidades → <strong>id_dependencia</strong>.</p>
            <p><strong>PERIODO</strong> (ej. 04/2026) → campos <strong>mes</strong> y <strong>anio</strong>.</p>
            <p><strong>CANT. EMPLEADOS</strong> → <strong>total_empleados</strong>.</p>
            <p><strong>REMUNERACION C/APORTE</strong> → <strong>total_remuneracion</strong>.</p>
            <p><strong>REMUNERACION S/APORTE</strong> → <strong>total_aportes</strong>.</p>
            <p><strong>ASIG. FAMILIAR</strong> → <strong>total_asignacion_familiar</strong>.</p>
            <p class="text-gray-400 mt-2">Si ya existe la misma dependencia para el mismo mes y año, se actualizan todos los valores. Cada importación recibe un <strong>numero_importacion</strong> para poder eliminarla completa.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="importar_agentes" value="1">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel</label>
                <input type="file" name="archivo_agentes" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-indigo-50 file:text-indigo-700">
            </div>
            <button type="submit" class="w-full bg-indigo-700 hover:bg-indigo-800 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i> Importar listado de empleados
            </button>
        </form>
    </div>

    <!-- Importar detalle de pagos (aportes) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-check-dollar text-teal-600"></i>
                </div>
                <div>
                    <h2 class="font-bold text-gray-800">Detalle de pagos</h2>
                    <p class="text-xs text-gray-500">Importar aportes por localidad (.xlsx)</p>
                </div>
            </div>
            <button type="button"
                    onclick="abrirAyudaImportacion('Ayuda: Detalle de pagos', 'Referencia para exportar pagos por concepto - Sección 4', 'InformesXLocalidad/ayuda/Pagos_X_Conceptos_Seccion_4.png', 'Ayuda para importar Detalle de pagos')"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-teal-200 text-teal-700 bg-teal-50 hover:bg-teal-100 text-xs font-semibold transition-colors"
                    title="Ver ayuda de importación de detalle de pagos">
                <i class="fas fa-question-circle"></i>
                Ayuda
            </button>
        </div>

        <p class="text-sm text-gray-600 mb-3">
            Actualmente: <strong><?= $db->query('SELECT COUNT(*) FROM aportes')->fetchColumn() ?></strong> registros en la base.
            <?php
            $ultimaImportacionAportes = (int) $db->query('SELECT COALESCE(MAX(numero_importacion), 0) FROM aportes')->fetchColumn();
            if ($ultimaImportacionAportes > 0):
            ?>
            · Última importación: <strong>#<?= $ultimaImportacionAportes ?></strong>
            <?php endif; ?>
        </p>

        <?php
        $importacionesAportes = $db->query("
            SELECT numero_importacion, COUNT(*) AS registros, MIN(FecPag) AS fecha_desde, MAX(FecPag) AS fecha_hasta
            FROM aportes
            WHERE numero_importacion IS NOT NULL AND numero_importacion > 0
            GROUP BY numero_importacion
            ORDER BY numero_importacion DESC
        ")->fetchAll();
        ?>

        <?php if (!empty($importacionesAportes)): ?>
        <div class="mb-4 border border-gray-100 rounded-lg overflow-hidden">
            <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                Importaciones cargadas
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($importacionesAportes as $imp): ?>
                <div class="px-3 py-2 flex items-center justify-between gap-3 text-sm">
                    <div>
                        <span class="font-medium text-gray-800">Importación #<?= (int) $imp['numero_importacion'] ?></span>
                        <span class="text-gray-500 text-xs ml-2"><?= (int) $imp['registros'] ?> registro(s)</span>
                        <?php if ($imp['fecha_desde']): ?>
                        <span class="text-gray-400 text-xs ml-1">
                            · <?= h(date('d/m/Y', strtotime($imp['fecha_desde']))) ?>
                            <?php if ($imp['fecha_hasta'] !== $imp['fecha_desde']): ?>
                            – <?= h(date('d/m/Y', strtotime($imp['fecha_hasta']))) ?>
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <form method="POST" onsubmit="return confirm('¿Eliminar toda la importación #<?= (int) $imp['numero_importacion'] ?>?')">
                        <input type="hidden" name="eliminar_importacion_aportes" value="1">
                        <input type="hidden" name="numero_importacion_aportes" value="<?= (int) $imp['numero_importacion'] ?>">
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600 space-y-1">
            <p class="font-semibold">Formato esperado (Detalle de pagos):</p>
            <code class="block leading-relaxed">Localidad, FecPag, ExpedN, ExpedA, NombeJur, NroChe, Importe, DescCpto, DescSubCpto</code>
            <p><strong>Localidad</strong> se vincula con la tabla <strong>localidades</strong> → <strong>id_localidad</strong>.</p>
            <p><strong>organismo</strong> se completa con <strong>AgeRet</strong> de la localidad.</p>
            <p class="text-gray-400 mt-2">No se insertan filas duplicadas por <strong>FecPag + ExpedN + NroChe + Importe</strong>. Cada importación recibe un <strong>numero_importacion</strong> para poder eliminarla completa.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="importar_aportes" value="1">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel</label>
                <input type="file" name="archivo_aportes" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-teal-50 file:text-teal-700">
            </div>
            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-upload mr-2"></i> Importar detalle de pagos
            </button>
        </form>
    </div>

</div>

<!-- Instrucciones de instalación -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="font-bold text-gray-800 mb-4"><i class="fas fa-terminal mr-2 text-gray-500"></i>Guía de instalación completa</h2>
    <div class="space-y-3 text-sm text-gray-700">
        <div class="flex gap-3">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
            <div>
                <p class="font-semibold">Crear la base de datos</p>
                <code class="block bg-gray-900 text-green-400 px-3 py-2 rounded mt-1 text-xs">mysql -u root -p &lt; database.sql</code>
            </div>
        </div>
        <div class="flex gap-3">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
            <div>
                <p class="font-semibold">Importar funcionarios</p>
                <code class="block bg-gray-900 text-green-400 px-3 py-2 rounded mt-1 text-xs">mysql -u root gestion_gubernamental &lt; funcionarios_data.sql</code>
            </div>
        </div>
        <div class="flex gap-3">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
            <div>
                <p class="font-semibold">Usar el importador web</p>
                <p class="text-gray-500">Usá el botón "Importar plan estratégico" de arriba para cargar la jerarquía del plan.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
            <div>
                <p class="font-semibold">Configurar credenciales</p>
                <p class="text-gray-500">Editá <code class="bg-gray-100 px-1 rounded">includes/config.php</code> con los datos de tu servidor MySQL.</p>
                <p class="text-gray-500 mt-1">Usuario por defecto: <code class="bg-gray-100 px-1 rounded">admin@lapampa.gov.ar</code> / <code class="bg-gray-100 px-1 rounded">password</code></p>
            </div>
        </div>
    </div>
</div>

</div>

<div id="ayuda-importacion-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/75 p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="ayuda-importacion-titulo"
     onclick="cerrarAyudaImportacion(event)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[92vh] overflow-hidden"
         onclick="event.stopPropagation()">
        <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-3 border-b border-gray-100">
            <div>
                <h2 id="ayuda-importacion-titulo" class="font-bold text-gray-900">Ayuda</h2>
                <p id="ayuda-importacion-descripcion" class="text-xs text-gray-500"></p>
            </div>
            <button type="button"
                    onclick="cerrarAyudaImportacion()"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-100"
                    aria-label="Cerrar ayuda">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-3 sm:p-5 overflow-auto max-h-[calc(92vh-4.5rem)] bg-gray-50">
            <img id="ayuda-importacion-imagen"
                 src=""
                 alt=""
                 class="w-full h-auto rounded-lg border border-gray-200 bg-white">
        </div>
    </div>
</div>

<script>
function abrirAyudaImportacion(titulo, descripcion, imagen, alt) {
    const modal = document.getElementById('ayuda-importacion-modal');
    const tituloEl = document.getElementById('ayuda-importacion-titulo');
    const descripcionEl = document.getElementById('ayuda-importacion-descripcion');
    const imagenEl = document.getElementById('ayuda-importacion-imagen');
    if (!modal) return;
    if (tituloEl) tituloEl.textContent = titulo || 'Ayuda';
    if (descripcionEl) descripcionEl.textContent = descripcion || '';
    if (imagenEl) {
        imagenEl.src = imagen || '';
        imagenEl.alt = alt || titulo || 'Ayuda de importación';
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function cerrarAyudaImportacion(event) {
    if (event && event.target && event.target.id !== 'ayuda-importacion-modal') return;
    const modal = document.getElementById('ayuda-importacion-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        cerrarAyudaImportacion();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
