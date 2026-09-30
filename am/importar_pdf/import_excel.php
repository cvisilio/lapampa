<?php

declare(strict_types=1);

/**
 * Procesamiento de extracto bancario exportado a Excel (.xlsx / .xls).
 * Variables desde import.php: $ruta, $destDir, $transferRecibidasSoloCuitLocalidad
 */

$importarPdfComposerAutoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!is_readable($importarPdfComposerAutoload)) {
    $msg = 'No se encontró Composer en importar_pdf/vendor. Subí la carpeta vendor completa o ejecutá en importar_pdf: composer install';
    if (function_exists('back_to_index')) {
        back_to_index('error', $msg);
        exit;
    }
    throw new RuntimeException($msg);
}
require_once $importarPdfComposerAutoload;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'pdf_import_matrix.php';

/**
 * Definición de respaldo: en hosting a veces se sube import_excel.php pero no pdf_import_matrix.php actualizado.
 */
if (!function_exists('pdf_import_merge_excel_day_summaries')) {
    /**
     * @param list<array<string, mixed>> $summaries
     *
     * @return array{months: list<string>, matrix: array}
     */
    function pdf_import_merge_excel_day_summaries(array $bundle, array $summaries): array
    {
        if ($summaries === []) {
            return $bundle;
        }
        $months = $bundle['months'] ?? [];
        $matrix = $bundle['matrix'] ?? [];
        $months = array_values(array_unique($months));
        foreach ($summaries as $s) {
            if (!is_array($s)) {
                continue;
            }
            $ym = $s['ym'] ?? '';
            $day = isset($s['day']) ? (int) $s['day'] : 0;
            if ($ym === '' || !preg_match('/^\d{4}-\d{2}$/', $ym) || $day < 1 || $day > 31) {
                continue;
            }
            if (!in_array($ym, $months, true)) {
                $months[] = $ym;
            }
            if (!isset($matrix[$day])) {
                $matrix[$day] = [];
            }
            $ex = $matrix[$day][$ym] ?? [
                'ubicar'             => '',
                'debitos'            => 0.0,
                'creditos'           => 0.0,
                'transfer_recibidas' => 0.0,
                'saldo_final'        => null,
            ];
            if (isset($s['debitos']) && is_numeric($s['debitos'])) {
                $ex['debitos'] = round((float) $s['debitos'], 2);
            }
            if (isset($s['creditos']) && is_numeric($s['creditos'])) {
                $ex['creditos'] = round((float) $s['creditos'], 2);
            }
            if (array_key_exists('saldo_final', $s) && $s['saldo_final'] !== null && is_numeric($s['saldo_final'])) {
                $ex['saldo_final'] = round((float) $s['saldo_final'], 2);
            }
            $matrix[$day][$ym] = $ex;
        }
        sort($months, SORT_STRING);
        for ($d = 1; $d <= 31; $d++) {
            if (!isset($matrix[$d])) {
                $matrix[$d] = [];
            }
            foreach ($months as $ym) {
                if (!isset($matrix[$d][$ym])) {
                    $matrix[$d][$ym] = [
                        'ubicar'             => '',
                        'debitos'            => 0.0,
                        'creditos'           => 0.0,
                        'transfer_recibidas' => 0.0,
                        'saldo_final'        => null,
                    ];
                }
            }
        }

        return ['months' => $months, 'matrix' => $matrix];
    }
}

if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class, true)) {
    $msg = 'PhpSpreadsheet no está instalado (falta vendor/phpoffice). En la carpeta importar_pdf ejecutá: composer install';
    if (function_exists('back_to_index')) {
        back_to_index('error', $msg);
        exit;
    }
    throw new RuntimeException($msg);
}

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

if (!isset($ruta) || !is_string($ruta) || $ruta === '' || !is_readable($ruta)) {
    back_to_index('error', 'Archivo Excel no disponible para procesar.');
}

if (!isset($destDir) || !is_string($destDir)) {
    back_to_index('error', 'Directorio de importaci\xC3\xB3n no configurado.');
}

$transferRecibidasSoloCuitLocalidad = $transferRecibidasSoloCuitLocalidad ?? false;

function excel_normalize_ws(string $s): string
{
    $s = str_replace(["\xC2\xA0", "\xE2\x80\x8B", "\t"], ' ', $s);
    $s = preg_replace('/[ ]{2,}/u', ' ', $s);
    $s = preg_replace('/\R+/u', "\n", $s);

    return trim($s);
}

/**
 * Minúsculas y sin acentos para comparar encabezados de columna.
 * No usar strtr() con cadenas UTF-8 de distinto tamaño en bytes: corrompe el texto (p. ej. "Débitos" → "duibitos").
 */
function excel_normalize_header_key(string $raw): string
{
    $key = mb_strtolower(preg_replace('/\s+/u', ' ', $raw));
    $from = ['á', 'à', 'ä', 'â', 'ã', 'å', 'é', 'è', 'ë', 'ê', 'í', 'ì', 'ï', 'î', 'ó', 'ò', 'ö', 'ô', 'õ', 'ú', 'ù', 'ü', 'û', 'ñ', 'ç'];
    $to = ['a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'n', 'c'];

    return str_replace($from, $to, $key);
}

/**
 * @return float|null valor numérico de celda (texto con miles o número)
 */
function excel_cell_money(Worksheet $sheet, string $coord): ?float
{
    $cell = $sheet->getCell($coord);
    $v = $cell->getValue();
    if ($v === null || $v === '') {
        return null;
    }
    if (is_numeric($v)) {
        return (float) $v;
    }
    $s = trim((string) $cell->getFormattedValue());
    if ($s === '') {
        return null;
    }

    return pdf_import_money_to_float($s);
}

function excel_normalize_fecha_cell(Worksheet $sheet, string $coord): ?string
{
    $cell = $sheet->getCell($coord);
    $v = $cell->getValue();
    if (ExcelDate::isDateTime($cell) && is_numeric($v)) {
        $ts = ExcelDate::excelToTimestamp((float) $v);

        return date('d/m/y', $ts);
    }
    $s = trim((string) $v);
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/', $s, $m)) {
        $d = (int) $m[1];
        $mo = (int) $m[2];
        $yRaw = $m[3];
        if (strlen($yRaw) === 2) {
            $yi = (int) $yRaw;
            $full = $yi >= 70 ? 1900 + $yi : 2000 + $yi;
        } else {
            $full = (int) $yRaw;
        }

        return sprintf('%02d/%02d/%02d', $d, $mo, $full % 100);
    }

    return null;
}

