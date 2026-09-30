<?php

declare(strict_types=1);

/**
 * Librería de importación de aportes (CSV / XLSX / XLS).
 * Columnas esperadas: LOCALIDAD | Montos | Conceptos
 * (también admite legado: id;localidad;monto;id_concepto;CONCEPTOS)
 */

require_once __DIR__ . '/../importar_pdf/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function importar_normalize_text(?string $s): string
{
    $s = trim((string) $s);
    if ($s === '') {
        return '';
    }
    $s = importar_fix_mojibake($s);
    $s = str_replace(["\xC2\xA0", "\t"], ' ', $s);
    if (function_exists('mb_strtolower')) {
        $s = mb_strtolower($s, 'UTF-8');
    } else {
        $s = strtolower($s);
    }
    $map = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
        'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        'ñ' => 'n', 'ç' => 'c',
    ];
    $s = strtr($s, $map);
    $s = preg_replace('/\s+/u', ' ', $s) ?? $s;
    return trim($s);
}

/**
 * Corrige texto doblemente codificado como UTF-8 (ej. "CaleufÃº" → "Caleufú").
 */
function importar_fix_mojibake(string $s): string
{
    if ($s === '' || !preg_match('/Ã.|Â./u', $s)) {
        return $s;
    }
    $try = @mb_convert_encoding($s, 'ISO-8859-1', 'UTF-8');
    if (!is_string($try) || $try === '') {
        return $s;
    }
    if (function_exists('mb_check_encoding') && !mb_check_encoding($try, 'UTF-8')) {
        return $s;
    }
    return $try;
}

/** Texto para comparar códigos cortos tipo D.G.E. / D.GE / DGE */
function importar_normalize_code(?string $s): string
{
    $s = importar_normalize_text($s);
    $s = preg_replace('/[^a-z0-9]/u', '', $s) ?? $s;
    return $s;
}

function importar_money_to_float(?string $s): ?float
{
    if ($s === null) {
        return null;
    }
    $s = trim($s);
    if ($s === '' || strcasecmp($s, 'null') === 0) {
        return null;
    }
    $s = preg_replace('/[^\d\-\.,]/u', '', $s) ?? '';
    if ($s === '' || $s === '-' || $s === '.' || $s === ',') {
        return null;
    }

    $lastDot = strrpos($s, '.');
    $lastCom = strrpos($s, ',');
    $decimalSep = null;

    if ($lastDot !== false && $lastCom !== false) {
        $rightDot = substr($s, $lastDot + 1);
        $rightCom = substr($s, $lastCom + 1);
        $isDotDec = (bool) preg_match('/^\d{1,2}$/', $rightDot);
        $isComDec = (bool) preg_match('/^\d{1,2}$/', $rightCom);
        if ($isDotDec && !$isComDec) {
            $decimalSep = '.';
        } elseif ($isComDec && !$isDotDec) {
            $decimalSep = ',';
        } else {
            $decimalSep = ($lastDot > $lastCom) ? '.' : ',';
        }
    } elseif ($lastDot !== false) {
        $right = substr($s, $lastDot + 1);
        // miles: 600.000  | decimal: 600.50
        $decimalSep = preg_match('/^\d{1,2}$/', $right) ? '.' : null;
    } elseif ($lastCom !== false) {
        $right = substr($s, $lastCom + 1);
        $decimalSep = preg_match('/^\d{1,2}$/', $right) ? ',' : null;
    }

    if ($decimalSep === ',') {
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    } elseif ($decimalSep === '.') {
        $s = str_replace(',', '', $s);
    } else {
        // sin decimal claro: quitar separadores de miles
        $s = str_replace(['.', ','], '', $s);
    }

    return is_numeric($s) ? (float) $s : null;
}

function importar_is_total_label(?string $s): bool
{
    $n = importar_normalize_code($s);
    return $n === 'total' || $n === 'totales' || $n === 'suma' || $n === 'sumatotal';
}

/**
 * ¿El texto de concepto corresponde a DGE (id típico 27)?
 */
