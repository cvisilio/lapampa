<?php

declare(strict_types=1);

/**
 * Matriz día (1–31) × mes (año-mes) con Ubicar, débitos, créditos, transferencias recibidas y saldo final del día.
 */

/** Mismo criterio que money_to_float en import.php (autónomo para este archivo). */
function pdf_import_money_to_float(?string $s): ?float
{
    if ($s === null) {
        return null;
    }
    $s = trim($s);
    if ($s === '') {
        return null;
    }
    $s = preg_replace('/[^\d\-\.,]/u', '', $s);
    $lastDot = strrpos($s, '.');
    $lastCom = strrpos($s, ',');
    $decimalSep = null;

    if ($lastDot !== false && $lastCom !== false) {
        $rightDot = substr($s, $lastDot + 1);
        $rightCom = substr($s, $lastCom + 1);
        $isDotDec = preg_match('/^\d{1,2}$/', $rightDot);
        $isComDec = preg_match('/^\d{1,2}$/', $rightCom);
        if ($isDotDec && !$isComDec) {
            $decimalSep = '.';
        } elseif ($isComDec && !$isDotDec) {
            $decimalSep = ',';
        } else {
            $decimalSep = ($lastDot > $lastCom) ? '.' : ',';
        }
    } elseif ($lastDot !== false) {
        $decimalSep = preg_match('/^\d{1,2}$/', substr($s, $lastDot + 1)) ? '.' : null;
    } elseif ($lastCom !== false) {
        $decimalSep = preg_match('/^\d{1,2}$/', substr($s, $lastCom + 1)) ? ',' : null;
    }

    if ($decimalSep === ',') {
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    } elseif ($decimalSep === '.') {
        $s = str_replace(',', '', $s);
    } else {
        $s = preg_replace('/[^\d\-]/', '', $s);
    }

    return is_numeric($s) ? (float) $s : null;
}

/**
 * CUIT del encabezado (ej. 30-99906214/4) -> solo dígitos para comparar con el PDF (30999062144).
 */
function pdf_import_normalize_cuit_digits(?string $cuit): ?string
{
    if ($cuit === null || trim($cuit) === '') {
        return null;
    }
    $d = preg_replace('/\D/', '', $cuit);
    if ($d === '') {
        return null;
    }
    if (strlen($d) === 11) {
        return $d;
    }
    if (strlen($d) > 11) {
        return substr($d, 0, 11);
    }

    return null;
}

/**
 * ¿El texto de concepto/comprobante (columnas del extracto, sin fecha ni importes/saldo) trae el CUIT del titular?
 * No se usa la línea completa: juntar todos los dígitos de la fila hacía falsos positivos (fecha + montos).
 */
