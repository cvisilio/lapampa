<?php

require_once __DIR__ . '/text_encoding.php';

function informeNombresMes(): array
{
    return [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];
}

function informeEnsureAgentesTable(PDO $db): void
{
    $db->exec("
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
    ");

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

function informeFilasSeccion1(): array
{
    return [
        ['campo' => 'importe_liquidacion', 'label' => 'COPA+FOCOCO+REGALIAS+FODO POBLACIONAL'],
        ['campo' => 'iss', 'label' => 'RETENCIÓN ISS'],
        ['campo' => 'antcop', 'label' => 'ANTCOP'],
        ['campo' => 'pmosblp', 'label' => 'PRESTAMOS BLP'],
        ['campo' => 'opypym', 'label' => 'OPyPym'],
        ['campo' => 'otros', 'label' => 'RETENCIÓN PARQUE AUTOMOTOR'],
        ['campo' => 'suma_neto', 'label' => 'Neto', 'destacado' => true],
    ];
}

function informeMesMax(PDO $db, int $anio): int
{
    return (int) $db->query("SELECT COALESCE(MAX(mes), 0) FROM retenciones WHERE anio = {$anio}")->fetchColumn();
}

function informeAgeRetLocalidad(PDO $db, int $localidadId): ?int
{
    $stmt = $db->prepare('SELECT AgeRet FROM localidades WHERE id = ? LIMIT 1');
    $stmt->execute([$localidadId]);
    $valor = $stmt->fetchColumn();
    if ($valor === false || $valor === null || $valor === '') {
        return null;
    }

    return (int) $valor;
}

function informeIssPeriodosDisponibles(PDO $db, int $localidadId): array
{
    $stmt = $db->prepare('
        SELECT DISTINCT anio, mes
        FROM deuda_iss
        WHERE id_localidad = ? AND id_concepto = 3
        ORDER BY anio DESC, mes DESC
    ');
    $stmt->execute([$localidadId]);

    return array_map(static fn(array $row): array => [
        'anio' => (int) $row['anio'],
        'mes' => (int) $row['mes'],
    ], $stmt->fetchAll());
}

function informeIssSeleccionarPeriodo(array $periodos, ?int $anio, ?int $mes): ?array
{
    if ($periodos === []) {
        return null;
    }

    if ($anio !== null && $anio > 0) {
        $delAnio = array_values(array_filter(
            $periodos,
            static fn(array $p): bool => $p['anio'] === $anio
        ));
        if ($delAnio === []) {
            return null;
        }
        if ($mes !== null && $mes > 0) {
            foreach ($delAnio as $periodo) {
                if ($periodo['mes'] === $mes) {
                    return $periodo;
                }
            }

            return null;
        }

        usort($delAnio, static fn(array $a, array $b): int => $b['mes'] <=> $a['mes']);

        return $delAnio[0];
    }

    return $periodos[0];
}

function informeIssMesMax(PDO $db, int $localidadId, int $anio): int
{
    $stmt = $db->prepare('SELECT COALESCE(MAX(mes), 0) FROM deuda_iss WHERE id_localidad = ? AND anio = ?');
    $stmt->execute([$localidadId, $anio]);

    return (int) $stmt->fetchColumn();
}

function informeIssImporteConcepto(PDO $db, int $localidadId, int $anio, int $mes, int $concepto): ?float
{
    $stmt = $db->prepare('
        SELECT COALESCE(SUM(importe), 0)
        FROM deuda_iss
        WHERE id_localidad = ? AND anio = ? AND mes = ? AND id_concepto = ?
    ');
    $stmt->execute([$localidadId, $anio, $mes, $concepto]);
    $valor = $stmt->fetchColumn();

    return $valor !== false ? (float) $valor : null;
}

function informeSacPorDefectoSeccion3(int $mes): bool
{
    return in_array($mes, [6, 12], true);
}

function informeResolverAplicarSacSeccion3(int $mes, ?string $aplicarSacParam): bool
{
    if ($aplicarSacParam === '1') {
        return true;
    }
    if ($aplicarSacParam === '0') {
        return false;
    }

    return informeSacPorDefectoSeccion3($mes);
}

function informeMesMultiplicaSeccion3(bool $aplicarSac): float
{
    return $aplicarSac ? 1.5 : 1.0;
}

function informeCalcularSeccion3DesdeAgentes(array $agentes, bool $aplicarSac): array
{
    $mesMultiplica = informeMesMultiplicaSeccion3($aplicarSac);
    $remuneracion = (float) ($agentes['total_remuneracion'] ?? 0) * $mesMultiplica * 0.83;
    $adicSinAporte = ((float) ($agentes['total_aportes'] ?? 0) * $mesMultiplica)
        + (float) ($agentes['total_asignacion_familiar'] ?? 0);
    $totalGeneral = $remuneracion + $adicSinAporte;

    return [
        'remuneracion' => $remuneracion,
        'adic_sin_aporte' => $adicSinAporte,
        'total_general' => $totalGeneral,
        'mes_multiplica' => $mesMultiplica,
        'aplicar_sac' => $aplicarSac,
    ];
}

function informeAyudaRemunerConAporte(bool $aplicarSac): array
{
    $factor = $aplicarSac ? '1,5' : '1';

    return [
        'lineas' => [
            'agentes.total_remuneracion × ' . $factor . ' × 0,83',
            '= REMUNER C/APORTE',
        ],
        'resumen' => $aplicarSac
            ? 'total_remuneracion × 1,5 × 0,83'
            : 'total_remuneracion × 0,83',
    ];
}

function informeAyudaAdicSinAporte(bool $aplicarSac): array
{
    $factor = $aplicarSac ? '1,5' : '1';

    return [
        'lineas' => [
            '(agentes.total_aportes × ' . $factor . ($aplicarSac ? ' con SAC)' : ')'),
            '+ agentes.total_asignacion_familiar',
            '= ADIC SIN APORTE',
        ],
        'resumen' => $aplicarSac
            ? '(total_aportes × 1,5) + total_asignacion_familiar'
            : 'total_aportes + total_asignacion_familiar',
    ];
}

function informeAgentesPeriodosDisponibles(PDO $db, int $localidadId): array
{
    informeEnsureAgentesTable($db);

    $stmt = $db->prepare('
        SELECT DISTINCT a.mes, a.anio
        FROM agentes a
        INNER JOIN (
            SELECT mes, anio, MAX(numero_importacion) AS max_importacion
            FROM agentes
            WHERE id_dependencia = ?
            GROUP BY mes, anio
        ) ult ON ult.mes = a.mes AND ult.anio = a.anio AND ult.max_importacion = a.numero_importacion
        WHERE a.id_dependencia = ?
        ORDER BY a.anio DESC, a.mes DESC
    ');
    $stmt->execute([$localidadId, $localidadId]);

    return array_map(static fn(array $row): array => [
        'mes' => (int) $row['mes'],
        'anio' => (int) $row['anio'],
    ], $stmt->fetchAll());
}

function informeAgentesPorPeriodo(PDO $db, int $localidadId, int $anio, int $mes): ?array
{
    $stmt = $db->prepare('
        SELECT a.total_empleados, a.total_remuneracion, a.total_aportes,
               a.total_asignacion_familiar, a.mes, a.anio
        FROM agentes a
        INNER JOIN (
            SELECT MAX(numero_importacion) AS max_importacion
            FROM agentes
            WHERE id_dependencia = ? AND anio = ? AND mes = ?
        ) ult ON ult.max_importacion = a.numero_importacion
        WHERE a.id_dependencia = ? AND a.anio = ? AND a.mes = ?
        LIMIT 1
    ');
    $stmt->execute([$localidadId, $anio, $mes, $localidadId, $anio, $mes]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $mes = (int) $row['mes'];
    $nombresMes = informeNombresMes();

    return [
        'total_empleados' => (int) $row['total_empleados'],
        'total_remuneracion' => (float) $row['total_remuneracion'],
        'total_aportes' => (float) $row['total_aportes'],
        'total_asignacion_familiar' => (float) $row['total_asignacion_familiar'],
        'mes' => $mes,
        'anio' => (int) $row['anio'],
        'mes_nombre' => $nombresMes[$mes] ?? (string) $mes,
    ];
}

function informeCargarSeccion3(
    PDO $db,
    int $localidadId,
    ?int $anio = null,
    ?int $mes = null,
    ?string $aplicarSacParam = null
): array
{
    $stmt = $db->prepare('SELECT id, localidad, cantidad_habitantes FROM localidades WHERE id = ? LIMIT 1');
    $stmt->execute([$localidadId]);
    $localidad = $stmt->fetch();
    if (!$localidad) {
        return ['error' => 'localidad_no_encontrada'];
    }

    $periodosDisponibles = informeAgentesPeriodosDisponibles($db, $localidadId);
    if ($periodosDisponibles === []) {
        return [
            'error' => 'sin_agentes',
            'localidad' => $localidad,
            'localidad_id' => $localidadId,
        ];
    }

    $seleccion = informeIssSeleccionarPeriodo($periodosDisponibles, $anio, $mes);
    if ($seleccion === null && $anio !== null && $anio > 0) {
        $seleccion = informeIssSeleccionarPeriodo($periodosDisponibles, $anio, null);
    }
    if ($seleccion === null) {
        $seleccion = $periodosDisponibles[0];
    }

    $anio = $seleccion['anio'];
    $mes = $seleccion['mes'];

    $agentes = informeAgentesPorPeriodo($db, $localidadId, $anio, $mes);
    if ($agentes === null) {
        return [
            'error' => 'sin_agentes_periodo',
            'localidad' => $localidad,
            'localidad_id' => $localidadId,
            'anio' => $anio,
            'mes' => $mes,
        ];
    }

    $nombresMes = informeNombresMes();
    $mesNombre = $nombresMes[$mes] ?? (string) $mes;
    $aplicarSac = informeResolverAplicarSacSeccion3($mes, $aplicarSacParam);
    $calculos = informeCalcularSeccion3DesdeAgentes($agentes, $aplicarSac);

    return array_merge([
        'localidad' => $localidad,
        'localidad_id' => $localidadId,
        'anio' => $anio,
        'mes' => $mes,
        'mes_nombre' => $mesNombre,
        'leyenda' => 'MASA SALARIAL mes de ' . $mesNombre . ' con SAC y Adicionales',
        'periodos_disponibles' => $periodosDisponibles,
        'periodos_agentes' => $periodosDisponibles,
        'empleados' => $agentes,
        'sac_por_defecto' => informeSacPorDefectoSeccion3($mes),
    ], $calculos);
}

function informeFormatoSeccion3(?float $valor): string
{
    if ($valor === null) {
        return '';
    }

    return '$' . number_format($valor, 0, ',', '.');
}

function informeFormatoExportacionSeccion3(?float $valor): string
{
    if ($valor === null) {
        return '';
    }

    return (string) (int) round($valor);
}

function informeColumnasSeccion2(): array
{
    return [
        ['key' => 'proyecciones', 'label' => 'Proyecciones', 'align' => 'left'],
        ['key' => 'copa_total', 'label' => 'Copa Total', 'align' => 'right'],
        ['key' => 'ret_iss', 'label' => 'RET ISS', 'align' => 'right'],
        ['key' => 'ret_ant_dto294', 'label' => 'Ret Ant /DTO 294', 'align' => 'right'],
        ['key' => 'neto', 'label' => 'Neto', 'align' => 'right', 'destacado' => true],
    ];
}

function informeRangoMesAnteriorSeccion2(?string $fechaReferencia = null): array
{
    $ref = new DateTime($fechaReferencia ?? 'today');
    $inicio = (clone $ref)->modify('first day of last month');
    $fin = (clone $ref)->modify('last day of last month');

    return [
        'inicio' => $inicio->format('Y-m-d'),
        'fin' => $fin->format('Y-m-d'),
        'mes' => (int) $inicio->format('n'),
        'anio' => (int) $inicio->format('Y'),
    ];
}

function informeEtiquetaMesAnteriorSeccion2(?string $fechaReferencia = null): string
{
    $rango = informeRangoMesAnteriorSeccion2($fechaReferencia);
    $nombresMes = informeNombresMes();

    return $nombresMes[$rango['mes']] ?? (string) $rango['mes'];
}

function informeFiltrarFilasProyeccionesSeccion2(
    array $filas,
    bool $incluirMesAnterior,
    ?string $fechaReferencia = null
): array {
    $hoy = $fechaReferencia ?? date('Y-m-d');
    $rangoMesAnterior = informeRangoMesAnteriorSeccion2($fechaReferencia);

    $filtradas = array_filter($filas, static function (array $fila) use ($hoy, $incluirMesAnterior, $rangoMesAnterior): bool {
        $fecha = (string) ($fila['fecha'] ?? '');
        if ($fecha === '') {
            return false;
        }
        if ($fecha > $hoy) {
            return true;
        }
        if ($incluirMesAnterior
            && $fecha >= $rangoMesAnterior['inicio']
            && $fecha <= $rangoMesAnterior['fin']) {
            return true;
        }

        return false;
    });

    return array_values($filtradas);
}

function informeHayProyeccionesMesAnteriorSeccion2(array $filas, ?string $fechaReferencia = null): bool
{
    $rango = informeRangoMesAnteriorSeccion2($fechaReferencia);
    foreach ($filas as $fila) {
        $fecha = (string) ($fila['fecha'] ?? '');
        if ($fecha >= $rango['inicio'] && $fecha <= $rango['fin']) {
            return true;
        }
    }

    return false;
}

function informeAplicarFiltroProyeccionesSeccion2(array $datos, bool $incluirMesAnterior): array
{
    if (!isset($datos['filas']) || isset($datos['error'])) {
        return $datos;
    }

    $filasTodas = $datos['filas'];
    $filas = informeFiltrarFilasProyeccionesSeccion2($filasTodas, $incluirMesAnterior);

    $datos['filas_todas'] = $filasTodas;
    $datos['filas'] = $filas;
    $datos['total_copa'] = array_sum(array_column($filas, 'importe'));
    $datos['incluir_mes_anterior'] = $incluirMesAnterior;
    $datos['tiene_mes_anterior'] = informeHayProyeccionesMesAnteriorSeccion2($filasTodas);
    $datos['mes_anterior_etiqueta'] = informeEtiquetaMesAnteriorSeccion2();
    $datos['rango_mes_anterior'] = informeRangoMesAnteriorSeccion2();

    return $datos;
}

function informeCargarSeccion2(PDO $db, int $localidadId, ?int $anio = null): array
{
    $anio = $anio ?? (int) date('Y');

    $stmt = $db->prepare('SELECT id, localidad FROM localidades WHERE id = ? LIMIT 1');
    $stmt->execute([$localidadId]);
    $localidad = $stmt->fetch();
    if (!$localidad) {
        return ['error' => 'localidad_no_encontrada'];
    }

    $stmtDist = $db->prepare('
        SELECT d.fecha, d.importe
        FROM distribucion_semanal d
        INNER JOIN (
            SELECT fecha, MAX(numero_importacion) AS max_importacion
            FROM distribucion_semanal
            WHERE id_loclidad = ?
            GROUP BY fecha
        ) ult ON ult.fecha = d.fecha AND ult.max_importacion = d.numero_importacion
        WHERE d.id_loclidad = ?
        ORDER BY d.fecha ASC
    ');
    $stmtDist->execute([$localidadId, $localidadId]);

    $filas = [];
    foreach ($stmtDist->fetchAll() as $row) {
        $filas[] = [
            'fecha' => $row['fecha'],
            'importe' => (float) $row['importe'],
        ];
    }

    if ($filas === []) {
        return [
            'error' => 'sin_distribucion',
            'localidad' => $localidad,
        ];
    }

    $totalCopa = array_sum(array_column($filas, 'importe'));
    $deudaIss = informeCargarDeudaIssUltimoPeriodo($db, $localidadId);
    $retAntDto294 = informeCargarParqueAutomotorUltimoMes($db, $localidadId, $anio);

    return [
        'localidad' => $localidad,
        'filas' => $filas,
        'total_copa' => $totalCopa,
        'deuda_iss' => $deudaIss,
        'ret_ant_dto294' => $retAntDto294,
    ];
}

function informeCargarDeudaIssUltimoPeriodo(PDO $db, int $localidadId): ?array
{
    $tabla = $db->query("SHOW TABLES LIKE 'deuda_iss'")->fetchColumn();
    if (!$tabla) {
        return null;
    }

    $stmtPeriodo = $db->prepare('
        SELECT anio, mes
        FROM deuda_iss
        WHERE id_localidad = ?
        ORDER BY anio DESC, mes DESC
        LIMIT 1
    ');
    $stmtPeriodo->execute([$localidadId]);
    $periodo = $stmtPeriodo->fetch();
    if (!$periodo) {
        return null;
    }

    $stmtTotal = $db->prepare('
        SELECT COALESCE(SUM(importe), 0)
        FROM deuda_iss
        WHERE id_localidad = ? AND anio = ? AND mes = ?
    ');
    $stmtTotal->execute([$localidadId, (int) $periodo['anio'], (int) $periodo['mes']]);

    return [
        'anio' => (int) $periodo['anio'],
        'mes' => (int) $periodo['mes'],
        'importe' => (float) $stmtTotal->fetchColumn(),
    ];
}

function informeCargarParqueAutomotorUltimoMes(PDO $db, int $localidadId, int $anio): ?array
{
    $stmt = $db->prepare('
        SELECT r.mes, r.otros
        FROM retenciones r
        INNER JOIN (
            SELECT mes, MAX(numero_importacion) AS max_importacion
            FROM retenciones
            WHERE id_localidad = ? AND anio = ?
            GROUP BY mes
        ) ult ON ult.mes = r.mes AND ult.max_importacion = r.numero_importacion
        WHERE r.id_localidad = ? AND r.anio = ?
        ORDER BY r.mes DESC
        LIMIT 1
    ');
    $stmt->execute([$localidadId, $anio, $localidadId, $anio]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    return [
        'anio' => $anio,
        'mes' => (int) $row['mes'],
        'importe' => (float) $row['otros'],
    ];
}

function informeFormatoFechaDistribucion(?string $fecha): string
{
    if ($fecha === null || $fecha === '') {
        return '';
    }

    $ts = strtotime($fecha);
    if ($ts === false) {
        return $fecha;
    }

    return date('d/m/Y', $ts);
}

function informeFormatoSeccion2(?float $valor): string
{
    if ($valor === null) {
        return '';
    }

    return '$' . number_format($valor, 0, ',', '.');
}

function informeFormatoExportacionSeccion2(?float $valor): string
{
    if ($valor === null) {
        return '';
    }

    return (string) (int) round($valor);
}

function informeNormalizarOrganismoAportes(?string $organismo): ?string
{
    return informeNormalizarFiltroAportes($organismo);
}

function informeNormalizarFiltroAportes(?string $valor): ?string
{
    $valor = trim((string) $valor);
    if ($valor === '' || $valor === 'todos') {
        return null;
    }

    return $valor;
}

function informeSqlExprOrganismoAportes(): string
{
    return 'COALESCE(NULLIF(TRIM(NombeJur), ""), "(Sin organismo)")';
}

function informeSqlExprConceptoAportes(): string
{
    return 'COALESCE(NULLIF(TRIM(DescCpto), ""), "(Sin concepto)")';
}

function informeNormalizarFechaAportes(?string $fecha): ?string
{
    $fecha = trim((string) $fecha);
    if ($fecha === '') {
        return null;
    }

    $ts = strtotime($fecha);
    if ($ts === false) {
        return null;
    }

    return date('Y-m-d', $ts);
}

/** @return array{0: string, 1: list<mixed>} */
function informeConstruirWhereAportes(
    int $localidadId,
    ?string $organismo,
    ?string $descCpto,
    ?string $fechaDesde = null,
    ?string $fechaHasta = null
): array
{
    $where = ['id_localidad = ?'];
    $params = [$localidadId];

    if ($organismo !== null) {
        $where[] = informeSqlExprOrganismoAportes() . ' = ?';
        $params[] = $organismo;
    }
    if ($descCpto !== null) {
        $where[] = informeSqlExprConceptoAportes() . ' = ?';
        $params[] = $descCpto;
    }
    if ($fechaDesde !== null) {
        $where[] = 'FecPag >= ?';
        $params[] = $fechaDesde;
    }
    if ($fechaHasta !== null) {
        $where[] = 'FecPag <= ?';
        $params[] = $fechaHasta;
    }

    return [implode(' AND ', $where), $params];
}

function informeCargarOpcionesAportes(
    PDO $db,
    int $localidadId,
    ?string $organismo,
    ?string $descCpto,
    ?string $fechaDesde = null,
    ?string $fechaHasta = null
): array
{
    [$where, $params] = informeConstruirWhereAportes($localidadId, null, $descCpto, $fechaDesde, $fechaHasta);
    $stmtOrg = $db->prepare('
        SELECT DISTINCT ' . informeSqlExprOrganismoAportes() . ' AS organismo
        FROM aportes
        WHERE ' . $where . '
        ORDER BY organismo
    ');
    $stmtOrg->execute($params);
    $organismos = array_column($stmtOrg->fetchAll(), 'organismo');

    [$whereC, $paramsC] = informeConstruirWhereAportes($localidadId, $organismo, null, $fechaDesde, $fechaHasta);
    $stmtCpto = $db->prepare('
        SELECT DISTINCT ' . informeSqlExprConceptoAportes() . ' AS concepto
        FROM aportes
        WHERE ' . $whereC . '
        ORDER BY concepto
    ');
    $stmtCpto->execute($paramsC);
    $conceptos = array_column($stmtCpto->fetchAll(), 'concepto');

    return [
        'organismos' => $organismos,
        'conceptos' => $conceptos,
    ];
}

function informeFormatoSeccion4(?float $valor): string
{
    if ($valor === null) {
        return '';
    }

    return '$' . number_format($valor, 2, ',', '.');
}

function informeFormatoExportacionSeccion4(?float $valor): string
{
    if ($valor === null) {
        return '';
    }

    return number_format($valor, 2, '.', '');
}

function informeCargarSeccion4(
    PDO $db,
    int $localidadId,
    ?string $organismoFiltro,
    ?string $descCptoFiltro = null,
    ?string $fechaDesdeFiltro = null,
    ?string $fechaHastaFiltro = null
): array
{
    $stmt = $db->prepare('SELECT id, localidad FROM localidades WHERE id = ? LIMIT 1');
    $stmt->execute([$localidadId]);
    $localidad = $stmt->fetch();
    if (!$localidad) {
        return ['error' => 'localidad_no_encontrada'];
    }

    $organismoFiltro = informeNormalizarFiltroAportes($organismoFiltro);
    $descCptoFiltro = informeNormalizarFiltroAportes($descCptoFiltro);
    $fechaDesdeFiltro = informeNormalizarFechaAportes($fechaDesdeFiltro);
    $fechaHastaFiltro = informeNormalizarFechaAportes($fechaHastaFiltro);
    if ($fechaDesdeFiltro !== null && $fechaHastaFiltro !== null && $fechaDesdeFiltro > $fechaHastaFiltro) {
        [$fechaDesdeFiltro, $fechaHastaFiltro] = [$fechaHastaFiltro, $fechaDesdeFiltro];
    }

    $stmtBase = $db->prepare('SELECT COUNT(*) FROM aportes WHERE id_localidad = ?');
    $stmtBase->execute([$localidadId]);
    if ((int) $stmtBase->fetchColumn() === 0) {
        return [
            'error' => 'sin_aportes',
            'localidad' => $localidad,
        ];
    }

    $opciones = informeCargarOpcionesAportes(
        $db,
        $localidadId,
        $organismoFiltro,
        $descCptoFiltro,
        $fechaDesdeFiltro,
        $fechaHastaFiltro
    );

    [$where, $params] = informeConstruirWhereAportes(
        $localidadId,
        $organismoFiltro,
        $descCptoFiltro,
        $fechaDesdeFiltro,
        $fechaHastaFiltro
    );

    $stmtTotales = $db->prepare('
        SELECT
            ' . informeSqlExprOrganismoAportes() . ' AS organismo,
            COUNT(*) AS cantidad,
            SUM(importe) AS total
        FROM aportes
        WHERE ' . $where . '
        GROUP BY ' . informeSqlExprOrganismoAportes() . '
        ORDER BY organismo
    ');
    $stmtTotales->execute($params);
    $totalesPorOrganismo = [];
    foreach ($stmtTotales->fetchAll() as $row) {
        $totalesPorOrganismo[] = [
            'organismo' => (string) $row['organismo'],
            'cantidad' => (int) $row['cantidad'],
            'total' => (float) $row['total'],
        ];
    }

    if ($totalesPorOrganismo === []) {
        return [
            'error' => 'sin_aportes_filtro',
            'localidad' => $localidad,
            'organismo_seleccionado' => $organismoFiltro,
            'desc_cpto_seleccionado' => $descCptoFiltro,
            'fecha_desde_filtro' => $fechaDesdeFiltro,
            'fecha_hasta_filtro' => $fechaHastaFiltro,
            'organismos_disponibles' => $opciones['organismos'],
            'conceptos_disponibles' => $opciones['conceptos'],
        ];
    }

    $stmtGeneral = $db->prepare('
        SELECT COUNT(*) AS cantidad, COALESCE(SUM(importe), 0) AS total,
               MIN(FecPag) AS fecha_desde, MAX(FecPag) AS fecha_hasta
        FROM aportes
        WHERE ' . $where . '
    ');
    $stmtGeneral->execute($params);
    $general = $stmtGeneral->fetch() ?: [];

    $filasDetalle = [];
    $totalOrganismoSeleccionado = null;
    $cantidadOrganismoSeleccionado = null;

    if ($organismoFiltro !== null) {
        $stmtDetalle = $db->prepare('
            SELECT FecPag, ExpedN, ExpedA, NombeJur, NroChe, importe, DescCpto, DescSubCpto
            FROM aportes
            WHERE ' . $where . '
            ORDER BY FecPag ASC, NroChe ASC, ExpedN ASC
        ');
        $stmtDetalle->execute($params);

        foreach ($stmtDetalle->fetchAll() as $row) {
            $filasDetalle[] = [
                'fecpag' => $row['FecPag'],
                'expedn' => $row['ExpedN'] !== null ? (int) $row['ExpedN'] : null,
                'expeda' => $row['ExpedA'] !== null ? (int) $row['ExpedA'] : null,
                'nombejur' => $row['NombeJur'] ?? '',
                'nroche' => $row['NroChe'] !== null ? (int) $row['NroChe'] : null,
                'importe' => (float) $row['importe'],
                'desccpto' => (string) ($row['DescCpto'] ?? ''),
                'descsubcpto' => (string) ($row['DescSubCpto'] ?? ''),
            ];
        }

        if ($filasDetalle === []) {
            return [
                'error' => 'sin_aportes_filtro',
                'localidad' => $localidad,
                'organismo_seleccionado' => $organismoFiltro,
                'desc_cpto_seleccionado' => $descCptoFiltro,
                'fecha_desde_filtro' => $fechaDesdeFiltro,
                'fecha_hasta_filtro' => $fechaHastaFiltro,
                'organismos_disponibles' => $opciones['organismos'],
                'conceptos_disponibles' => $opciones['conceptos'],
            ];
        }

        $totalOrganismoSeleccionado = array_sum(array_column($filasDetalle, 'importe'));
        $cantidadOrganismoSeleccionado = count($filasDetalle);
    }

    return [
        'localidad' => $localidad,
        'organismo_seleccionado' => $organismoFiltro,
        'desc_cpto_seleccionado' => $descCptoFiltro,
        'fecha_desde_filtro' => $fechaDesdeFiltro,
        'fecha_hasta_filtro' => $fechaHastaFiltro,
        'organismos_disponibles' => $opciones['organismos'],
        'conceptos_disponibles' => $opciones['conceptos'],
        'totales_por_organismo' => $totalesPorOrganismo,
        'total_general' => (float) ($general['total'] ?? 0),
        'cantidad_general' => (int) ($general['cantidad'] ?? 0),
        'fecha_desde' => $general['fecha_desde'] ?? null,
        'fecha_hasta' => $general['fecha_hasta'] ?? null,
        'filas_detalle' => $filasDetalle,
        'total_organismo_seleccionado' => $totalOrganismoSeleccionado,
        'cantidad_organismo_seleccionado' => $cantidadOrganismoSeleccionado,
    ];
}

function informeCargarRetenciones(PDO $db, int $localidadId, int $anio): array
{
    $stmtRet = $db->prepare('
        SELECT r.mes, r.importe_liquidacion, r.iss, r.antcop, r.pmosblp, r.opypym, r.otros, r.suma_neto
        FROM localidades l
        INNER JOIN retenciones r ON r.id_localidad = l.id AND r.anio = ?
        INNER JOIN (
            SELECT mes, MAX(numero_importacion) AS max_importacion
            FROM retenciones
            WHERE id_localidad = ? AND anio = ?
            GROUP BY mes
        ) ult ON ult.mes = r.mes AND ult.max_importacion = r.numero_importacion
        WHERE l.id = ? AND r.id_localidad = ?
        ORDER BY r.mes
    ');
    $stmtRet->execute([$anio, $localidadId, $anio, $localidadId, $localidadId]);

    $datosPorMes = [];
    foreach ($stmtRet->fetchAll() as $row) {
        $datosPorMes[(int) $row['mes']] = $row;
    }

    return $datosPorMes;
}

function informeFormatoPantalla(?float $valor, string $campo): string
{
    if ($valor === null) {
        return '';
    }
    if ($valor == 0.0 && in_array($campo, ['pmosblp', 'antcop'], true)) {
        return '';
    }
    return '$' . number_format($valor, 0, ',', '.');
}

function informeFormatoExportacion(?float $valor, string $campo): string
{
    if ($valor === null) {
        return '';
    }
    if ($valor == 0.0 && in_array($campo, ['pmosblp', 'antcop'], true)) {
        return '';
    }
    return (string) (int) round($valor);
}

function informeSlugLocalidad(string $nombre): string
{
    $slug = mb_strtolower($nombre, 'UTF-8');
    $slug = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', ' '],
        ['a', 'e', 'i', 'o', 'u', 'n', '_'],
        $slug
    );
    $slug = preg_replace('/[^a-z0-9_]+/', '', $slug) ?? $slug;
    return trim($slug, '_') ?: 'localidad';
}

function informeEnviarCsv(string $filename, array $rows): never
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    foreach ($rows as $row) {
        fputcsv($out, $row, ';');
    }
    fclose($out);
    exit;
}

/** @param array<string, string> $extras */
function informeFilasEncabezadoExport(array $localidad, int $seccion, array $extras = []): array
{
    $titulos = [
        1 => '1 - Retenciones mensuales',
        2 => '2 - Distribución semanal',
        3 => '3 - Masa salarial',
        4 => '4 - Detalle de pagos',
    ];

    $rows = [
        ['Localidad', nombreLocalidad($localidad['localidad'])],
        ['Sección', $titulos[$seccion] ?? (string) $seccion],
    ];

    foreach ($extras as $label => $valor) {
        $rows[] = [$label, $valor];
    }

    $rows[] = [];

    return $rows;
}

function informeExportarSeccion(
    PDO $db,
    int $seccion,
    int $localidadId,
    int $anio,
    ?int $issAnio = null,
    ?int $issMes = null,
    ?int $agentesAnio = null,
    ?int $agentesMes = null,
    ?string $organismoAportes = null,
    ?string $descCptoAportes = null,
    ?string $fechaDesdeAportes = null,
    ?string $fechaHastaAportes = null,
    ?string $aplicarSacParam = null,
    ?bool $incluirMesAnteriorSeccion2 = null
): never
{
    $stmtLoc = $db->prepare('SELECT id, localidad FROM localidades WHERE id = ? LIMIT 1');
    $stmtLoc->execute([$localidadId]);
    $localidad = $stmtLoc->fetch();
    if (!$localidad) {
        throw new RuntimeException('Localidad no encontrada.');
    }

    $slug = informeSlugLocalidad(nombreLocalidad($localidad['localidad']));
    $filename = "informe_seccion_{$seccion}_{$slug}_{$anio}.csv";

    if ($seccion === 1) {
        $mesMax = informeMesMax($db, $anio);
        if ($mesMax < 1) {
            throw new RuntimeException("No hay retenciones cargadas para el año {$anio}.");
        }

        $datosPorMes = informeCargarRetenciones($db, $localidadId, $anio);
        if ($datosPorMes === []) {
            throw new RuntimeException('No hay retenciones para esta localidad en el año seleccionado.');
        }

        $nombresMes = informeNombresMes();
        $filas = informeFilasSeccion1();
        $rows = informeFilasEncabezadoExport($localidad, 1, ['Año' => (string) $anio]);

        $header = [''];
        for ($m = 1; $m <= $mesMax; $m++) {
            $header[] = $nombresMes[$m] ?? (string) $m;
        }
        $rows[] = $header;
        $rows[] = array_fill(0, $mesMax + 1, '');

        foreach ($filas as $idx => $fila) {
            if ($idx > 0) {
                $rows[] = array_fill(0, $mesMax + 1, '');
            }
            $linea = [$fila['label']];
            for ($m = 1; $m <= $mesMax; $m++) {
                $campo = $fila['campo'];
                $valor = isset($datosPorMes[$m]) ? (float) ($datosPorMes[$m][$campo] ?? 0) : null;
                $linea[] = informeFormatoExportacion($valor, $campo);
            }
            $rows[] = $linea;
        }

        informeEnviarCsv($filename, $rows);
    }

    if ($seccion === 2) {
        $incluirMesAnterior = $incluirMesAnteriorSeccion2 === true;
        $datos = informeCargarSeccion2($db, $localidadId, $anio);
        if (isset($datos['error'])) {
            $mensajes = [
                'localidad_no_encontrada' => 'Localidad no encontrada.',
                'sin_distribucion' => 'No hay distribución semanal cargada para «'
                    . nombreLocalidad($localidad['localidad']) . '».',
            ];
            throw new RuntimeException($mensajes[$datos['error']] ?? 'No se pudo generar la sección 2.');
        }
        $datos = informeAplicarFiltroProyeccionesSeccion2($datos, $incluirMesAnterior);

        $fechaDesde = $datos['filas'][0]['fecha'] ?? null;
        $fechaHasta = $datos['filas'][count($datos['filas']) - 1]['fecha'] ?? null;
        $extras = [
            'Vista' => $incluirMesAnterior
                ? 'Mes anterior + fechas posteriores'
                : 'Solo fechas posteriores a hoy',
        ];
        if ($fechaDesde && $fechaHasta) {
            $periodo = informeFormatoFechaDistribucion($fechaDesde);
            if ($fechaHasta !== $fechaDesde) {
                $periodo .= ' - ' . informeFormatoFechaDistribucion($fechaHasta);
            }
            $extras['Período'] = $periodo;
        }

        $rows = informeFilasEncabezadoExport($localidad, 2, $extras);
        $rows[] = ['Proyecciones', 'Copa Total', 'RET ISS', 'Ret Ant /DTO 294', 'Neto'];
        foreach ($datos['filas'] as $fila) {
            $rows[] = [
                informeFormatoFechaDistribucion($fila['fecha']),
                informeFormatoExportacionSeccion2($fila['importe']),
                '',
                '',
                '',
            ];
        }
        $totalCopa = (float) ($datos['total_copa'] ?? 0);
        $deudaIss = !empty($datos['deuda_iss']) ? (float) $datos['deuda_iss']['importe'] : 0.0;
        $retAntDto294 = !empty($datos['ret_ant_dto294']) ? (float) $datos['ret_ant_dto294']['importe'] : 0.0;
        $rows[] = [
            'TOTAL',
            informeFormatoExportacionSeccion2($totalCopa),
            !empty($datos['deuda_iss']) ? informeFormatoExportacionSeccion2($deudaIss) : '',
            !empty($datos['ret_ant_dto294']) ? informeFormatoExportacionSeccion2($retAntDto294) : '',
            informeFormatoExportacionSeccion2($totalCopa - $deudaIss - $retAntDto294),
        ];

        informeEnviarCsv($filename, $rows);
    }

    if ($seccion === 3) {
        $datos = informeCargarSeccion3($db, $localidadId, $issAnio, $issMes, $aplicarSacParam);
        if (isset($datos['error'])) {
            $mensajes = [
                'localidad_no_encontrada' => 'Localidad no encontrada.',
                'sin_agentes' => 'No hay listado de empleados importado para esta localidad.',
                'sin_agentes_periodo' => 'No hay datos del listado de empleados para '
                    . ($datos['mes'] ?? '') . '/' . ($datos['anio'] ?? '') . '.',
            ];
            throw new RuntimeException($mensajes[$datos['error']] ?? 'No se pudo generar la sección 3.');
        }

        $filename = "informe_seccion_3_{$slug}_{$datos['anio']}_mes{$datos['mes']}.csv";

        $rows = informeFilasEncabezadoExport($localidad, 3, [
            'Período listado empleados' => $datos['mes_nombre'] . ' ' . $datos['anio'],
            'Aplicar SAC (× 1,5)' => !empty($datos['aplicar_sac']) ? 'Sí' : 'No',
        ]);
        $rows[] = ['Mes ' . $datos['mes'], 'REMUNER C/APORTE', 'ADIC SIN APORTE', 'TOTAL GENERAL'];
        $rows[] = [
            $datos['leyenda'],
            informeFormatoExportacionSeccion3($datos['remuneracion']),
            informeFormatoExportacionSeccion3($datos['adic_sin_aporte']),
            informeFormatoExportacionSeccion3($datos['total_general']),
        ];

        $poblacion = $datos['localidad']['cantidad_habitantes'] ?? null;
        if ($poblacion !== null && $poblacion !== '') {
            $rows[] = ['POBLACIÓN', (string) (int) $poblacion];
        }

        if (!empty($datos['empleados'])) {
            $emp = $datos['empleados'];
            $rows[] = [
                'CANTIDAD DE EMPLEADOS (' . $emp['mes_nombre'] . ' ' . $emp['anio'] . ')',
                (string) $emp['total_empleados'],
            ];
        }

        informeEnviarCsv($filename, $rows);
    }

    if ($seccion === 4) {
        $datos = informeCargarSeccion4(
            $db,
            $localidadId,
            $organismoAportes,
            $descCptoAportes,
            $fechaDesdeAportes,
            $fechaHastaAportes
        );
        if (isset($datos['error'])) {
            $mensajes = [
                'sin_aportes' => 'No hay detalle de pagos cargado para esta localidad.',
                'sin_aportes_filtro' => 'No hay pagos con los filtros seleccionados.',
                'sin_aportes_organismo' => 'No hay pagos para el organismo seleccionado.',
                'localidad_no_encontrada' => 'Localidad no encontrada.',
            ];
            throw new RuntimeException($mensajes[$datos['error']] ?? 'No se pudo generar la sección 4.');
        }

        $organismoSlug = $datos['organismo_seleccionado']
            ? informeSlugLocalidad($datos['organismo_seleccionado'])
            : 'todos';
        $conceptoSlug = $datos['desc_cpto_seleccionado']
            ? informeSlugLocalidad($datos['desc_cpto_seleccionado'])
            : 'todos';
        $filename = "informe_seccion_4_{$slug}_{$organismoSlug}_{$conceptoSlug}.csv";

        $extras = [];
        if (!empty($datos['fecha_desde']) && !empty($datos['fecha_hasta'])) {
            $periodo = informeFormatoFechaDistribucion($datos['fecha_desde']);
            if ($datos['fecha_hasta'] !== $datos['fecha_desde']) {
                $periodo .= ' - ' . informeFormatoFechaDistribucion($datos['fecha_hasta']);
            }
            $extras['Período'] = $periodo;
        }
        if ($datos['desc_cpto_seleccionado']) {
            $extras['Concepto (DescCpto)'] = $datos['desc_cpto_seleccionado'];
        }
        if ($datos['organismo_seleccionado']) {
            $extras['Organismo'] = $datos['organismo_seleccionado'];
        }
        if ($datos['fecha_desde_filtro'] || $datos['fecha_hasta_filtro']) {
            $desde = $datos['fecha_desde_filtro'] ? informeFormatoFechaDistribucion($datos['fecha_desde_filtro']) : 'inicio';
            $hasta = $datos['fecha_hasta_filtro'] ? informeFormatoFechaDistribucion($datos['fecha_hasta_filtro']) : 'fin';
            $extras['Filtro fechas'] = $desde . ' - ' . $hasta;
        }

        $rows = informeFilasEncabezadoExport($localidad, 4, $extras);

        if ($datos['organismo_seleccionado']) {
            $rows[] = [];
            $rows[] = ['FecPag', 'ExpedN', 'ExpedA', 'NroChe', 'Importe', 'DescCpto', 'DescSubCpto'];
            foreach ($datos['filas_detalle'] as $fila) {
                $rows[] = [
                    informeFormatoFechaDistribucion($fila['fecpag']),
                    $fila['expedn'] !== null ? (string) $fila['expedn'] : '',
                    $fila['expeda'] !== null ? (string) $fila['expeda'] : '',
                    $fila['nroche'] !== null ? (string) $fila['nroche'] : '',
                    informeFormatoExportacionSeccion4($fila['importe']),
                    $fila['desccpto'],
                    $fila['descsubcpto'],
                ];
            }
            $rows[] = [];
            $rows[] = [
                'TOTAL ' . $datos['organismo_seleccionado'],
                (string) ($datos['cantidad_organismo_seleccionado'] ?? 0) . ' pago(s)',
                '',
                '',
                informeFormatoExportacionSeccion4($datos['total_organismo_seleccionado'] ?? 0),
            ];
            $rows[] = [
                'TOTAL TODOS LOS ORGANISMOS',
                (string) $datos['cantidad_general'] . ' pago(s)',
                '',
                '',
                informeFormatoExportacionSeccion4($datos['total_general']),
            ];
        } else {
            $rows[] = ['Organismo', 'Cantidad', 'Importe total'];
            foreach ($datos['totales_por_organismo'] as $fila) {
                $rows[] = [
                    $fila['organismo'],
                    (string) $fila['cantidad'],
                    informeFormatoExportacionSeccion4($fila['total']),
                ];
            }
            $rows[] = [];
            $rows[] = [
                'TOTAL TODOS LOS ORGANISMOS',
                (string) $datos['cantidad_general'],
                informeFormatoExportacionSeccion4($datos['total_general']),
            ];
        }

        informeEnviarCsv($filename, $rows);
    }

    throw new RuntimeException('Sección de informe no válida.');
}