function importar_is_dge_concept(?string $concepto): bool
{
    $code = importar_normalize_code($concepto);
    if ($code === '') {
        return false;
    }
    if (in_array($code, ['dge', 'dgedeficitygastosdeemergencia'], true)) {
        return true;
    }
    if (str_starts_with($code, 'dge') && strlen($code) <= 6) {
        return true;
    }
    $txt = importar_normalize_text($concepto);
    if (str_contains($txt, 'deficit') && str_contains($txt, 'emergencia')) {
        return true;
    }
    if (preg_match('/\bd\s*\.?\s*g\s*\.?\s*e\b/u', $txt)) {
        return true;
    }
    return false;
}

/**
 * @return array{0: list<array{id:int,localidad:string,norm:string}>, 1: array<string,int>}
 */
function importar_load_localidades(mysqli $con): array
{
    @mysqli_set_charset($con, 'utf8mb4');
    $list = [];
    $byNorm = [];
    $res = mysqli_query($con, 'SELECT id, localidad FROM localidades ORDER BY localidad');
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $id = (int) $row['id'];
            $nombre = importar_fix_mojibake((string) $row['localidad']);
            $norm = importar_normalize_text($nombre);
            $item = ['id' => $id, 'localidad' => $nombre, 'norm' => $norm];
            $list[] = $item;
            if ($norm !== '' && !isset($byNorm[$norm])) {
                $byNorm[$norm] = $id;
            }
        }
    }
    return [$list, $byNorm];
}

/**
 * @return array{0: list<array{id:int,motivo:string,norm:string,code:string}>, 1: ?int}
 */
function importar_load_motivos(mysqli $con): array
{
    @mysqli_set_charset($con, 'utf8mb4');
    $list = [];
    $dgeId = null;
    $res = mysqli_query($con, 'SELECT id, motivo FROM objetivos_motivos ORDER BY id');
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $id = (int) $row['id'];
            $motivo = importar_fix_mojibake((string) $row['motivo']);
            $item = [
                'id' => $id,
                'motivo' => $motivo,
                'norm' => importar_normalize_text($motivo),
                'code' => importar_normalize_code($motivo),
            ];
            $list[] = $item;
            if ($dgeId === null && importar_is_dge_concept($motivo)) {
                $dgeId = $id;
            }
        }
    }
    if ($dgeId === null) {
        // fallback histórico
        $dgeId = 27;
    }
    return [$list, $dgeId];
}

/**
 * Expande abreviaturas frecuentes de planillas de aportes.
 */
function importar_expand_localidad_aliases(string $norm): string
{
    $s = trim($norm);
    if ($s === '') {
        return '';
    }

    // Alias exactos (ya normalizados, minúsculas, sin tildes)
    static $exact = [
        'alg. del aguila' => 'algarrobo del aguila',
        'alg del aguila' => 'algarrobo del aguila',
        'bdo. larroude' => 'bernardo larroude',
        'bdo larroude' => 'bernardo larroude',
        'gral. san martin' => 'general san martin',
        'gral san martin' => 'general san martin',
        'gdor. duval' => 'gobernador duval',
        'gdor duval' => 'gobernador duval',
        'coronel h. lagos' => 'coronel hilario lagos',
        'coronel h lagos' => 'coronel hilario lagos',
        'cuchillo co' => 'cuchillo co',
        'loventuel' => 'loventue',
        'colonia santa maria' => 'colonia santa maria',
    ];
    if (isset($exact[$s])) {
        $s = $exact[$s];
    }

    // Prefijos abreviados
    $replacements = [
        '/^alg\.?\s+del\s+/' => 'algarrobo del ',
        '/^bdo\.?\s+/' => 'bernardo ',
        '/^gral\.?\s+/' => 'general ',
        '/^gdor\.?\s+/' => 'gobernador ',
        '/^col\.?\s+/' => 'colonia ',
        '/^ing\.?\s+/' => 'ingeniero ',
        '/^int\.?\s+/' => 'intendente ',
    ];
    foreach ($replacements as $re => $to) {
        $s = preg_replace($re, $to, $s) ?? $s;
    }

    return trim($s);
}