function excel_find_header_row(Worksheet $sheet): ?int
{
    $maxR = min(80, (int) $sheet->getHighestRow());
    for ($r = 1; $r <= $maxR; $r++) {
        $a = trim((string) $sheet->getCell('A'.$r)->getValue());
        $a = preg_replace('/^\x{FEFF}/u', '', $a) ?? $a;
        $a = mb_strtolower($a);
        if ($a === 'fecha' || str_starts_with($a, 'fecha')) {
            return $r;
        }
    }

    return null;
}

/**
 * Primera fila donde hay movimiento: fecha válida en columna fecha y concepto no vacío.
 * Algunos extractos ponen una fila de leyendas y la siguiente sigue siendo encabezado o vacía;
 * los importes están una o más filas más abajo.
 */
function excel_find_first_data_row(Worksheet $sheet, int $headerRow, array $colMap, int $maxExtra = 15): int
{
    $Lf = $colMap['fecha'];
    $Lc = $colMap['concepto'];
    $headerish = [
        'concepto', 'descripcion', 'descripción', 'detalle', 'comprobante', 'comprobantes',
        'debito', 'débito', 'debitos', 'débitos', 'credito', 'crédito', 'creditos', 'créditos',
        'saldo', 'importe', 'importes', 'movimientos', 'fecha', 'fecha valor', 'fecha de movimiento',
    ];
    for ($offset = 1; $offset <= $maxExtra; $offset++) {
        $r = $headerRow + $offset;
        $fecNorm = excel_normalize_fecha_cell($sheet, $Lf.$r);
        if ($fecNorm === null) {
            continue;
        }
        $concepto = trim((string) $sheet->getCell($Lc.$r)->getValue());
        if ($concepto === '') {
            continue;
        }
        $cLower = mb_strtolower(preg_replace('/\s+/u', ' ', $concepto));
        if (in_array($cLower, $headerish, true)) {
            continue;
        }
        if (preg_match('/^(total|totales|subtotal)\b/u', $cLower)) {
            continue;
        }

        return $r;
    }

    return $headerRow + 1;
}

/**
 * CSV exportado del home banking sin fila "Fecha"/"Concepto": columnas A–F = fecha, concepto, comprob., débito, crédito, saldo.
 *
 * @return ?array{colMap: array<string, string>, headerRow: int}
 */
function banksheet_infer_positional_layout(Worksheet $sheet): ?array
{
    $maxColIdx = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    if ($maxColIdx < 6) {
        return null;
    }

    $highestRow = (int) $sheet->getHighestRow();
    $scanMax = min($highestRow, 5000);

    for ($r = 1; $r <= $scanMax; $r++) {
        $a = trim((string) $sheet->getCell('A'.$r)->getValue());
        $a = preg_replace('/^\x{FEFF}/u', '', $a) ?? $a;
        if ($a === '' || !preg_match('/^\d{1,2}\/\d{1,2}\/\d{2,4}$/', $a)) {
            continue;
        }

        $b = trim((string) $sheet->getCell('B'.$r)->getValue());
        if ($b === '') {
            continue;
        }
        $bLower = mb_strtolower($b);
        if (str_contains($bLower, 'titulares:') || str_contains($bLower, 'cantidad de titulares')) {
            continue;
        }

        $deb = excel_cell_money($sheet, 'D'.$r);
        $cre = excel_cell_money($sheet, 'E'.$r);
        $sal = excel_cell_money($sheet, 'F'.$r);

        $hasDebCre = ($deb !== null && abs($deb) > 1e-5) || ($cre !== null && abs($cre) > 1e-5);
        if (!$hasDebCre && ($sal === null || abs($sal) < 1e-5)) {
            continue;
        }

        $colMap = [
            'fecha'    => 'A',
            'concepto' => 'B',
            'comprob'  => 'C',
            'debito'   => 'D',
            'credito'  => 'E',
            'saldo'    => 'F',
        ];

        return ['colMap' => $colMap, 'headerRow' => $r - 1];
    }

    return null;
}

/**
 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
 */
function banksheet_load_spreadsheet(string $ruta): Spreadsheet
{
    $low = strtolower($ruta);
    if (str_ends_with($low, '.csv')) {
        $reader = new CsvReader();
        $reader->setInputEncoding('UTF-8');
        $reader->setFallbackEncoding(CsvReader::DEFAULT_FALLBACK_ENCODING);
        $reader->setDelimiter(',');
        $reader->setEnclosure('"');

        return $reader->load($ruta);
    }

    return IOFactory::load($ruta);
}

/**
 * @return ?array<string, string> letras de columna por clave
 */
function excel_map_columns(Worksheet $sheet, int $headerRow): ?array
{
    $lastCol = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    $map = [];
    for ($ci = 1; $ci <= $lastCol; $ci++) {
        $L = Coordinate::stringFromColumnIndex($ci);
        $raw = trim((string) $sheet->getCell($L.$headerRow)->getValue());
        if ($raw === '') {
            continue;
        }
        $key = excel_normalize_header_key($raw);

        if (str_contains($key, 'fecha') && !isset($map['fecha'])) {
            $map['fecha'] = $L;
        } elseif ((str_contains($key, 'concepto') || str_contains($key, 'descrip')) && !isset($map['concepto'])) {
            $map['concepto'] = $L;
        } elseif (str_contains($key, 'comprob') && !isset($map['comprob'])) {
            $map['comprob'] = $L;
        } elseif ((str_contains($key, 'debito') || str_contains($key, 'débito')) && !isset($map['debito'])) {
            $map['debito'] = $L;
        } elseif ((str_contains($key, 'credito') || str_contains($key, 'crédito')) && !isset($map['credito'])) {
            $map['credito'] = $L;
        } elseif (str_contains($key, 'saldo') && !isset($map['saldo'])) {
            $map['saldo'] = $L;
        }
    }
    if (!isset($map['fecha'], $map['concepto'])) {
        return null;
    }
    if (isset($map['saldo'])) {
        $probe = $headerRow + 2;
        $L = $map['saldo'];
        $mv = excel_cell_money($sheet, $L.$probe);
        if ($mv === null) {
            $idx = Coordinate::columnIndexFromString($L);
            if ($idx > 1) {
                $L2 = Coordinate::stringFromColumnIndex($idx - 1);
                if (excel_cell_money($sheet, $L2.$probe) !== null) {
                    $map['saldo'] = $L2;
                }
            }
        }
    }

    return $map;
}

