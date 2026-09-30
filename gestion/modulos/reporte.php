<?php
/**
 * modulos/reporte.php
 * Genera un reporte HTML imprimible del estado del plan estratégico.
 * Acceso: reporte.php?tipo=estrategia&id=1  |  reporte.php?tipo=general
 */

require_once '../includes/config.php';
requireLogin();

$db   = getDB();
$tipo = $_GET['tipo'] ?? 'general';
$id   = (int)($_GET['id'] ?? 0);

// ── Datos según tipo ──────────────────────────────────────────
$titulo_reporte = 'Reporte General del Plan Estratégico';
$datos          = [];
$fecha          = date('d/m/Y H:i');

if ($tipo === 'estrategia' && $id) {
    $est = $db->query("
        SELECT e.*, o.nombre_completo as org_nombre, f.nombre_apellido as responsable
        FROM estrategias e
        LEFT JOIN organismos o ON o.id = e.organismo_responsable_id
        LEFT JOIN funcionarios f ON f.id = e.funcionario_responsable_id
        WHERE e.id = $id
    ")->fetch();

    if (!$est) { http_response_code(404); die('Estrategia no encontrada'); }

    $titulo_reporte = 'Reporte de Estrategia: ' . $est['codigo'];

    $programas = $db->query("
        SELECT prog.*,
               o.sigla as org_sigla, f.nombre_apellido as responsable,
               COUNT(DISTINCT a.id) as total_acciones,
               COUNT(DISTINCT p.id) as total_proyectos,
               ROUND(AVG(p.porcentaje_avance), 1) as avance_promedio
        FROM programas prog
        LEFT JOIN organismos o ON o.id = prog.organismo_responsable_id
        LEFT JOIN funcionarios f ON f.id = prog.funcionario_responsable_id
        LEFT JOIN componentes comp ON comp.programa_id = prog.id
        LEFT JOIN acciones a ON a.componente_id = comp.id
        LEFT JOIN proyectos p ON p.accion_id = a.id
        WHERE prog.estrategia_id = $id
        GROUP BY prog.id
        ORDER BY prog.codigo
    ")->fetchAll();

} elseif ($tipo === 'proyecto' && $id) {
    $proyecto = $db->query("
        SELECT p.*,
               a.codigo as accion_codigo, a.descripcion as accion_desc,
               comp.codigo as comp_codigo,
               prog.codigo as prog_codigo,
               e.codigo as est_codigo,
               o.nombre_completo as org_nombre, o.sigla,
               f.nombre_apellido as responsable, f.cargo
        FROM proyectos p
        JOIN acciones a ON a.id = p.accion_id
        JOIN componentes comp ON comp.id = a.componente_id
        JOIN programas prog ON prog.id = comp.programa_id
        JOIN estrategias e ON e.id = prog.estrategia_id
        LEFT JOIN organismos o ON o.id = p.organismo_id
        LEFT JOIN funcionarios f ON f.id = p.funcionario_responsable_id
        WHERE p.id = $id
    ")->fetch();

    if (!$proyecto) { http_response_code(404); die('Proyecto no encontrado'); }
    $titulo_reporte = 'Ficha de Proyecto: ' . $proyecto['nombre'];

    $hitos = $db->query("SELECT * FROM hitos WHERE proyecto_id = $id ORDER BY fecha_programada")->fetchAll();
    $novedades = $db->query("
        SELECT n.*, f.nombre_apellido FROM novedades n
        LEFT JOIN funcionarios f ON f.id = n.funcionario_id
        WHERE n.tipo = 'proyecto' AND n.referencia_id = $id
        ORDER BY n.created_at DESC
    ")->fetchAll();

} else {
    // Reporte general
    $resumen = $db->query("
        SELECT
            (SELECT COUNT(*) FROM estrategias)   as total_estrategias,
            (SELECT COUNT(*) FROM programas)     as total_programas,
            (SELECT COUNT(*) FROM componentes)   as total_componentes,
            (SELECT COUNT(*) FROM acciones)      as total_acciones,
            (SELECT COUNT(*) FROM proyectos)     as total_proyectos,
            (SELECT COUNT(*) FROM proyectos WHERE estado='en_ejecucion') as activos,
            (SELECT COUNT(*) FROM proyectos WHERE estado='completado')   as completados,
            (SELECT ROUND(AVG(porcentaje_avance),1) FROM proyectos)     as avance_promedio,
            (SELECT COALESCE(SUM(presupuesto_total),0)    FROM proyectos) as presupuesto_total,
            (SELECT COALESCE(SUM(presupuesto_ejecutado),0) FROM proyectos) as ejecutado_total
    ")->fetch();

    $por_organismo = $db->query("
        SELECT o.sigla, o.nombre_completo,
               COUNT(DISTINCT p.id) as proyectos,
               COUNT(DISTINCT a.id) as acciones,
               COALESCE(SUM(p.presupuesto_total), 0) as presupuesto
        FROM organismos o
        LEFT JOIN acciones a ON a.organismo_responsable_id = o.id
        LEFT JOIN proyectos p ON p.organismo_id = o.id
        GROUP BY o.id
        HAVING proyectos > 0 OR acciones > 0
        ORDER BY proyectos DESC
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= h($titulo_reporte) ?> | La Pampa</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Inter', sans-serif; color: #1E293B; background: #fff; font-size: 13px; }

  .header-reporte {
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
    color: white;
    padding: 2rem 2.5rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }
  .header-reporte h1 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem; }
  .header-reporte p  { font-size: 0.8rem; opacity: 0.8; }

  .body-reporte { padding: 2rem 2.5rem; }

  .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
  .kpi-box  { border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 1rem; text-align: center; }
  .kpi-box .val { font-size: 1.75rem; font-weight: 800; color: #0F172A; }
  .kpi-box .lbl { font-size: 0.7rem; color: #64748B; text-transform: uppercase; letter-spacing: .06em; }

  table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
  thead tr { background: #1e3a8a; color: white; }
  thead th { padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; text-transform: uppercase; }
  tbody tr:nth-child(even) { background: #F8FAFC; }
  tbody td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #E2E8F0; }

  .badge { display: inline-block; padding: .15rem .5rem; border-radius: 9999px; font-size: .68rem; font-weight: 600; }
  .badge-green { background:#D1FAE5; color:#065F46; }
  .badge-blue  { background:#DBEAFE; color:#1E40AF; }
  .badge-gray  { background:#F1F5F9; color:#475569; }
  .badge-red   { background:#FEE2E2; color:#991B1B; }

  .progress { height: 8px; background: #E2E8F0; border-radius: 4px; overflow: hidden; }
  .progress-fill { height: 100%; background: #1D4ED8; border-radius: 4px; }

  .section-title { font-size: 1rem; font-weight: 700; color: #1E293B; margin: 1.5rem 0 0.75rem; padding-bottom: 0.5rem; border-bottom: 2px solid #E2E8F0; }

  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
  .info-item .label { font-size: .68rem; text-transform: uppercase; letter-spacing:.06em; color:#64748B; margin-bottom:.2rem; }
  .info-item .value { font-weight: 600; color: #1E293B; }

  .print-btn { position: fixed; top: 1rem; right: 1rem; background: #1D4ED8; color: #fff; border: none; padding: .5rem 1rem; border-radius: .5rem; cursor: pointer; font-size: .8rem; }
  @media print { .print-btn { display: none; } body { font-size: 11px; } }
</style>
</head>
<body>

<button class="print-btn" onclick="window.print()">🖨️ Imprimir / PDF</button>

<div class="header-reporte">
  <div>
    <p style="font-size:.75rem;opacity:.7;margin-bottom:.25rem">PROVINCIA DE LA PAMPA</p>
    <h1><?= h($titulo_reporte) ?></h1>
    <p>Generado: <?= $fecha ?> · Usuario: <?= h($_SESSION['usuario_nombre'] ?? '') ?></p>
  </div>
  <div style="text-align:right;opacity:.8">
    <p style="font-size:1.5rem;font-weight:800">GOV</p>
    <p style="font-size:.75rem">Sistema de Gestión</p>
  </div>
</div>

<div class="body-reporte">

<?php if ($tipo === 'general'): ?>
  <!-- ── REPORTE GENERAL ── -->
  <p class="section-title">Resumen ejecutivo del Plan</p>
  <div class="kpi-grid">
    <div class="kpi-box"><p class="val"><?= $resumen['total_estrategias'] ?></p><p class="lbl">Estrategias</p></div>
    <div class="kpi-box"><p class="val"><?= $resumen['total_acciones'] ?></p><p class="lbl">Acciones</p></div>
    <div class="kpi-box"><p class="val"><?= $resumen['total_proyectos'] ?></p><p class="lbl">Proyectos</p></div>
    <div class="kpi-box"><p class="val"><?= $resumen['avance_promedio'] ?>%</p><p class="lbl">Avance prom.</p></div>
    <div class="kpi-box"><p class="val"><?= $resumen['activos'] ?></p><p class="lbl">En ejecución</p></div>
    <div class="kpi-box"><p class="val"><?= $resumen['completados'] ?></p><p class="lbl">Completados</p></div>
    <div class="kpi-box"><p class="val">$<?= number_format($resumen['presupuesto_total']/1e6,1) ?>M</p><p class="lbl">Presupuesto total</p></div>
    <div class="kpi-box"><p class="val">$<?= number_format($resumen['ejecutado_total']/1e6,1) ?>M</p><p class="lbl">Ejecutado</p></div>
  </div>

  <p class="section-title">Participación por organismo</p>
  <table>
    <thead><tr><th>Organismo</th><th>Nombre</th><th>Proyectos</th><th>Acciones</th><th>Presupuesto</th></tr></thead>
    <tbody>
    <?php foreach($por_organismo as $org): ?>
    <tr>
      <td><strong><?= h($org['sigla']) ?></strong></td>
      <td><?= h($org['nombre_completo']) ?></td>
      <td><?= $org['proyectos'] ?></td>
      <td><?= $org['acciones'] ?></td>
      <td><?= $org['presupuesto'] > 0 ? '$'.number_format($org['presupuesto']/1e6,2).'M' : '—' ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

<?php elseif ($tipo === 'proyecto' && !empty($proyecto)): ?>
  <!-- ── FICHA DE PROYECTO ── -->
  <p class="section-title">Información del proyecto</p>
  <div class="info-grid">
    <div class="info-item"><p class="label">Nombre</p><p class="value"><?= h($proyecto['nombre']) ?></p></div>
    <div class="info-item"><p class="label">Estado</p><p class="value"><?= ucfirst(str_replace('_',' ',$proyecto['estado'])) ?></p></div>
    <div class="info-item"><p class="label">Organismo</p><p class="value"><?= h($proyecto['org_nombre'] ?? '—') ?></p></div>
    <div class="info-item"><p class="label">Responsable</p><p class="value"><?= h($proyecto['responsable'] ?? '—') ?></p></div>
    <div class="info-item"><p class="label">Localidad</p><p class="value"><?= h($proyecto['localidad'] ?: '—') ?></p></div>
    <div class="info-item"><p class="label">N° Expediente</p><p class="value"><?= h($proyecto['numero_expediente'] ?: '—') ?></p></div>
    <div class="info-item"><p class="label">Fecha inicio</p><p class="value"><?= $proyecto['fecha_inicio'] ? date('d/m/Y', strtotime($proyecto['fecha_inicio'])) : '—' ?></p></div>
    <div class="info-item"><p class="label">Fecha fin estimada</p><p class="value"><?= $proyecto['fecha_fin_estimada'] ? date('d/m/Y', strtotime($proyecto['fecha_fin_estimada'])) : '—' ?></p></div>
    <div class="info-item"><p class="label">Presupuesto total</p><p class="value"><?= $proyecto['presupuesto_total'] ? '$'.number_format($proyecto['presupuesto_total'],0,'.','.') : '—' ?></p></div>
    <div class="info-item"><p class="label">Ejecutado</p><p class="value"><?= '$'.number_format($proyecto['presupuesto_ejecutado'],0,'.','.') ?></p></div>
  </div>

  <p class="label" style="margin-bottom:.5rem">Avance: <?= $proyecto['porcentaje_avance'] ?>%</p>
  <div class="progress" style="margin-bottom:1.5rem">
    <div class="progress-fill" style="width:<?= $proyecto['porcentaje_avance'] ?>%"></div>
  </div>

  <p class="section-title">Jerarquía en el plan</p>
  <p style="font-family:monospace;font-size:.8rem;color:#1D4ED8">
    <?= h($proyecto['est_codigo']) ?> → <?= h($proyecto['prog_codigo']) ?> → <?= h($proyecto['comp_codigo']) ?> → <?= h($proyecto['accion_codigo']) ?>
  </p>

  <?php if (!empty($novedades)): ?>
  <p class="section-title">Bitácora de avances</p>
  <table>
    <thead><tr><th>Fecha</th><th>Título</th><th>Descripción</th><th>Reportado por</th></tr></thead>
    <tbody>
    <?php foreach($novedades as $nv): ?>
    <tr>
      <td><?= date('d/m/Y', strtotime($nv['created_at'])) ?></td>
      <td><?= h($nv['titulo']) ?></td>
      <td><?= h(mb_substr($nv['descripcion'],0,120)) ?></td>
      <td><?= h($nv['nombre_apellido'] ?? 'Sistema') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

<?php elseif ($tipo === 'estrategia' && !empty($est)): ?>
  <!-- ── REPORTE DE ESTRATEGIA ── -->
  <div class="info-grid">
    <div class="info-item"><p class="label">Código</p><p class="value"><?= h($est['codigo']) ?></p></div>
    <div class="info-item"><p class="label">Estado</p><p class="value"><?= ucfirst(str_replace('_',' ',$est['estado'])) ?></p></div>
    <div class="info-item"><p class="label">Organismo responsable</p><p class="value"><?= h($est['org_nombre'] ?? '—') ?></p></div>
    <div class="info-item"><p class="label">Funcionario responsable</p><p class="value"><?= h($est['responsable'] ?? '—') ?></p></div>
  </div>
  <p style="font-size:.875rem;color:#374151;margin-bottom:1.5rem"><?= h($est['descripcion']) ?></p>

  <p class="section-title">Programas de la estrategia</p>
  <table>
    <thead><tr><th>Código</th><th>Descripción</th><th>Estado</th><th>Organismo</th><th>Acciones</th><th>Proyectos</th><th>Avance</th></tr></thead>
    <tbody>
    <?php foreach($programas as $prog): ?>
    <tr>
      <td><code><?= h($prog['codigo']) ?></code></td>
      <td><?= h(mb_substr($prog['descripcion'],0,60)) ?></td>
      <td><?= ucfirst(str_replace('_',' ',$prog['estado'])) ?></td>
      <td><?= h($prog['org_sigla'] ?? '—') ?></td>
      <td><?= $prog['total_acciones'] ?></td>
      <td><?= $prog['total_proyectos'] ?></td>
      <td><?= $prog['avance_promedio'] ?? 0 ?>%</td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<p style="margin-top:3rem;font-size:.7rem;color:#94A3B8;text-align:center">
  Documento generado automáticamente por el Sistema de Gestión Gubernamental · Provincia de La Pampa · <?= $fecha ?>
</p>
</div>
</body>
</html>
