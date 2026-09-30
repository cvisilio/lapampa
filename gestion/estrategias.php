<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Estrategias';
$breadcrumb = [['label' => 'Estrategias']];

// Asignar responsable a estrategia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asignar_est'])) {
    requireRol('admin','editor');
    $db->prepare("UPDATE estrategias SET organismo_responsable_id=?, funcionario_responsable_id=?, estado=?, updated_at=NOW() WHERE id=?")
       ->execute([
           (int)$_POST['organismo_id'] ?: null,
           (int)$_POST['funcionario_id'] ?: null,
           $_POST['estado'],
           (int)$_POST['id']
       ]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Estrategia actualizada.'];
    header('Location: estrategias.php');
    exit;
}

// Cargar jerarquía completa
$estrategias = $db->query("
    SELECT e.*,
           o.sigla as org_sigla, o.color_hex,
           f.nombre_apellido as responsable,
           (SELECT COUNT(*) FROM programas p WHERE p.estrategia_id = e.id) as total_programas,
           (SELECT COUNT(*) FROM acciones a 
            JOIN componentes c ON c.id = a.componente_id 
            JOIN programas p ON p.id = c.programa_id 
            WHERE p.estrategia_id = e.id) as total_acciones,
           (SELECT COUNT(*) FROM proyectos pr
            JOIN acciones a ON a.id = pr.accion_id
            JOIN componentes c ON c.id = a.componente_id
            JOIN programas p ON p.id = c.programa_id
            WHERE p.estrategia_id = e.id) as total_proyectos
    FROM estrategias e
    LEFT JOIN organismos o ON o.id = e.organismo_responsable_id
    LEFT JOIN funcionarios f ON f.id = e.funcionario_responsable_id
    ORDER BY e.codigo
")->fetchAll();

$organismos  = $db->query("SELECT id, sigla, nombre_completo FROM organismos ORDER BY sigla")->fetchAll();
include 'includes/header.php';
?>

<div class="space-y-5">
<div>
    <h1 class="text-2xl font-bold text-gray-900">Estrategias del Plan</h1>
    <p class="text-gray-500 text-sm"><?= count($estrategias) ?> estrategias · Asignación de responsabilidades</p>
</div>

<div class="space-y-4">
<?php foreach($estrategias as $est): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-start gap-4 p-5" style="border-left: 4px solid <?= h($est['color_hex'] ?? '#3B82F6') ?>">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <code class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-mono"><?= h($est['codigo']) ?></code>
                <?= badgeEstado($est['estado']) ?>
            </div>
            <p class="font-semibold text-gray-900"><?= h($est['descripcion']) ?></p>
            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500">
                <span><i class="fas fa-layer-group mr-1 text-blue-400"></i><?= $est['total_programas'] ?> programas</span>
                <span><i class="fas fa-tasks mr-1 text-purple-400"></i><?= $est['total_acciones'] ?> acciones</span>
                <span><i class="fas fa-project-diagram mr-1 text-green-400"></i><?= $est['total_proyectos'] ?> proyectos</span>
                <?php if ($est['responsable']): ?>
                <span class="text-blue-600"><i class="fas fa-user mr-1"></i><?= h($est['responsable']) ?> (<?= h($est['org_sigla']) ?>)</span>
                <?php else: ?>
                <span class="text-amber-500"><i class="fas fa-exclamation-triangle mr-1"></i>Sin responsable asignado</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="acciones.php?estrategia=<?= $est['id'] ?>" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg text-xs flex items-center gap-1">
                <i class="fas fa-tasks"></i> Ver acciones
            </a>
            <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
            <button type="button"
                    class="border border-blue-300 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-xs flex items-center gap-1 js-open-assign-modal"
                    data-id="<?= $est['id'] ?>"
                    data-codigo="<?= h($est['codigo']) ?>"
                    data-estado="<?= h($est['estado']) ?>"
                    data-organismo="<?= (int)($est['organismo_responsable_id'] ?? 0) ?>"
                    data-funcionario="<?= (int)($est['funcionario_responsable_id'] ?? 0) ?>">
                <i class="fas fa-user-tag"></i> Asignar
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
</div>

<?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
<div id="assign-modal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 js-assign-close"></div>
    <div class="relative mx-auto mt-20 w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Asignar estrategia</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600 js-assign-close"><i class="fas fa-times"></i></button>
        </div>
        <p class="text-xs text-gray-500 mb-3">Código: <span id="assign-modal-code" class="font-medium text-gray-700">—</span></p>
        <form method="POST" id="assign-modal-form" class="space-y-3">
            <input type="hidden" name="asignar_est" value="1">
            <input type="hidden" name="id" id="assign-modal-id" value="">
            <select name="estado" id="assign-modal-estado" class="border border-gray-300 rounded px-2 py-2 text-sm w-full">
                <?php foreach(['pendiente','en_curso','completada','suspendida'] as $s): ?>
                <option value="<?= $s ?>"><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="organismo_id" id="assign-modal-organismo" class="border border-gray-300 rounded px-2 py-2 text-sm w-full">
                <option value="">— Organismo —</option>
                <?php foreach($organismos as $org): ?>
                <option value="<?= $org['id'] ?>"><?= h($org['sigla']) ?> — <?= h($org['nombre_completo']) ?></option>
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
