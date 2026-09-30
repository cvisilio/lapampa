<?php
require_once 'includes/config.php';

// Si ya está logueado, redirigir
if (!empty($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email && $pass) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['usuario_id']     = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            $_SESSION['rol']            = $user['rol'];
            $_SESSION['organismo_id']   = $user['organismo_id'];

            // Actualizar último acceso
            $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?")->execute([$user['id']]);

            header('Location: index.php');
            exit;
        } else {
            $error = 'Credenciales incorrectas. Verificá tu email y contraseña.';
        }
    } else {
        $error = 'Completá todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-2xl mb-4 backdrop-blur">
            <i class="fas fa-landmark text-white text-4xl"></i>
        </div>
        <h1 class="text-white text-xl sm:text-2xl font-bold">Sistema de Gestión Gubernamental</h1>
        <p class="text-blue-300 mt-1">Provincia de La Pampa</p>
    </div>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8">
        <h2 class="text-gray-800 text-lg sm:text-xl font-bold mb-6">Iniciar sesión</h2>

        <?php if ($error): ?>
            <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email institucional</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" required autofocus autocomplete="username"
                           value="<?= h($_POST['email'] ?? '') ?>"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                           placeholder="usuario@lapampa.gov.ar">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                           class="w-full pl-10 pr-11 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                           placeholder="••••••••">
                    <button type="button"
                            id="toggle-password"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1"
                            aria-label="Mostrar contraseña"
                            title="Mostrar contraseña">
                        <i class="fas fa-eye" id="toggle-password-icon"></i>
                    </button>
                </div>
            </div>
            <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Ingresar al sistema
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-6">
            Acceso restringido a personal autorizado.<br>
        </p>
    </div>
</div>
<script>
(function () {
    const input = document.getElementById('password');
    const btn = document.getElementById('toggle-password');
    const icon = document.getElementById('toggle-password-icon');
    if (!input || !btn || !icon) return;

    btn.addEventListener('click', function () {
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        icon.classList.toggle('fa-eye', visible);
        icon.classList.toggle('fa-eye-slash', !visible);
        btn.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        btn.setAttribute('title', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
    });
})();
</script>
</body>
</html>
