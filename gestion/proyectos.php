<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Proyectos';
$breadcrumb = [['label' => 'Proyectos']];

// ── ACCION: Guardar nuevo / editar proyecto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_proyecto'])) {
    requireRol('admin','editor');

    $campos = [
        'accion_id'               => (int)($_POST['accion_id'] ?? 0),
        'nombre'                  => trim($_POST['nombre'] ?? ''),
        'descripcion'             => trim($_POST['descripcion'] ?? ''),
        'organismo_id'            => (int)($_POST['organismo_id'] ?? 0) ?: null,
        'funcionario_responsable_id' => (int)($_POST['funcionario_responsable_id'] ?? 0) ?: null,
        'estado'                  => $_POST['estado'] ?? 'idea',
        'prioridad'               => $_POST['prioridad'] ?? 'media',
        'presupuesto_total'       => str_replace(['.','$',' '],'',$_POST['presupuesto_total'] ?? '') ?: null,
        'fuente_financiamiento'   => trim($_POST['fuente_financiamiento'] ?? ''),
        'localidad'               => trim($_POST['localidad'] ?? ''),
        'beneficiarios_estimados' => (int)($_POST['beneficiarios_estimados'] ?? 0) ?: null,
        'fecha_inicio'            => $_POST['fecha_inicio'] ?: null,
        'fecha_fin_estimada'      => $_POST['fecha_fin_estimada'] ?: null,
        'porcentaje_avance'       => (int)($_POST['porcentaje_avance'] ?? 0),
        'numero_expediente'       => trim($_POST['numero_expediente'] ?? ''),
        'observaciones'           => trim($_POST['observaciones'] ?? ''),
    ];

    $id_edit = (int)($_POST['id'] ?? 0);

    try {
        if ($id_edit) {
            $sql = "UPDATE proyectos SET " . implode(', ', array_map(fn($k) => "$k = :$k", array_keys($campos))) . ", updated_at=NOW() WHERE id = :_id";
            $campos['_id'] = $id_edit;
        } else {
            $sql = "INSERT INTO proyectos (" . implode(',', array_keys($campos)) . ") VALUES (:" . implode(',:', array_keys($campos)) . ")";
        }
        $db->prepare($sql)->execute($campos);

        // Registrar novedad automática
        $pid = $id_edit ?: $db->lastInsertId();
        $db->prepare("INSERT INTO novedades (tipo, referencia_id, titulo, descripcion, funcionario_id) VALUES ('proyecto', ?, ?, ?, ?)")
           ->execute([$pid, $id_edit ? 'Proyecto actualizado' : 'Proyecto creado', $campos['nombre'], $_SESSION['usuario_id'] ?? null]);

        $_SESSION['flash'] = ['type'=>'success','msg'=>$id_edit ? 'Proyecto actualizado correctamente.' : 'Proyecto creado correctamente.'];
        header('Location: proyectos.php');
        exit;
    } catch (Exception $e) {
        $error_form = 'Error al guardar: ' . $e->getMessage();
    }
}

// ── ACCION: Cambio rápido de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    requireRol('admin','editor');
    $pid    = (int)$_POST['proyecto_id'];
    $estado = $_POST['nuevo_estado'];
    $avance = (int)($_POST['avance'] ?? 0);
    $db->prepare("UPDATE proyectos SET estado=?, porcentaje_avance=?, updated_at=NOW() WHERE id=?")->execute([$estado, $avance, $pid]);
    header('Location: proyectos.php?id=' . $pid);
    exit;
}

