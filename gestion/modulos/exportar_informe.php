<?php
require_once '../includes/config.php';
require_once '../includes/informes_helpers.php';
requireLogin();

$db = getDB();
$seccion = max(1, min(4, (int) ($_GET['seccion'] ?? 1)));
$localidadId = (int) ($_GET['localidad_id'] ?? 0);
$anio = (int) date('Y');
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

try {
    if ($localidadId <= 0) {
        throw new RuntimeException('Debe seleccionar una localidad.');
    }
    informeExportarSeccion(
        $db,
        $seccion,
        $localidadId,
        $anio,
        $issAnio > 0 ? $issAnio : null,
        $issMes > 0 ? $issMes : null,
        $agentesAnio > 0 ? $agentesAnio : null,
        $agentesMes > 0 ? $agentesMes : null,
        $organismoAportes !== '' ? $organismoAportes : null,
        $descCptoAportes !== '' ? $descCptoAportes : null,
        $fechaDesdeAportes !== '' ? $fechaDesdeAportes : null,
        $fechaHastaAportes !== '' ? $fechaHastaAportes : null,
        $aplicarSacParam,
        $incluirMesAnteriorSeccion2
    );
} catch (Throwable $e) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Error al exportar: ' . $e->getMessage();
    exit;
}