function pdf_import_concept_area_contains_cuit(string $concept, ?string $comprobante, string $cuitDigits): bool
{
    if ($cuitDigits === '') {
        return false;
    }
    $hay = trim($concept . ' ' . ($comprobante ?? ''));
    if ($hay === '') {
        return false;
    }
    // PDF a veces separa dígitos del CUIT con espacios: "30 99 906214 4"
    $hayCompactDigits = preg_replace('/(\d)\s+(?=\d)/u', '$1', $hay) ?? $hay;
    foreach ([$hay, $hayCompactDigits] as $probe) {
        if ($probe !== '' && str_contains($probe, $cuitDigits)) {
            return true;
        }
    }
    $hay = $hayCompactDigits;
    // Formato típico: 20-12345678-9, 30-99906214/4, con puntos, etc.
    if (preg_match_all('/\b\d{2}[-\/.]?\d{8,9}[-\/.]?\d\b/u', $hay, $m)) {
        foreach ($m[0] as $tok) {
            if (pdf_import_normalize_cuit_digits($tok) === $cuitDigits) {
                return true;
            }
        }
    }
    // 11 dígitos seguidos, sin formar parte de un número más largo (evita CBU / montos pegados)
    if (preg_match_all('/(?<![0-9])(\d{11})(?![0-9])/u', $hay, $m2)) {
        foreach ($m2[1] as $blk) {
            if ($blk === $cuitDigits) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Une renglones partidos por el extractor de PDF (fecha al inicio de línea = nuevo movimiento).
 */
function pdf_import_chunk_merge_wrapped_lines(string $chunk): string
{
    $rawLines = preg_split('/\R+/u', $chunk) ?: [];
    $out = [];
    $buf = '';
    foreach ($rawLines as $ln) {
        $ln = trim($ln);
        if ($ln === '') {
            continue;
        }
        if (preg_match('/^\d{2}\/\d{2}\/(?:\d{2}|\d{4})\b/u', $ln)) {
            if ($buf !== '') {
                $out[] = $buf;
            }
            $buf = $ln;
        } else {
            $buf .= ($buf === '' ? '' : ' ') . $ln;
        }
    }
    if ($buf !== '') {
        $out[] = $buf;
    }

    return implode("\n", $out);
}

function pdf_import_transfer_line_is_table_header(string $ln): bool
{
    $t = mb_strtoupper(trim($ln), 'UTF-8');
    if ($t === '' || mb_strlen($t, 'UTF-8') > 120) {
        return false;
    }
    if (preg_match('/^(FECHA|COMPROBANTE|IMPORTE|SALDO|DEBITO|DEBITOS|CREDITO|CREDITOS|MOVIMIENTOS?)\b/u', $t)) {
        return true;
    }
    if (str_contains($t, 'CONCEPTO') && (str_contains($t, 'IMPORTE') || str_contains($t, 'SALDO'))) {
        return true;
    }

    return false;
}

/**
 * dd/mm/yy o dd/mm/aaaa -> [ ym => 'Y-m', day => int ] o null.
 *
 * @return ?array{ym: string, day: int}
 */
function pdf_import_fecha_transfer_to_ym_day(string $fecha): ?array
{
    if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{2}|\d{4})$/', $fecha, $g)) {
        return null;
    }
    $d = (int) $g[1];
    $mo = (int) $g[2];
    $yy = $g[3];
    if (strlen($yy) === 4) {
        $year = (int) $yy;
    } else {
        $y2 = (int) $yy;
        $year = $y2 >= 70 ? (1900 + $y2) : (2000 + $y2);
    }
    if ($d < 1 || $d > 31 || $mo < 1 || $mo > 12) {
        return null;
    }

    return ['ym' => sprintf('%04d-%02d', $year, $mo), 'day' => $d];
}

/**
 * Importe en extractos Banco La Pampa (y similares): siempre con centavos .XX o ,XX.
 * Los comprobantes tipo 000050851 NO deben interpretarse como dinero.
 */
function pdf_import_is_peso_amount_token(string $s): bool
{
    $s = trim($s);
    if ($s === '') {
        return false;
    }
    // Formato AR: 1.234,56
    if (preg_match('/^[\-]?\d{1,3}(?:\.\d{3})*,\d{2}$/u', $s)) {
        return true;
    }
    // Formato con punto decimal y miles por punto: 1234.56 o 1.234.567.89 (legado extractor)
    if (preg_match('/^[\-]?\d+(?:\.\d{3})*\.\d{2}$/u', $s)) {
        return true;
    }
    // Formato US: 245,633.23
    if (preg_match('/^[\-]?\d{1,3}(?:,\d{3})+\.\d{2}$/u', $s)) {
        return true;
    }

    return false;
}

/**
 * Una línea ya unida: fecha + texto + importe [+ saldo]. Parseo desde la derecha.
 *
 * @return ?array{fecha: string, concepto: string, comprobante: ?string, importe_str: string}
 */
function pdf_import_parse_transfer_movimiento_line(string $line): ?array
{
    if (!preg_match('/^(\d{2}\/\d{2}\/(?:\d{2}|\d{4}))\s+(.+)$/u', trim($line), $head)) {
        return null;
    }
    $fecha = $head[1];
    $rest = rtrim($head[2]);

    // Monto con centavos obligatorios (evita tomar 000050851 como importe)
    $peso = '[\-]?\d{1,3}(?:\.\d{3})*,\d{2}|[\-]?\d+(?:\.\d{3})*\.\d{2}|[\-]?\d{1,3}(?:,\d{3})+\.\d{2}';
    $two = '/^(.+)\s+(' . $peso . ')\s+(' . $peso . ')\s*$/u';
    $one = '/^(.+)\s+(' . $peso . ')\s*$/u';

    if (preg_match($two, $rest, $m) && pdf_import_is_peso_amount_token($m[2]) && pdf_import_is_peso_amount_token($m[3])) {
        $before = trim($m[1]);
        $importeStr = $m[2];
    } elseif (preg_match($one, $rest, $m) && pdf_import_is_peso_amount_token($m[2])) {
        $before = trim($m[1]);
        $importeStr = $m[2];
    } else {
        return null;
    }

    $comprob = null;
    $concept = $before;
    // Último bloque solo dígitos (comprob.) antes del importe; puede haber token alfanum (COELSA) en el medio
    if (preg_match('/^(.*)\s+(\d{4,})$/u', $before, $mb)) {
        $tail = $mb[2];
        if (!(strlen($tail) === 11 && ctype_digit($tail))) {
            $concept = trim($mb[1]);
            $comprob = $tail;
        }
    }

    return [
        'fecha' => $fecha,
        'concepto' => $concept,
        'comprobante' => $comprob,
        'importe_str' => $importeStr,
    ];
}

/**
 * Suma por día/mes del importe en líneas tipo movimiento dentro del texto ya recortado a la sección.
 *
 * @param bool $soloCoincideCuitLocalidad true: solo importes cuyo concepto/comprobante incluya el CUIT del titular;
 *                                        false: todas las líneas del bloque por día.
 * @param array<string, true>|null $dedupeSignatures Si no es null, no suma dos veces la misma fila (fecha|comprob|importe).
 * @return array<string, array<int, float>> [ 'Y-m' => [ day => sum ] ]
 */
function pdf_import_sum_transfer_recibidas_from_chunk(string $chunk, ?string $cuitDigits, bool $soloCoincideCuitLocalidad = true, ?array &$dedupeSignatures = null): array
{
    $by = [];
    $cuitOk = $cuitDigits !== null && $cuitDigits !== '';
    if ($soloCoincideCuitLocalidad) {
        if (!$cuitOk) {
            return $by;
        }
        $mustMatchCuit = true;
    } else {
        $mustMatchCuit = false;
    }

    $merged = pdf_import_chunk_merge_wrapped_lines($chunk);
    $lines = preg_split('/\R+/u', $merged) ?: [];
    $pesoLegacy = '[\-]?\d{1,3}(?:\.\d{3})*,\d{2}|[\-]?\d+(?:\.\d{3})*\.\d{2}|[\-]?\d{1,3}(?:,\d{3})+\.\d{2}';
    $lineRegexLegacy = '/^(\d{2}\/\d{2}\/(?:\d{2}|\d{4}))\s+(.+?)\s+(?:([0-9]{4,})\s+)?(' . $pesoLegacy . ')\s+(' . $pesoLegacy . ')$/mu';
    $lineRegexLegacyOne = '/^(\d{2}\/\d{2}\/(?:\d{2}|\d{4}))\s+(.+?)\s+(?:([0-9]{4,})\s+)?(' . $pesoLegacy . ')$/mu';

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || pdf_import_transfer_line_is_table_header($line)) {
            continue;
        }

        $parsed = pdf_import_parse_transfer_movimiento_line($line);
        if ($parsed === null && preg_match($lineRegexLegacy, $line, $row)) {
            $parsed = [
                'fecha' => $row[1],
                'concepto' => trim($row[2]),
                'comprobante' => (isset($row[3]) && $row[3] !== '') ? trim((string) $row[3]) : null,
                'importe_str' => $row[4],
            ];
        }
        if ($parsed === null && preg_match($lineRegexLegacyOne, $line, $row)) {
            $parsed = [
                'fecha' => $row[1],
                'concepto' => trim($row[2]),
                'comprobante' => (isset($row[3]) && $row[3] !== '') ? trim((string) $row[3]) : null,
                'importe_str' => $row[4],
            ];
        }
        if ($parsed === null) {
            continue;
        }

        $comprob = $parsed['comprobante'];
        if ($mustMatchCuit && !pdf_import_concept_area_contains_cuit(trim($parsed['concepto']), $comprob, (string) $cuitDigits)) {
            continue;
        }

        $fd = pdf_import_fecha_transfer_to_ym_day($parsed['fecha']);
        if ($fd === null) {
            continue;
        }
        $imp = pdf_import_money_to_float($parsed['importe_str']);
        if ($imp === null) {
            continue;
        }

        // Misma fila repetida en varias páginas del anexo: normalizar espacios en toda la línea
        $sig = preg_replace('/\s+/u', ' ', trim($line));
        if ($dedupeSignatures !== null) {
            if (isset($dedupeSignatures[$sig])) {
                continue;
            }
            $dedupeSignatures[$sig] = true;
        }

        $ym = $fd['ym'];
        $d = $fd['day'];
        if (!isset($by[$ym][$d])) {
            $by[$ym][$d] = 0.0;
        }
        $by[$ym][$d] += abs($imp);
    }

    return $by;
}

