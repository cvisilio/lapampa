<?php
require_once 'includes/config.php';
requireRol('admin');

$db = getDB();
$page_title = 'Usuarios';
$breadcrumb = [['label' => 'Usuarios del sistema']];

$rolesPermitidos = ['admin', 'editor', 'visor'];

function usuarioNormalizarRol(string $rol, array $rolesPermitidos): string
{
    return in_array($rol, $rolesPermitidos, true) ? $rol : 'visor';
}

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $rol = usuarioNormalizarRol($_POST['rol'] ?? 'visor', $rolesPermitidos);
    $org_id = (int) ($_POST['organismo_id'] ?? 0) ?: null;

    if ($nombre && $email && $pass) {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->prepare('INSERT INTO usuarios (nombre, email, password_hash, rol, organismo_id) VALUES (?,?,?,?,?)')
               ->execute([$nombre, $email, $hash, $rol, $org_id]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Usuario creado correctamente.'];
        } catch (Exception $e) {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Error: el email ya existe o hubo un problema.'];
        }
    }
    header('Location: usuarios.php');
    exit;
}

// Editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $rol = usuarioNormalizarRol($_POST['rol'] ?? 'visor', $rolesPermitidos);
    $org_id = (int) ($_POST['organismo_id'] ?? 0) ?: null;

    if ($id <= 0 || $nombre === '' || $email === '') {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Completá nombre y email.'];
        header('Location: usuarios.php?edit=' . max(0, $id));
        exit;
    }

    if ($id === 1) {
        $rol = 'admin';
    }

    try {
        $stmtDup = $db->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1');
        $stmtDup->execute([$email, $id]);
        if ($stmtDup->fetchColumn()) {
            throw new RuntimeException('El email ya está en uso por otro usuario.');
        }

        if ($pass !== '') {
            if (strlen($pass) < 8) {
                throw new RuntimeException('La contraseña debe tener al menos 8 caracteres.');
            }
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->prepare('UPDATE usuarios SET nombre = ?, email = ?, password_hash = ?, rol = ?, organismo_id = ? WHERE id = ?')
               ->execute([$nombre, $email, $hash, $rol, $org_id, $id]);
        } else {
            $db->prepare('UPDATE usuarios SET nombre = ?, email = ?, rol = ?, organismo_id = ? WHERE id = ?')
               ->execute([$nombre, $email, $rol, $org_id, $id]);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Usuario actualizado correctamente.'];
        header('Location: usuarios.php');
        exit;
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Error al actualizar: ' . $e->getMessage()];
        header('Location: usuarios.php?edit=' . $id);
        exit;
    }
}

// Cambiar estado
if (isset($_GET['toggle']) && $_GET['toggle']) {
    $uid = (int) $_GET['toggle'];
    $db->prepare('UPDATE usuarios SET activo = NOT activo WHERE id = ? AND id != 1')->execute([$uid]);
    header('Location: usuarios.php');
    exit;
}

$editId = (int) ($_GET['edit'] ?? 0);
$editUsuario = null;
if ($editId > 0) {
    $stmtEdit = $db->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
    $stmtEdit->execute([$editId]);
    $editUsuario = $stmtEdit->fetch() ?: null;
}

