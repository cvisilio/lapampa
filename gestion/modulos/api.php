<?php
/**
 * modulos/api.php
 * Endpoint AJAX para operaciones asíncronas del sistema
 *
 * Uso desde JS:
 *   fetch('modulos/api.php?action=funcionarios_by_organismo&organismo_id=5')
 *   fetch('modulos/api.php', { method:'POST', body: formData })
 */

require_once '../includes/config.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db     = getDB();

try {
    switch ($action) {

        // ── Funcionarios filtrados por organismo ──────────────────
        case 'funcionarios_by_organismo':
            $org_id = (int)($_GET['organismo_id'] ?? 0);
            if (!$org_id) { echo json_encode([]); exit; }

            $stmt = $db->prepare("
                SELECT id, nombre_apellido, cargo
                FROM funcionarios
                WHERE organismo_id = ? AND activo = 1
                ORDER BY nombre_apellido
            ");
            $stmt->execute([$org_id]);
            echo json_encode($stmt->fetchAll());
            break;

        // ── Buscar funcionarios (autocompletado) ──────────────────
        case 'buscar_funcionarios':
            $q = '%' . trim($_GET['q'] ?? '') . '%';
            $stmt = $db->prepare("
                SELECT f.id, f.nombre_apellido, f.cargo, o.sigla
                FROM funcionarios f
                JOIN organismos o ON o.id = f.organismo_id
                WHERE (f.nombre_apellido LIKE ? OR f.cargo LIKE ?) AND f.activo = 1
                ORDER BY f.nombre_apellido
                LIMIT 20
            ");
            $stmt->execute([$q, $q]);
            echo json_encode($stmt->fetchAll());
            break;

        // ── Acciones de un componente ─────────────────────────────
        case 'acciones_by_componente':
            $comp_id = (int)($_GET['componente_id'] ?? 0);
            $stmt = $db->prepare("SELECT id, codigo, descripcion FROM acciones WHERE componente_id = ? ORDER BY codigo");
            $stmt->execute([$comp_id]);
            echo json_encode($stmt->fetchAll());
            break;

        // ── Acciones de un programa (todas sus componentes) ────────
        case 'acciones_by_programa':
            $prog_id = (int)($_GET['programa_id'] ?? 0);
            $stmt = $db->prepare("
                SELECT a.id, a.codigo, a.descripcion, comp.codigo as comp_codigo
                FROM acciones a
                JOIN componentes comp ON comp.id = a.componente_id
                WHERE comp.programa_id = ?
                ORDER BY a.codigo
            ");
            $stmt->execute([$prog_id]);
            echo json_encode($stmt->fetchAll());
            break;

        // ── Estadísticas rápidas de un proyecto ───────────────────
        case 'stats_proyecto':
            $pid = (int)($_GET['id'] ?? 0);
            $stmt = $db->prepare("
                SELECT
                    p.porcentaje_avance,
                    p.estado,
                    p.presupuesto_total,
                    p.presupuesto_ejecutado,
                    (SELECT COUNT(*) FROM hitos WHERE proyecto_id = p.id) as total_hitos,
                    (SELECT COUNT(*) FROM hitos WHERE proyecto_id = p.id AND estado = 'completado') as hitos_ok,
                    (SELECT COUNT(*) FROM novedades WHERE tipo='proyecto' AND referencia_id = p.id) as total_novedades
                FROM proyectos p WHERE p.id = ?
            ");
            $stmt->execute([$pid]);
            echo json_encode($stmt->fetch() ?: []);
            break;

        // ── Actualizar avance de proyecto (AJAX) ──────────────────
        case 'update_avance':
            requireRol('admin', 'editor');
            $pid    = (int)($_POST['proyecto_id'] ?? 0);
            $avance = max(0, min(100, (int)($_POST['avance'] ?? 0)));
            $estado = $_POST['estado'] ?? null;

            if (!$pid) throw new Exception('ID de proyecto inválido');

            $sql    = "UPDATE proyectos SET porcentaje_avance = ?, updated_at = NOW()";
            $params = [$avance];

            if ($estado) { $sql .= ", estado = ?"; $params[] = $estado; }
            $sql .= " WHERE id = ?";
            $params[] = $pid;

            $db->prepare($sql)->execute($params);
            echo json_encode(['ok' => true, 'avance' => $avance]);
            break;

        // ── Registrar novedad rápida (AJAX) ───────────────────────
        case 'nueva_novedad':
            requireRol('admin', 'editor');
            $tipo   = $_POST['tipo']         ?? 'proyecto';
            $ref    = (int)($_POST['ref_id'] ?? 0);
            $titulo = trim($_POST['titulo']   ?? '');
            $desc   = trim($_POST['desc']     ?? '');

            if (!$titulo || !$desc || !$ref) throw new Exception('Datos incompletos');

            $db->prepare("INSERT INTO novedades (tipo, referencia_id, titulo, descripcion, funcionario_id) VALUES (?,?,?,?,?)")
               ->execute([$tipo, $ref, $titulo, $desc, $_SESSION['usuario_id'] ?? null]);

            echo json_encode(['ok' => true, 'id' => $db->lastInsertId()]);
            break;

        // ── KPIs para dashboard (refresco sin recarga) ─────────────
        case 'dashboard_kpis':
            $kpis = $db->query("
                SELECT
                    (SELECT COUNT(*) FROM proyectos WHERE estado='en_ejecucion') as proyectos_activos,
                    (SELECT COUNT(*) FROM proyectos WHERE estado='completado')   as proyectos_completados,
                    (SELECT COUNT(*) FROM acciones  WHERE estado='en_curso')     as acciones_curso,
                    (SELECT ROUND(AVG(porcentaje_avance),1) FROM proyectos)      as avance_promedio,
                    (SELECT COALESCE(SUM(presupuesto_ejecutado),0) FROM proyectos) as ejecutado_total
            ")->fetch();
            echo json_encode($kpis);
            break;

        // ── Proyectos recientes ────────────────────────────────────
        case 'proyectos_recientes':
            $limit = min((int)($_GET['limit'] ?? 5), 20);
            $rows  = $db->query("
                SELECT p.id, p.nombre, p.estado, p.porcentaje_avance,
                       a.codigo as accion_codigo, o.sigla, o.color_hex
                FROM proyectos p
                JOIN acciones a ON a.id = p.accion_id
                LEFT JOIN organismos o ON o.id = p.organismo_id
                ORDER BY p.updated_at DESC
                LIMIT $limit
            ")->fetchAll();
            echo json_encode($rows);
            break;

        // ── Verificar sesión activa ───────────────────────────────
        case 'ping':
            echo json_encode(['ok' => true, 'usuario' => $_SESSION['usuario_nombre'] ?? '']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no reconocida: ' . h($action)]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