// ── VER DETALLE
$detalle = null;
if (!empty($_GET['id'])) {
    $detalle = $db->prepare("
        SELECT p.*, 
               a.codigo as accion_codigo, a.descripcion as accion_desc,
               comp.codigo as componente_codigo,
               prog.codigo as programa_codigo,
               e.codigo as estrategia_codigo,
               o.nombre_completo as organismo_nombre, o.sigla as organismo_sigla, o.color_hex,
               f.nombre_apellido as responsable_nombre, f.cargo as responsable_cargo
        FROM proyectos p
        JOIN acciones a ON a.id = p.accion_id
        JOIN componentes comp ON comp.id = a.componente_id
        JOIN programas prog ON prog.id = comp.programa_id
        JOIN estrategias e ON e.id = prog.estrategia_id
        LEFT JOIN organismos o ON o.id = p.organismo_id
        LEFT JOIN funcionarios f ON f.id = p.funcionario_responsable_id
        WHERE p.id = ?
    ");
    $detalle->execute([(int)$_GET['id']]);
    $detalle = $detalle->fetch();

    $hitos = $db->prepare("SELECT * FROM hitos WHERE proyecto_id = ? ORDER BY fecha_programada")->execute([(int)$_GET['id']]) ? 
             $db->query("SELECT * FROM hitos WHERE proyecto_id = " . (int)$_GET['id'] . " ORDER BY fecha_programada")->fetchAll() : [];

    $novedades_proyecto = $db->query("
        SELECT n.*, f.nombre_apellido FROM novedades n
        LEFT JOIN funcionarios f ON f.id = n.funcionario_id
        WHERE n.tipo = 'proyecto' AND n.referencia_id = " . (int)$_GET['id'] . "
        ORDER BY n.created_at DESC LIMIT 10
    ")->fetchAll();
}

// ── MODO FORMULARIO
$modo_form = isset($_GET['action']) && $_GET['action'] === 'new';
$edit_id   = (int)($_GET['edit'] ?? 0);
$pre_accion_id = (int)($_GET['accion'] ?? 0);
$edit_data = null;
if ($edit_id) {
    $edit_data = $db->query("SELECT * FROM proyectos WHERE id=$edit_id")->fetch();
    $modo_form = true;
}

// ── LISTADO con filtros
$where = ['1=1'];
$params = [];

if (!empty($_GET['organismo'])) {
    $where[] = 'p.organismo_id = ?'; $params[] = (int)$_GET['organismo'];
}
if (!empty($_GET['estado'])) {
    $where[] = 'p.estado = ?'; $params[] = $_GET['estado'];
}
if (!empty($_GET['q'])) {
    $where[] = '(p.nombre LIKE ? OR a.codigo LIKE ?)';
    $params[] = '%'.$_GET['q'].'%'; $params[] = '%'.$_GET['q'].'%';
}

$where_sql = implode(' AND ', $where);
$stmt = $db->prepare("
    SELECT p.*, a.codigo as accion_codigo,
           o.sigla as org_sigla, o.color_hex,
           f.nombre_apellido as responsable
    FROM proyectos p
    JOIN acciones a ON a.id = p.accion_id
    LEFT JOIN organismos o ON o.id = p.organismo_id
    LEFT JOIN funcionarios f ON f.id = p.funcionario_responsable_id
    WHERE $where_sql
    ORDER BY p.updated_at DESC
");
$stmt->execute($params);
$proyectos = $stmt->fetchAll();

// Para los selects del formulario
$organismos   = $db->query("SELECT * FROM organismos ORDER BY nombre_completo")->fetchAll();
$acciones_sel = $db->query("
    SELECT a.id, a.codigo, a.descripcion,
           comp.codigo as comp_codigo, prog.codigo as prog_codigo, e.codigo as est_codigo,
           a.organismo_responsable_id
    FROM acciones a
    JOIN componentes comp ON comp.id = a.componente_id
    JOIN programas prog ON prog.id = comp.programa_id
    JOIN estrategias e ON e.id = prog.estrategia_id
    ORDER BY a.codigo
")->fetchAll();

$pre_accion_org_id = 0;
if ($modo_form && !$edit_id && $pre_accion_id) {
    $stmt = $db->prepare("SELECT organismo_responsable_id FROM acciones WHERE id = ? LIMIT 1");
    $stmt->execute([$pre_accion_id]);
    $pre_accion_org_id = (int)($stmt->fetchColumn() ?: 0);
}

include 'includes/header.php';
?>

<div class="space-y-5">

<!-- Header -->
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Proyectos</h1>
        <p class="text-gray-500 text-sm"><?= count($proyectos) ?> proyecto(s) registrado(s)</p>
    </div>
    <?php if (in_array($_SESSION['rol']??'', ['admin','editor'])): ?>
    <a href="proyectos.php?action=new" class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        <i class="fas fa-plus"></i> Nuevo proyecto
    </a>
    <?php endif; ?>
</div>

<?php if (isset($error_form)): ?>
<div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"><?= h($error_form) ?></div>
<?php endif; ?>

<!-- ============ DETALLE ============ -->
<?php if ($detalle): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Hero -->
    <div class="px-6 py-5 border-b border-gray-100" style="border-left: 4px solid <?= h($detalle['color_hex'] ?? '#3B82F6') ?>">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs text-gray-400 font-mono"><?= h($detalle['accion_codigo']) ?></span>
                    <?= badgeEstado($detalle['estado']) ?>
                    <?= badgePrioridad($detalle['prioridad']) ?>
                </div>
                <h2 class="text-xl font-bold text-gray-900"><?= h($detalle['nombre']) ?></h2>
                <p class="text-gray-500 text-sm mt-1"><?= h($detalle['descripcion']) ?></p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
                <a href="proyectos.php?edit=<?= $detalle['id'] ?>" class="inline-flex items-center gap-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <?php endif; ?>
                <a href="proyectos.php" class="inline-flex items-center gap-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <!-- Avance -->
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Avance del proyecto</span>
                <span class="font-semibold"><?= $detalle['porcentaje_avance'] ?>%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="bg-blue-500 h-3 rounded-full transition-all" style="width:<?= $detalle['porcentaje_avance'] ?>%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-0 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">

        <!-- Datos del proyecto -->
        <div class="col-span-2 p-5 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Organismo responsable</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= h($detalle['organismo_nombre'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Funcionario responsable</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= h($detalle['responsable_nombre'] ?? '—') ?></p>
                    <p class="text-gray-400 text-xs"><?= h($detalle['responsable_cargo'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Localidad</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= h($detalle['localidad'] ?: '—') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Nº Expediente</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= h($detalle['numero_expediente'] ?: '—') ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Fecha inicio</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= $detalle['fecha_inicio'] ? date('d/m/Y',strtotime($detalle['fecha_inicio'])) : '—' ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Fecha fin estimada</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= $detalle['fecha_fin_estimada'] ? date('d/m/Y',strtotime($detalle['fecha_fin_estimada'])) : '—' ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Presupuesto total</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= $detalle['presupuesto_total'] ? formatPesos((float)$detalle['presupuesto_total']) : '—' ?></p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Ejecutado</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= $detalle['presupuesto_ejecutado'] ? formatPesos((float)$detalle['presupuesto_ejecutado']) : '$0' ?></p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Fuente de financiamiento</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= h($detalle['fuente_financiamiento'] ?: '—') ?></p>
                </div>
                <?php if ($detalle['beneficiarios_estimados']): ?>
                <div>
                    <p class="text-gray-400 text-xs font-medium uppercase tracking-wider">Beneficiarios estimados</p>
                    <p class="font-semibold text-gray-800 mt-1"><?= number_format($detalle['beneficiarios_estimados'],0,'.','.') ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Jerarquía -->
            <div class="bg-blue-50 rounded-lg p-3 text-xs">
                <p class="text-blue-400 font-semibold uppercase tracking-wider mb-2">Jerarquía del plan estratégico</p>
                <div class="flex flex-wrap items-center gap-1 text-blue-700">
                    <span class="bg-blue-100 px-2 py-0.5 rounded font-mono"><?= h($detalle['estrategia_codigo']) ?></span>
                    <i class="fas fa-chevron-right text-blue-300"></i>
                    <span class="bg-blue-100 px-2 py-0.5 rounded font-mono"><?= h($detalle['programa_codigo']) ?></span>
                    <i class="fas fa-chevron-right text-blue-300"></i>
                    <span class="bg-blue-100 px-2 py-0.5 rounded font-mono"><?= h($detalle['componente_codigo']) ?></span>
                    <i class="fas fa-chevron-right text-blue-300"></i>
                    <span class="bg-blue-100 px-2 py-0.5 rounded font-mono font-semibold"><?= h($detalle['accion_codigo']) ?></span>
                </div>
            </div>

            <?php if ($detalle['observaciones']): ?>
            <div>
                <p class="text-gray-400 text-xs font-medium uppercase tracking-wider mb-1">Observaciones</p>
                <p class="text-gray-700 text-sm bg-gray-50 rounded-lg p-3"><?= nl2br(h($detalle['observaciones'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Actualizar estado rápido -->
            <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
            <div class="border-t border-gray-100 pt-4">
                <p class="text-sm font-medium text-gray-700 mb-3">Actualizar estado y avance</p>
                <form method="POST" class="flex flex-wrap items-end gap-3">
                    <input type="hidden" name="proyecto_id" value="<?= $detalle['id'] ?>">
                    <input type="hidden" name="cambiar_estado" value="1">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Estado</label>
                        <select name="nuevo_estado" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            <?php foreach (['idea','formulacion','aprobado','en_ejecucion','completado','cancelado'] as $est): ?>
                            <option value="<?= $est ?>" <?= $detalle['estado'] === $est ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$est)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">% Avance</label>
                        <input type="number" name="avance" min="0" max="100" value="<?= $detalle['porcentaje_avance'] ?>"
                               class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
                        Guardar cambio
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- Lateral: Novedades -->
        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="font-semibold text-gray-800 text-sm">Bitácora / Novedades</p>
                <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
                <a href="novedades.php?nuevo=1&tipo=proyecto&ref=<?= $detalle['id'] ?>" 
                   class="text-blue-600 text-xs hover:underline">+ Agregar</a>
                <?php endif; ?>
            </div>
            <div class="space-y-3">
                <?php if (empty($novedades_proyecto)): ?>
                <p class="text-gray-400 text-xs">Sin novedades aún.</p>
                <?php else: ?>
                <?php foreach($novedades_proyecto as $nv): ?>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex justify-between items-start">
                        <p class="text-xs font-semibold text-gray-800"><?= h($nv['titulo']) ?></p>
                        <span class="text-xs text-gray-400"><?= date('d/m',strtotime($nv['created_at'])) ?></span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1"><?= h(mb_substr($nv['descripcion'],0,120)) ?><?= strlen($nv['descripcion'])>120 ? '...' : '' ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= h($nv['nombre_apellido'] ?? 'Sistema') ?></p>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============ FORMULARIO ============ -->
<?php elseif ($modo_form): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-6"><?= $edit_id ? 'Editar proyecto' : 'Nuevo proyecto' ?></h2>
    <form method="POST" class="space-y-5">
        <input type="hidden" name="guardar_proyecto" value="1">
        <?php if ($edit_id): ?><input type="hidden" name="id" value="<?= $edit_id ?>"><?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <!-- Acción vinculada -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Acción del Plan Estratégico <span class="text-red-500">*</span></label>
                <input type="text"
                       id="accion-search"
                       placeholder="Buscar por código de acción, descripción o estrategia..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-blue-500">
                <select name="accion_id" id="accion_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Seleccioná la acción —</option>
                    <?php foreach($acciones_sel as $acc): ?>
                    <?php $selected_accion = (int)($edit_data['accion_id'] ?? $pre_accion_id); ?>
                    <option value="<?= $acc['id'] ?>" <?= $selected_accion == $acc['id'] ? 'selected' : '' ?>>
                        [<?= h($acc['est_codigo']) ?>] <?= h($acc['codigo']) ?> — <?= h(mb_substr($acc['descripcion'],0,80)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Nombre -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del proyecto <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" required
                       value="<?= h($edit_data['nombre'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Construcción de red de riego zona norte">
            </div>

            <!-- Descripción -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                          placeholder="Descripción detallada del proyecto..."><?= h($edit_data['descripcion'] ?? '') ?></textarea>
            </div>

            <!-- Organismo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Organismo responsable</label>
                <select name="organismo_id" id="sel-organismo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Sin asignar —</option>
                    <?php foreach($organismos as $org): ?>
                    <?php $selected_org = (int)($edit_data['organismo_id'] ?? $pre_accion_org_id); ?>
                    <option value="<?= $org['id'] ?>" <?= $selected_org == $org['id'] ? 'selected' : '' ?>>
                        <?= h($org['sigla']) ?> — <?= h($org['nombre_completo']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Funcionario responsable -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Funcionario responsable</label>
                <select name="funcionario_responsable_id" id="sel-funcionario" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Sin asignar —</option>
                    <?php
                    $funcs = $db->query("SELECT f.id, f.nombre_apellido, f.cargo, f.organismo_id, o.sigla FROM funcionarios f JOIN organismos o ON o.id=f.organismo_id WHERE f.activo=1 ORDER BY f.nombre_apellido")->fetchAll();
                    foreach($funcs as $fn):
                    ?>
                    <option value="<?= $fn['id'] ?>" data-organismo="<?= $fn['organismo_id'] ?>" <?= ($edit_data['funcionario_responsable_id']??0) == $fn['id'] ? 'selected' : '' ?>>
                        <?= h($fn['nombre_apellido']) ?> — <?= h($fn['cargo']) ?> (<?= h($fn['sigla']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estado / Prioridad -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <?php foreach (['idea'=>'Idea','formulacion'=>'Formulación','aprobado'=>'Aprobado','en_ejecucion'=>'En ejecución','completado'=>'Completado','cancelado'=>'Cancelado'] as $k=>$v): ?>
                    <option value="<?= $k ?>" <?= ($edit_data['estado']??'idea') === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                <select name="prioridad" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="alta" <?= ($edit_data['prioridad']??'') === 'alta' ? 'selected' : '' ?>>Alta</option>
                    <option value="media" <?= ($edit_data['prioridad']??'media') === 'media' ? 'selected' : '' ?>>Media</option>
                    <option value="baja" <?= ($edit_data['prioridad']??'') === 'baja' ? 'selected' : '' ?>>Baja</option>
                </select>
            </div>

            <!-- Presupuesto -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Presupuesto total ($)</label>
                <input type="text" name="presupuesto_total" value="<?= h($edit_data['presupuesto_total'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fuente de financiamiento</label>
                <input type="text" name="fuente_financiamiento" value="<?= h($edit_data['fuente_financiamiento'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Presupuesto provincial, Nación, BID...">
            </div>

            <!-- Fechas -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" value="<?= h($edit_data['fecha_inicio'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin estimada</label>
                <input type="date" name="fecha_fin_estimada" value="<?= h($edit_data['fecha_fin_estimada'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Localidad / Beneficiarios -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localidad / Territorio</label>
                <input type="text" name="localidad" value="<?= h($edit_data['localidad'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Santa Rosa, Toda la provincia...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nº Expediente</label>
                <input type="text" name="numero_expediente" value="<?= h($edit_data['numero_expediente'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Avance -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Porcentaje de avance: <span id="lbl_avance"><?= $edit_data['porcentaje_avance'] ?? 0 ?>%</span></label>
                <input type="range" name="porcentaje_avance" min="0" max="100" value="<?= $edit_data['porcentaje_avance'] ?? 0 ?>"
                       oninput="document.getElementById('lbl_avance').textContent=this.value+'%'"
                       class="w-full accent-blue-600">
            </div>

            <!-- Observaciones -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea name="observaciones" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"><?= h($edit_data['observaciones'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-save mr-2"></i><?= $edit_id ? 'Guardar cambios' : 'Crear proyecto' ?>
            </button>
            <a href="proyectos.php" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- ============ LISTADO ============ -->
<?php else: ?>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="q" value="<?= h($_GET['q']??'') ?>" placeholder="Nombre o código..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
            <select name="estado" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <?php foreach (['idea','formulacion','aprobado','en_ejecucion','completado','cancelado'] as $est): ?>
                <option value="<?= $est ?>" <?= ($_GET['estado']??'') === $est ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$est)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Organismo</label>
            <select name="organismo" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <?php foreach($organismos as $org): ?>
                <option value="<?= $org['id'] ?>" <?= ($_GET['organismo']??'') == $org['id'] ? 'selected' : '' ?>><?= h($org['sigla']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
            <i class="fas fa-search mr-1"></i> Filtrar
        </button>
        <a href="proyectos.php" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Limpiar</a>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <?php if (empty($proyectos)): ?>
    <div class="py-16 text-center">
        <i class="fas fa-project-diagram text-4xl text-gray-200 mb-4 block"></i>
        <p class="text-gray-400">No hay proyectos que coincidan con los filtros.</p>
        <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
        <a href="proyectos.php?action=new" class="inline-flex items-center gap-2 mt-4 bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
            <i class="fas fa-plus"></i> Crear el primer proyecto
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-left">
                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Proyecto</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Acción</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Organismo</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Estado</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Avance</th>
                <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Responsable</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php foreach($proyectos as $p): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:<?= h($p['color_hex']??'#6B7280') ?>"></div>
                        <a href="proyectos.php?id=<?= $p['id'] ?>" class="font-medium text-gray-900 hover:text-blue-700">
                            <?= h(mb_substr($p['nombre'],0,60)) ?><?= strlen($p['nombre'])>60?'...':'' ?>
                        </a>
                    </div>
                    <?php if ($p['localidad']): ?>
                    <p class="text-xs text-gray-400 ml-4 mt-0.5"><i class="fas fa-map-marker-alt mr-1"></i><?= h($p['localidad']) ?></p>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-600"><?= h($p['accion_codigo']) ?></code></td>
                <td class="px-4 py-3">
                    <?php if ($p['org_sigla']): ?>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full" style="background:<?= h($p['color_hex']) ?>"></span>
                        <span class="text-gray-700"><?= h($p['org_sigla']) ?></span>
                    </span>
                    <?php else: ?><span class="text-gray-300">—</span><?php endif; ?>
                </td>
                <td class="px-4 py-3"><?= badgeEstado($p['estado']) ?></td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-20 bg-gray-100 rounded-full h-1.5">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width:<?= $p['porcentaje_avance'] ?>%"></div>
                        </div>
                        <span class="text-xs text-gray-500"><?= $p['porcentaje_avance'] ?>%</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600 text-xs"><?= h(mb_substr($p['responsable']??'—',0,25)) ?></td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center gap-1 justify-end">
                        <a href="proyectos.php?id=<?= $p['id'] ?>" class="text-blue-600 hover:text-blue-800 p-1.5 rounded hover:bg-blue-50" title="Ver detalle">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
                        <a href="proyectos.php?edit=<?= $p['id'] ?>" class="text-gray-500 hover:text-gray-700 p-1.5 rounded hover:bg-gray-100" title="Editar">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php endif; ?>
</div>

<?php if ($modo_form): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('accion-search');
    const select = document.getElementById('accion_id');
    if (!input || !select) return;

    const options = Array.from(select.options).map(opt => ({
        value: opt.value,
        text: opt.textContent,
        selected: opt.selected
    }));

    const render = () => {
        const q = input.value.toLowerCase().trim();
        const current = select.value;

        select.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '— Seleccioná la acción —';
        select.appendChild(placeholder);

        options.forEach(opt => {
            if (!opt.value) return;
            if (q && !opt.text.toLowerCase().includes(q)) return;
            const el = document.createElement('option');
            el.value = opt.value;
            el.textContent = opt.text;
            select.appendChild(el);
        });

        if (current && select.querySelector(`option[value="${current}"]`)) {
            select.value = current;
        } else if (!q) {
            const initial = options.find(o => o.selected);
            if (initial && select.querySelector(`option[value="${initial.value}"]`)) {
                select.value = initial.value;
            }
        }
    };

    input.addEventListener('input', render);
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
