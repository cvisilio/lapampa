<?php
require_once 'includes/config.php';
require_once 'includes/text_encoding.php';
require_once 'includes/informes_helpers.php';
requireLogin();

$db = getDB();
$page_title = 'Informes por localidad';
$breadcrumb = [['label' => 'Informes por localidad']];

$anio = (int) date('Y');
$localidadId = (int) ($_GET['localidad_id'] ?? 0);
$seccion = max(1, min(4, (int) ($_GET['seccion'] ?? 1)));
$issAnio = (int) ($_GET['iss_anio'] ?? 0);
$issMes = (int) ($_GET['iss_mes'] ?? 0);
$agentesAnio = (int) ($_GET['agentes_anio'] ?? 0);
$agentesMes = (int) ($_GET['agentes_mes'] ?? 0);
$organismoAportes = trim((string) ($_GET['organismo'] ?? 'todos'));
$descCptoAportes = trim((string) ($_GET['desc_cpto'] ?? 'todos'));
$fechaDesdeAportes = trim((string) ($_GET['fecha_desde'] ?? ''));
$fechaHastaAportes = trim((string) ($_GET['fecha_hasta'] ?? ''));
$aplicarSacParam = array_key_exists('aplicar_sac', $_GET)
    ? (string) $_GET['aplicar_sac']
    : null;
$incluirMesAnteriorSeccion2 = isset($_GET['incluir_mes_anterior']) && $_GET['incluir_mes_anterior'] === '1';