/**
 * @param list<array{id:int,localidad:string,norm:string}> $localidades
 * @param array<string,int> $byNorm
 * @return array{id:?int,nombre:?string,error:?string}
 */
function importar_match_localidad(string $nombreArchivo, array $localidades, array $byNorm): array
{
    $raw = trim($nombreArchivo);
    if ($raw === '') {
        return ['id' => null, 'nombre' => null, 'error' => 'Localidad vacía'];
    }
    // Si viene numérico, tratar como id
    if (preg_match('/^\d+$/', $raw)) {
        $id = (int) $raw;
        foreach ($localidades as $loc) {
            if ($loc['id'] === $id) {
                return ['id' => $id, 'nombre' => $loc['localidad'], 'error' => null];
            }
        }
        return ['id' => null, 'nombre' => $raw, 'error' => "ID localidad $id no existe"];
    }

    $norm = importar_expand_localidad_aliases(importar_normalize_text($raw));
    $code = importar_normalize_code($norm);

    if (isset($byNorm[$norm])) {
        $id = $byNorm[$norm];
        foreach ($localidades as $loc) {
            if ($loc['id'] === $id) {
                return ['id' => $id, 'nombre' => $loc['localidad'], 'error' => null];
            }
        }
    }

    // Match por código sin espacios/puntos
    foreach ($localidades as $loc) {
        if ($code !== '' && importar_normalize_code($loc['norm']) === $code) {
            return ['id' => $loc['id'], 'nombre' => $loc['localidad'], 'error' => null];
        }
    }

    // match parcial: nombre archivo contenido en DB o viceversa
    $candidates = [];
    foreach ($localidades as $loc) {
        if ($loc['norm'] === '') {
            continue;
        }
        if (str_contains($loc['norm'], $norm) || str_contains($norm, $loc['norm'])) {
            $candidates[] = $loc;
        }
    }
    if (count($candidates) === 1) {
        return ['id' => $candidates[0]['id'], 'nombre' => $candidates[0]['localidad'], 'error' => null];
    }
    if (count($candidates) > 1) {
        return ['id' => null, 'nombre' => $raw, 'error' => 'Localidad ambigua: ' . $raw];
    }

    // Tokens significativos (ignora del/de/la)
    $tokens = array_values(array_filter(
        preg_split('/\s+/', $norm) ?: [],
        static fn ($t) => strlen($t) >= 3 && !in_array($t, ['del', 'de', 'la', 'las', 'los', 'el'], true)
    ));
    if ($tokens !== []) {
        $tokenHits = [];
        foreach ($localidades as $loc) {
            $okAll = true;
            foreach ($tokens as $t) {
                if (!str_contains($loc['norm'], $t) && !str_contains(importar_normalize_code($loc['norm']), importar_normalize_code($t))) {
                    $okAll = false;
                    break;
                }
            }
            if ($okAll) {
                $tokenHits[] = $loc;
            }
        }
        if (count($tokenHits) === 1) {
            return ['id' => $tokenHits[0]['id'], 'nombre' => $tokenHits[0]['localidad'], 'error' => null];
        }
    }

    return ['id' => null, 'nombre' => $raw, 'error' => 'Localidad no encontrada: ' . $raw];
}

/**
 * @param list<array{id:int,motivo:string,norm:string,code:string}> $motivos
 * @return array{id:?int,motivo:?string,error:?string}
 */
