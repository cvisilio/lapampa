<?php
/**
 * modulos/exportar.php
 * Exporta datos del sistema a CSV descargable.
 *
 * Uso: exportar.php?tipo=proyectos
 *      exportar.php?tipo=acciones
 *      exportar.php?tipo=funcionarios
 *      exportar.php?tipo=novedades
 *      exportar.php?tipo=localidades
 */

require_once '../includes/config.php';
requireLogin();

$db   = getDB();
$tipo = $_GET['tipo'] ?? 'proyectos';

// ── Función para enviar CSV ────────────────────────────────────
function enviarCSV(string $nombre, array $rows): void {
    $filename = $nombre . '_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    // BOM para que Excel reconozca UTF-8
    fputs($out, "\xEF\xBB\xBF");

    if (!empty($rows)) {
        fputcsv($out, array_keys($rows[0]), ';');
        foreach ($rows as $row) {
            fputcsv($out, $row, ';');
        }
    }
    fclose($out);
    exit;
}

// ── Exportar proyectos ─────────────────────────────────────────
if ($tipo === 'proyectos') {
    $rows = $db->query("
        SELECT
            p.id                       AS 'ID',
            p.nombre                   AS 'Nombre del proyecto',
            p.estado                   AS 'Estado',
            p.prioridad                AS 'Prioridad',
            p.porcentaje_avance        AS '% Avance',
            a.codigo                   AS 'Acción vinculada',
            o.sigla                    AS 'Organismo',
            o.nombre_completo          AS 'Nombre organismo',
            f.nombre_apellido          AS 'Responsable',
            f.cargo                    AS 'Cargo responsable',
            p.localidad                AS 'Localidad',
            p.presupuesto_total        AS 'Presupuesto total',
            p.presupuesto_ejecutado    AS 'Presupuesto ejecutado',
            p.fuente_financiamiento    AS 'Fuente financiamiento',
            p.numero_expediente        AS 'N° Expediente',
            p.fecha_inicio             AS 'Fecha inicio',
            p.fecha_fin_estimada       AS 'Fecha fin estimada',
            p.beneficiarios_estimados  AS 'Beneficiarios estimados',
            p.observaciones            AS 'Observaciones',
            p.created_at               AS 'Fecha creación',
            p.updated_at               AS 'Última actualización'
        FROM proyectos p
        JOIN acciones a ON a.id = p.accion_id
        LEFT JOIN organismos o ON o.id = p.organismo_id
        LEFT JOIN funcionarios f ON f.id = p.funcionario_responsable_id
        ORDER BY p.updated_at DESC
    ")->fetchAll();
    enviarCSV('proyectos', $rows);
}

// ── Exportar acciones con responsables ────────────────────────
if ($tipo === 'acciones') {
    $rows = $db->query("
        SELECT
            a.codigo                 AS 'Código acción',
            e.codigo                 AS 'Estrategia',
            prog.codigo              AS 'Programa',
            comp.codigo              AS 'Componente',
            a.descripcion            AS 'Descripción acción',
            a.estado                 AS 'Estado',
            o.sigla                  AS 'Organismo responsable',
            f.nombre_apellido        AS 'Funcionario responsable',
            f.cargo                  AS 'Cargo',
            COUNT(p.id)              AS 'Cantidad de proyectos'
        FROM acciones a
        JOIN componentes comp ON comp.id = a.componente_id
        JOIN programas prog ON prog.id = comp.programa_id
        JOIN estrategias e ON e.id = prog.estrategia_id
        LEFT JOIN organismos o ON o.id = a.organismo_responsable_id
        LEFT JOIN funcionarios f ON f.id = a.funcionario_responsable_id
        LEFT JOIN proyectos p ON p.accion_id = a.id
        GROUP BY a.id
        ORDER BY a.codigo
    ")->fetchAll();
    enviarCSV('acciones_con_responsables', $rows);
}

// ── Exportar funcionarios ──────────────────────────────────────
if ($tipo === 'funcionarios') {
    $rows = $db->query("
        SELECT
            f.nombre_apellido   AS 'Apellido y Nombre',
            f.documento         AS 'DNI',
            f.cargo             AS 'Cargo',
            o.sigla             AS 'Organismo',
            o.nombre_completo   AS 'Nombre organismo',
            f.situacion         AS 'Situación',
            f.email             AS 'Email',
            COUNT(DISTINCT a.id) AS 'Acciones a cargo',
            COUNT(DISTINCT p.id) AS 'Proyectos a cargo'
        FROM funcionarios f
        JOIN organismos o ON o.id = f.organismo_id
        LEFT JOIN acciones a ON a.funcionario_responsable_id = f.id
        LEFT JOIN proyectos p ON p.funcionario_responsable_id = f.id
        WHERE f.activo = 1
        GROUP BY f.id
        ORDER BY f.nombre_apellido
    ")->fetchAll();
    enviarCSV('funcionarios', $rows);
}

// ── Exportar plan completo (jerarquía + estado) ────────────────
if ($tipo === 'plan_completo') {
    $rows = $db->query("
        SELECT
            e.codigo        AS 'Estrategia (código)',
            e.descripcion   AS 'Estrategia (descripción)',
            e.estado        AS 'Estado estrategia',
            prog.codigo     AS 'Programa (código)',
            prog.descripcion AS 'Programa (descripción)',
            prog.estado     AS 'Estado programa',
            comp.codigo     AS 'Componente (código)',
            comp.descripcion AS 'Componente (descripción)',
            comp.estado     AS 'Estado componente',
            a.codigo        AS 'Acción (código)',
            a.descripcion   AS 'Acción (descripción)',
            a.estado        AS 'Estado acción',
            oa.sigla        AS 'Organismo acción',
            fa.nombre_apellido AS 'Responsable acción',
            COUNT(p.id)     AS 'Proyectos vinculados',
            ROUND(AVG(p.porcentaje_avance),1) AS 'Avance promedio proyectos'
        FROM estrategias e
        JOIN programas prog ON prog.estrategia_id = e.id
        JOIN componentes comp ON comp.programa_id = prog.id
        JOIN acciones a ON a.componente_id = comp.id
        LEFT JOIN organismos oa ON oa.id = a.organismo_responsable_id
        LEFT JOIN funcionarios fa ON fa.id = a.funcionario_responsable_id
        LEFT JOIN proyectos p ON p.accion_id = a.id
        GROUP BY a.id
        ORDER BY e.codigo, prog.codigo, comp.codigo, a.codigo
    ")->fetchAll();
    enviarCSV('plan_estrategico_completo', $rows);
}

// ── Exportar novedades ─────────────────────────────────────────
if ($tipo === 'novedades') {
    $rows = $db->query("
        SELECT
            n.created_at        AS 'Fecha',
            n.tipo              AS 'Tipo',
            n.referencia_id     AS 'ID referencia',
            n.titulo            AS 'Título',
            n.descripcion       AS 'Descripción',
            f.nombre_apellido   AS 'Reportado por',
            o.sigla             AS 'Organismo'
        FROM novedades n
        LEFT JOIN funcionarios f ON f.id = n.funcionario_id
        LEFT JOIN organismos o ON o.id = f.organismo_id
        ORDER BY n.created_at DESC
    ")->fetchAll();
    enviarCSV('novedades_bitacora', $rows);
}

// ── Exportar localidades ───────────────────────────────────────
if ($tipo === 'localidades') {
    require_once '../includes/text_encoding.php';

    $tiposLocalidad = [
        1 => 'Otro',
        2 => 'Comisión de Fomento',
        3 => 'Localidad',
    ];

    $deptColumns = $db->query('SHOW COLUMNS FROM departamentos')->fetchAll();
    $deptColumnNames = array_map(static fn(array $col): string => $col['Field'], $deptColumns);
    $deptLabelField = 'id';
    foreach (['nombre', 'descripcion', 'departamento', 'detalle', 'denominacion', 'label'] as $candidate) {
        if (in_array($candidate, $deptColumnNames, true)) {
            $deptLabelField = $candidate;
            break;
        }
    }
    $deptNameExpr = $deptLabelField === 'id' ? 'CAST(d.id AS CHAR)' : "d.`{$deptLabelField}`";

    $localidadesColumns = $db->query('SHOW COLUMNS FROM localidades')->fetchAll();
    $localidadesColumnNames = array_map(static fn(array $col): string => $col['Field'], $localidadesColumns);
    $tieneDocumento = in_array('documento', $localidadesColumnNames, true);

    $q = trim((string) ($_GET['q'] ?? ''));
    $partidoFilter = trim((string) ($_GET['partido'] ?? ''));
    $regionFilter = trim((string) ($_GET['region_am'] ?? ''));
    $tipoFilter = (string) ($_GET['tipo_localidad'] ?? '');

    $where = ['1=1'];
    $params = [];

    if ($q !== '') {
        if ($tieneDocumento) {
            $where[] = '(l.localidad LIKE ? OR l.intendente LIKE ? OR l.documento LIKE ?)';
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        } else {
            $where[] = '(l.localidad LIKE ? OR l.intendente LIKE ?)';
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }
    }
    if ($partidoFilter !== '') {
        $where[] = 'l.partido = ?';
        $params[] = $partidoFilter;
    }
    if ($regionFilter !== '') {
        $where[] = 'l.region_am = ?';
        $params[] = $regionFilter;
    }
    if ($tipoFilter !== '' && ctype_digit($tipoFilter)) {
        $where[] = 'l.tipo_localidad = ?';
        $params[] = (int) $tipoFilter;
    }

    $stmt = $db->prepare("
        SELECT
            l.localidad,
            l.intendente,
            " . ($tieneDocumento ? 'l.documento,' : "NULL AS documento,") . "
            l.partido,
            l.cantidad_habitantes,
            {$deptNameExpr} AS departamento_nombre,
            l.region_am,
            l.tipo_localidad,
            l.AgeRet
        FROM localidades l
        LEFT JOIN departamentos d ON d.id = l.departamento_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY l.localidad
    ");
    $stmt->execute($params);

    $rows = [];
    foreach ($stmt->fetchAll() as $row) {
        $rows[] = [
            'Localidad' => nombreLocalidad($row['localidad'] ?? ''),
            'Intendente' => ($row['intendente'] ?? '') !== '' ? textoLegible($row['intendente']) : '',
            'Documento' => $row['documento'] ?? '',
            'Partido' => ($row['partido'] ?? '') !== '' ? textoLegible($row['partido']) : '',
            'Habitantes' => $row['cantidad_habitantes'] !== null ? (int) $row['cantidad_habitantes'] : '',
            'Departamento' => $row['departamento_nombre'] ?? '',
            'Región AM' => $row['region_am'] ?? '',
            'Tipo' => $tiposLocalidad[(int) ($row['tipo_localidad'] ?? 0)] ?? '',
            'AgeRet' => $row['AgeRet'] ?? '',
        ];
    }

    enviarCSV('localidades', $rows);
}

http_response_code(400);
echo 'Tipo de exportación no válido. Opciones: proyectos, acciones, funcionarios, plan_completo, novedades, localidades';
