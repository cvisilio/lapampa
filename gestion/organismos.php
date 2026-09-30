<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Organismos';
$breadcrumb = [['label' => 'Organismos']];

$organismos = $db->query("
    SELECT o.*,
           COUNT(DISTINCT f.id) as total_funcionarios,
           COUNT(DISTINCT a.id) as total_acciones,
           COUNT(DISTINCT p.id) as total_proyectos
    FROM organismos o
    LEFT JOIN funcionarios f ON f.organismo_id = o.id AND f.activo = 1
    LEFT JOIN acciones a ON a.organismo_responsable_id = o.id
    LEFT JOIN proyectos p ON p.organismo_id = o.id
    GROUP BY o.id
    ORDER BY o.nombre_completo
")->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-5">
<div>
    <h1 class="text-2xl font-bold text-gray-900">Organismos y Ministerios</h1>
    <p class="text-gray-500 text-sm"><?= count($organismos) ?> organismos registrados</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
<?php foreach($organismos as $org): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
    <div class="h-1.5" style="background-color:<?= h($org['color_hex']) ?>"></div>
    <div class="p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                 style="background-color:<?= h($org['color_hex']) ?>">
                <?= mb_substr($org['sigla'], 0, 2) ?>
            </div>
            <div>
                <p class="font-bold text-gray-900 text-sm"><?= h($org['sigla']) ?></p>
                <p class="text-gray-500 text-xs leading-tight mt-0.5"><?= h($org['nombre_completo']) ?></p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
            <div class="bg-gray-50 rounded-lg p-2">
                <p class="text-lg font-bold text-gray-800"><?= $org['total_funcionarios'] ?></p>
                <p class="text-xs text-gray-400">Funcionarios</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-2">
                <p class="text-lg font-bold text-gray-800"><?= $org['total_acciones'] ?></p>
                <p class="text-xs text-gray-400">Acciones</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-2">
                <p class="text-lg font-bold text-gray-800"><?= $org['total_proyectos'] ?></p>
                <p class="text-xs text-gray-400">Proyectos</p>
            </div>
        </div>
        <div class="mt-3 flex gap-2">
            <a href="funcionarios.php?organismo=<?= $org['id'] ?>"
               class="flex-1 text-center text-xs border border-gray-200 text-gray-600 hover:bg-gray-50 py-1.5 rounded-lg transition-colors">
                <i class="fas fa-users mr-1"></i> Ver funcionarios
            </a>
            <a href="acciones.php?organismo=<?= $org['id'] ?>"
               class="flex-1 text-center text-xs border border-gray-200 text-gray-600 hover:bg-gray-50 py-1.5 rounded-lg transition-colors">
                <i class="fas fa-tasks mr-1"></i> Ver acciones
            </a>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
</div>

<?php include 'includes/footer.php'; ?>
