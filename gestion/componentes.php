<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Componentes';
$breadcrumb = [['label' => 'Componentes']];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_comp'])) {
    requireRol('admin','editor');
    $db->prepare("UPDATE componentes SET organismo_responsable_id=?, funcionario_responsable_id=?, estado=?, updated_at=NOW() WHERE id=?")
       ->execute([
           (int)$_POST['organismo_id'] ?: null,
           (int)$_POST['funcionario_id'] ?: null,
           $_POST['estado'],
           (int)$_POST['id']
       ]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Componente actualizado.'];
    header('Location: componentes.php');
    exit;
}

$q = trim($_GET['q'] ?? '');
$where = $q ? "WHERE (comp.codigo LIKE '%$q%' OR comp.descripcion LIKE '%$q%')" : '';

$componentes = $db->query("
    SELECT comp.*,
           prog.codigo as prog_codigo,
           e.codigo as est_codigo,
           o.sigla as org_sigla, o.color_hex,
           f.nombre_apellido as responsable,
           COUNT(DISTINCT a.id) as total_acciones
    FROM componentes comp
    JOIN programas prog ON prog.id = comp.programa_id
    JOIN estrategias e ON e.id = prog.estrategia_id
    LEFT JOIN organismos o ON o.id = comp.organismo_responsable_id
    LEFT JOIN funcionarios f ON f.id = comp.funcionario_responsable_id
    LEFT JOIN acciones a ON a.componente_id = comp.id
    $where
    GROUP BY comp.id
    ORDER BY comp.codigo
    LIMIT 200
")->fetchAll();

$organismos  = $db->query("SELECT id, sigla FROM organismos ORDER BY sigla")->fetchAll();
include 'includes/header.php';
?>

<div class="space-y-5">
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Componentes</h1>
        <p class="text-gray-500 text-sm"><?= count($componentes) ?> componentes</p>
    </div>
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="<?= h($q) ?>" placeholder="Buscar componente..."
               class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 w-64">
        <button type="submit" class="bg-blue-700 text-white px-3 py-2 rounded-lg text-sm">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm">
    <thead>
        <tr class="bg-gray-50 border-b border-gray-100 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Código</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Descripción</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Estado</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Organismo / Resp.</th>
            <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider text-center">Acciones</th>
            <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
            <th class="px-4 py-3"></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
    <?php foreach($componentes as $comp): ?>
    <tr class="hover:bg-gray-50">
        <td class="px-4 py-3">
            <code class="text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-700 block"><?= h($comp['codigo']) ?></code>
            <span class="text-xs text-gray-400 mt-0.5 block"><?= h($comp['est_codigo']) ?> › <?= h($comp['prog_codigo']) ?></span>
        </td>
        <td class="px-4 py-3">
            <p class="text-gray-800 text-sm"><?= h(mb_substr($comp['descripcion'],0,80)) ?><?= mb_strlen($comp['descripcion'])>80?'...':'' ?></p>
        </td>
        <td class="px-4 py-3"><?= badgeEstado($comp['estado']) ?></td>
        <td class="px-4 py-3">
            <?php if ($comp['org_sigla']): ?>
            <span class="inline-flex items-center gap-1 text-xs">
                <span class="w-2 h-2 rounded-full" style="background:<?= h($comp['color_hex']) ?>"></span>
                <?= h($comp['org_sigla']) ?>
            </span>
            <p class="text-xs text-gray-400 mt-0.5"><?= h(mb_substr($comp['responsable']??'',0,20)) ?></p>
            <?php else: ?><span class="text-gray-300 text-xs">Sin asignar</span><?php endif; ?>
        </td>
        <td class="px-4 py-3 text-center">
            <a href="acciones.php?componente=<?= $comp['id'] ?>" class="inline-flex items-center justify-center w-6 h-6 bg-purple-100 text-purple-700 rounded-full text-xs font-semibold hover:bg-purple-200">
                <?= $comp['total_acciones'] ?>
            </a>
        </td>
        <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
        <td class="px-4 py-3">
            <button type="button"
                    class="text-xs text-blue-600 hover:text-blue-800 js-open-assign-modal"
                    data-id="<?= $comp['id'] ?>"
                    data-codigo="<?= h($comp['codigo']) ?>"
                    data-estado="<?= h($comp['estado']) ?>"
                    data-organismo="<?= (int)($comp['organismo_responsable_id'] ?? 0) ?>"
                    data-funcionario="<?= (int)($comp['funcionario_responsable_id'] ?? 0) ?>">
                <i class="fas fa-user-tag"></i>
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
            <h3 class="text-base font-semibold text-gray-900">Asignar componente</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600 js-assign-close"><i class="fas fa-times"></i></button>
        </div>
        <p class="text-xs text-gray-500 mb-3">Código: <span id="assign-modal-code" class="font-medium text-gray-700">—</span></p>
        <form method="POST" id="assign-modal-form" class="space-y-3">
            <input type="hidden" name="asignar_comp" value="1">
            <input type="hidden" name="id" id="assign-modal-id" value="">
            <select name="estado" id="assign-modal-estado" class="border border-gray-300 rounded px-2 py-2 text-sm w-full">
                <?php foreach(['pendiente','en_curso','completada','suspendida'] as $s): ?>
                <option value="<?= $s ?>"><?= ucfirst(str_replace('_',' ',$s)) ?></option>
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
