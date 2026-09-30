<?php

/**
 * Procesamiento de extracto bancario en PDF (invocado desde import.php).
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('DEBUG_IMPORT')) {
    define('DEBUG_IMPORT', false);
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/pdf_import_matrix.php';

function normalize_ws(string $s): string {
    // normaliza NBSP, zero-width y tabs
    $s = str_replace(["\xC2\xA0", "\xE2\x80\x8B", "\xE2\x80\x8C", "\xE2\x80\x8D", "\t"], ' ', $s);
    // colapsa espacios m�ltiples y normaliza saltos
    $s = preg_replace('/[ ]{2,}/u', ' ', $s);
    $s = preg_replace('/\R+/u', "\n", $s);
    // limpia espacios a fin de l�nea para ayudar a los $ parsers de columna
    $s = preg_replace('/[ \t]+$/mu', '', $s);
    return trim($s);
}


/** Convertir "43.040,17" o "43,040.17" a float */
function money_to_float(?string $s): ?float {
    if ($s === null) return null;
    $s = trim($s);
    if ($s === '') return null;
    $s = preg_replace('/[^\d\-\.,]/u', '', $s);
    $lastDot = strrpos($s, '.'); $lastCom = strrpos($s, ','); $decimalSep = null;

    if ($lastDot !== false && $lastCom !== false) {
        $rightDot = substr($s, $lastDot + 1);
        $rightCom = substr($s, $lastCom + 1);
        $isDotDec = preg_match('/^\d{1,2}$/', $rightDot);
        $isComDec = preg_match('/^\d{1,2}$/', $rightCom);
        if     ($isDotDec && !$isComDec) $decimalSep = '.';
        elseif ($isComDec && !$isDotDec) $decimalSep = ',';
        else                              $decimalSep = ($lastDot > $lastCom) ? '.' : ',';
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
    return is_numeric($s) ? (float)$s : null;
}

/**
 * Extrae texto con Smalot PdfParser.
 * Si $ignoreEncryption es true, usa setIgnoreEncryption (workaround oficial, deprecado) para PDFs con /Encrypt
 * que en la pr�ctica no llevan streams cifrados; no sustituye contrase�a ni descifrado real.
 */
function smalot_pdf_text_from_path(string $ruta, bool $ignoreEncryption = false): string
{
    if ($ignoreEncryption) {
        $config = new \Smalot\PdfParser\Config();
        $config->setIgnoreEncryption(true);
        $parser = new \Smalot\PdfParser\Parser([], $config);
    } else {
        $parser = new \Smalot\PdfParser\Parser();
    }
    $pdf  = $parser->parseFile($ruta);
    $text = (string) $pdf->getText();

    if (mb_strlen(trim($text), 'UTF-8') < 20) {
        $pages = $pdf->getPages();
        if (!empty($pages)) {
            $buf = [];
            foreach ($pages as $p) {
                $buf[] = $p->getText();
            }
            $concat = trim(implode("\n", $buf));
            if (mb_strlen($concat, 'UTF-8') > mb_strlen($text, 'UTF-8')) {
                $text = $concat;
            }
        }
    }

    return $text;
}

/**
 * Cada aparicion de "Resumen Consolidado ... Saldo Final" y los 4 montos (inicial, debitos, creditos, final).
 * Los importes pueden ir en la misma linea o en la siguiente (muy comun en extractos PDF).
 * Patrones solo ASCII; D.bitos / Cr.ditos admiten la vocal acentuada aunque el PDF venga en Latin-1.
 */
function find_resumenes_consolidado_rows(string $chunk): array {
    $header = '/Resumen\s+Consolidado\s+Saldo\s+Inicial\s+Total\s+(?:D.bitos|Debitos)\s+Total\s+(?:Cr.ditos|Creditos)\s+Saldo\s+Final/miu';
    $rows = [];
    $p = 0;
    while (preg_match($header, $chunk, $mh, PREG_OFFSET_CAPTURE, $p)) {
        $abs = (int) $mh[0][1];
        $end = $abs + strlen($mh[0][0]);
        $rest = substr($chunk, $end);
        if (preg_match('/^\s*(?:\R\s*){0,5}?([\-]?[\d\.,]+)\s+([\-]?[\d\.,]+)\s+([\-]?[\d\.,]+)\s+([\-]?[\d\.,]+)/u', $rest, $ma)) {
            $rows[] = [
                'inicial'  => trim($ma[1]),
                'debitos'  => trim($ma[2]),
                'creditos' => trim($ma[3]),
                'final'    => trim($ma[4]),
            ];
        }
        $p = $abs + 1;
    }
    return $rows;
}

/**
 * Localiza el intérprete Python (portable del proyecto o del sistema).
 */
function pdf_import_find_python(): ?string
{
    $candidates = [
        __DIR__ . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'python' . DIRECTORY_SEPARATOR . 'python.exe',
        __DIR__ . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'python' . DIRECTORY_SEPARATOR . 'python',
    ];
    foreach ($candidates as $bin) {
        if (is_file($bin)) {
            return $bin;
        }
    }

    // PATH del sistema
    $whichCmd = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'command -v';
    foreach (['python', 'python3', 'py'] as $cmd) {
        $out = [];
        $code = 1;
        @exec($whichCmd . ' ' . escapeshellarg($cmd) . ' 2>NUL', $out, $code);
        if ($code === 0 && !empty($out[0]) && is_file(trim($out[0]))) {
            return trim($out[0]);
        }
    }

    return null;
}

/**
 * Usa tools/unlock_pdf.py (pypdf) para generar una copia sin /Encrypt.
 * Devuelve la ruta del PDF liberado o null si falló.
 */
function pdf_import_unlock_with_python(string $srcPdf, string $destDir, string $password = ''): ?string
{
    $python = pdf_import_find_python();
    $script = __DIR__ . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'unlock_pdf.py';
    if ($python === null || !is_file($script) || !is_readable($srcPdf)) {
        return null;
    }
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }

    $base = pathinfo($srcPdf, PATHINFO_FILENAME);
    $out = rtrim($destDir, '/\\') . DIRECTORY_SEPARATOR . $base . '__unlocked_' . date('Ymd_His') . '.pdf';

    $cmd = escapeshellarg($python) . ' ' . escapeshellarg($script) . ' '
        . escapeshellarg($srcPdf) . ' ' . escapeshellarg($out);
    if ($password !== '') {
        $cmd .= ' --password ' . escapeshellarg($password);
    }
    $cmd .= ' 2>&1';

    $lines = [];
    $code = 1;
    @exec($cmd, $lines, $code);
    if ($code !== 0 || !is_file($out) || filesize($out) < 64) {
        return null;
    }

    return $out;
}