/**
 * Titular / CUIT / CBU desde el encabezado de la hoja (misma idea que el PDF).
 *
 * @return array{titular: ?string, cuit: ?string, cbu: ?string, cuenta: ?string}
 */
function excel_extract_bank_header(Worksheet $sheet, int $beforeRow): array
{
    $buf = '';
    $maxCol = min(12, Coordinate::columnIndexFromString($sheet->getHighestColumn()));
    $last = max(1, $beforeRow - 1);
    for ($r = 1; $r <= $last; $r++) {
        for ($c = 1; $c <= $maxCol; $c++) {
            $L = Coordinate::stringFromColumnIndex($c);
            $buf .= ' '.(string) $sheet->getCell($L.$r)->getValue();
        }
        $buf .= "\n";
    }
    $norm = excel_normalize_ws($buf);
    if (function_exists('mb_check_encoding') && !mb_check_encoding($norm, 'UTF-8')) {
        $norm = mb_convert_encoding($norm, 'UTF-8', 'Windows-1252');
    }

    $header = [
        'titular' => null,
        'cuit'    => null,
        'cbu'     => null,
        'cuenta'  => null,
    ];
    $cuitHeaderPatterns = [
        '/Titulares?:\s*(.{1,400}?)\s+CUIT:\s*([0-9\-\/.]+)/us',
        '/Titulares?:\s*(.{1,400}?)(?:\R\s*)+CUIT:\s*([0-9\-\/.]+)/us',
        '/Titular\s+de\s+la\s+cuenta[:\s]+(.{1,400}?)\s+CUIT[:\s]+([0-9\-\/.]+)/uis',
    ];
    foreach ($cuitHeaderPatterns as $pat) {
        if (preg_match($pat, $norm, $mh)) {
            $header['titular'] = trim(preg_replace('/\s+/u', ' ', $mh[1]));
            $header['cuit']    = trim(preg_replace('/\s+/u', '', $mh[2]));
            break;
        }
    }
    if (($header['cuit'] ?? null) !== null && pdf_import_normalize_cuit_digits($header['cuit']) === null) {
        $header['cuit'] = null;
    }
    if (($header['cuit'] ?? null) === null) {
        $headSlice = substr($norm, 0, 15000);
        if (preg_match_all('/\bCUIT\s*[\:\/]?\s*([0-9\-\/.]{10,22})/ui', $headSlice, $mall)) {
            foreach ($mall[1] as $cand) {
                $stripped = trim(preg_replace('/\s+/u', '', (string) $cand));
                if (pdf_import_normalize_cuit_digits($stripped) !== null) {
                    $header['cuit'] = $stripped;
                    break;
                }
            }
        }
    }
    if (preg_match('/\b(\d{22})\b/u', $norm, $mcbu)) {
        $header['cbu'] = $mcbu[1];
    }
    if (preg_match('/Cuenta\s+Corriente\s+Bancaria\s*\-\s*(.+)(?:\R|$)/u', $norm, $mcta)) {
        $header['cuenta'] = trim($mcta[0]);
    }

    return $header;
}

/**
 * Firma estable de una línea de movimiento (débito y/o crédito) para detectar duplicados en exportes XLSX
 * que repiten el mismo bloque de movimientos más abajo en la hoja.
 */
function banksheet_movement_signature_from_parts(
    string $fecNorm,
    string $concepto,
    ?string $comprob,
    ?float $deb,
    ?float $cre
): string {
    $c = preg_replace('/\s+/u', ' ', trim($concepto));
    $compN = ($comprob !== null && $comprob !== '') ? trim($comprob) : '';
    $d = $deb !== null ? sprintf('%.6F', $deb) : '';
    $cr = $cre !== null ? sprintf('%.6F', $cre) : '';

    return $fecNorm."\x1F".$c."\x1F".$compN."\x1F".$d."\x1F".$cr;
}

/**
 * Por cada firma, la primera fila en la hoja (número menor) = copia que se conserva.
 *
 * @return array<string, int>
 */
function banksheet_build_first_row_by_signature(
    Worksheet $sheet,
    int $dataStartRow,
    int $highestRow,
    array $colMap
): array {
    $first = [];
    $Lc = $colMap['concepto'];
    $Lf = $colMap['fecha'];
    for ($r = $dataStartRow; $r <= $highestRow; $r++) {
        $concepto = trim((string) $sheet->getCell($Lc.$r)->getValue());
        if ($concepto === '' || stripos($concepto, 'SALDO ANTERIOR') !== false) {
            continue;
        }
        $fecNorm = excel_normalize_fecha_cell($sheet, $Lf.$r);
        if ($fecNorm === null) {
            continue;
        }
        $comp = isset($colMap['comprob']) ? trim((string) $sheet->getCell($colMap['comprob'].$r)->getValue()) : '';
        if ($comp === '') {
            $comp = null;
        }
        $deb = isset($colMap['debito']) ? excel_cell_money($sheet, $colMap['debito'].$r) : null;
        $cre = isset($colMap['credito']) ? excel_cell_money($sheet, $colMap['credito'].$r) : null;
        if (($deb === null || abs($deb) < 0.00001) && ($cre === null || abs($cre) < 0.00001)) {
            continue;
        }
        $sig = banksheet_movement_signature_from_parts($fecNorm, $concepto, $comp, $deb, $cre);
        if (!isset($first[$sig]) || $r < $first[$sig]) {
            $first[$sig] = $r;
        }
    }

    return $first;
}