function importar_match_concepto(string $conceptoArchivo, ?int $idConceptoArchivo, array $motivos, ?int $dgeId): array
{
    if ($idConceptoArchivo !== null && $idConceptoArchivo > 0) {
        foreach ($motivos as $m) {
            if ($m['id'] === $idConceptoArchivo) {
                return ['id' => $m['id'], 'motivo' => $m['motivo'], 'error' => null];
            }
        }
    }

    $raw = trim($conceptoArchivo);
    if ($raw === '' && ($idConceptoArchivo === null || $idConceptoArchivo <= 0)) {
        return ['id' => null, 'motivo' => null, 'error' => 'Concepto vacío'];
    }

    if (importar_is_dge_concept($raw) && $dgeId !== null) {
        foreach ($motivos as $m) {
            if ($m['id'] === $dgeId) {
                return ['id' => $m['id'], 'motivo' => $m['motivo'], 'error' => null];
            }
        }
        return ['id' => $dgeId, 'motivo' => 'DGE', 'error' => null];
    }

    $norm = importar_normalize_text($raw);
    $code = importar_normalize_code($raw);
    foreach ($motivos as $m) {
        if ($m['norm'] === $norm || ($code !== '' && $m['code'] === $code)) {
            return ['id' => $m['id'], 'motivo' => $m['motivo'], 'error' => null];
        }
    }
    foreach ($motivos as $m) {
        if ($norm !== '' && (str_contains($m['norm'], $norm) || str_contains($norm, $m['norm']))) {
            return ['id' => $m['id'], 'motivo' => $m['motivo'], 'error' => null];
        }
    }

    return ['id' => null, 'motivo' => $raw, 'error' => 'Concepto no encontrado: ' . $raw];
}

/**
 * Lee la primera hoja como matriz de filas (valores string).
 *
 * @return list<list<string>>
 */
function importar_read_sheet_rows(string $path, string $ext): array
{
    $ext = strtolower($ext);
    if ($ext === 'csv') {
        $rows = [];
        $fh = fopen($path, 'rb');
        if ($fh === false) {
            throw new RuntimeException('No se pudo abrir el CSV');
        }
        // detectar BOM
        $bom = fread($fh, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($fh);
        }
        // detectar delimitador con la primera línea
        $firstPos = ftell($fh);
        $firstLine = fgets($fh);
        if ($firstLine === false) {
            fclose($fh);
            return [];
        }
        $semi = substr_count($firstLine, ';');
        $comma = substr_count($firstLine, ',');
        $tab = substr_count($firstLine, "\t");
        $delim = ';';
        if ($tab > $semi && $tab > $comma) {
            $delim = "\t";
        } elseif ($comma > $semi) {
            $delim = ',';
        }
        fseek($fh, $firstPos);
        while (($data = fgetcsv($fh, 0, $delim)) !== false) {
            $rows[] = array_map(static function ($v) {
                return trim((string) $v);
            }, $data);
        }
        fclose($fh);
        return $rows;
    }

    if (!in_array($ext, ['xlsx', 'xls'], true)) {
        throw new RuntimeException('Extensión no soportada: ' . $ext);
    }
    if (!class_exists(IOFactory::class)) {
        throw new RuntimeException('PhpSpreadsheet no disponible (importar_pdf/vendor).');
    }

    // Muchas planillas traen un "used range" enorme (estilo hasta fila 1M).
    // Leemos solo datos, acotando filas/columnas, sin toArray() completo.
    $reader = IOFactory::createReaderForFile($path);
    if (method_exists($reader, 'setReadDataOnly')) {
        $reader->setReadDataOnly(true);
    }
    if (method_exists($reader, 'setReadEmptyCells')) {
        $reader->setReadEmptyCells(false);
    }
    $ss = $reader->load($path);
    $sheet = $ss->getActiveSheet();

    $highestRow = (int) $sheet->getHighestDataRow();
    $highestCol = (string) $sheet->getHighestDataColumn();
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
    if ($highestRow < 1) {
        $highestRow = (int) $sheet->getHighestRow();
    }
    if ($highestColIndex < 1) {
        $highestColIndex = 3;
    }

    // Tope de seguridad: aportes municipales no necesitan miles de filas/columnas.
    $maxRows = min(max($highestRow, 1), 5000);
    $maxCols = min(max($highestColIndex, 3), 12);

    $cellToString = static function ($v): string {
        if ($v === null) {
            return '';
        }
        if (is_bool($v)) {
            return $v ? '1' : '0';
        }
        if (is_int($v)) {
            return (string) $v;
        }
        if (is_float($v)) {
            if (floor($v) == $v) {
                return (string) (int) $v;
            }
            return rtrim(rtrim(sprintf('%.8F', $v), '0'), '.');
        }
        return trim((string) $v);
    };

    $rows = [];
    $emptyStreak = 0;
    for ($r = 1; $r <= $maxRows; $r++) {
        $line = [];
        $any = false;
        for ($c = 1; $c <= $maxCols; $c++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $cell = $sheet->getCell($colLetter . $r);
            $val = $cell->getValue();
            // Total u otros montos pueden venir como fórmula (=SUM(...))
            if (is_string($val) && str_starts_with($val, '=')) {
                try {
                    $val = $cell->getCalculatedValue();
                } catch (Throwable $e) {
                    // dejar la fórmula; el parser de dinero la descartará
                }
            }
            $str = $cellToString($val);
            $line[] = $str;
            if ($str !== '') {
                $any = true;
            }
        }
        if (!$any) {
            $emptyStreak++;
            // cortar si hay un bloque largo vacío (fin real de la planilla)
            if ($emptyStreak >= 25 && $rows !== []) {
                break;
            }
            continue;
        }
        $emptyStreak = 0;
        $rows[] = $line;
    }

    $ss->disconnectWorksheets();
    unset($ss, $sheet, $reader);

    return $rows;
}