/* ====== 5) Extraer texto con Smalot ====== */
if (!class_exists('\Smalot\PdfParser\Parser')) {
    back_to_index('error', 'smalot/pdfparser no disponible (composer).');
}

$text = '';
$pdfUnlockTried = false;
$pdfUnlockPath = null;
$pdfPassword = isset($_POST['pdf_password']) ? trim((string) $_POST['pdf_password']) : '';

try {
    $text = smalot_pdf_text_from_path($ruta, false);
} catch (\Throwable $e) {
    $msg = trim($e->getMessage());

    if (stripos($msg, 'Secured pdf file') !== false) {
        try {
            $text = smalot_pdf_text_from_path($ruta, true);
        } catch (\Throwable $e2) {
            $msg = trim($e2->getMessage());
            // Fallback: liberar con Python (pypdf) y reintentar
            if (stripos($msg, 'Secured pdf file') !== false) {
                $pdfUnlockTried = true;
                $pdfUnlockPath = pdf_import_unlock_with_python($ruta, $destDir, $pdfPassword);
                if ($pdfUnlockPath !== null) {
                    try {
                        $text = smalot_pdf_text_from_path($pdfUnlockPath, false);
                        $ruta = $pdfUnlockPath;
                        $usandoLiberado = true;
                    } catch (\Throwable $e3) {
                        $msg = trim($e3->getMessage());
                    }
                }
            }
            if (trim($text) === '') {
                if (!$usandoLiberado && stripos($msg, 'Secured pdf file') !== false) {
                    $instruccion = "El PDF esta protegido o no se pudo leer el texto.\n"
                        . "Se intento liberarlo con Python y no alcanzo.\n"
                        . "Genera una copia sin restricciones (Guardar como / Imprimir a PDF) y vuelve a subirla.";
                    if ($pdfUnlockTried && $pdfUnlockPath === null) {
                        $instruccion .= "\n(Verifica tools/python + pypdf: pip install -r tools/requirements.txt)";
                    }
                    back_to_index('needs_unsecured', $instruccion);
                }
                back_to_index('error', "Error al parsear PDF (".($usandoLiberado ? 'liberado' : 'original')."): ".$msg);
            }
        }
    } else {
        back_to_index('error', "Error al parsear PDF (".($usandoLiberado ? 'liberado' : 'original')."): ".$msg);
    }
}