/**
 * Suma por día/mes de transferencias recibidas (concepto), análogo al PDF.
 *
 * @param array<string, int>|null $firstRowBySig si no es null, solo cuenta la primera fila de cada firma (evita duplicados XLSX).
 * @param int|null               $dataStartRow   si no es null, primera fila de datos (misma que excel_find_first_data_row); si es null, headerRow+1.
 * @return array<string, array<int, float>>
 */
function excel_aggregate_transfer_recibidas(
    Worksheet $sheet,
    int $headerRow,
    array $map,
    ?string $headerCuit,
    bool $soloCoincideCuitLocalidad,
    ?array $firstRowBySig = null,
    ?int $dataStartRow = null
): array {
    $by = [];
    $cuitDigits = pdf_import_normalize_cuit_digits($headerCuit);
    $highestRow = (int) $sheet->getHighestRow();
    $start = $dataStartRow ?? ($headerRow + 1);
    for ($r = $start; $r <= $highestRow; $r++) {
        $Lc = $map['concepto'] ?? 'B';
        $concepto = trim((string) $sheet->getCell($Lc.$r)->getValue());
        if ($concepto === '') {
            continue;
        }
        $hayTrf = stripos($concepto, 'TRANSFERENCIA') !== false && stripos($concepto, 'RECIB') !== false;
        if (!$hayTrf) {
            continue;
        }
        $comprob = isset($map['comprob']) ? trim((string) $sheet->getCell($map['comprob'].$r)->getValue()) : null;
        if ($comprob === '') {
            $comprob = null;
        }
        if ($soloCoincideCuitLocalidad) {
            if ($cuitDigits === null || $cuitDigits === '') {
                continue;
            }
            if (!pdf_import_concept_area_contains_cuit($concepto, $comprob, $cuitDigits)) {
                continue;
            }
        }
        $fecNorm = excel_normalize_fecha_cell($sheet, ($map['fecha'] ?? 'A').$r);
        if ($fecNorm === null) {
            continue;
        }
        $fd = pdf_import_fecha_transfer_to_ym_day($fecNorm);
        if ($fd === null) {
            continue;
        }
        $deb = isset($map['debito']) ? excel_cell_money($sheet, $map['debito'].$r) : null;
        $cre = isset($map['credito']) ? excel_cell_money($sheet, $map['credito'].$r) : null;
        $imp = 0.0;
        if ($deb !== null && abs($deb) > 0.0000001) {
            $imp = abs((float) $deb);
        } elseif ($cre !== null && abs($cre) > 0.0000001) {
            $imp = abs((float) $cre);
        }
        if ($imp <= 0) {
            continue;
        }
        if ($firstRowBySig !== null) {
            $sig = banksheet_movement_signature_from_parts($fecNorm, $concepto, $comprob, $deb, $cre);
            if (!isset($firstRowBySig[$sig]) || $firstRowBySig[$sig] !== $r) {
                continue;
            }
        }
        $ym = $fd['ym'];
        $d = $fd['day'];
        if (!isset($by[$ym][$d])) {
            $by[$ym][$d] = 0.0;
        }
        $by[$ym][$d] += $imp;
    }

    return $by;
}

function banksheet_row_text_cell_a(Worksheet $sheet, int $r): string
{
    $a = trim((string) $sheet->getCell('A'.$r)->getFormattedValue());

    return preg_replace('/\s+/u', ' ', $a) ?? $a;
}

function banksheet_is_transferencias_recibidas_title_row(Worksheet $sheet, int $r): bool
{
    $t = mb_strtoupper(banksheet_row_text_cell_a($sheet, $r), 'UTF-8');

    return $t === 'TRANSFERENCIAS RECIBIDAS' || str_starts_with($t, 'TRANSFERENCIAS RECIBIDAS');
}

/**
 * Encabezado de la tabla anexa (Fec.Mov., Detalle, Comprob., Importe en columnas variables o mezclado en A).
 *
 * @return array{fechaCol: int, detalleCol: int, comprobCol: ?int, importeCol: ?int, merged_fecha_detalle: bool}
 */
function banksheet_tr_rec_map_header(Worksheet $sheet, int $headerRow, int $lastCol): array
{
    $fechaCi = null;
    $detalleCi = null;
    $comprobCi = null;
    $importeCi = null;
    for ($ci = 1; $ci <= $lastCol; $ci++) {
        $L = Coordinate::stringFromColumnIndex($ci);
        $raw = trim((string) $sheet->getCell($L.$headerRow)->getFormattedValue());
        if ($raw === '') {
            continue;
        }
        $t = mb_strtolower(preg_replace('/\s+/u', ' ', $raw) ?? $raw);
        if (preg_match('/^fec\.?\s*mov/i', $raw) && mb_strlen($raw) < 40) {
            $fechaCi = $ci;
        }
        if (str_contains($t, 'detalle') || str_contains($t, 'transacc')) {
            $detalleCi = $ci;
        }
        if (preg_match('/^comprob/i', $t)) {
            $comprobCi = $ci;
        }
        if (preg_match('/^importe\.?\s*$/iu', $raw)) {
            $importeCi = $ci;
        }
    }
    $aRaw = trim((string) $sheet->getCell('A'.$headerRow)->getFormattedValue());
    $aLow = mb_strtolower($aRaw);
    $mergedWide = mb_strlen($aRaw) > 35 && str_contains($aLow, 'fec') && str_contains($aLow, 'mov')
        && str_contains($aLow, 'importe');
    if ($mergedWide || ($fechaCi === null && $importeCi === null && str_contains($aLow, 'importe'))) {
        for ($ci = 2; $ci <= $lastCol; $ci++) {
            $L = Coordinate::stringFromColumnIndex($ci);
            $raw = trim((string) $sheet->getCell($L.$headerRow)->getFormattedValue());
            if ($raw === '') {
                continue;
            }
            $tl = mb_strtolower($raw);
            if (preg_match('/^importe\.?\s*$/iu', $raw)) {
                $importeCi = $ci;
            }
            if (preg_match('/^comprob/i', $tl)) {
                $comprobCi = $ci;
            }
        }

        return [
            'fechaCol'             => 1,
            'detalleCol'           => 1,
            'comprobCol'           => $comprobCi,
            'importeCol'           => $importeCi,
            'merged_fecha_detalle' => true,
        ];
    }
    if ($fechaCi === null) {
        $fechaCi = 1;
    }
    if ($detalleCi === null) {
        $detalleCi = 2;
    }

    return [
        'fechaCol'             => $fechaCi,
        'detalleCol'           => $detalleCi,
        'comprobCol'           => $comprobCi,
        'importeCol'           => $importeCi,
        'merged_fecha_detalle' => false,
    ];
}

