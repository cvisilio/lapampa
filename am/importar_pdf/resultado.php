<?php

declare(strict_types=1);

require_once __DIR__ . '/../conexion.php';
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

?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<title>Resultado importación</title>
<link href="../css/style.css" rel="stylesheet" type="text/css" media="screen">
<style>
    /* Columnas de importes: evitar que el símbolo $ y los miles queden apretados */
    .table-import-pesos th.text-right,
    .table-import-pesos td.text-right {
        min-width: 11.5rem;
        padding-left: 12px;
        padding-right: 12px;
        white-space: nowrap;
    }
</style>

<div class="container">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <?php $importar_menu_pdf = 1;
                include __DIR__ . '/../menu.php'; ?>
            </div>
        </div>

        <h2>Importación — vista por día y mes</h2>
        <p class="text-muted">Filas: días del 1 al 31. Por mes: Ubicar, Débitos, Créditos, suma en &laquo;TRANSFERENCIAS RECIBIDAS&raquo; según la opción al importar (solo CUIT titular / todas), Saldo final.</p>

        <?php
        $bundle = ($data !== null && !empty($data['matrix']) && is_array($data['matrix'])) ? $data['matrix'] : null;
        $months = $bundle !== null ? ($bundle['months'] ?? []) : [];
        $cells = $bundle !== null ? ($bundle['matrix'] ?? []) : [];

        $cacheMissing = ($token !== '' && $data === null);
        $noBundle = ($data !== null && $bundle === null);
        $emptyMonths = ($bundle !== null && $months === []);
        $showTable = ($data !== null && $bundle !== null && !$emptyMonths);

        if (!$showTable): ?>
            <?php if ($token === ''): ?>
            <div class="alert alert-warning">No hay una importaci&oacute;n en esta sesi&oacute;n. Import&aacute; un <strong>PDF o Excel</strong> desde <a href="../importar_pdf.php">Importar PDF / Excel</a> y volv&eacute; a esta pantalla.</div>
            <?php elseif ($cacheMissing): ?>
            <div class="alert alert-warning">No se encontraron los datos guardados (archivo temporal borrado o sesi&oacute;n distinta). Volv&eacute; a <a href="../importar_pdf.php">importar el archivo</a>.</div>
            <?php elseif ($noBundle): ?>
            <div class="alert alert-danger">Los datos guardados est&aacute;n incompletos. Intent&aacute; <a href="../importar_pdf.php">importar de nuevo</a>.</div>
            <?php elseif ($emptyMonths): ?>
            <div class="alert alert-info">
                La importaci&oacute;n se guard&oacute;, pero no se detectaron <strong>movimientos con fecha</strong> para armar la grilla (o el extracto no trae l&iacute;neas en el formato esperado).
                <?php if (isset($data['movimientos_count'])): ?>
                    Movimientos detectados: <strong><?php echo (int) $data['movimientos_count']; ?></strong>.
                <?php endif; ?>
                Revis&aacute; el archivo o prob&aacute; con el PDF del banco. <a href="../importar_pdf.php">Volver a importar</a>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <?php
            $info = $data['titular_info'] ?? ($data['header'] ?? []);

            $columnTotals = [];
            foreach ($months as $ym) {
                $columnTotals[$ym] = [
                    'debitos'              => 0.0,
                    'creditos'             => 0.0,
                    'transfer_recibidas'   => 0.0,
                    'total_saldo_final'    => 0.0,
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
            ?>
            <p>
                <a href="export_excel.php" class="btn btn-success"><span class="glyphicon glyphicon-download-alt"></span> Exportar a Excel (CSV)</a>
                <a href="../importar_pdf.php" class="btn btn-default">Volver a importar</a>
            </p>
            <?php if (!empty($info['titular']) || !empty($info['cuit'])): ?>
                <p><strong>Titular:</strong> <?php echo htmlspecialchars((string) ($info['titular'] ?? '')); ?>
                    <?php if (!empty($info['cuit'])): ?>
                        &nbsp;| <strong>CUIT:</strong> <?php echo htmlspecialchars((string) $info['cuit']); ?>
                    <?php endif; ?>
                    <?php if (!empty($info['cbu'])): ?>
                        &nbsp;| <strong>CBU:</strong> <?php echo htmlspecialchars((string) $info['cbu']); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php if (array_key_exists('transfer_recibidas_solo_cuit_localidad', $data)): ?>
                <p class="text-muted small">
                    <strong>Transferencias recibidas:</strong>
                    <?php if (!empty($data['transfer_recibidas_solo_cuit_localidad'])): ?>
                        solo l&iacute;neas donde el concepto/comprobante incluye el CUIT del titular.
                    <?php else: ?>
                        todas las del bloque por d&iacute;a (sin filtrar por CUIT).
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <div style="overflow-x:auto;">
                <table class="table table-bordered table-condensed table-striped table-import-pesos" style="font-size:12px;">
                    <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align:middle;">Día</th>
                        <?php foreach ($months as $ym): ?>
                            <th colspan="5" class="text-center bg-info"><?php echo htmlspecialchars(pdf_import_month_label_es($ym)); ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($months as $_ym): ?>
                            <th>Ubicar</th>
                            <th class="text-right">Débitos</th>
                            <th class="text-right">Créditos</th>
                            <th class="text-right" title="Suma importes sección TRANSFERENCIAS RECIBIDAS">Transf. recib.</th>
                            <th class="text-right">Saldo final</th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($day = 1; $day <= 31; $day++): ?>
                        <tr>
                            <td><strong><?php echo $day; ?></strong></td>
                            <?php foreach ($months as $ym):
                                $c = $cells[$day][$ym] ?? ['ubicar' => '', 'debitos' => 0.0, 'creditos' => 0.0, 'transfer_recibidas' => 0.0, 'saldo_final' => null];
                                ?>
                                <td><?php echo htmlspecialchars((string) ($c['ubicar'] ?? '')); ?></td>
                                <td class="text-right"><?php echo $c['debitos'] != 0.0 ? pdf_import_format_ar_pesos_html((float) $c['debitos']) : ''; ?></td>
                                <td class="text-right"><?php echo $c['creditos'] != 0.0 ? pdf_import_format_ar_pesos_html((float) $c['creditos']) : ''; ?></td>
                                <td class="text-right"><?php echo isset($c['transfer_recibidas']) && (float) $c['transfer_recibidas'] != 0.0 ? pdf_import_format_ar_pesos_html((float) $c['transfer_recibidas']) : ''; ?></td>
                                <td class="text-right"><?php echo isset($c['saldo_final']) && $c['saldo_final'] !== null
                                    ? pdf_import_format_ar_pesos_html((float) $c['saldo_final']) : ''; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                    <tfoot>
                    <tr class="active">
                        <td><strong>TOTAL</strong></td>
                        <?php foreach ($months as $ym):
                            $t = $columnTotals[$ym] ?? [
                                'debitos' => 0.0, 'creditos' => 0.0, 'transfer_recibidas' => 0.0, 'total_saldo_final' => 0.0,
                            ];
                            ?>
                            <td></td>
                            <td class="text-right"><strong><?php echo $t['debitos'] != 0.0 ? pdf_import_format_ar_pesos_html($t['debitos']) : ''; ?></strong></td>
                            <td class="text-right"><strong><?php echo $t['creditos'] != 0.0 ? pdf_import_format_ar_pesos_html($t['creditos']) : ''; ?></strong></td>
                            <td class="text-right"><strong><?php echo $t['transfer_recibidas'] != 0.0 ? pdf_import_format_ar_pesos_html($t['transfer_recibidas']) : ''; ?></strong></td>
                            <td class="text-right" title="Suma de saldos finales del mes">
                                <strong><?php echo $t['total_saldo_final'] != 0.0 ? pdf_import_format_ar_pesos_html($t['total_saldo_final']) : ''; ?></strong>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