/**
 * Detecta índices de columnas LOCALIDAD / Monto / Concepto (y opcionales id).
 *
 * @param list<string> $header
 * @return array{localidad:int,monto:int,concepto:int,id_localidad:?int,id_concepto:?int,has_header:bool}
 */
function importar_detect_columns(array $header): array
{
    $map = [
        'localidad' => null,
        'monto' => null,
        'concepto' => null,
        'id_localidad' => null,
        'id_concepto' => null,
    ];
    foreach ($header as $i => $col) {
        $n = importar_normalize_code($col);
        if ($n === '') {
            continue;
        }
        if (in_array($n, ['localidad', 'localidades', 'municipio', 'municipios'], true)) {
            $map['localidad'] = (int) $i;
        } elseif (in_array($n, ['monto', 'montos', 'importe', 'importes', 'valor', 'valores'], true)) {
            $map['monto'] = (int) $i;
        } elseif (in_array($n, ['concepto', 'conceptos', 'motivo', 'motivos', 'afectacion'], true)) {
            $map['concepto'] = (int) $i;
        } elseif (in_array($n, ['id', 'idlocalidad', 'idloc'], true)) {
            $map['id_localidad'] = (int) $i;
        } elseif (in_array($n, ['idconcepto', 'idmotivos', 'idmotivo'], true)) {
            $map['id_concepto'] = (int) $i;
        }
    }

    $hasHeader = $map['localidad'] !== null || $map['monto'] !== null || $map['concepto'] !== null;

    // Formato nuevo por posición: col1 LOCALIDAD, col2 Montos, col3 Conceptos
    if ($map['localidad'] === null && $map['monto'] === null && $map['concepto'] === null) {
        // ¿parece header texto?
        $c0 = importar_normalize_code($header[0] ?? '');
        if ($c0 === 'localidad' || $c0 === 'id') {
            $hasHeader = true;
        }
        // legado 5 cols: id;localidad;monto;id_concepto;concepto
        if (count($header) >= 5 && ($map['id_localidad'] !== null || $c0 === 'id')) {
            $map['id_localidad'] = $map['id_localidad'] ?? 0;
            $map['localidad'] = 1;
            $map['monto'] = 2;
            $map['id_concepto'] = $map['id_concepto'] ?? 3;
            $map['concepto'] = 4;
            $hasHeader = true;
        } else {
            // sin header reconocible: asumir 3 columnas
            $map['localidad'] = 0;
            $map['monto'] = 1;
            $map['concepto'] = 2;
            $hasHeader = false;
        }
    } else {
        if ($map['localidad'] === null) {
            $map['localidad'] = 0;
        }
        if ($map['monto'] === null) {
            $map['monto'] = 1;
        }
        if ($map['concepto'] === null) {
            $map['concepto'] = 2;
        }
    }

    return [
        'localidad' => (int) $map['localidad'],
        'monto' => (int) $map['monto'],
        'concepto' => (int) $map['concepto'],
        'id_localidad' => $map['id_localidad'],
        'id_concepto' => $map['id_concepto'],
        'has_header' => $hasHeader,
    ];
}