function banksheet_tr_rec_row_should_stop(Worksheet $sheet, int $r): bool
{
    $a = mb_strtoupper(banksheet_row_text_cell_a($sheet, $r), 'UTF-8');
    if ($a === '') {
        return false;
    }
    if (banksheet_is_transferencias_recibidas_title_row($sheet, $r)) {
        return true;
    }
    $markers = [
        'TRANSFERENCIAS ENVIADAS', 'TRANSFERENCIAS ENVIADA', 'TRANSFERENCIAS EMITIDAS', 'TRANSFERENCIAS EMITIDA',
        'OTROS MOVIMIENTOS', 'RESUMEN CONSOLIDADO', 'IVA TOTAL', 'SALDO ANTERIOR', 'FECHA EMISIÓN', 'FECHA EMISION',
    ];
    foreach ($markers as $mk) {
        if (str_contains($a, $mk)) {
            return true;
        }
    }

    return false;
}

/**
 * Importe en fila de la sección TRANSFERENCIAS RECIBIDAS (columna Importe o la más a la derecha con monto).
 */
function banksheet_tr_rec_row_importe(
    Worksheet $sheet,
    int $r,
    int $lastCol,
    ?int $importeColIdx,
    int $minMoneyColIdx
): ?float {
    if ($importeColIdx !== null) {
        $L = Coordinate::stringFromColumnIndex($importeColIdx);
        $v = excel_cell_money($sheet, $L.$r);
        if ($v !== null && abs($v) > 0.00001) {
            return abs((float) $v);
        }
        foreach ([1, -1] as $off) {
            $cj = $importeColIdx + $off;
            if ($cj < $minMoneyColIdx || $cj > $lastCol) {
                continue;
            }
            $Lj = Coordinate::stringFromColumnIndex($cj);
            $v2 = excel_cell_money($sheet, $Lj.$r);
            if ($v2 !== null && abs($v2) > 0.00001) {
                return abs((float) $v2);
            }
        }
    }
    for ($ci = $lastCol; $ci >= $minMoneyColIdx; $ci--) {
        $L = Coordinate::stringFromColumnIndex($ci);
        $v = excel_cell_money($sheet, $L.$r);
        if ($v !== null && abs($v) > 0.00001) {
            return abs((float) $v);
        }
    }

    return null;
}

/**
 * Suma por día desde bloques "TRANSFERENCIAS RECIBIDAS" (CUIT en detalle como 30999062144; cabecera 30-99906214/4).
 *
 * @return array<string, array<int, float>>
 */
function banksheet_aggregate_transfer_recibidas_sections(
    Worksheet $sheet,
    ?string $headerCuit,
    bool $soloCoincideCuitLocalidad
): array {
    $by = [];
    $cuitDigits = pdf_import_normalize_cuit_digits($headerCuit);
    if ($soloCoincideCuitLocalidad && ($cuitDigits === null || $cuitDigits === '')) {
        return $by;
    }
    $highestRow = (int) $sheet->getHighestRow();
    $lastCol = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    $titles = [];
    for ($r = 1; $r <= $highestRow; $r++) {
        if (banksheet_is_transferencias_recibidas_title_row($sheet, $r)) {
            $titles[] = $r;
        }
    }
    if ($titles === []) {
        return $by;
    }
    $dedupe = [];
    $nT = count($titles);
    for ($ti = 0; $ti < $nT; $ti++) {
        $titleR = $titles[$ti];
        $nextTitle = ($ti + 1 < $nT) ? $titles[$ti + 1] : null;
        $hardEnd = min($highestRow, $titleR + 400);
        $endR = $nextTitle !== null ? min($nextTitle - 1, $hardEnd) : $hardEnd;
        $hdrRow = $titleR + 1;
        if ($hdrRow > $endR) {
            continue;
        }
        $map = banksheet_tr_rec_map_header($sheet, $hdrRow, $lastCol);
        $dataStart = $titleR + 2;
        $importeCol = $map['importeCol'];
        $fechaCol = $map['fechaCol'];
        $detalleCol = $map['detalleCol'];
        $comprobCol = $map['comprobCol'];
        $mergedFd = $map['merged_fecha_detalle'];
        $minMoney = max(3, $comprobCol ?? 3);

        for ($r = $dataStart; $r <= $endR; $r++) {
            if (banksheet_tr_rec_row_should_stop($sheet, $r)) {
                break;
            }
            $Lf = Coordinate::stringFromColumnIndex($fechaCol);
            $fecRaw = trim((string) $sheet->getCell($Lf.$r)->getFormattedValue());
            if ($fecRaw === '') {
                continue;
            }
            $concepto = '';
            $comprob = null;
            if ($mergedFd && preg_match('/^(\d{1,2}\/\d{1,2}\/\d{2,4})\s+(.+)$/u', $fecRaw, $mm)) {
                $fecNorm = banksheet_tr_rec_normalize_fecha_slash($mm[1]);
                $concepto = trim($mm[2]);
            } else {
                $fecNorm = excel_normalize_fecha_cell($sheet, $Lf.$r);
                if ($fecNorm === null && preg_match('/^(\d{1,2}\/\d{1,2}\/\d{2,4})\s*$/u', trim($fecRaw), $mm2)) {
                    $fecNorm = banksheet_tr_rec_normalize_fecha_slash($mm2[1]);
                }
                if ($fecNorm === null) {
                    continue;
                }
                $Ld = Coordinate::stringFromColumnIndex($detalleCol);
                $concepto = trim((string) $sheet->getCell($Ld.$r)->getFormattedValue());
            }
            if ($fecNorm === null || $concepto === '') {
                continue;
            }
            if ($comprobCol !== null) {
                $Lc = Coordinate::stringFromColumnIndex($comprobCol);
                $compStr = trim((string) $sheet->getCell($Lc.$r)->getFormattedValue());
                $comprob = $compStr !== '' ? $compStr : null;
            }
            if (stripos($concepto, 'fec.mov') !== false && stripos($concepto, 'detalle') !== false) {
                continue;
            }
            if ($soloCoincideCuitLocalidad && !pdf_import_concept_area_contains_cuit($concepto, $comprob, (string) $cuitDigits)) {
                continue;
            }
            $imp = banksheet_tr_rec_row_importe($sheet, $r, $lastCol, $importeCol, $minMoney);
            if ($imp === null || $imp <= 0) {
                continue;
            }
            $fd = pdf_import_fecha_transfer_to_ym_day($fecNorm);
            if ($fd === null) {
                continue;
            }
            $sig = $fecNorm."\x1F".preg_replace('/\s+/u', ' ', $concepto)."\x1F".($comprob ?? '')."\x1F".sprintf('%.4F', $imp);
            if (isset($dedupe[$sig])) {
                continue;
            }
            $dedupe[$sig] = true;
            $ym = $fd['ym'];
            $d = $fd['day'];
            if (!isset($by[$ym][$d])) {
                $by[$ym][$d] = 0.0;
            }
            $by[$ym][$d] += $imp;
        }
    }

    return $by;
}