$localidades = $db->prepare('
    SELECT l.id, l.localidad,
           COUNT(DISTINCT r.mes) AS meses_con_datos
    FROM localidades l
    LEFT JOIN retenciones r ON r.id_localidad = l.id AND r.anio = ?
    GROUP BY l.id, l.localidad
    ORDER BY l.localidad
');
$localidades->execute([$anio]);
$localidades = $localidades->fetchAll();

$mesMax = informeMesMax($db, $anio);
$localidadActual = null;
$datosPorMes = [];
$datosSeccion2 = null;
$datosSeccion3 = null;
$datosSeccion4 = null;
$errorInforme = null;
$nombresMes = informeNombresMes();
$filasSeccion1 = informeFilasSeccion1();
$columnasSeccion2 = informeColumnasSeccion2();

if ($localidadId > 0) {
    $stmtLoc = $db->prepare('SELECT id, localidad, AgeRet, cantidad_habitantes FROM localidades WHERE id = ? LIMIT 1');
    $stmtLoc->execute([$localidadId]);
    $localidadActual = $stmtLoc->fetch() ?: null;

    if (!$localidadActual) {
        $errorInforme = 'La localidad seleccionada no existe.';
        $localidadId = 0;
    } elseif ($seccion === 1) {
        if ($mesMax < 1) {
            $errorInforme = "No hay retenciones cargadas para el año {$anio}.";
        } else {
            $datosPorMes = informeCargarRetenciones($db, $localidadId, $anio);
            if ($datosPorMes === []) {
                $errorInforme = "No hay retenciones vinculadas a «" . nombreLocalidad($localidadActual['localidad']) . "» (id {$localidadId}) para el año {$anio}. "
                    . 'Verificá que la importación haya asignado correctamente id_localidad en la tabla retenciones.';
            }
        }
    } elseif ($seccion === 2) {
        $datosSeccion2 = informeCargarSeccion2($db, $localidadId, $anio);
        if (isset($datosSeccion2['error'])) {
            $errorInforme = match ($datosSeccion2['error']) {
                'sin_distribucion' => 'No hay distribución semanal cargada para «'
                    . nombreLocalidad($localidadActual['localidad']) . '».',
                default => 'No se pudo cargar la sección 2.',
            };
            $datosSeccion2 = null;
        } else {
            $datosSeccion2 = informeAplicarFiltroProyeccionesSeccion2($datosSeccion2, $incluirMesAnteriorSeccion2);
        }
    } elseif ($seccion === 3) {
        $datosSeccion3 = informeCargarSeccion3(
            $db,
            $localidadId,
            $issAnio > 0 ? $issAnio : null,
            $issMes > 0 ? $issMes : null,
            $aplicarSacParam
        );
        if (isset($datosSeccion3['error'])) {
            $errorInforme = match ($datosSeccion3['error']) {
                'sin_agentes' => 'No hay listado de empleados importado para «'
                    . nombreLocalidad($localidadActual['localidad']) . '».',
                'sin_agentes_periodo' => 'No hay datos del listado de empleados para el mes '
                    . ($datosSeccion3['mes'] ?? '') . '/' . ($datosSeccion3['anio'] ?? '') . '.',
                default => 'No se pudo cargar la sección 3.',
            };
            $datosSeccion3 = null;
        }
    } elseif ($seccion === 4) {
        $datosSeccion4 = informeCargarSeccion4(
            $db,
            $localidadId,
            $organismoAportes,
            $descCptoAportes,
            $fechaDesdeAportes,
            $fechaHastaAportes
        );
        if (isset($datosSeccion4['error'])) {
            $errorInforme = match ($datosSeccion4['error']) {
                'sin_aportes' => 'No hay detalle de pagos cargado para «'
                    . nombreLocalidad($localidadActual['localidad']) . '». Importá el Excel en Importar datos.',
                'sin_aportes_filtro' => 'No hay pagos con los filtros seleccionados en «'
                    . nombreLocalidad($localidadActual['localidad']) . '».',
                'sin_aportes_organismo' => 'No hay pagos para el organismo seleccionado en «'
                    . nombreLocalidad($localidadActual['localidad']) . '».',
                default => 'No se pudo cargar la sección 4.',
            };
            $datosSeccion4 = null;
        }
    }
}

function informeUrlSeccion4(
    int $localidadId,
    ?string $organismo = null,
    ?string $descCpto = null,
    ?string $fechaDesde = null,
    ?string $fechaHasta = null
): string {
    $params = [
        'localidad_id' => $localidadId,
        'seccion' => 4,
        'organismo' => $organismo ?? 'todos',
        'desc_cpto' => $descCpto ?? 'todos',
        'fecha_desde' => $fechaDesde ?? '',
        'fecha_hasta' => $fechaHasta ?? '',
    ];

    return 'informes.php?' . http_build_query($params);
}

function informeUrlExportar(
    int $localidadId,
    int $seccion,
    ?int $issAnio = null,
    ?int $issMes = null,
    ?int $agentesAnio = null,
    ?int $agentesMes = null,
    ?string $organismo = null,
    ?string $descCpto = null,
    ?string $fechaDesde = null,
    ?string $fechaHasta = null,
    ?bool $aplicarSac = null,
    ?bool $incluirMesAnterior = null
): string {
    $url = 'modulos/exportar_informe.php?localidad_id=' . $localidadId . '&seccion=' . $seccion;
    if ($issAnio !== null && $issAnio > 0 && $issMes !== null && $issMes > 0) {
        $url .= '&iss_anio=' . $issAnio . '&iss_mes=' . $issMes;
    }
    if ($agentesAnio !== null && $agentesAnio > 0 && $agentesMes !== null && $agentesMes > 0) {
        $url .= '&agentes_anio=' . $agentesAnio . '&agentes_mes=' . $agentesMes;
    }
    if ($organismo !== null && $organismo !== '') {
        $url .= '&organismo=' . rawurlencode($organismo);
    }
    if ($descCpto !== null && $descCpto !== '') {
        $url .= '&desc_cpto=' . rawurlencode($descCpto);
    }
    if ($fechaDesde !== null && $fechaDesde !== '') {
        $url .= '&fecha_desde=' . rawurlencode($fechaDesde);
    }
    if ($fechaHasta !== null && $fechaHasta !== '') {
        $url .= '&fecha_hasta=' . rawurlencode($fechaHasta);
    }
    if ($aplicarSac !== null) {
        $url .= '&aplicar_sac=' . ($aplicarSac ? '1' : '0');
    }
    if ($incluirMesAnterior !== null) {
        $url .= '&incluir_mes_anterior=' . ($incluirMesAnterior ? '1' : '0');
    }

    return $url;
}

include 'includes/header.php';
?>

<style>
    .informe-contenido .informe-tabla { font-size: 1.0625rem; }
    .informe-contenido .informe-tabla th,
    .informe-contenido .informe-tabla td { font-size: 1.0625rem; }
    .informe-contenido .informe-tabla--negrita,
    .informe-contenido .informe-tabla--negrita th,
    .informe-contenido .informe-tabla--negrita td { font-weight: 700; }
    .informe-formula-ayuda { position: relative; display: inline-flex; vertical-align: middle; }
    .informe-formula-ayuda__btn {
        color: rgba(255, 255, 255, 0.8);
        line-height: 1;
        padding: 0.125rem;
        border-radius: 9999px;
    }
    .informe-formula-ayuda__btn:hover,
    .informe-formula-ayuda__btn:focus-visible {
        color: #fff;
        outline: none;
    }
    .informe-formula-ayuda__panel {
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        z-index: 30;
        width: 18rem;
        max-width: min(18rem, 80vw);
        padding: 0.75rem 0.875rem;
        border-radius: 0.5rem;
        background: #fff;
        color: #374151;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.45;
        text-transform: none;
        letter-spacing: normal;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
        border: 1px solid #e5e7eb;
        display: none;
    }
    .informe-formula-ayuda.is-open .informe-formula-ayuda__panel { display: block; }
    .informe-formula-ayuda__panel strong {
        display: block;
        color: #111827;
        font-size: 0.8125rem;
        margin-bottom: 0.375rem;
    }
    .informe-formula-ayuda__panel p + p { margin-top: 0.25rem; }
    .informe-formula-ayuda__panel .resumen {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #f3f4f6;
        color: #1d4ed8;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.6875rem;
        word-break: break-word;
    }
</style>

<div class="space-y-5 informe-contenido">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Informes por localidad</h1>
            <p class="text-gray-500 text-sm">Informe financiero — Año <?= $anio ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="seccion" value="<?= $seccion ?>">
            <div class="flex-1 min-w-56">
                <label class="block text-xs font-medium text-gray-600 mb-1">Localidad</label>
                <select name="localidad_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                        onchange="this.form.submit()">
                    <option value="">— Seleccioná localidad —</option>
                    <?php foreach ($localidades as $loc): ?>
                    <option value="<?= (int) $loc['id'] ?>" <?= $localidadId === (int) $loc['id'] ? 'selected' : '' ?>>
                        <?= h(nombreLocalidad($loc['localidad'])) ?><?= (int) $loc['meses_con_datos'] === 0 ? ' (sin datos ' . $anio . ')' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($localidadId > 0): ?>
            <a href="informes.php?localidad_id=<?= $localidadId ?>&seccion=<?= $seccion ?>"
               class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                Actualizar
            </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="flex gap-2 overflow-x-auto pb-1 -mx-1 px-1 snap-x snap-mandatory">
        <?php for ($s = 1; $s <= 4; $s++): ?>
        <?php
        $activa = $seccion === $s;
        $url = $localidadId > 0 ? "informes.php?localidad_id={$localidadId}&seccion={$s}" : '#';
        $clases = $activa
            ? 'bg-blue-700 text-white border-blue-700'
            : ($s === 1 || $localidadId > 0
                ? 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed');
        ?>
        <a href="<?= $s === 1 || $localidadId > 0 ? h($url) : '#' ?>"
           class="inline-flex items-center px-4 py-2 rounded-lg text-sm sm:text-base font-medium border transition-colors shrink-0 snap-start <?= $clases ?>">
            Sección <?= $s ?>
        </a>
        <?php endfor; ?>
    </div>

    <?php if (!$localidadId): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 py-16 text-center">
        <i class="fas fa-map-marker-alt text-4xl text-gray-200 mb-3 block"></i>
        <p class="text-gray-400 text-base">Seleccioná una localidad para ver el informe.</p>
    </div>

    <?php elseif ($errorInforme): ?>
    <div class="p-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-base">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= h($errorInforme) ?>
    </div>

    <?php elseif ($seccion === 1 && !empty($datosPorMes)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Sección 1 — Retenciones mensuales</h2>
                <p class="text-base text-gray-500"><?= h(nombreLocalidad($localidadActual['localidad'])) ?> · Enero a <?= h($nombresMes[$mesMax] ?? $mesMax) ?> <?= $anio ?></p>
            </div>
        </div>

        <div class="overflow-x-auto table-scroll">
            <table class="w-full informe-tabla min-w-[640px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-4 py-4 text-left font-semibold min-w-64"></th>
                        <?php for ($m = 1; $m <= $mesMax; $m++): ?>
                        <th class="px-4 py-4 text-right font-semibold min-w-36 capitalize"><?= h($nombresMes[$m] ?? (string) $m) ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filasSeccion1 as $idx => $fila): ?>
                    <?php if ($idx > 0): ?>
                    <tr class="h-2 bg-gray-50"><td colspan="<?= $mesMax + 1 ?>"></td></tr>
                    <?php endif; ?>
                    <tr class="<?= !empty($fila['destacado']) ? 'bg-blue-50 font-bold text-xl' : 'hover:bg-gray-50' ?>">
                        <td class="px-4 py-4 text-gray-800 border-t border-gray-100"><?= h($fila['label']) ?></td>
                        <?php for ($m = 1; $m <= $mesMax; $m++): ?>
                        <?php
                        $campo = $fila['campo'];
                        $valor = isset($datosPorMes[$m]) ? (float) ($datosPorMes[$m][$campo] ?? 0) : null;
                        ?>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap<?= !empty($fila['destacado']) ? ' text-xl font-bold' : '' ?>">
                            <?= h(informeFormatoPantalla($valor, $campo)) ?>
                        </td>
                        <?php endfor; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <a href="<?= h(informeUrlExportar($localidadId, 1)) ?>"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
    </div>

    <?php elseif ($seccion === 2 && !empty($datosSeccion2)): ?>
    <?php
    $filasSeccion2 = $datosSeccion2['filas'];
    $fechaDesde = $filasSeccion2[0]['fecha'] ?? null;
    $fechaHasta = $filasSeccion2[count($filasSeccion2) - 1]['fecha'] ?? null;
    $deudaIssSeccion2 = $datosSeccion2['deuda_iss'] ?? null;
    $retAntDto294Seccion2 = $datosSeccion2['ret_ant_dto294'] ?? null;
    $totalCopaSeccion2 = (float) ($datosSeccion2['total_copa'] ?? 0);
    $importeDeudaIssSeccion2 = $deudaIssSeccion2 ? (float) $deudaIssSeccion2['importe'] : 0.0;
    $importeRetAntDto294Seccion2 = $retAntDto294Seccion2 ? (float) $retAntDto294Seccion2['importe'] : 0.0;
    $netoSeccion2 = $totalCopaSeccion2 - $importeDeudaIssSeccion2 - $importeRetAntDto294Seccion2;
    $incluirMesAnteriorActivo = !empty($datosSeccion2['incluir_mes_anterior']);
    $mesAnteriorEtiqueta = $datosSeccion2['mes_anterior_etiqueta'] ?? informeEtiquetaMesAnteriorSeccion2();
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Sección 2 — Distribución semanal</h2>
                <p class="text-base text-gray-500">
                    <?= h(nombreLocalidad($localidadActual['localidad'])) ?>
                    <?php if ($fechaDesde && $fechaHasta): ?>
                    · <?= h(informeFormatoFechaDistribucion($fechaDesde)) ?>
                    <?= $fechaHasta !== $fechaDesde ? ' – ' . h(informeFormatoFechaDistribucion($fechaHasta)) : '' ?>
                    <?php endif; ?>
                </p>
                <p class="text-sm text-gray-400 mt-1">
                    <?= $incluirMesAnteriorActivo
                        ? 'Mostrando proyecciones del mes anterior y fechas posteriores a hoy.'
                        : 'Mostrando solo proyecciones con fecha posterior a hoy.' ?>
                </p>
            </div>
        </div>

        <div class="overflow-x-auto table-scroll">
            <table class="w-full informe-tabla min-w-[640px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <?php foreach ($columnasSeccion2 as $col): ?>
                        <th class="px-4 py-4 font-semibold min-w-40 <?= ($col['align'] ?? 'right') === 'left' ? 'text-left' : 'text-right' ?><?= !empty($col['destacado']) ? ' bg-blue-900' : '' ?>">
                            <?= h($col['label']) ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($filasSeccion2 === []): ?>
                    <tr>
                        <td colspan="<?= count($columnasSeccion2) ?>" class="px-4 py-8 text-center text-gray-500 border-t border-gray-100">
                            No hay proyecciones con fecha posterior a hoy.
                            <?php if (!empty($datosSeccion2['tiene_mes_anterior']) && !$incluirMesAnteriorActivo): ?>
                            Podés ver las de <?= h($mesAnteriorEtiqueta) ?> con el botón inferior.
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($filasSeccion2 as $fila): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-gray-800 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoFechaDistribucion($fila['fecha'])) ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion2($fila['importe'])) ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-400 border-t border-gray-100 font-mono whitespace-nowrap"></td>
                        <td class="px-4 py-4 text-right text-gray-400 border-t border-gray-100 font-mono whitespace-nowrap"></td>
                        <td class="px-4 py-4 text-right text-gray-400 border-t border-gray-100 font-mono whitespace-nowrap font-bold"></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="bg-blue-50 font-bold">
                        <td class="px-4 py-4 text-gray-900 border-t border-gray-200">
                            TOTAL
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap text-lg">
                            <?= h(informeFormatoSeccion2($totalCopaSeccion2)) ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap text-lg">
                            <?= $deudaIssSeccion2 ? h(informeFormatoSeccion2((float) $deudaIssSeccion2['importe'])) : '' ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap text-lg">
                            <?= $retAntDto294Seccion2 ? h(informeFormatoSeccion2((float) $retAntDto294Seccion2['importe'])) : '' ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap text-lg">
                            <?= h(informeFormatoSeccion2($netoSeccion2)) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <?php if (!empty($datosSeccion2['tiene_mes_anterior'])): ?>
            <form method="GET" class="inline-flex items-center justify-end">
                <input type="hidden" name="localidad_id" value="<?= $localidadId ?>">
                <input type="hidden" name="seccion" value="2">
                <input type="hidden" name="incluir_mes_anterior" value="<?= $incluirMesAnteriorActivo ? '0' : '1' ?>">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium border transition-colors
                               <?= $incluirMesAnteriorActivo
                                   ? 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                                   : 'border-blue-200 bg-blue-50 text-blue-800 hover:bg-blue-100' ?>">
                    <i class="fas <?= $incluirMesAnteriorActivo ? 'fa-eye-slash' : 'fa-calendar-alt' ?>"></i>
                    <?= $incluirMesAnteriorActivo
                        ? 'Ocultar mes anterior'
                        : 'Ver mes anterior (' . h($mesAnteriorEtiqueta) . ')' ?>
                </button>
            </form>
            <?php endif; ?>
            <a href="<?= h(informeUrlExportar($localidadId, 2, incluirMesAnterior: $incluirMesAnteriorActivo)) ?>"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
    </div>

    <?php elseif ($seccion === 3 && !empty($datosSeccion3)): ?>
    <?php
    $periodosIss = $datosSeccion3['periodos_disponibles'] ?? [];
    $aniosIss = [];
    foreach ($periodosIss as $periodoIss) {
        $aniosIss[$periodoIss['anio']] = true;
    }
    $aniosIss = array_keys($aniosIss);
    rsort($aniosIss);
    $mesesIssAnio = [];
    foreach ($periodosIss as $periodoIss) {
        if ($periodoIss['anio'] === (int) $datosSeccion3['anio']) {
            $mesesIssAnio[] = $periodoIss['mes'];
        }
    }
    rsort($mesesIssAnio);
    ?>
    <?php
    $ayudaRemuner = informeAyudaRemunerConAporte(!empty($datosSeccion3['aplicar_sac']));
    $ayudaAdicSinAporte = informeAyudaAdicSinAporte(!empty($datosSeccion3['aplicar_sac']));
    $aplicarSacActivo = !empty($datosSeccion3['aplicar_sac']);
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Sección 3 — Masa salarial</h2>
                <p class="text-base text-gray-500">
                    <?= h(nombreLocalidad($localidadActual['localidad'])) ?>
                    · <?= h($datosSeccion3['mes_nombre']) ?> <?= (int) $datosSeccion3['anio'] ?>
                </p>
            </div>
            <?php if (count($aniosIss) > 0): ?>
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <input type="hidden" name="localidad_id" value="<?= $localidadId ?>">
                <input type="hidden" name="seccion" value="3">
                <?php if (!empty($datosSeccion3['empleados'])): ?>
                <input type="hidden" name="agentes_anio" value="<?= (int) $datosSeccion3['anio'] ?>">
                <input type="hidden" name="agentes_mes" value="<?= (int) $datosSeccion3['mes'] ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Año listado empleados</label>
                    <select name="iss_anio"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 min-w-28"
                            onchange="this.form.submit()">
                        <?php foreach ($aniosIss as $anioIss): ?>
                        <option value="<?= (int) $anioIss ?>" <?= (int) $datosSeccion3['anio'] === (int) $anioIss ? 'selected' : '' ?>>
                            <?= (int) $anioIss ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mes listado empleados</label>
                    <select name="iss_mes"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 min-w-36 capitalize"
                            onchange="this.form.submit()">
                        <?php foreach ($mesesIssAnio as $mesIss): ?>
                        <option value="<?= (int) $mesIss ?>" <?= (int) $datosSeccion3['mes'] === (int) $mesIss ? 'selected' : '' ?>>
                            <?= h($nombresMes[$mesIss] ?? (string) $mesIss) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto table-scroll">
            <table class="w-full informe-tabla informe-tabla--negrita min-w-[640px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-4 py-4 text-left font-bold min-w-80">Mes <?= (int) $datosSeccion3['mes'] ?></th>
                        <th class="px-4 py-4 text-right font-bold min-w-40">
                            <span class="inline-flex items-center justify-end gap-1.5">
                                REMUNER C/APORTE
                                <span class="informe-formula-ayuda" data-informe-formula-ayuda>
                                    <button type="button"
                                            class="informe-formula-ayuda__btn"
                                            aria-label="Ver fórmula de REMUNER C/APORTE"
                                            aria-expanded="false"
                                            title="Ver fórmula de cálculo">
                                        <i class="fas fa-question-circle text-sm"></i>
                                    </button>
                                    <span class="informe-formula-ayuda__panel" role="tooltip">
                                        <strong>Fórmula de cálculo</strong>
                                        <?php foreach ($ayudaRemuner['lineas'] as $lineaAyuda): ?>
                                        <p><?= h($lineaAyuda) ?></p>
                                        <?php endforeach; ?>
                                        <p class="resumen"><?= h($ayudaRemuner['resumen']) ?></p>
                                    </span>
                                </span>
                            </span>
                        </th>
                        <th class="px-4 py-4 text-right font-bold min-w-40">
                            <span class="inline-flex items-center justify-end gap-1.5">
                                ADIC SIN APORTE
                                <span class="informe-formula-ayuda" data-informe-formula-ayuda>
                                    <button type="button"
                                            class="informe-formula-ayuda__btn"
                                            aria-label="Ver fórmula de ADIC SIN APORTE"
                                            aria-expanded="false"
                                            title="Ver fórmula de cálculo">
                                        <i class="fas fa-question-circle text-sm"></i>
                                    </button>
                                    <span class="informe-formula-ayuda__panel" role="tooltip">
                                        <strong>Fórmula de cálculo</strong>
                                        <?php foreach ($ayudaAdicSinAporte['lineas'] as $lineaAyuda): ?>
                                        <p><?= h($lineaAyuda) ?></p>
                                        <?php endforeach; ?>
                                        <p class="resumen"><?= h($ayudaAdicSinAporte['resumen']) ?></p>
                                    </span>
                                </span>
                            </span>
                        </th>
                        <th class="px-4 py-4 text-right font-bold min-w-40">TOTAL GENERAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-gray-800 border-t border-gray-100"><?= h($datosSeccion3['leyenda']) ?></td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion3($datosSeccion3['remuneracion'])) ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion3($datosSeccion3['adic_sin_aporte'])) ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion3($datosSeccion3['total_general'])) ?>
                        </td>
                    </tr>
                    <?php if (!empty($localidadActual['cantidad_habitantes'])): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-gray-800 border-t border-gray-100" colspan="4">
                            POBLACIÓN
                            <span class="mx-2">=</span>
                            <span class="font-mono font-bold text-cyan-600 text-xl"><?= h(number_format((int) $localidadActual['cantidad_habitantes'], 0, ',', '.')) ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($datosSeccion3['empleados'])): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-gray-800 border-t border-gray-100" colspan="4">
                            CANTIDAD DE EMPLEADOS
                            <span class="mx-2">=</span>
                            <span class="font-mono font-bold text-cyan-600 text-xl"><?= h(number_format((int) $datosSeccion3['empleados']['total_empleados'], 0, ',', '.')) ?></span>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
            <form method="GET" class="inline-flex items-center justify-end">
                <input type="hidden" name="localidad_id" value="<?= $localidadId ?>">
                <input type="hidden" name="seccion" value="3">
                <input type="hidden" name="iss_anio" value="<?= (int) $datosSeccion3['anio'] ?>">
                <input type="hidden" name="iss_mes" value="<?= (int) $datosSeccion3['mes'] ?>">
                <input type="hidden" name="aplicar_sac" id="aplicar-sac-val" value="<?= $aplicarSacActivo ? '1' : '0' ?>">
                <label class="inline-flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer select-none px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-50">
                    <input type="checkbox"
                           id="aplicar-sac-check"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           <?= $aplicarSacActivo ? 'checked' : '' ?>
                           onchange="document.getElementById('aplicar-sac-val').value = this.checked ? '1' : '0'; this.form.submit();">
                    <span>
                        Aplicar SAC
                        <span class="text-gray-500">(× 1,5 en remuneración y aportes)</span>
                        <?php if (!empty($datosSeccion3['sac_por_defecto']) && !$aplicarSacActivo): ?>
                        <span class="text-amber-600 text-xs block">Desactivado manualmente</span>
                        <?php elseif (empty($datosSeccion3['sac_por_defecto']) && $aplicarSacActivo): ?>
                        <span class="text-blue-600 text-xs block">Activado manualmente</span>
                        <?php endif; ?>
                    </span>
                </label>
            </form>
            <a href="<?= h(informeUrlExportar(
                $localidadId,
                3,
                (int) $datosSeccion3['anio'],
                (int) $datosSeccion3['mes'],
                !empty($datosSeccion3['empleados']) ? (int) $datosSeccion3['empleados']['anio'] : null,
                !empty($datosSeccion3['empleados']) ? (int) $datosSeccion3['empleados']['mes'] : null,
                null,
                null,
                null,
                null,
                $aplicarSacActivo
            )) ?>"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
    </div>

    <?php elseif ($seccion === 4 && !empty($datosSeccion4)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Sección 4 — Detalle de pagos</h2>
                <p class="text-base text-gray-500">
                    <?= h(nombreLocalidad($localidadActual['localidad'])) ?>
                    <?php if (!empty($datosSeccion4['fecha_desde']) && !empty($datosSeccion4['fecha_hasta'])): ?>
                    · <?= h(informeFormatoFechaDistribucion($datosSeccion4['fecha_desde'])) ?>
                    <?php if ($datosSeccion4['fecha_hasta'] !== $datosSeccion4['fecha_desde']): ?>
                    – <?= h(informeFormatoFechaDistribucion($datosSeccion4['fecha_hasta'])) ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty($datosSeccion4['desc_cpto_seleccionado'])): ?>
                    · Concepto: <strong><?= h($datosSeccion4['desc_cpto_seleccionado']) ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($datosSeccion4['fecha_desde_filtro']) || !empty($datosSeccion4['fecha_hasta_filtro'])): ?>
                    · Fechas:
                    <strong>
                        <?= h($datosSeccion4['fecha_desde_filtro'] ? informeFormatoFechaDistribucion($datosSeccion4['fecha_desde_filtro']) : 'inicio') ?>
                        –
                        <?= h($datosSeccion4['fecha_hasta_filtro'] ? informeFormatoFechaDistribucion($datosSeccion4['fecha_hasta_filtro']) : 'fin') ?>
                    </strong>
                    <?php endif; ?>
                </p>
            </div>
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <input type="hidden" name="localidad_id" value="<?= $localidadId ?>">
                <input type="hidden" name="seccion" value="4">
                <div class="min-w-40">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fecha desde</label>
                    <input type="date" name="fecha_desde"
                           value="<?= h($datosSeccion4['fecha_desde_filtro'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                           onchange="this.form.submit()">
                </div>
                <div class="min-w-40">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Fecha hasta</label>
                    <input type="date" name="fecha_hasta"
                           value="<?= h($datosSeccion4['fecha_hasta_filtro'] ?? '') ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                           onchange="this.form.submit()">
                </div>
                <div class="min-w-56">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Concepto (DescCpto)</label>
                    <select name="desc_cpto"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            onchange="this.form.submit()">
                        <option value="todos" <?= empty($datosSeccion4['desc_cpto_seleccionado']) ? 'selected' : '' ?>>
                            Todos los conceptos
                        </option>
                        <?php foreach ($datosSeccion4['conceptos_disponibles'] as $cpto): ?>
                        <option value="<?= h($cpto) ?>" <?= ($datosSeccion4['desc_cpto_seleccionado'] ?? '') === $cpto ? 'selected' : '' ?>>
                            <?= h($cpto) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="min-w-56">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Organismo / Jurisdicción</label>
                    <select name="organismo"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                            onchange="this.form.submit()">
                        <option value="todos" <?= empty($datosSeccion4['organismo_seleccionado']) ? 'selected' : '' ?>>
                            Todos los organismos (totales)
                        </option>
                        <?php foreach ($datosSeccion4['organismos_disponibles'] as $org): ?>
                        <option value="<?= h($org) ?>" <?= ($datosSeccion4['organismo_seleccionado'] ?? '') === $org ? 'selected' : '' ?>>
                            <?= h($org) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if (empty($datosSeccion4['organismo_seleccionado'])): ?>
        <div class="overflow-x-auto table-scroll">
            <table class="w-full informe-tabla min-w-[640px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-4 py-4 text-left font-semibold min-w-72">Organismo / Jurisdicción</th>
                        <th class="px-4 py-4 text-right font-semibold min-w-28">Cantidad</th>
                        <th class="px-4 py-4 text-right font-semibold min-w-40">Importe total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datosSeccion4['totales_por_organismo'] as $fila): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 text-gray-800 border-t border-gray-100">
                            <a href="<?= h(informeUrlSeccion4(
                                $localidadId,
                                $fila['organismo'],
                                $datosSeccion4['desc_cpto_seleccionado'] ?? 'todos',
                                $datosSeccion4['fecha_desde_filtro'] ?? '',
                                $datosSeccion4['fecha_hasta_filtro'] ?? ''
                            )) ?>"
                               class="text-blue-700 hover:text-blue-900 hover:underline">
                                <?= h($fila['organismo']) ?>
                            </a>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= (int) $fila['cantidad'] ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion4($fila['total'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-blue-50 font-bold">
                        <td class="px-4 py-4 text-gray-900 border-t border-gray-200">
                            TOTAL<?= !empty($datosSeccion4['desc_cpto_seleccionado']) ? ' (filtro concepto)' : '' ?> — TODOS LOS ORGANISMOS
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap">
                            <?= (int) $datosSeccion4['cantidad_general'] ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap text-lg">
                            <?= h(informeFormatoSeccion4($datosSeccion4['total_general'])) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-4 text-sm">
            <div>
                <span class="text-gray-500">Total organismo:</span>
                <strong class="text-gray-900 font-mono ml-1"><?= h(informeFormatoSeccion4($datosSeccion4['total_organismo_seleccionado'])) ?></strong>
                <span class="text-gray-400">(<?= (int) $datosSeccion4['cantidad_organismo_seleccionado'] ?> pago(s))</span>
            </div>
            <div>
                <span class="text-gray-500">Total todos los organismos:</span>
                <strong class="text-gray-900 font-mono ml-1"><?= h(informeFormatoSeccion4($datosSeccion4['total_general'])) ?></strong>
                <span class="text-gray-400">(<?= (int) $datosSeccion4['cantidad_general'] ?> pago(s))</span>
            </div>
        </div>
        <div class="overflow-x-auto table-scroll">
            <table class="w-full informe-tabla min-w-[960px]">
                <thead>
                    <tr class="bg-slate-800 text-white">
                        <th class="px-4 py-4 text-left font-semibold">FecPag</th>
                        <th class="px-4 py-4 text-right font-semibold">ExpedN</th>
                        <th class="px-4 py-4 text-right font-semibold">ExpedA</th>
                        <th class="px-4 py-4 text-right font-semibold">NroChe</th>
                        <th class="px-4 py-4 text-right font-semibold">Importe</th>
                        <th class="px-4 py-4 text-left font-semibold">DescCpto</th>
                        <th class="px-4 py-4 text-left font-semibold">DescSubCpto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datosSeccion4['filas_detalle'] as $fila): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoFechaDistribucion($fila['fecpag'])) ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= $fila['expedn'] !== null ? h((string) $fila['expedn']) : '' ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= $fila['expeda'] !== null ? h((string) $fila['expeda']) : '' ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= $fila['nroche'] !== null ? h((string) $fila['nroche']) : '' ?>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-900 border-t border-gray-100 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion4($fila['importe'])) ?>
                        </td>
                        <td class="px-4 py-3 text-gray-800 border-t border-gray-100"><?= h($fila['desccpto']) ?></td>
                        <td class="px-4 py-3 text-gray-800 border-t border-gray-100"><?= h($fila['descsubcpto']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-blue-50 font-bold">
                        <td class="px-4 py-4 text-gray-900 border-t border-gray-200" colspan="4">
                            TOTAL <?= h($datosSeccion4['organismo_seleccionado']) ?>
                        </td>
                        <td class="px-4 py-4 text-right text-gray-900 border-t border-gray-200 font-mono whitespace-nowrap">
                            <?= h(informeFormatoSeccion4($datosSeccion4['total_organismo_seleccionado'])) ?>
                        </td>
                        <td class="px-4 py-4 border-t border-gray-200" colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <a href="<?= h(informeUrlExportar(
                $localidadId,
                4,
                null,
                null,
                null,
                null,
                $datosSeccion4['organismo_seleccionado'] ?? 'todos',
                $datosSeccion4['desc_cpto_seleccionado'] ?? 'todos',
                $datosSeccion4['fecha_desde_filtro'] ?? '',
                $datosSeccion4['fecha_hasta_filtro'] ?? ''
            )) ?>"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
    </div>

    <?php elseif ($seccion !== 4): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="py-16 text-center px-5">
            <i class="fas fa-tools text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-gray-500 font-medium text-lg">Sección <?= $seccion ?> en desarrollo</p>
            <p class="text-gray-400 text-base mt-1">Próximamente disponible para <?= h(nombreLocalidad($localidadActual['localidad'])) ?>.</p>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <a href="<?= h(informeUrlExportar($localidadId, $seccion)) ?>"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('[data-informe-formula-ayuda]').forEach((contenedor) => {
    const boton = contenedor.querySelector('button');
    const panel = contenedor.querySelector('.informe-formula-ayuda__panel');
    if (!boton || !panel) return;

    boton.addEventListener('click', (event) => {
        event.stopPropagation();
        const abierto = contenedor.classList.contains('is-open');
        document.querySelectorAll('[data-informe-formula-ayuda].is-open').forEach((otro) => {
            otro.classList.remove('is-open');
            const otroBoton = otro.querySelector('button');
            if (otroBoton) otroBoton.setAttribute('aria-expanded', 'false');
        });
        if (!abierto) {
            contenedor.classList.add('is-open');
            boton.setAttribute('aria-expanded', 'true');
        }
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('[data-informe-formula-ayuda].is-open').forEach((contenedor) => {
        contenedor.classList.remove('is-open');
        const boton = contenedor.querySelector('button');
        if (boton) boton.setAttribute('aria-expanded', 'false');
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('[data-informe-formula-ayuda].is-open').forEach((contenedor) => {
        contenedor.classList.remove('is-open');
        const boton = contenedor.querySelector('button');
        if (boton) boton.setAttribute('aria-expanded', 'false');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