/**
 * Recorre todas las apariciones de "TRANSFERENCIAS RECIBIDAS" en el extracto y acumula importes por día.
 *
 * @param bool $soloCoincideCuitLocalidad Ver pdf_import_sum_transfer_recibidas_from_chunk().
 * @return array<string, array<int, float>>
 */
function pdf_import_aggregate_transfer_recibidas(string $norm, ?string $headerCuit = null, bool $soloCoincideCuitLocalidad = true): array
{
    $merged = [];
    $dedupeSignatures = [];
    $cuitDigits = pdf_import_normalize_cuit_digits($headerCuit);
    $titlePatterns = [
        '/TRANSFERENCIAS\s+RECIBIDAS/ui',
        '/Transferencias\s+recibidas/ui',
    ];
    $titleHits = [];
    foreach ($titlePatterns as $titleRe) {
        if (preg_match_all($titleRe, $norm, $th, PREG_OFFSET_CAPTURE)) {
            foreach ($th[0] as $hit) {
                $titleHits[] = $hit;
            }
        }
    }
    if ($titleHits === []) {
        return $merged;
    }
    usort($titleHits, static function (array $a, array $b): int {
        return ((int) $a[1]) <=> ((int) $b[1]);
    });
    $seenPos = [];
    $dedupHits = [];
    foreach ($titleHits as $hit) {
        $pos = (int) $hit[1];
        $dup = false;
        foreach ($seenPos as $sp) {
            if (abs($pos - $sp) < 8) {
                $dup = true;
                break;
            }
        }
        if ($dup) {
            continue;
        }
        $seenPos[] = $pos;
        $dedupHits[] = $hit;
    }
    $titleHits = $dedupHits;

    $endMarkers = [
        'TRANSFERENCIAS\s+ENVIADAS',
        'TRANSFERENCIAS\s+ENVIADA',
        'TRANSFERENCIAS\s+EMITIDAS',
        'TRANSFERENCIAS\s+EMITIDA',
        'OTROS\s+MOVIMIENTOS',
        'RESUMEN\s+CONSOLIDADO',
        'IVA\s+Total',
        'SALDO\s+ANTERIOR',
        'Saldo\s+final\s+al',
        'TRANSFERENCIAS\s+RECIBIDAS',
        // Banco La Pampa: nueva página repite bloque con "CUIT:" solo en línea
        '\R\s*CUIT\s*:\s*\R',
    ];

    foreach ($titleHits as $hit) {
        $matchText = $hit[0];
        $bytePos = (int) $hit[1];
        $afterTitle = $bytePos + strlen($matchText);
        $sub = substr($norm, $afterTitle);
        $cut = strlen($sub);
        foreach ($endMarkers as $mk) {
            if (!preg_match('/' . $mk . '/ui', $sub, $em, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            $p = (int) $em[0][1];
            // Evitar cortar en un segundo encabezado "TRANSFERENCIAS RECIBIDAS" pegado al primero
            if ($p === 0 && $mk === 'TRANSFERENCIAS\s+RECIBIDAS') {
                continue;
            }
            if ($p < $cut) {
                $cut = $p;
            }
        }
        $chunk = substr($sub, 0, $cut);
        $part = pdf_import_sum_transfer_recibidas_from_chunk($chunk, $cuitDigits, $soloCoincideCuitLocalidad, $dedupeSignatures);
        foreach ($part as $ym => $days) {
            foreach ($days as $d => $val) {
                $merged[$ym][$d] = ($merged[$ym][$d] ?? 0.0) + (float) $val;
            }
        }
    }
    return $merged;
}

/**
 * Incorpora transfer_recibidas a la matriz y unifica meses.
 *
 * @param array{months: list<string>, matrix: array} $bundle
 * @param array<string, array<int, float>> $trfSums
 * @return array{months: list<string>, matrix: array}
 */
function pdf_import_enrich_matrix_transfer_recibidas(array $bundle, array $trfSums): array
{
    $oldMatrix = $bundle['matrix'] ?? [];
    $oldMonths = $bundle['months'] ?? [];
    $allMonths = array_values(array_unique(array_merge($oldMonths, array_keys($trfSums))));
    sort($allMonths, SORT_STRING);

    $matrix = [];
    for ($day = 1; $day <= 31; $day++) {
        $matrix[$day] = [];
        foreach ($allMonths as $ym) {
            $base = $oldMatrix[$day][$ym] ?? [
                'ubicar' => '',
                'debitos' => 0.0,
                'creditos' => 0.0,
                'saldo_final' => null,
                'transfer_recibidas' => 0.0,
            ];
            if (!isset($base['transfer_recibidas'])) {
                $base['transfer_recibidas'] = 0.0;
            }
            $add = isset($trfSums[$ym][$day]) ? (float) $trfSums[$ym][$day] : 0.0;
            $base['transfer_recibidas'] = round((float) $base['transfer_recibidas'] + $add, 2);
            $matrix[$day][$ym] = $base;
        }
    }

    return ['months' => $allMonths, 'matrix' => $matrix];
}

function pdf_import_month_label_es(string $ym): string
{
    static $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];
    $parts = explode('-', $ym);
    if (count($parts) !== 2) {
        return $ym;
    }
    $y = (int) $parts[0];
    $m = (int) $parts[1];
    return ($meses[$m] ?? "Mes $m") . ' ' . $y;
}

/** Símbolo $ adelante; miles con punto y decimal con coma (tablas HTML). */
function pdf_import_format_ar_pesos_html(float $value): string
{
    return '$ ' . number_format($value, 2, ',', '.');
}

/** Símbolo $ adelante; decimal con punto sin separador de miles (export CSV). */
function pdf_import_format_ar_pesos_csv(float $value): string
{
    return '$ ' . number_format($value, 2, '.', '');
}

/**
 * Saldo de cierre mostrado en la fila TOTAL: el saldo_final del último día (1–31) que tenga
 * débitos, créditos o transferencias recibidas en la celda. Evita tomar un saldo “huérfano”
 * en un día alto (p. ej. 31) sin importes en la grilla.
 *
 * @param array<int, array<string, array<string, mixed>>> $matrix
 */
function pdf_import_ultimo_saldo_mes_para_total(array $matrix, string $ym): ?float
{
    $ultimo = null;
    for ($d = 1; $d <= 31; $d++) {
        $c = $matrix[$d][$ym] ?? null;
        if (!is_array($c)) {
            continue;
        }
        $deb = (float) ($c['debitos'] ?? 0);
        $cre = (float) ($c['creditos'] ?? 0);
        $trf = (float) ($c['transfer_recibidas'] ?? 0);
        $tieneImportes = abs($deb) > 0.00001 || abs($cre) > 0.00001 || abs($trf) > 0.00001;
        if (!$tieneImportes) {
            continue;
        }
        if (!array_key_exists('saldo_final', $c) || $c['saldo_final'] === null || $c['saldo_final'] === '') {
            continue;
        }
        $ultimo = (float) $c['saldo_final'];
    }
    if ($ultimo !== null) {
        return $ultimo;
    }
    for ($d = 31; $d >= 1; $d--) {
        $c = $matrix[$d][$ym] ?? null;
        if ($c !== null && array_key_exists('saldo_final', $c) && $c['saldo_final'] !== null && $c['saldo_final'] !== '') {
            return (float) $c['saldo_final'];
        }
    }

    return null;
}

/**
 * @param array<int, array<string, mixed>> $movimientos filas del parser (fecha dd/mm/yy o dd/mm/aaaa)
 * @return array{months: list<string>, matrix: array<int, array<string, array{ubicar:string, debitos:float, creditos:float, saldo_final:?float}>>}
 */
function pdf_import_build_daily_matrix(array $movimientos): array
{
    $byMonthDay = [];

    foreach ($movimientos as $m) {
        $fec = $m['fecha'] ?? '';
        if (!is_string($fec) || !preg_match('/^(\d{2})\/(\d{2})\/(\d{2}|\d{4})$/', $fec, $g)) {
            continue;
        }
        $d = (int) $g[1];
        $mo = (int) $g[2];
        $yyRaw = $g[3];
        if (strlen($yyRaw) === 4) {
            $year = (int) $yyRaw;
        } else {
            $yy = (int) $yyRaw;
            $year = $yy >= 70 ? (1900 + $yy) : (2000 + $yy);
        }
        $ym = sprintf('%04d-%02d', $year, $mo);
        if ($d < 1 || $d > 31) {
            continue;
        }

        if (!isset($byMonthDay[$ym][$d])) {
            $byMonthDay[$ym][$d] = [
                'debitos' => 0.0,
                'creditos' => 0.0,
                'saldo_last' => null,
                'conceptos' => [],
            ];
        }

        $tipo = $m['tipo'] ?? null;
        $importe = $m['importe'] ?? null;
        if ($tipo === null && $importe !== null && is_numeric($importe)) {
            $iv = (float) $importe;
            if ($iv < 0) {
                $tipo = 'debito';
            } elseif ($iv > 0) {
                $tipo = 'credito';
            }
        }
        if ($importe !== null) {
            if ($tipo === 'debito') {
                $byMonthDay[$ym][$d]['debitos'] += abs((float) $importe);
            } elseif ($tipo === 'credito') {
                $byMonthDay[$ym][$d]['creditos'] += abs((float) $importe);
            }
        }

        if (isset($m['saldo']) && $m['saldo'] !== null && is_numeric($m['saldo'])) {
            $byMonthDay[$ym][$d]['saldo_last'] = (float) $m['saldo'];
        }
        if (!empty($m['concepto'])) {
            $byMonthDay[$ym][$d]['conceptos'][] = (string) $m['concepto'];
        }
    }

    $months = array_keys($byMonthDay);
    sort($months, SORT_STRING);

    $matrix = [];
    for ($day = 1; $day <= 31; $day++) {
        $matrix[$day] = [];
        foreach ($months as $ym) {
            if (!isset($byMonthDay[$ym][$day])) {
                $matrix[$day][$ym] = [
                    'ubicar' => '',
                    'debitos' => 0.0,
                    'creditos' => 0.0,
                    'transfer_recibidas' => 0.0,
                    'saldo_final' => null,
                ];
                continue;
            }
            $cell = $byMonthDay[$ym][$day];
            $concepts = array_unique(array_filter($cell['conceptos']));
            $ubicar = implode('; ', $concepts);
            if (strlen($ubicar) > 120) {
                $ubicar = substr($ubicar, 0, 117) . '...';
            }
            $matrix[$day][$ym] = [
                'ubicar' => $ubicar,
                'debitos' => round((float) $cell['debitos'], 2),
                'creditos' => round((float) $cell['creditos'], 2),
                'transfer_recibidas' => 0.0,
                'saldo_final' => $cell['saldo_last'] !== null ? round((float) $cell['saldo_last'], 2) : null,
            ];
        }
    }

    return ['months' => $months, 'matrix' => $matrix];
}

/**
 * Aplica totales leídos del resumen Excel (Saldo final al dd/mm/aaaa + Total Débitos/Créditos en la fila de abajo).
 * Sobrescribe débitos, créditos y saldo_final del día/mes en la matriz cuando vienen informados.
 *
 * @param list<array{ym: string, day: int, debitos: ?float, creditos: ?float, saldo_final: ?float}> $summaries
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

/**
 * Si no hay movimientos, una fila por período del resumen (saldo final al dd/mm/aaaa).
 *
 * @param array<int, array<string, mixed>> $periodos
 * @return array{months: list<string>, matrix: array<int, array<string, array{ubicar:string, debitos:float, creditos:float, saldo_final:?float}>>}
 */
function pdf_import_build_matrix_from_periodos(array $periodos): array
{
    $byMonthDay = [];

    foreach ($periodos as $p) {
        $fecha = $p['fecha'] ?? '';
        if (!is_string($fecha) || !preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $g)) {
            continue;
        }
        $d = (int) $g[1];
        $mo = (int) $g[2];
        $year = (int) $g[3];
        $ym = sprintf('%04d-%02d', $year, $mo);
        if ($d < 1 || $d > 31) {
            continue;
        }

        $byMonthDay[$ym][$d] = [
            'ubicar' => 'Resumen consolidado (PDF)',
            'debitos' => isset($p['total_debitos']) && is_numeric($p['total_debitos']) ? round((float) $p['total_debitos'], 2) : 0.0,
            'creditos' => isset($p['total_creditos']) && is_numeric($p['total_creditos']) ? round((float) $p['total_creditos'], 2) : 0.0,
            'transfer_recibidas' => 0.0,
            'saldo_final' => isset($p['saldo_final']) && is_numeric($p['saldo_final']) ? round((float) $p['saldo_final'], 2) : null,
        ];
    }

    $months = array_keys($byMonthDay);
    sort($months, SORT_STRING);

    $matrix = [];
    for ($day = 1; $day <= 31; $day++) {
        $matrix[$day] = [];
        foreach ($months as $ym) {
            $matrix[$day][$ym] = $byMonthDay[$ym][$day] ?? [
                'ubicar' => '',
                'debitos' => 0.0,
                'creditos' => 0.0,
                'transfer_recibidas' => 0.0,
                'saldo_final' => null,
            ];
        }
    }

    return ['months' => $months, 'matrix' => $matrix];
}

/**
 * @param array<string, mixed> $payload
 */
function pdf_import_save_cache(string $destDir, array $payload): string
{
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }
    $token = bin2hex(random_bytes(12));
    $path = $destDir . '/import_cache_' . $token . '.json';
    $payload['_saved_at'] = time();
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('json_encode fallo');
    }
    file_put_contents($path, $json);
    return $token;
}

/**
 * @return ?array<string, mixed>
 */
function pdf_import_load_cache(string $destDir, string $token): ?array
{
    if ($token === '' || !preg_match('/^[a-f0-9]{24}$/', $token)) {
        return null;
    }
    $path = $destDir . '/import_cache_' . $token . '.json';
    if (!is_readable($path)) {
        return null;
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        return null;
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return null;
    }
    if (isset($data['matrix']['matrix']) && is_array($data['matrix']['matrix'])) {
        $fixed = [];
        foreach ($data['matrix']['matrix'] as $d => $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($row as $ym => $c) {
                $fixed[(int) $d][$ym] = $c;
            }
        }
        $data['matrix']['matrix'] = $fixed;
    }
    return $data;
}