function banksheet_tr_rec_normalize_fecha_slash(string $s): ?string
{
    $s = trim($s);
    if (!preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2}|\d{4})$/', $s, $g)) {
        return null;
    }
    $d = (int) $g[1];
    $mo = (int) $g[2];
    $yRaw = $g[3];
    if (strlen($yRaw) === 4) {
        $yi = (int) $yRaw % 100;
    } else {
        $yi = (int) $yRaw;
    }

    return sprintf('%02d/%02d/%02d', $d, $mo, $yi);
}

/**
 * @param array<string, array<int, float>> $a
 * @param array<string, array<int, float>> $b
 *
 * @return array<string, array<int, float>>
 */
function banksheet_merge_transfer_sums_by_day(array $a, array $b): array
{
    foreach ($b as $ym => $days) {
        if (!is_array($days)) {
            continue;
        }
        foreach ($days as $day => $v) {
            if (!is_numeric($v)) {
                continue;
            }
            if (!isset($a[$ym])) {
                $a[$ym] = [];
            }
            if (!isset($a[$ym][$day])) {
                $a[$ym][$day] = 0.0;
            }
            $a[$ym][$day] += (float) $v;
        }
    }

    return $a;
}

/**
 * Fecha tipo "al 04/08/2025" o celda solo "04/08/2025" junto a "Saldo final".
 *
 * @return ?array{ym: string, day: int}
 */
function banksheet_resumen_parse_fecha_al(string $t): ?array
{
    $t = trim($t);
    if ($t === '') {
        return null;
    }
    $fecha = null;
    if (preg_match('/al\s*(\d{1,2}\/\d{1,2}\/\d{4})\b/ui', $t, $m)) {
        $fecha = $m[1];
    } elseif (preg_match('/al\s*(\d{1,2}\/\d{1,2}\/\d{2})\b/ui', $t, $m)) {
        $fecha = $m[1];
    } elseif (preg_match('/^(\d{1,2}\/\d{1,2}\/\d{4})$/', $t, $m)) {
        $fecha = $m[1];
    } elseif (preg_match('/^(\d{1,2}\/\d{1,2}\/\d{2})$/', $t, $m)) {
        $fecha = $m[1];
    }
    if ($fecha === null || !preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2}|\d{4})$/', $fecha, $g)) {
        return null;
    }
    $d = (int) $g[1];
    $mo = (int) $g[2];
    $yRaw = $g[3];
    if (strlen($yRaw) === 4) {
        $year = (int) $yRaw;
    } else {
        $yi = (int) $yRaw;
        $year = $yi >= 70 ? 1900 + $yi : 2000 + $yi;
    }
    if ($d < 1 || $d > 31 || $mo < 1 || $mo > 12) {
        return null;
    }

    return ['ym' => sprintf('%04d-%02d', $year, $mo), 'day' => $d];
}

function banksheet_resumen_section_end_row(int $anchorR, ?int $nextAnchorR, int $highestRow, int $maxSpan = 55): int
{
    $cap = $anchorR + $maxSpan;
    if ($nextAnchorR !== null) {
        return min($cap, $nextAnchorR - 1, $highestRow);
    }

    return min($cap, $highestRow);
}

function banksheet_first_money_in_row(Worksheet $sheet, int $row, int $lastColIdx, int $minColIdx = 1): ?float
{
    for ($ci = $minColIdx; $ci <= $lastColIdx; $ci++) {
        $L = Coordinate::stringFromColumnIndex($ci);
        $t = trim((string) $sheet->getCell($L.$row)->getFormattedValue());
        if ($t !== '' && preg_match('/^\d{1,2}\/\d{1,2}\/\d{2,4}$/', $t)) {
            continue;
        }
        if ($t !== '' && preg_match('/saldo\s*final|total(?:es)?\s+d[eé]bito|total(?:es)?\s+cr[eé]dito/ui', $t)) {
            continue;
        }
        $v = excel_cell_money($sheet, $L.$row);
        if ($v !== null && abs($v) > 0.00001) {
            return abs($v);
        }
    }

    return null;
}

/**
 * Importe en la fila de valores bajo un encabezado de columna (p. ej. "Total Créditos" en F pero monto en E).
 * Orden: misma columna primero; luego -1/+1 para desalineos del PDF/Excel.
 */
function banksheet_money_in_row_under_header(
    Worksheet $sheet,
    int $headerColIdx,
    int $valueRow,
    int $lastColIdx
): ?float {
    foreach ([0, -1, 1, 2, -2] as $off) {
        $cj = $headerColIdx + $off;
        if ($cj < 1 || $cj > $lastColIdx) {
            continue;
        }
        $Lj = Coordinate::stringFromColumnIndex($cj);
        $v = excel_cell_money($sheet, $Lj.$valueRow);
        if ($v !== null && abs($v) > 0.00001) {
            return abs($v);
        }
    }

    return null;
}

