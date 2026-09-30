<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Funcionarios';
$breadcrumb = [['label' => 'Funcionarios']];
$can_manage = in_array($_SESSION['rol'] ?? '', ['admin', 'editor']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_funcionario'])) {
    requireRol('admin', 'editor');
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre_apellido'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $organismo_id = (int)($_POST['organismo_id'] ?? 0);

    if ($nombre === '' || $cargo === '' || !$organismo_id) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Completá nombre, cargo y organismo.'];
        header('Location: funcionarios.php' . ($id ? '?edit=' . $id : '?action=new'));
        exit;
    }

    if ($id) {
        $db->prepare("
            UPDATE funcionarios
            SET nombre_apellido = ?, cargo = ?, documento = ?, organismo_id = ?, updated_at = NOW()
            WHERE id = ?
        ")->execute([$nombre, $cargo, $documento ?: null, $organismo_id, $id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Funcionario actualizado correctamente.'];
    } else {
        $db->prepare("
            INSERT INTO funcionarios (nombre_apellido, cargo, documento, organismo_id, activo)
            VALUES (?, ?, ?, ?, 1)
        ")->execute([$nombre, $cargo, $documento ?: null, $organismo_id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Funcionario agregado correctamente.'];
    }

    header('Location: funcionarios.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_funcionario'])) {
    requireRol('admin', 'editor');
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        $db->prepare("UPDATE funcionarios SET activo = 0, updated_at = NOW() WHERE id = ?")->execute([$id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Funcionario eliminado correctamente.'];
    }
    header('Location: funcionarios.php');
    exit;
}

$q = trim($_GET['q'] ?? '');
$org_id = (int)($_GET['organismo'] ?? 0);
$modo_form = $can_manage && (($_GET['action'] ?? '') === 'new' || !empty($_GET['edit']));
$edit_id = (int)($_GET['edit'] ?? 0);
$edit_data = null;
if ($modo_form && $edit_id) {
    $stmtEdit = $db->prepare("SELECT * FROM funcionarios WHERE id = ? LIMIT 1");
    $stmtEdit->execute([$edit_id]);
    $edit_data = $stmtEdit->fetch();
}

$where = ['f.activo = 1'];
$params = [];

if ($q) {
    $where[] = '(f.nombre_apellido LIKE ? OR f.cargo LIKE ? OR f.documento LIKE ?)';
    $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
}
if ($org_id) {
    $where[] = 'f.organismo_id = ?';
    $params[] = $org_id;
}

$stmt = $db->prepare("
    SELECT f.*, o.sigla, o.nombre_completo as org_nombre, o.color_hex,
           (SELECT COUNT(*) FROM proyectos p WHERE p.funcionario_responsable_id = f.id) as proyectos_a_cargo,
           (SELECT COUNT(*) FROM acciones a WHERE a.funcionario_responsable_id = f.id) as acciones_a_cargo
    FROM funcionarios f
    JOIN organismos o ON o.id = f.organismo_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY
        CASE
            WHEN UPPER(TRIM(f.cargo)) LIKE 'MINISTRO%' THEN 1
            WHEN UPPER(TRIM(f.cargo)) LIKE 'SECRETARIO PRIVADO%' THEN 2
            WHEN UPPER(TRIM(f.cargo)) LIKE 'SUBSEC%' THEN 3
            WHEN UPPER(TRIM(f.cargo)) LIKE 'DIRECTORA GRAL%'
              OR UPPER(TRIM(f.cargo)) LIKE 'DIRECTORA GENERAL%' THEN 4
            WHEN UPPER(TRIM(f.cargo)) LIKE 'DIRECTOR GRAL%'
              OR UPPER(TRIM(f.cargo)) LIKE 'DIRECTOR GENERAL%' THEN 5
            WHEN UPPER(TRIM(f.cargo)) LIKE 'DIR. GRAL%'
              OR UPPER(REPLACE(TRIM(f.cargo), ' ', '')) LIKE 'DIR.GRAL%' THEN 6
            WHEN UPPER(TRIM(f.cargo)) LIKE 'DIRECTOR%'
             AND UPPER(TRIM(f.cargo)) NOT LIKE 'DIRECTORA%' THEN 7
            WHEN UPPER(TRIM(f.cargo)) LIKE 'DIR.%'
             AND UPPER(TRIM(f.cargo)) NOT LIKE 'DIR. GRAL%'
             AND UPPER(REPLACE(TRIM(f.cargo), ' ', '')) NOT LIKE 'DIR.GRAL%' THEN 8
            WHEN UPPER(TRIM(f.cargo)) LIKE 'DIRECTORA%' THEN 9
            WHEN UPPER(REPLACE(REPLACE(TRIM(f.cargo), ' ', ''), '.', '')) LIKE 'SUBDIRGRAL%' THEN 10
            WHEN UPPER(TRIM(f.cargo)) LIKE 'SUBDIR%' THEN 11
            ELSE 99
        END,
        f.nombre_apellido
    LIMIT 200
");
$stmt->execute($params);
$funcionarios = $stmt->fetchAll();

$organismos = $db->query("SELECT * FROM organismos ORDER BY sigla")->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-5">
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Funcionarios</h1>
        <p class="text-gray-500 text-sm"><?= count($funcionarios) ?> resultado(s)</p>
    </div>
    <?php if ($can_manage): ?>
    <a href="funcionarios.php?action=new" class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
        <i class="fas fa-plus"></i> Agregar funcionario
    </a>
    <?php endif; ?>
</div>

<?php if ($modo_form): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
    <h2 class="text-base font-semibold text-gray-900 mb-4"><?= $edit_id ? 'Editar funcionario' : 'Nuevo funcionario' ?></h2>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="guardar_funcionario" value="1">
        <?php if ($edit_id): ?><input type="hidden" name="id" value="<?= $edit_id ?>"><?php endif; ?>

        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">Nombre y apellido</label>
            <input type="text" name="nombre_apellido" required value="<?= h($edit_data['nombre_apellido'] ?? '') ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Cargo</label>
            <input type="text" name="cargo" required value="<?= h($edit_data['cargo'] ?? '') ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Documento</label>
            <input type="text" name="documento" value="<?= h($edit_data['documento'] ?? '') ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                   placeholder="Opcional">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">Organismo</label>
            <select name="organismo_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">— Seleccioná organismo —</option>
                <?php foreach($organismos as $org): ?>
                <option value="<?= $org['id'] ?>" <?= (int)($edit_data['organismo_id'] ?? 0) === (int)$org['id'] ? 'selected' : '' ?>>
                    <?= h($org['sigla']) ?> — <?= h($org['nombre_completo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2 flex gap-2">
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
                <?= $edit_id ? 'Guardar cambios' : 'Agregar funcionario' ?>
            </button>
            <a href="funcionarios.php" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <input type="text" name="q" value="<?= h($q) ?>" placeholder="Nombre, cargo, DNI..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Organismo</label>
            <select name="organismo" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                <?php foreach($organismos as $org): ?>
                <option value="<?= $org['id'] ?>" <?= $org_id == $org['id'] ? 'selected' : '' ?>>
                    <?= h($org['sigla']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
            <i class="fas fa-search mr-1"></i> Filtrar
        </button>
        <a href="funcionarios.php" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Limpiar</a>
    </form>
</div>

<!-- Grilla de cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
<?php foreach($funcionarios as $f): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
    <div class="h-1.5" style="background-color:<?= h($f['color_hex']) ?>"></div>
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm font-bold"
                 style="background-color:<?= h($f['color_hex']) ?>">
                <?= mb_substr($f['nombre_apellido'], 0, 1) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm leading-tight"><?= h($f['nombre_apellido']) ?></p>
                <p class="text-gray-500 text-xs mt-0.5 leading-tight"><?= h($f['cargo']) ?></p>
                <div class="flex items-center gap-1.5 mt-1.5">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium"><?= h($f['sigla']) ?></span>
                    <?php if ($f['documento']): ?>
                    <span class="text-xs text-gray-400">DNI <?= h($f['documento']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ($f['proyectos_a_cargo'] > 0 || $f['acciones_a_cargo'] > 0): ?>
        <div class="mt-3 flex gap-3 text-xs text-gray-500 border-t border-gray-50 pt-3">
            <?php if ($f['proyectos_a_cargo'] > 0): ?>
            <span><i class="fas fa-project-diagram text-blue-400 mr-1"></i><?= $f['proyectos_a_cargo'] ?> proyectos</span>
            <?php endif; ?>
            <?php if ($f['acciones_a_cargo'] > 0): ?>
            <span><i class="fas fa-tasks text-purple-400 mr-1"></i><?= $f['acciones_a_cargo'] ?> acciones</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if ($can_manage): ?>
        <div class="mt-3 pt-3 border-t border-gray-50 flex items-center gap-2">
            <a href="funcionarios.php?edit=<?= $f['id'] ?>" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar funcionario?');">
                <input type="hidden" name="eliminar_funcionario" value="1">
                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                <button type="submit" class="inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
<?php if (empty($funcionarios)): ?>
<div class="col-span-3 py-16 text-center">
    <i class="fas fa-users text-4xl text-gray-200 mb-3 block"></i>
    <p class="text-gray-400">No se encontraron funcionarios.</p>
</div>
<?php endif; ?>
</div>
</div>

<?php include 'includes/footer.php'; ?>
