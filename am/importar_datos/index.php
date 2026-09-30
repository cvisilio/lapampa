<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<?php
require_once 'classes/Login.php';

$login = new Login();
if ($login->isUserLoggedIn() != true) {
    header('location: login.php');
    exit;
}

include_once 'conexion.php';
require_once __DIR__ . '/import_lib.php';

$message_stauts_class = '';
$import_status_message = '';
if (!empty($_GET['import_status'])) {
    switch ($_GET['import_status']) {
        case 'success':
            $message_stauts_class = 'alert-success';
            $import_status_message = 'Transferencias importadas satisfactoriamente.';
            break;
        case 'error':
            $message_stauts_class = 'alert-danger';
            $import_status_message = 'Error al importar.';
            break;
        case 'invalid_file':
            $message_stauts_class = 'alert-danger';
            $import_status_message = 'Archivo no valido. Subi CSV, XLSX o XLS.';
            break;
        case 'no_file':
            $message_stauts_class = 'alert-warning';
            $import_status_message = 'No se recibio el archivo.';
            break;
        case 'preview_ok':
            $message_stauts_class = 'alert-success';
            $import_status_message = 'Vista previa OK. Revisa los datos y confirma la importacion.';
            break;
        case 'preview_errors':
            $message_stauts_class = 'alert-warning';
            $import_status_message = 'La vista previa tiene errores o el Total no coincide. Corrige el archivo.';
            break;
        case 'deleted_last':
            $message_stauts_class = 'alert-success';
            $import_status_message = 'Ultima importacion eliminada.';
            break;
        default:
            $message_stauts_class = '';
            $import_status_message = '';
    }
    if (!empty($_GET['why']) && is_string($_GET['why'])) {
        $detail = trim($_GET['why']);
        if ($detail !== '') {
            $import_status_message .= ' ' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8');
        }
    }
}

$previewToken = isset($_GET['token']) ? (string) $_GET['token'] : '';
if ($previewToken === '' && !empty($_SESSION['importar_preview_token'])) {
    $previewToken = (string) $_SESSION['importar_preview_token'];
}
$previewCache = $previewToken !== '' ? importar_load_preview_cache($previewToken) : null;
$parsed = ($previewCache !== null && !empty($previewCache['parsed']) && is_array($previewCache['parsed']))
    ? $previewCache['parsed']
    : null;
$canImport = is_array($parsed) && !empty($parsed['can_import']);

$lastImportNum = 0;
$lastImportCount = 0;
$hasNumCol = false;
$colChk = @mysqli_query($con, "SHOW COLUMNS FROM transferencias LIKE 'numero_importacion'");
if ($colChk && mysqli_num_rows($colChk) > 0) {
    $hasNumCol = true;
    $mRes = mysqli_query($con, 'SELECT COALESCE(MAX(numero_importacion), 0) AS max_n FROM transferencias');
    if ($mRes) {
        $mRow = mysqli_fetch_assoc($mRes);
        $lastImportNum = (int) ($mRow['max_n'] ?? 0);
    }
    if ($lastImportNum > 0) {
        $cRes = mysqli_query($con, 'SELECT COUNT(*) AS c FROM transferencias WHERE numero_importacion = ' . $lastImportNum);
        if ($cRes) {
            $cRow = mysqli_fetch_assoc($cRes);
            $lastImportCount = (int) ($cRow['c'] ?? 0);
        }
    }
}
?>
<title>Importar Transferencia</title>
<link href="css/style.css" rel="stylesheet" type="text/css" media="screen">