if (trim($text) === '') {
    // Ultimo intento: liberar con Python aunque Smalot no haya tirado "Secured"
    if (!$pdfUnlockTried) {
        $pdfUnlockTried = true;
        $pdfUnlockPath = pdf_import_unlock_with_python($ruta, $destDir, $pdfPassword);
        if ($pdfUnlockPath !== null) {
            try {
                $text = smalot_pdf_text_from_path($pdfUnlockPath, false);
                $ruta = $pdfUnlockPath;
                $usandoLiberado = true;
            } catch (\Throwable $eUnlock) {
                $text = '';
            }
        }
    }
}

if (trim($text) === '') {
    if (!$usandoLiberado) {
        back_to_index('needs_unsecured', "No se pudo extraer texto del original. Sub\xC3\xAD el archivo documento1_liberado (re-guardado sin restricciones).");
    }
    back_to_index('needs_ocr', "La versi\xC3\xB3n liberada tampoco trae texto embebido. Parece un escaneo: aplic\xC3\xA1 OCR y reintent\xC3\xA1.");
}

/* ====== 6) Parseo ====== */
$norm = normalize_ws(
    // tambi�n limpiamos NBSP y variantes invisibles
    str_replace(["\xC2\xA0", "\xE2\x80\x8B", "\xE2\x80\x8C", "\xE2\x80\x8D"], ' ', $text)
);
if (function_exists('mb_check_encoding') && !mb_check_encoding($norm, 'UTF-8')) {
    $norm = mb_convert_encoding($norm, 'UTF-8', 'Windows-1252');
}

