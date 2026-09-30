<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Acciones';
$breadcrumb = [['label' => 'Acciones']];

// Asignar responsable a acción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar'])) {
    requireRol('admin','editor');
    $db->prepare("UPDATE acciones SET organismo_responsable_id=?, funcionario_responsable_id=?, estado=?, updated_at=NOW() WHERE id=?")
       ->execute([
           (int)$_POST['organismo_id'] ?: null,
           (int)$_POST['funcionario_id'] ?: null,
           $_POST['estado'],
           (int)$_POST['accion_id']
       ]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Acción actualizada correctamente.'];
    header('Location: acciones.php?' . http_build_query(array_filter([
        'q' => $_GET['q'] ?? '',
        'estrategia' => $_GET['estrategia'] ?? '',
        'organismo' => $_GET['organismo'] ?? '',
        'estado' => $_GET['estado'] ?? '',
        'componente' => $_GET['componente'] ?? ''
    ])));
    exit;
}

// Filtros
$q = trim($_GET['q'] ?? '');
$est_id = (int)($_GET['estrategia'] ?? 0);
$org_id = (int)($_GET['organismo'] ?? 0);
$estado = $_GET['estado'] ?? '';
$comp_id = (int)($_GET['componente'] ?? 0);
$context_estrategia = null;
$context_componente = null;

$where = ['1=1'];
$params = [];

if ($q) {
    $where[] = '(a.codigo LIKE ? OR a.descripcion LIKE ?)';
    $params[] = "%$q%"; $params[] = "%$q%";
}
if ($est_id) {
    $where[] = 'e.id = ?'; $params[] = $est_id;
}
if ($org_id) {
    $where[] = 'a.organismo_responsable_id = ?'; $params[] = $org_id;
}
if ($estado) {
    $where[] = 'a.estado = ?'; $params[] = $estado;
}
if ($comp_id) {
    $where[] = 'a.componente_id = ?'; $params[] = $comp_id;
}