<div class="container">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <?php
                $importar_menu = 1;
                include 'menu.php';
                ?>
            </div>
        </div>

        <h2>Importar aportes (CSV / Excel)</h2>
        <p class="text-muted">
            Formato esperado: columnas <strong>LOCALIDAD</strong>, <strong>Montos</strong>, <strong>Conceptos</strong>.
            Concepto habitual: <em>D.G.E.</em> / <em>D.GE</em> = Deficit y Gastos de Emergencia.
            Al final, una fila <strong>Total</strong> con la suma de montos.
        </p>

        <?php if ($import_status_message !== '') { ?>
            <div class="alert <?php echo $message_stauts_class; ?>"><?php echo $import_status_message; ?></div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-body">
                <form action="importar_datos/import.php" method="post" enctype="multipart/form-data" id="preview_form" class="form-inline" style="margin-bottom:16px;">
                    <div class="form-group" style="margin-right:10px;">
                        <input type="file" name="file" id="file" accept=".csv,.xlsx,.xls,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                    </div>
                    <button type="submit" class="btn btn-default" name="preview_data" value="1">Analizar / Vista previa</button>
                </form>

                <?php if (is_array($parsed)) { ?>
                    <?php
                    $rows = $parsed['rows'] ?? [];
                    $totalArchivo = $parsed['total_archivo'] ?? null;
                    $totalFilas = (float) ($parsed['total_filas'] ?? 0);
                    $totalOk = !empty($parsed['total_ok']);
                    $warnings = $parsed['warnings'] ?? [];
                    $fileName = htmlspecialchars((string) ($previewCache['file_name'] ?? ''), ENT_QUOTES, 'UTF-8');
                    ?>
                    <h4>Vista previa<?php echo $fileName !== '' ? ': ' . $fileName : ''; ?></h4>

                    <?php foreach ($warnings as $w) { ?>
                        <div class="alert alert-warning" style="padding:8px 12px;"><?php echo htmlspecialchars((string) $w, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php } ?>

                    <p>
                        <strong>Suma filas:</strong> <?php echo importar_format_money($totalFilas); ?>
                        <?php if ($totalArchivo !== null) { ?>
                            &nbsp;|&nbsp; <strong>Total archivo:</strong> <?php echo importar_format_money((float) $totalArchivo); ?>
                            &nbsp;
                            <?php if ($totalOk) { ?>
                                <span class="label label-success">Total OK</span>
                            <?php } else { ?>
                                <span class="label label-danger">Total no coincide</span>
                            <?php } ?>
                        <?php } ?>
                    </p>

                    <div style="overflow-x:auto; max-height:420px; overflow-y:auto;">
                        <table class="table table-bordered table-condensed table-striped" style="font-size:13px;">
                            <thead>
                            <tr>
                                <th>id_localidad</th>
                                <th>Localidad</th>
                                <th class="text-right">Monto</th>
                                <th>Concepto</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($rows === []) { ?>
                                <tr><td colspan="5">Sin filas para importar.</td></tr>
                            <?php } else { ?>
                                <?php foreach ($rows as $r) {
                                    $ok = !empty($r['ok']);
                                    $trClass = $ok ? '' : 'danger';
                                    ?>
                                    <tr class="<?php echo $trClass; ?>">
                                        <td><?php echo $r['id_localidad'] !== null ? (int) $r['id_localidad'] : ''; ?></td>
                                        <td><?php echo htmlspecialchars((string) ($r['localidad'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="text-right"><?php echo importar_format_money((float) ($r['monto'] ?? 0)); ?></td>
                                        <td>
                                            <?php
                                            $concepto = (string) ($r['concepto'] ?? '');
                                            $idc = $r['id_concepto'] ?? null;
                                            echo htmlspecialchars($concepto, ENT_QUOTES, 'UTF-8');
                                            if ($idc !== null) {
                                                echo ' <span class="text-muted">(' . (int) $idc . ')</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($ok) { ?>
                                                <span class="label label-success">OK</span>
                                            <?php } else { ?>
                                                <span class="label label-danger">Error</span>
                                                <?php echo htmlspecialchars((string) ($r['error'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <form action="importar_datos/import.php" method="post" id="import_form" style="margin-top:12px;">
                        <input type="hidden" name="preview_token" value="<?php echo htmlspecialchars($previewToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit"
                                class="btn btn-primary"
                                name="import_data"
                                value="1"
                                <?php echo $canImport ? '' : 'disabled'; ?>
                                title="<?php echo $canImport ? 'Confirmar importacion' : 'Corrige errores / Total para habilitar'; ?>">
                            IMPORTAR
                        </button>
                        <?php if (!$canImport) { ?>
                            <span class="text-muted" style="margin-left:8px;">El boton se habilita cuando todas las filas estan OK y el Total coincida.</span>
                        <?php } ?>
                    </form>
                <?php } ?>

                <hr>
                <h4>Ultimas transferencias importadas</h4>
                <?php if ($hasNumCol && $lastImportNum > 0) { ?>
                    <form action="importar_datos/import.php" method="post" style="margin-bottom:12px;"
                          onsubmit="return confirm('Eliminar la importacion N° <?php echo $lastImportNum; ?> (<?php echo $lastImportCount; ?> registros)?');">
                        <button type="submit" class="btn btn-danger btn-sm" name="delete_last_import" value="1">
                            Eliminar ultima importacion (N° <?php echo $lastImportNum; ?> &mdash; <?php echo $lastImportCount; ?> regs.)
                        </button>
                    </form>
                <?php } ?>
                <div class="row">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>N° imp.</th>
                            <th>Localidad</th>
                            <th>monto</th>
                            <th>Afectacion</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tables = 'transferencias, localidades, objetivos_motivos';
                        $campos = 'transferencias.*, localidades.localidad, objetivos_motivos.motivo';
                        $sWhere = ' transferencias.id_localidad=localidades.id and transferencias.afectacion=objetivos_motivos.id';
                        $query = mysqli_query($con, "SELECT $campos FROM $tables where $sWhere order by transferencias.id DESC LIMIT 80");

                        if ($query && mysqli_num_rows($query)) {
                            while ($rowsDb = mysqli_fetch_assoc($query)) {
                                $fecha_registro = $rowsDb['fecha_registro'];
                                $fecha = $fecha_registro;
                                if (strpos((string) $fecha_registro, ' ') !== false) {
                                    list($date, $hora) = explode(' ', $fecha_registro);
                                    list($Y, $m, $d) = explode('-', $date);
                                    $fecha = $d . '-' . $m . '-' . $Y;
                                }
                                $motivo = ucfirst((string) $rowsDb['motivo']);
                                $nImp = isset($rowsDb['numero_importacion']) ? (int) $rowsDb['numero_importacion'] : 1;
                                ?>
                                <tr>
                                    <td><?php echo $nImp; ?></td>
                                    <td><?php echo htmlspecialchars((string) $rowsDb['localidad'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td align="right"><?php echo '$' . number_format((float) $rowsDb['monto'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($motivo, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $fecha, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="5">No hay registros.....</td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
