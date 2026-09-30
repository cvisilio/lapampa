<?php

declare(strict_types=1);

require_once __DIR__ . '/../classes/Login.php';
require_once __DIR__ . '/pdf_import_matrix.php';

$login = new Login();
if ($login->isUserLoggedIn() !== true) {
    header('Location: ../login.php');
    exit;
}

$destDir = __DIR__ . '/documentos_pdf';
$token = isset($_SESSION['pdf_import_token']) ? (string) $_SESSION['pdf_import_token'] : '';
$data = $token !== '' ? pdf_import_load_cache($destDir, $token) : null;

$bundle = ($data !== null && !empty($data['matrix']) && is_array($data['matrix'])) ? $data['matrix'] : null;
$months = $bundle !== null ? ($bundle['months'] ?? []) : [];
$cells = $bundle !== null ? ($bundle['matrix'] ?? []) : [];
$info = $data['titular_info'] ?? ($data['header'] ?? []);

if ($data === null || $bundle === null || $months === []) {
    header('Location: resultado.php');
    exit;
}

$fname = 'import_pdf_' . date('Y-m-d_His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $fname . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
if ($out === false) {
    exit;
}

// BOM Excel UTF-8
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Encabezado informativo (igual criterio que resultado.php)
$titular = trim((string) ($info['titular'] ?? ''));
$cuit = trim((string) ($info['cuit'] ?? ''));
if ($titular !== '' || $cuit !== '') {
    $meta = [];
    if ($titular !== '') {
        $meta[] = 'Titular: ' . $titular;
    }
    if ($cuit !== '') {
        $meta[] = 'CUIT: ' . $cuit;
    }
    fputcsv($out, [implode(' | ', $meta)], ';');
    fputcsv($out, [''], ';');
}

$header = ['Día'];
foreach ($months as $ym) {
    $label = pdf_import_month_label_es($ym);
    $header[] = $label . ' - Ubicar';
    $header[] = $label . ' - Débitos';
    $header[] = $label . ' - Créditos';
    $header[] = $label . ' - Transf. recibidas (importe)';
    $header[] = $label . ' - Saldo final';
}
fputcsv($out, $header, ';');

$columnTotals = [];
foreach ($months as $ym) {
    $columnTotals[$ym] = [
        'debitos'            => 0.0,
        'creditos'           => 0.0,
        'transfer_recibidas' => 0.0,
        'total_saldo_final'  => 0.0,
    ];
    for ($d = 1; $d <= 31; $d++) {
        $c = $cells[$d][$ym] ?? [
            'ubicar' => '', 'debitos' => 0.0, 'creditos' => 0.0,
            'transfer_recibidas' => 0.0, 'saldo_final' => null,
        ];
        $columnTotals[$ym]['debitos'] += (float) ($c['debitos'] ?? 0);
        $columnTotals[$ym]['creditos'] += (float) ($c['creditos'] ?? 0);
        $columnTotals[$ym]['transfer_recibidas'] += (float) ($c['transfer_recibidas'] ?? 0);
    }
    for ($d = 1; $d <= 31; $d++) {
        $sf = $cells[$d][$ym]['saldo_final'] ?? null;
        if ($sf !== null && $sf !== '') {
            $columnTotals[$ym]['total_saldo_final'] += (float) $sf;
        }
    }
}

for ($day = 1; $day <= 31; $day++) {
    $line = [(string) $day];
    foreach ($months as $ym) {
        $c = $cells[$day][$ym] ?? ['ubicar' => '', 'debitos' => 0.0, 'creditos' => 0.0, 'transfer_recibidas' => 0.0, 'saldo_final' => null];
        $line[] = (string) ($c['ubicar'] ?? '');
        $line[] = isset($c['debitos']) && (float) $c['debitos'] != 0.0 ? pdf_import_format_ar_pesos_csv((float) $c['debitos']) : '';
        $line[] = isset($c['creditos']) && (float) $c['creditos'] != 0.0
            ? pdf_import_format_ar_pesos_csv((float) $c['creditos']) : '';
        $tr = $c['transfer_recibidas'] ?? 0.0;
        $line[] = (float) $tr != 0.0 ? pdf_import_format_ar_pesos_csv((float) $tr) : '';
        $sf = $c['saldo_final'] ?? null;
        $line[] = $sf !== null && $sf !== '' ? pdf_import_format_ar_pesos_csv((float) $sf) : '';
    }
    fputcsv($out, $line, ';');
}

$totalLine = ['TOTAL'];
foreach ($months as $ym) {
    $t = $columnTotals[$ym] ?? [
        'debitos' => 0.0, 'creditos' => 0.0, 'transfer_recibidas' => 0.0, 'total_saldo_final' => 0.0,
    ];
    $totalLine[] = '';
    $totalLine[] = $t['debitos'] != 0.0 ? pdf_import_format_ar_pesos_csv($t['debitos']) : '';
    $totalLine[] = $t['creditos'] != 0.0 ? pdf_import_format_ar_pesos_csv($t['creditos']) : '';
    $totalLine[] = $t['transfer_recibidas'] != 0.0 ? pdf_import_format_ar_pesos_csv($t['transfer_recibidas']) : '';
    $totalLine[] = $t['total_saldo_final'] != 0.0 ? pdf_import_format_ar_pesos_csv($t['total_saldo_final']) : '';
}
fputcsv($out, $totalLine, ';');

fclose($out);
exit;