if ($est_id) {
    $stmt = $db->prepare("SELECT id, codigo, descripcion FROM estrategias WHERE id = ? LIMIT 1");
    $stmt->execute([$est_id]);
    $context_estrategia = $stmt->fetch() ?: null;
}
if ($comp_id) {
    $stmt = $db->prepare("
        SELECT c.id, c.codigo, c.descripcion, e.id AS estrategia_id, e.codigo AS estrategia_codigo, e.descripcion AS estrategia_descripcion
        FROM componentes c
        JOIN programas p ON p.id = c.programa_id
        JOIN estrategias e ON e.id = p.estrategia_id
        WHERE c.id = ?
        LIMIT 1
    ");
    $stmt->execute([$comp_id]);
    $context_componente = $stmt->fetch() ?: null;
    if (!$context_estrategia && !empty($context_componente['estrategia_id'])) {
        $context_estrategia = [
            'id' => $context_componente['estrategia_id'],
            'codigo' => $context_componente['estrategia_codigo'],
            'descripcion' => $context_componente['estrategia_descripcion']
        ];
    }
}

$stmt = $db->prepare("
    SELECT a.*,
           comp.codigo as comp_codigo,
           prog.codigo as prog_codigo,
           e.id as estrategia_id, e.codigo as est_codigo,
           o.sigla as org_sigla, o.color_hex,
           f.nombre_apellido as responsable_nombre,
           (SELECT COUNT(*) FROM proyectos p WHERE p.accion_id = a.id) as total_proyectos
    FROM acciones a
    JOIN componentes comp ON comp.id = a.componente_id
    JOIN programas prog ON prog.id = comp.programa_id
    JOIN estrategias e ON e.id = prog.estrategia_id
    LEFT JOIN organismos o ON o.id = a.organismo_responsable_id
    LEFT JOIN funcionarios f ON f.id = a.funcionario_responsable_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY a.codigo
    LIMIT 300
");
$stmt->execute($params);
$acciones = $stmt->fetchAll();

$estrategias = $db->query("SELECT id, codigo, descripcion FROM estrategias ORDER BY codigo")->fetchAll();
$organismos  = $db->query("SELECT id, sigla, nombre_completo FROM organismos ORDER BY sigla")->fetchAll();
include 'includes/header.php';
?>

<div class="space-y-5">
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Acciones del Plan</h1>
        <p class="text-gray-500 text-sm"><?= count($acciones) ?> resultado(s)</p>
        <?php if ($context_estrategia || $context_componente): ?>
        <div class="mt-2 flex flex-wrap gap-2 text-xs">
            <?php if ($context_estrategia): ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                <i class="fas fa-chess"></i>
                Estrategia: <?= h($context_estrategia['codigo']) ?> — <?= h(mb_substr($context_estrategia['descripcion'], 0, 60)) ?><?= mb_strlen($context_estrategia['descripcion']) > 60 ? '...' : '' ?>
                <a href="acciones.php?<?= h(http_build_query(array_filter([
                    'q' => $q,
                    'organismo' => $org_id ?: '',
                    'estado' => $estado,
                    'componente' => $comp_id ?: ''
                ]))) ?>"
                   class="ml-1 text-blue-500 hover:text-blue-700"
                   title="Quitar filtro de estrategia">
                    <i class="fas fa-times"></i>
                </a>
            </span>
            <?php endif; ?>
            <?php if ($context_componente): ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                <i class="fas fa-puzzle-piece"></i>
                Componente: <?= h($context_componente['codigo']) ?> — <?= h(mb_substr($context_componente['descripcion'], 0, 60)) ?><?= mb_strlen($context_componente['descripcion']) > 60 ? '...' : '' ?>
                <a href="acciones.php?<?= h(http_build_query(array_filter([
                    'q' => $q,
                    'estrategia' => $est_id ?: '',
                    'organismo' => $org_id ?: '',
                    'estado' => $estado
                ]))) ?>"
                   class="ml-1 text-purple-500 hover:text-purple-700"
                   title="Quitar filtro de componente">
                    <i class="fas fa-times"></i>
                </a>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <?php if ($comp_id): ?>
        <input type="hidden" name="componente" value="<?= $comp_id ?>">
        <?php endif; ?>
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="q" value="<?= h($q) ?>" placeholder="Código o descripción..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Estrategia</label>
            <select name="estrategia" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                <?php foreach($estrategias as $e): ?>
                <option value="<?= $e['id'] ?>" <?= $est_id == $e['id'] ? 'selected' : '' ?>>
                    <?= h($e['codigo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
            <select name="estado" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <option value="pendiente" <?= $estado==='pendiente'?'selected':'' ?>>Pendiente</option>
                <option value="en_curso" <?= $estado==='en_curso'?'selected':'' ?>>En curso</option>
                <option value="completada" <?= $estado==='completada'?'selected':'' ?>>Completada</option>
                <option value="suspendida" <?= $estado==='suspendida'?'selected':'' ?>>Suspendida</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
            <i class="fas fa-search mr-1"></i> Filtrar
        </button>
        <a href="acciones.php" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Limpiar</a>
    </form>
</div>

<!-- Tabla con asignación inline -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm">
    <thead>
        <tr class="bg-gray-50 border-b border-gray-100 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider w-48">Código</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Descripción</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Estado</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Organismo</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Responsable</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider text-center">Proy.</th>
            <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Asignar</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
    <?php foreach($acciones as $acc): ?>
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-3">
            <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 block"><?= h($acc['codigo']) ?></code>
            <span class="text-xs text-gray-400 mt-0.5 block"><?= h($acc['est_codigo']) ?> › <?= h($acc['prog_codigo']) ?></span>
        </td>
        <td class="px-4 py-3">
            <p class="text-gray-800 text-sm leading-snug"><?= h(mb_substr($acc['descripcion'],0,100)) ?><?= mb_strlen($acc['descripcion'])>100?'...':'' ?></p>
        </td>
        <td class="px-4 py-3"><?= badgeEstado($acc['estado']) ?></td>
        <td class="px-4 py-3">
            <?php if ($acc['org_sigla']): ?>
            <span class="inline-flex items-center gap-1.5 text-xs">
                <span class="w-2 h-2 rounded-full" style="background:<?= h($acc['color_hex']) ?>"></span>
                <?= h($acc['org_sigla']) ?>
            </span>
            <?php else: ?><span class="text-gray-300 text-xs">Sin asignar</span><?php endif; ?>
        </td>
        <td class="px-4 py-3 text-xs text-gray-600"><?= h(mb_substr($acc['responsable_nombre']??'—',0,25)) ?></td>
        <td class="px-4 py-3 text-center">
            <?php if ($acc['total_proyectos'] > 0): ?>
            <a href="proyectos.php?accion=<?= $acc['id'] ?>" class="inline-flex items-center justify-center w-6 h-6 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold hover:bg-blue-200">
                <?= $acc['total_proyectos'] ?>
            </a>
            <?php else: ?>
            <a href="proyectos.php?action=new&accion=<?= $acc['id'] ?>"
               class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700 hover:bg-green-200 hover:text-green-800 text-base font-bold leading-none transition-colors"
               title="Agregar proyecto">+</a>
            <?php endif; ?>
        </td>
        <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
        <td class="px-4 py-3">
            <button type="button"
                    class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 js-open-assign-modal"
                    data-id="<?= $acc['id'] ?>"
                    data-codigo="<?= h($acc['codigo']) ?>"
                    data-estado="<?= h($acc['estado']) ?>"
                    data-organismo="<?= (int)($acc['organismo_responsable_id'] ?? 0) ?>"
                    data-funcionario="<?= (int)($acc['funcionario_responsable_id'] ?? 0) ?>">
                <i class="fas fa-user-tag"></i> Asignar
            </button>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>

<?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
<div id="assign-modal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 js-assign-close"></div>
    <div class="relative mx-auto mt-20 w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Asignar acción</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600 js-assign-close"><i class="fas fa-times"></i></button>
        </div>
        <p class="text-xs text-gray-500 mb-3">Código: <span id="assign-modal-code" class="font-medium text-gray-700">—</span></p>
        <form method="POST" id="assign-modal-form" class="space-y-3">
            <input type="hidden" name="asignar" value="1">
            <input type="hidden" name="accion_id" id="assign-modal-id" value="">
            <select name="estado" id="assign-modal-estado" class="border border-gray-300 rounded px-2 py-2 text-sm w-full">
                <?php foreach(['pendiente','en_curso','completada','suspendida'] as $est): ?>
                <option value="<?= $est ?>"><?= ucfirst(str_replace('_',' ',$est)) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="organismo_id" id="assign-modal-organismo" class="border border-gray-300 rounded px-2 py-2 text-sm w-full">
                <option value="">— Organismo —</option>
                <?php foreach($organismos as $org): ?>
                <option value="<?= $org['id'] ?>"><?= h($org['sigla']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="funcionario_id" id="assign-modal-funcionario" class="border border-gray-300 rounded px-2 py-2 text-sm w-full">
                <option value="">— Funcionario —</option>
            </select>
            <div class="flex gap-2">
                <button type="button" class="border border-gray-300 text-gray-600 px-3 py-2 rounded text-sm w-1/2 js-assign-close">Cancelar</button>
                <button type="submit" class="bg-blue-700 text-white px-3 py-2 rounded text-sm w-1/2">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