function banksheet_find_total_money_below_label(
    Worksheet $sheet,
    int $startSearchFromRow,
    int $endRow,
    int $lastColIdx,
    string $labelRegex
): ?float {
    for ($r = $startSearchFromRow; $r <= $endRow; $r++) {
        for ($ci = 1; $ci <= $lastColIdx; $ci++) {
            $L = Coordinate::stringFromColumnIndex($ci);
            $raw = trim((string) $sheet->getCell($L.$r)->getFormattedValue());
            if ($raw === '' || mb_strlen($raw) > 100) {
                continue;
            }
            if (!preg_match($labelRegex, $raw)) {
                continue;
            }
            $valueRow = $r + 1;
            if ($valueRow > $endRow) {
                continue;
            }
            // No usar la fila de valores desde col. 1: suele ser "Saldo inicial" (p. ej. C303) antes que débitos/créditos.
            $v = banksheet_money_in_row_under_header($sheet, $ci, $valueRow, $lastColIdx);
            if ($v !== null) {
                return $v;
            }

            return banksheet_first_money_in_row($sheet, $valueRow, $lastColIdx, $ci);
        }
    }

    return null;
}

function banksheet_find_saldo_monto_near_anchor(
    Worksheet $sheet,
    int $anchorR,
    int $dateColIdx,
    int $lastColIdx
): ?float {
    $right = banksheet_first_money_in_row($sheet, $anchorR, $lastColIdx, $dateColIdx + 1);
    if ($right !== null) {
        return $right;
    }

    return banksheet_first_money_in_row($sheet, $anchorR + 1, $lastColIdx, 1);
}

/**
 * Bloques resumen por día: "Saldo final" + fecha "al dd/mm/aaaa" en la fila; más abajo "Total Débitos" / "Total Créditos"
 * con el importe en la fila siguiente.
 *
 * @return list<array{ym: string, day: int, debitos: ?float, creditos: ?float, saldo_final: ?float}>
 */
function banksheet_extract_excel_day_summaries(Worksheet $sheet): array
{
    $highestRow = (int) $sheet->getHighestRow();
    if ($highestRow < 3) {
        return [];
    }
    $lastCol = Coordinate::columnIndexFromString($sheet->getHighestColumn());
    $anchors = [];
    for ($r = 1; $r <= $highestRow; $r++) {
        for ($ci = 1; $ci <= $lastCol; $ci++) {
            $L = Coordinate::stringFromColumnIndex($ci);
            $raw = trim((string) $sheet->getCell($L.$r)->getFormattedValue());
            if ($raw === '' || mb_strlen($raw) > 120) {
                continue;
            }
            if (!preg_match('/saldo\s*final/ui', $raw)) {
                continue;
            }
            $parsedDate = null;
            $dateColIdx = null;
            for ($c2 = $ci + 1; $c2 <= min($ci + 14, $lastCol); $c2++) {
                $L2 = Coordinate::stringFromColumnIndex($c2);
                $t2 = trim((string) $sheet->getCell($L2.$r)->getFormattedValue());
                $parsedDate = banksheet_resumen_parse_fecha_al($t2);
                if ($parsedDate !== null) {
                    $dateColIdx = $c2;
                    break;
                }
            }
            if ($parsedDate === null) {
                $parsedDate = banksheet_resumen_parse_fecha_al($raw);
                if ($parsedDate !== null) {
                    $dateColIdx = $ci;
                }
            }
            if ($parsedDate === null) {
                continue;
            }
            $anchors[] = [
                'r'         => $r,
                'dateCol'   => $dateColIdx ?? ($ci + 1),
                'ym'        => $parsedDate['ym'],
                'day'       => $parsedDate['day'],
            ];
            break;
        }
    }
    if ($anchors === []) {
        return [];
    }
    usort($anchors, static function (array $a, array $b): int {
        return $a['r'] <=> $b['r'];
    });
    $out = [];
    $n = count($anchors);
    for ($i = 0; $i < $n; $i++) {
        $ar = $anchors[$i];
        $nextR = ($i + 1 < $n) ? (int) $anchors[$i + 1]['r'] : null;
        $endRow = banksheet_resumen_section_end_row((int) $ar['r'], $nextR, $highestRow);
        $searchFrom = (int) $ar['r'] + 1;
        $totDeb = banksheet_find_total_money_below_label(
            $sheet,
            $searchFrom,
            $endRow,
            $lastCol,
            '/total(?:es)?\s+d[eé]bitos?\b/ui'
        );
        $totCre = banksheet_find_total_money_below_label(
            $sheet,
            $searchFrom,
            $endRow,
            $lastCol,
            '/total(?:es)?\s+cr[eé]ditos?\b/ui'
        );
        $saldo = banksheet_find_saldo_monto_near_anchor($sheet, (int) $ar['r'], (int) $ar['dateCol'], $lastCol);
        $row = [
            'ym'          => $ar['ym'],
            'day'         => (int) $ar['day'],
            'debitos'     => $totDeb,
            'creditos'    => $totCre,
            'saldo_final' => $saldo,
        ];
        if ($totDeb !== null || $totCre !== null || $saldo !== null) {
            $out[] = $row;
        }
    }

    return $out;
}

try {
    $spreadsheet = banksheet_load_spreadsheet($ruta);
} catch (Throwable $e) {
    back_to_index('error', 'No se pudo leer el archivo: '.$e->getMessage());
}

$sheet = $spreadsheet->getActiveSheet();
$importSource = str_ends_with(strtolower($ruta), '.csv') ? 'csv' : 'excel';

$headerRow = excel_find_header_row($sheet);
$colMap = $headerRow !== null ? excel_map_columns($sheet, $headerRow) : null;

$needInfer = ($colMap === null || !isset($colMap['fecha'], $colMap['concepto']));
if (!$needInfer && !isset($colMap['debito']) && !isset($colMap['credito'])) {
    $needInfer = true;
}