/* --- Cabecera: Titular, CUIT, CBU (ultimo de 22 digitos), tipo de cuenta --- */
$header = [
    'titular' => null,
    'cuit'    => null,
    'cbu'     => null,
    'cuenta'  => null, // �Cuenta Corriente Bancaria - ��
];
$cuitHeaderPatterns = [
    // Misma l�nea: "Titular(es): ... CUIT: xx-xxxxxxxx-x"
    '/Titulares?:\s*(.{1,400}?)\s+CUIT:\s*([0-9\-\/.]+)/us',
    // CUIT en l�nea siguiente al titular (PDF a veces parte el bloque)
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
    // Captura ruidosa: dejar solo si normaliza a 11 d�gitos
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
if (preg_match('/Cuenta\s+Corriente\s+Bancaria\s*\-\s*(.+)\R/u', $norm, $mcta)) {
    $header['cuenta'] = trim($mcta[0]);
}

/* --- Saldo anterior --- */
$saldoAnterior = null;
if (preg_match('/SALDO\s+ANTERIOR\s+([\-]?\d[\d\.\,]*)/iu', $norm, $msa)) {
    $saldoAnterior = money_to_float($msa[1]);
}

/* --- Todos los "Saldo final al dd/mm/aaaa" + Total debitos / creditos (Resumen Consolidado) --- */
$saldoFinalPattern = '/Saldo\s+final\s+al\s+(\d{2})\/(\d{2})\/(\d{4})\s+([\-]?[\d\.,]+)/iu';

$periodos = [];
$lookbackBytes = 14000;
$lookforwardBytes = 2500;

if (preg_match_all($saldoFinalPattern, $norm, $sfAll, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
    $sfCount = count($sfAll);
    foreach ($sfAll as $idx => $row) {
        $fullOff = (int) $row[0][1];
        $dd = $row[1][0];
        $mm = $row[2][0];
        $yyyy = $row[3][0];
        $saldoStr = $row[4][0];

        $paired = null;
        $nextSaldoOff = ($idx + 1 < $sfCount) ? (int) $sfAll[$idx + 1][0][1] : strlen($norm);
        $prevSaldoOff = ($idx > 0) ? (int) $sfAll[$idx - 1][0][1] : max(0, $fullOff - $lookbackBytes);

        // Prioridad: buscar el resumen del mismo periodo entre este "Saldo final al ..."
        // y el próximo saldo final (evita mezclar con el periodo anterior).
        $chunkInPeriod = substr($norm, $fullOff, max(0, $nextSaldoOff - $fullOff));
        $resRowsInPeriod = find_resumenes_consolidado_rows($chunkInPeriod);
        if ($resRowsInPeriod !== []) {
            $paired = $resRowsInPeriod[0];
        }

        // Fallback: si no aparece adelante, intentar el tramo previo más cercano.
        if ($paired === null) {
            $chunkBack = substr($norm, $prevSaldoOff, max(0, $fullOff - $prevSaldoOff));
            $resRows = find_resumenes_consolidado_rows($chunkBack);
            if ($resRows !== []) {
                $paired = $resRows[array_key_last($resRows)];
            }
        }
        if ($paired === null) {
            $chunkFwd = substr($norm, $fullOff, $lookforwardBytes);
            $resRowsF = find_resumenes_consolidado_rows($chunkFwd);
            if ($resRowsF !== []) {
                $paired = $resRowsF[0];
            }
        }

        $fechaCompleta = $dd . '/' . $mm . '/' . $yyyy;
        $p = $paired ?? [];
        $periodos[] = [
            'dia_mes'            => $dd . '-' . $mm,
            'fecha'              => $fechaCompleta,
            'saldo_final_str'    => $saldoStr,
            'saldo_final'        => money_to_float($saldoStr),
            'saldo_inicial_str'  => $p['inicial'] ?? null,
            'total_debitos_str'  => $p['debitos'] ?? null,
            'total_creditos_str' => $p['creditos'] ?? null,
            'resumen_final_str'  => $p['final'] ?? null,
            'saldo_inicial'      => money_to_float($p['inicial'] ?? null),
            'total_debitos'      => money_to_float($p['debitos'] ?? null),
            'total_creditos'     => money_to_float($p['creditos'] ?? null),
            'resumen_final'      => money_to_float($p['final'] ?? null),
        ];
    }
}

/* Compatibilidad: ultimo "Saldo final" en el PDF */
$fechaSaldo = null;
$saldoFinalStr = null;
$saldoFinal = null;
$resumen = ['inicial' => null, 'debitos' => null, 'creditos' => null, 'final' => null];

if ($periodos !== []) {
    $ult = $periodos[array_key_last($periodos)];
    $fechaSaldo = $ult['fecha'];
    $saldoFinalStr = $ult['saldo_final_str'];
    $saldoFinal = $ult['saldo_final'];
    $resumen = [
        'inicial'  => $ult['saldo_inicial_str'],
        'debitos'  => $ult['total_debitos_str'],
        'creditos' => $ult['total_creditos_str'],
        'final'    => $ult['resumen_final_str'] ?? $ult['saldo_final_str'],
    ];
} else {
    if (preg_match('/Saldo\s+final\s+al\s+(\d{2}\/\d{2}\/\d{4})\s+([\-]?[\d\.,]+)/iu', $norm, $m)) {
        $fechaSaldo = $m[1];
        $saldoFinalStr = $m[2];
        $saldoFinal = money_to_float($saldoFinalStr);
    }
    $fbRows = find_resumenes_consolidado_rows($norm);
    if ($fbRows !== []) {
        $r0 = $fbRows[0];
        $resumen = [
            'inicial'  => $r0['inicial'],
            'debitos'  => $r0['debitos'],
            'creditos' => $r0['creditos'],
            'final'    => $r0['final'],
        ];
    }
}

$resumenFloats = [
    'inicial'  => money_to_float($resumen['inicial']),
    'debitos'  => money_to_float($resumen['debitos']),
    'creditos' => money_to_float($resumen['creditos']),
    'final'    => money_to_float($resumen['final']),
];

/* --- IVA Total (si aparece) --- */
$ivaTotal = null;
if (preg_match('/IVA\s+Total\s+([\-]?\d[\d\.\,]*)/iu', $norm, $miva)) {
    $ivaTotal = money_to_float($miva[1]);
}

/* --- Movimientos: detectamos l�neas con:
   dd/mm/yy o dd/mm/aaaa  [concepto�]  [comprob opcional]  importe  saldo
   Ej:
   04/06/25 TRANSF. PROPIA RECIB   2660569              40000.00            3849.86
   30/06/25 IMPUESTO DE SELLOS                         635.00               3214.86
   (Clasificamos d�bito/cr�dito comparando con el saldo anterior de cada fila)
--- */
$movimientos = [];
$lineRegex = '/^(\d{2}\/\d{2}\/(?:\d{2}|\d{4}))\s+(.+?)\s+(?:([0-9]{4,})\s+)?([\-]?\d[\d\.\,]*)\s+([\-]?\d[\d\.\,]*)$/mu';

$prevSaldo = $saldoAnterior;
if ($prevSaldo === null) {
    // fallback: intentar primer saldo de la primera l�nea con saldo si existe
    if (preg_match($lineRegex, $norm, $mtmp)) {
        $prevSaldo = money_to_float($mtmp[5]);
    }
}

if (preg_match_all($lineRegex, $norm, $mm, PREG_SET_ORDER)) {
    foreach ($mm as $row) {
        $fec     = $row[1];                         // dd/mm/yy
        $concept = trim($row[2]);
        $comprob = isset($row[3]) ? trim($row[3]) : null;
        $importe = money_to_float($row[4]);
        $saldo   = money_to_float($row[5]);

        // Determinar d�bito/cr�dito por comparaci�n de saldos
        $tipo = null;
        if ($prevSaldo !== null && $saldo !== null && $importe !== null) {
            if ($saldo > $prevSaldo) $tipo = 'credito';
            elseif ($saldo < $prevSaldo) $tipo = 'debito';
        }

        $movimientos[] = [
            'fecha'      => $fec,
            'concepto'   => $concept,
            'comprobante'=> $comprob,
            'importe'    => $importe,
            'saldo'      => $saldo,
            'tipo'       => $tipo, // 'debito'/'credito'/null
        ];
        $prevSaldo = $saldo;
    }
}

/* ====== 7) Salida ====== */
$why  = "Usando=" . ($usandoLiberado ? 'documento1_liberado' : 'documento1 (original)') . "\n";
$why .= "Titular=" . ($header['titular'] ?? '(n/d)') . " | CUIT=" . ($header['cuit'] ?? '(n/d)') . " | CBU=" . ($header['cbu'] ?? '(n/d)') . "\n";
$why .= "TransferRecib.soloCUITlocalidad=" . ($transferRecibidasSoloCuitLocalidad ? '1' : '0') . "\n";
$why .= "SaldoAnterior=" . var_export($saldoAnterior, true) . "\n";
$why .= "FechaSaldo (ultimo bloque)=" . ($fechaSaldo ?? '(no encontrado)') . "\n";
$why .= "SaldoFinal (ultimo bloque)=" . ($saldoFinalStr ?? '(no encontrado)') . " (float=" . var_export($saldoFinal, true) . ")\n";
$why .= "Periodos (todos los Saldo final al ... con debitos/creditos):\n" . print_r($periodos, true);
$why .= "Resumen (ultimo periodo / compat):\n" . print_r([
    'inicial'  => $resumen['inicial']  . ' (float='.$resumenFloats['inicial'].')',
    'debitos'  => $resumen['debitos']  . ' (float='.$resumenFloats['debitos'].')',
    'creditos' => $resumen['creditos'] . ' (float='.$resumenFloats['creditos'].')',
    'final'    => $resumen['final']    . ' (float='.$resumenFloats['final'].')',
    'iva_total'=> $ivaTotal,
], true);
$why .= "Movimientos (primeros 5):\n" . print_r(array_slice($movimientos,0,5), true);

$pdfTableToken = null;
try {
    $matrixBundle = pdf_import_build_daily_matrix($movimientos);
    if ($matrixBundle['months'] === []) {
        $matrixBundle = pdf_import_build_matrix_from_periodos($periodos);
    }
    // Aun con movimientos detectados, usar los totales del resumen por día
    // (Saldo final al dd/mm/aaaa + Total Débitos/Créditos) para completar/corregir celdas.
    $summaryRowsByKey = [];
    foreach ($periodos as $p) {
        if (!is_array($p)) {
            continue;
        }
        $fecha = isset($p['fecha']) ? (string) $p['fecha'] : '';
        if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $gf)) {
            continue;
        }
        $row = [
            'ym' => sprintf('%04d-%02d', (int) $gf[3], (int) $gf[2]),
            'day' => (int) $gf[1],
            'debitos' => isset($p['total_debitos']) && is_numeric($p['total_debitos']) ? (float) $p['total_debitos'] : null,
            'creditos' => isset($p['total_creditos']) && is_numeric($p['total_creditos']) ? (float) $p['total_creditos'] : null,
            'saldo_final' => isset($p['saldo_final']) && is_numeric($p['saldo_final']) ? (float) $p['saldo_final'] : null,
        ];
        $k = $row['ym'] . '#' . $row['day'];
        $score = 0;
        foreach (['debitos', 'creditos', 'saldo_final'] as $fld) {
            if ($row[$fld] !== null) {
                $score += 1;
            }
        }
        if (($row['debitos'] ?? 0.0) > 0.0) {
            $score += 2;
        }
        if (($row['creditos'] ?? 0.0) > 0.0) {
            $score += 2;
        }
        $magnitude = abs((float) ($row['debitos'] ?? 0.0)) + abs((float) ($row['creditos'] ?? 0.0));

        if (!isset($summaryRowsByKey[$k])) {
            $summaryRowsByKey[$k] = ['row' => $row, 'score' => $score, 'magnitude' => $magnitude];
            continue;
        }
        $prev = $summaryRowsByKey[$k];
        if ($score > $prev['score'] || ($score === $prev['score'] && $magnitude > $prev['magnitude'])) {
            $summaryRowsByKey[$k] = ['row' => $row, 'score' => $score, 'magnitude' => $magnitude];
        }
    }
    $summaryRows = array_values(array_map(static function (array $x): array {
        return $x['row'];
    }, $summaryRowsByKey));
    if ($summaryRows !== []) {
        $matrixBundle = pdf_import_merge_excel_day_summaries($matrixBundle, $summaryRows);
    }
    $trfSums = pdf_import_aggregate_transfer_recibidas($norm, $header['cuit'] ?? null, $transferRecibidasSoloCuitLocalidad);
    $matrixBundle = pdf_import_enrich_matrix_transfer_recibidas($matrixBundle, $trfSums);
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
        'import_source'                          => 'pdf',
    ]);
    $_SESSION['pdf_import_token'] = $pdfTableToken;
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
} catch (Throwable $e) {
    $why .= "\n\n(Aviso: no se guardo cache para tabla/Excel: " . $e->getMessage() . ")\n";
}

if (!DEBUG_IMPORT && $pdfTableToken !== null) {
    header('Location: resultado.php', true, 303);
    exit;
}

if (DEBUG_IMPORT) {
    echo "<h3>DEBUG IMPORT</h3>";
    echo "STATUS: <b>success</b><br>";
    if ($why !== '') {
        echo "WHY:<pre style='white-space:pre-wrap'>" . htmlspecialchars($why) . "</pre>";
    }
    if ($pdfTableToken !== null) {
        echo '<p><a class="btn btn-default" href="resultado.php">Ver tabla y exportar a Excel</a></p>';
    }
    exit;
}

header('Location: ../importar_pdf.php?import_status=' . rawurlencode('success'));
exit;