$usuarios = $db->query('
    SELECT u.*, o.sigla FROM usuarios u
    LEFT JOIN organismos o ON o.id = u.organismo_id
    ORDER BY u.nombre
')->fetchAll();
$organismos = $db->query('SELECT id, sigla, nombre_completo FROM organismos ORDER BY sigla')->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-5">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Usuarios del sistema</h1>
        <p class="text-gray-500 text-sm"><?= count($usuarios) ?> usuarios registrados</p>
    </div>
    <?php if (!$editUsuario): ?>
    <button type="button" onclick="document.getElementById('form-usuario').classList.toggle('hidden')"
            class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium">
        <i class="fas fa-plus"></i> Nuevo usuario
    </button>
    <?php endif; ?>
</div>

<?php if ($editUsuario): ?>
<div class="bg-white rounded-xl shadow-sm border border-blue-200 p-6">
    <div class="flex items-center justify-between gap-3 mb-4">
        <h2 class="font-bold text-gray-800">Editar usuario</h2>
        <a href="usuarios.php" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
    </div>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="editar_usuario" value="1">
        <input type="hidden" name="id" value="<?= (int) $editUsuario['id'] ?>">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
            <input type="text" name="nombre" required value="<?= h($editUsuario['nombre']) ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required value="<?= h($editUsuario['email']) ?>"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
            <input type="password" name="password" minlength="8" autocomplete="new-password"
                   placeholder="Dejar vacío para no cambiar"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <?php if ((int) $editUsuario['id'] === 1): ?>
            <input type="hidden" name="rol" value="admin">
            <input type="text" value="Administrador" disabled
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500">
            <p class="text-xs text-gray-400 mt-1">El usuario principal siempre es administrador.</p>
            <?php else: ?>
            <select name="rol" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="visor" <?= $editUsuario['rol'] === 'visor' ? 'selected' : '' ?>>Visor (solo lectura)</option>
                <option value="editor" <?= $editUsuario['rol'] === 'editor' ? 'selected' : '' ?>>Editor (carga y edición)</option>
                <option value="admin" <?= $editUsuario['rol'] === 'admin' ? 'selected' : '' ?>>Administrador (acceso total)</option>
            </select>
            <?php endif; ?>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Organismo (opcional)</label>
            <select name="organismo_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">— Sin restricción —</option>
                <?php foreach ($organismos as $org): ?>
                <option value="<?= (int) $org['id'] ?>" <?= (int) ($editUsuario['organismo_id'] ?? 0) === (int) $org['id'] ? 'selected' : '' ?>>
                    <?= h($org['sigla']) ?> — <?= h($org['nombre_completo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2 flex items-center gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                Guardar cambios
            </button>
            <a href="usuarios.php" class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Form nuevo usuario -->
<div id="form-usuario" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="font-bold text-gray-800 mb-4">Crear nuevo usuario</h2>
    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="crear_usuario" value="1">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
            <input type="text" name="nombre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" name="password" required minlength="8" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <select name="rol" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="visor">Visor (solo lectura)</option>
                <option value="editor">Editor (carga y edición)</option>
                <option value="admin">Administrador (acceso total)</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Organismo (opcional)</label>
            <select name="organismo_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">— Sin restricción —</option>
                <?php foreach ($organismos as $org): ?>
                <option value="<?= (int) $org['id'] ?>"><?= h($org['sigla']) ?> — <?= h($org['nombre_completo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="md:col-span-2 flex items-center gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                Crear usuario
            </button>
            <button type="button" onclick="document.getElementById('form-usuario').classList.add('hidden')"
                    class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm">
                Cancelar
            </button>
        </div>
    </form>
</div>

<!-- Tabla usuarios -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-sm min-w-[720px]">
    <thead>
        <tr class="bg-gray-50 border-b border-gray-100 text-left">
            <th class="px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Usuario</th>
            <th class="px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Rol</th>
            <th class="px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Organismo</th>
            <th class="px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Último acceso</th>
            <th class="px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Estado</th>
            <th class="px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider text-right">Acciones</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
    <?php foreach ($usuarios as $u): ?>
    <tr class="hover:bg-gray-50 <?= $editId === (int) $u['id'] ? 'bg-blue-50/50' : '' ?>">
        <td class="px-5 py-3">
            <p class="font-medium text-gray-900"><?= h($u['nombre']) ?></p>
            <p class="text-xs text-gray-400"><?= h($u['email']) ?></p>
        </td>
        <td class="px-5 py-3">
            <?php
            $rol_cls = ['admin' => 'bg-red-100 text-red-700', 'editor' => 'bg-blue-100 text-blue-700', 'visor' => 'bg-gray-100 text-gray-600'];
            $cls = $rol_cls[$u['rol']] ?? 'bg-gray-100 text-gray-600';
            ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $cls ?>">
                <?= ucfirst(h($u['rol'])) ?>
            </span>
        </td>
        <td class="px-5 py-3 text-gray-600 text-xs"><?= h($u['sigla'] ?? 'Todos') ?></td>
        <td class="px-5 py-3 text-gray-500 text-xs whitespace-nowrap">
            <?= $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca' ?>
        </td>
        <td class="px-5 py-3">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $u['activo'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
        </td>
        <td class="px-5 py-3 text-right">
            <div class="inline-flex items-center gap-2 flex-wrap justify-end">
                <a href="usuarios.php?edit=<?= (int) $u['id'] ?>"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors">
                    <i class="fas fa-pen-to-square"></i> Editar
                </a>
                <?php if ((int) $u['id'] !== 1): ?>
                <a href="usuarios.php?toggle=<?= (int) $u['id'] ?>"
                   class="text-xs <?= $u['activo'] ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' ?>"
                   onclick="return confirm('¿Confirmar cambio de estado?')">
                    <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                </a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