if ($needInfer) {
    $inferred = banksheet_infer_positional_layout($sheet);
    if ($inferred === null) {
        back_to_index(
            'error',
            'No se reconoci\xC3\xB3 el formato. Para Excel: fila con columnas Fecha y Concepto. Para CSV de extracto: columnas fecha, concepto, comprobante, d\xC3\xA9bitos, cr\xC3\xA9ditos y saldo (m\xC3\xADnimo hasta la columna F).'
        );
    }
    $colMap = $inferred['colMap'];
    $headerRow = $inferred['headerRow'];
}

if ($headerRow === null || $colMap === null) {
    back_to_index('error', 'No se pudieron detectar las columnas del movimiento.');
}

$header = excel_extract_bank_header($sheet, $headerRow);

$highestRow = (int) $sheet->getHighestRow();
$dataStartRow = excel_find_first_data_row($sheet, $headerRow, $colMap);
$firstRowBySig = banksheet_build_first_row_by_signature($sheet, $dataStartRow, $highestRow, $colMap);

$movimientos = [];

for ($r = $dataStartRow; $r <= $highestRow; $r++) {
    $concepto = trim((string) $sheet->getCell($colMap['concepto'].$r)->getValue());
    if ($concepto === '') {
        continue;
    }
    if (stripos($concepto, 'SALDO ANTERIOR') !== false) {
        continue;
    }

    $fecNorm = excel_normalize_fecha_cell($sheet, $colMap['fecha'].$r);
    if ($fecNorm === null) {
        continue;
    }

    $deb = isset($colMap['debito']) ? excel_cell_money($sheet, $colMap['debito'].$r) : null;
    $cre = isset($colMap['credito']) ? excel_cell_money($sheet, $colMap['credito'].$r) : null;

    $saldo = null;
    if (isset($colMap['saldo'])) {
        $saldo = excel_cell_money($sheet, $colMap['saldo'].$r);
        if ($saldo === null) {
            $idx = Coordinate::columnIndexFromString($colMap['saldo']);
            if ($idx > 1) {
                $left = Coordinate::stringFromColumnIndex($idx - 1);
                $saldo = excel_cell_money($sheet, $left.$r);
            }
        }
    }

    $comp = isset($colMap['comprob']) ? trim((string) $sheet->getCell($colMap['comprob'].$r)->getValue()) : null;
    if ($comp === '') {
        $comp = null;
    }

    if (($deb === null || abs($deb) < 0.00001) && ($cre === null || abs($cre) < 0.00001)) {
        continue;
    }

    $sig = banksheet_movement_signature_from_parts($fecNorm, $concepto, $comp, $deb, $cre);
    if (!isset($firstRowBySig[$sig]) || $firstRowBySig[$sig] !== $r) {
        continue;
    }

    if ($deb !== null && abs($deb) > 0.00001) {
        $movimientos[] = [
            'fecha'       => $fecNorm,
            'concepto'    => $concepto,
            'comprobante' => $comp,
            'importe'     => abs((float) $deb),
            'saldo'       => $saldo,
            'tipo'        => 'debito',
        ];
    }
    if ($cre !== null && abs($cre) > 0.00001) {
        $movimientos[] = [
            'fecha'       => $fecNorm,
            'concepto'    => $concepto,
            'comprobante' => $comp,
            'importe'     => abs((float) $cre),
            'saldo'       => $saldo,
            'tipo'        => 'credito',
        ];
    }
}

$periodos = [];

$excelDaySummaries = ($importSource === 'excel' || $importSource === 'csv')
    ? banksheet_extract_excel_day_summaries($sheet)
    : [];

$matrixBundle = pdf_import_build_daily_matrix($movimientos);
if ($matrixBundle['months'] === []) {
    $matrixBundle = pdf_import_build_matrix_from_periodos($periodos);
}
if ($excelDaySummaries !== []) {
    $matrixBundle = pdf_import_merge_excel_day_summaries($matrixBundle, $excelDaySummaries);
}

$trfSums = excel_aggregate_transfer_recibidas(
    $sheet,
    $headerRow,
    $colMap,
    $header['cuit'] ?? null,
    (bool) $transferRecibidasSoloCuitLocalidad,
    $firstRowBySig,
    $dataStartRow
);
$trfFromSections = banksheet_aggregate_transfer_recibidas_sections(
    $sheet,
    $header['cuit'] ?? null,
    (bool) $transferRecibidasSoloCuitLocalidad
);
$trfSums = banksheet_merge_transfer_sums_by_day($trfSums, $trfFromSections);
$matrixBundle = pdf_import_enrich_matrix_transfer_recibidas($matrixBundle, $trfSums);

$why = 'Fuente='.($importSource === 'csv' ? 'CSV' : 'Excel')."\n";
$why .= 'Titular='.($header['titular'] ?? '(n/d)').' | CUIT='.($header['cuit'] ?? '(n/d)').' | CBU='.($header['cbu'] ?? '(n/d)')."\n";
$why .= 'Movimientos='.count($movimientos)."\n";

$pdfTableToken = null;
try {
    $pdfTableToken = pdf_import_save_cache($destDir, [
        'header'       => $header,
        'periodos'     => $periodos,
        'matrix'       => $matrixBundle,
        'titular_info' => [
            'titular' => $header['titular'],
            'cuit'    => $header['cuit'],
            'cbu'     => $header['cbu'],
        ],
        'transfer_recibidas_solo_cuit_localidad' => $transferRecibidasSoloCuitLocalidad,
        'movimientos_count'                      => count($movimientos),
        'import_source'                          => $importSource,
    ]);
    $_SESSION['pdf_import_token'] = $pdfTableToken;
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
} catch (Throwable $e) {
    back_to_index('error', 'No se pudo guardar la importaci\xC3\xB3n: '.$e->getMessage());
}

if (!DEBUG_IMPORT && $pdfTableToken !== null) {
    header('Location: resultado.php', true, 303);
    exit;
}

if (DEBUG_IMPORT) {
    echo '<h3>DEBUG IMPORT EXCEL</h3><pre>'.htmlspecialchars($why).'</pre>';
    echo '<p><a href="resultado.php">Ver resultado</a></p>';
    exit;
}

header('Location: ../importar_pdf.php?import_status='.rawurlencode('success'));
exit;