/**
 * @return array{
 *   rows: list<array<string,mixed>>,
 *   total_archivo: ?float,
 *   total_filas: float,
 *   total_ok: bool,
 *   errors: int,
 *   warnings: list<string>,
 *   can_import: bool
 * }
 */
function importar_parse_aportes_file(string $path, string $ext, mysqli $con): array
{
    $sheetRows = importar_read_sheet_rows($path, $ext);
    if ($sheetRows === []) {
        return [
            'rows' => [],
            'total_archivo' => null,
            'total_filas' => 0.0,
            'total_ok' => false,
            'errors' => 1,
            'warnings' => ['El archivo no tiene filas'],
            'can_import' => false,
        ];
    }

    [$localidades, $byNorm] = importar_load_localidades($con);
    [$motivos, $dgeId] = importar_load_motivos($con);

    // Buscar fila de encabezado (puede haber título arriba: "Aportes semana ...")
    $headerRowIdx = 0;
    $cols = null;
    $scanLimit = min(10, count($sheetRows));
    for ($h = 0; $h < $scanLimit; $h++) {
        $candidate = importar_detect_columns($sheetRows[$h]);
        $joined = importar_normalize_code(implode(' ', $sheetRows[$h]));
        $looksHeader = str_contains($joined, 'localidad')
            && (str_contains($joined, 'monto') || str_contains($joined, 'concepto'));
        if ($looksHeader || ($candidate['has_header'] && (
            importar_normalize_code($sheetRows[$h][$candidate['localidad']] ?? '') === 'localidad'
            || importar_normalize_code($sheetRows[$h][$candidate['monto']] ?? '') === 'montos'
            || importar_normalize_code($sheetRows[$h][$candidate['monto']] ?? '') === 'monto'
        ))) {
            $headerRowIdx = $h;
            $cols = $candidate;
            $cols['has_header'] = true;
            break;
        }
    }
    if ($cols === null) {
        $cols = importar_detect_columns($sheetRows[0]);
        $headerRowIdx = 0;
    }

    $start = !empty($cols['has_header']) ? ($headerRowIdx + 1) : 0;

    $outRows = [];
    $totalArchivo = null;
    $sum = 0.0;
    $errors = 0;
    $warnings = [];

    for ($i = $start, $n = count($sheetRows); $i < $n; $i++) {
        $r = $sheetRows[$i];
        $cell = static function (int $idx) use ($r): string {
            return isset($r[$idx]) ? trim((string) $r[$idx]) : '';
        };

        $locRaw = $cell((int) $cols['localidad']);
        $montoRaw = $cell((int) $cols['monto']);
        $concRaw = $cell((int) $cols['concepto']);
        $idLocRaw = $cols['id_localidad'] !== null ? $cell((int) $cols['id_localidad']) : '';
        $idConcRaw = $cols['id_concepto'] !== null ? $cell((int) $cols['id_concepto']) : '';

        // fila vacía
        if ($locRaw === '' && $montoRaw === '' && $concRaw === '' && $idLocRaw === '' && $idConcRaw === '') {
            continue;
        }

        $monto = importar_money_to_float($montoRaw);

        // fila TOTAL
        if (importar_is_total_label($locRaw) || (importar_is_total_label($concRaw) && $locRaw === '')) {
            $totalArchivo = $monto;
            continue;
        }
        // total al final sin etiqueta: última fila con solo monto
        if ($locRaw === '' && $concRaw === '' && $monto !== null && $i === $n - 1) {
            $totalArchivo = $monto;
            continue;
        }

        // filas sin monto: se omiten (localidad vacía de monto)
        if ($monto === null || abs($monto) < 0.00001) {
            if ($locRaw !== '' && $concRaw === '' && ($idConcRaw === '' || $idConcRaw === '0')) {
                continue; // localidad sin movimiento
            }
            if ($locRaw !== '') {
                $outRows[] = [
                    'line' => $i + 1,
                    'id_localidad' => null,
                    'localidad' => $locRaw,
                    'monto' => 0.0,
                    'monto_str' => $montoRaw,
                    'id_concepto' => null,
                    'concepto' => $concRaw,
                    'ok' => false,
                    'error' => 'Monto inválido o vacío',
                ];
                $errors++;
            }
            continue;
        }

        $nombreParaMatch = $locRaw !== '' ? $locRaw : $idLocRaw;
        if ($idLocRaw !== '' && preg_match('/^\d+$/', $idLocRaw)) {
            $matchLoc = importar_match_localidad($idLocRaw, $localidades, $byNorm);
            if ($matchLoc['id'] === null && $locRaw !== '') {
                $matchLoc = importar_match_localidad($locRaw, $localidades, $byNorm);
            }
        } else {
            $matchLoc = importar_match_localidad($nombreParaMatch, $localidades, $byNorm);
        }

        $idConc = ($idConcRaw !== '' && preg_match('/^\d+$/', $idConcRaw)) ? (int) $idConcRaw : null;
        $matchConc = importar_match_concepto($concRaw, $idConc, $motivos, $dgeId);

        $errParts = [];
        if ($matchLoc['error']) {
            $errParts[] = $matchLoc['error'];
        }
        if ($matchConc['error']) {
            $errParts[] = $matchConc['error'];
        }
        $ok = $errParts === [];
        if (!$ok) {
            $errors++;
        }

        $sum += $monto;
        $outRows[] = [
            'line' => $i + 1,
            'id_localidad' => $matchLoc['id'],
            'localidad' => $matchLoc['nombre'] ?? $locRaw,
            'monto' => $monto,
            'monto_str' => $montoRaw,
            'id_concepto' => $matchConc['id'],
            'concepto' => $matchConc['motivo'] ?? $concRaw,
            'concepto_archivo' => $concRaw,
            'ok' => $ok,
            'error' => $ok ? null : implode(' | ', $errParts),
        ];
    }

    $totalOk = true;
    if ($totalArchivo !== null) {
        $totalOk = abs($totalArchivo - $sum) < 0.02;
        if (!$totalOk) {
            $warnings[] = sprintf(
                'El Total del archivo (%s) no coincide con la suma de filas (%s).',
                number_format($totalArchivo, 2, ',', '.'),
                number_format($sum, 2, ',', '.')
            );
        }
    } else {
        $warnings[] = 'No se encontró fila Total al final; se usará la suma de las filas.';
    }

    if ($outRows === []) {
        $errors++;
        $warnings[] = 'No hay filas importables.';
    }

    $canImport = $errors === 0 && $outRows !== [] && $totalOk;

    return [
        'rows' => $outRows,
        'total_archivo' => $totalArchivo,
        'total_filas' => round($sum, 2),
        'total_ok' => $totalOk,
        'errors' => $errors,
        'warnings' => $warnings,
        'can_import' => $canImport,
    ];
}

function importar_format_money(float $v): string
{
    return '$ ' . number_format($v, 2, ',', '.');
}

function importar_save_preview_cache(array $payload): string
{
    $dir = __DIR__ . '/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $token = bin2hex(random_bytes(12));
    $path = $dir . '/preview_' . $token . '.json';
    $payload['_saved_at'] = time();
    file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $token;
}

function importar_load_preview_cache(string $token): ?array
{
    if ($token === '' || !preg_match('/^[a-f0-9]{24}$/', $token)) {
        return null;
    }
    $path = __DIR__ . '/cache/preview_' . $token . '.json';
    if (!is_readable($path)) {
        return null;
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

function importar_clear_preview_cache(string $token): void
{
    if ($token === '' || !preg_match('/^[a-f0-9]{24}$/', $token)) {
        return;
    }
    $path = __DIR__ . '/cache/preview_' . $token . '.json';
    if (is_file($path)) {
        @unlink($path);
    }
}
