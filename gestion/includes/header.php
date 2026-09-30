<?php
// includes/header.php
requireLogin();
$usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuario_rol = $_SESSION['rol'] ?? 'visor';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title ?? 'Inicio') ?> | <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/app.css">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style>
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
            line-height: 1.35;
            transition: background-color 0.15s, color 0.15s;
        }
        .sidebar-link:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }
        .sidebar-link.active {
            background-color: #ffffff;
            color: #1e293b;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }
        .sidebar-link:not(.active) {
            color: #f1f5f9;
        }
        .sidebar-link:not(.active) i {
            color: #e2e8f0;
        }
        .sidebar-link.active i {
            color: #334155;
        }
        .sidebar-section {
            color: #cbd5e1;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.75rem 0.75rem 0.25rem;
        }
        [x-cloak] { display: none !important; }
        @media (max-width: 1023px) {
            .informe-contenido .informe-tabla { font-size: 0.875rem; }
            .informe-contenido .informe-tabla th,
            .informe-contenido .informe-tabla td { font-size: 0.875rem; padding-left: 0.75rem; padding-right: 0.75rem; }
        }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen overflow-x-hidden"
      x-data="{ sidebarOpen: false }"
      x-init="
        const mq = window.matchMedia('(min-width: 1024px)');
        sidebarOpen = mq.matches;
        mq.addEventListener('change', (e) => { sidebarOpen = e.matches; });
      ">

<!-- Overlay móvil -->
<div x-show="sidebarOpen"
     x-cloak
     x-transition:enter="transition-opacity ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
     aria-hidden="true"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex flex-col w-[min(100vw-3rem,16rem)] sm:w-64 bg-slate-800 shadow-xl transition-transform duration-300 -translate-x-full lg:translate-x-0 overscroll-y-contain"
       :class="{ 'translate-x-0': sidebarOpen }"
       aria-label="Menú principal">
    
    <!-- Logo -->
    <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 border-b border-slate-600">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-landmark text-slate-700 text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-white font-bold text-base leading-tight truncate">Gestión Gov.</p>
                <p class="text-slate-300 text-sm">La Pampa</p>
            </div>
        </div>
        <button type="button"
                @click="sidebarOpen = false"
                class="lg:hidden text-slate-300 hover:text-white p-2 -mr-1"
                aria-label="Cerrar menú">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 min-h-0 overflow-y-auto overscroll-y-contain p-3 space-y-0.5"
         @click="if ($event.target.closest('a') && window.innerWidth < 1024) sidebarOpen = false">
        <p class="sidebar-section">Principal</p>
        <a href="index.php" class="sidebar-link <?= $current_page === 'index' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt w-4"></i> Dashboard
        </a>
        
        <p class="sidebar-section">Plan Estratégico</p>
        <a href="estrategias.php" class="sidebar-link <?= $current_page === 'estrategias' ? 'active' : '' ?>">
            <i class="fas fa-chess w-4"></i> Estrategias
        </a>
        <a href="programas.php" class="sidebar-link <?= $current_page === 'programas' ? 'active' : '' ?>">
            <i class="fas fa-layer-group w-4"></i> Programas
        </a>
        <a href="componentes.php" class="sidebar-link <?= $current_page === 'componentes' ? 'active' : '' ?>">
            <i class="fas fa-puzzle-piece w-4"></i> Componentes
        </a>
        <a href="acciones.php" class="sidebar-link <?= $current_page === 'acciones' ? 'active' : '' ?>">
            <i class="fas fa-tasks w-4"></i> Acciones
        </a>

        <p class="sidebar-section">Ejecución</p>
        <a href="proyectos.php" class="sidebar-link <?= $current_page === 'proyectos' ? 'active' : '' ?>">
            <i class="fas fa-project-diagram w-4"></i> Proyectos
        </a>
        <a href="novedades.php" class="sidebar-link <?= $current_page === 'novedades' ? 'active' : '' ?>">
            <i class="fas fa-newspaper w-4"></i> Novedades / Bitácora
        </a>

        <p class="sidebar-section">Actores</p>
        <a href="funcionarios.php" class="sidebar-link <?= $current_page === 'funcionarios' ? 'active' : '' ?>">
            <i class="fas fa-users w-4"></i> Funcionarios
        </a>
        <a href="organismos.php" class="sidebar-link <?= $current_page === 'organismos' ? 'active' : '' ?>">
            <i class="fas fa-building-columns w-4"></i> Organismos
        </a>
        <a href="localidades.php" class="sidebar-link <?= $current_page === 'localidades' ? 'active' : '' ?>">
            <i class="fas fa-map-marker-alt w-4"></i> Localidades
        </a>

        <p class="sidebar-section">Informes</p>
        <a href="informes.php" class="sidebar-link <?= $current_page === 'informes' ? 'active' : '' ?>">
            <i class="fas fa-file-alt w-4"></i> Informes por localidad
        </a>

        <?php if ($usuario_rol === 'admin'): ?>
        <p class="sidebar-section">Administración</p>
        <a href="usuarios.php" class="sidebar-link <?= $current_page === 'usuarios' ? 'active' : '' ?>">
            <i class="fas fa-user-shield w-4"></i> Usuarios del sistema
        </a>
        <?php endif; ?>
        <?php if (in_array($usuario_rol, ['admin', 'editor'], true)): ?>
        <?php if ($usuario_rol !== 'admin'): ?>
        <p class="sidebar-section">Carga de datos</p>
        <?php endif; ?>
        <a href="importar.php" class="sidebar-link <?= $current_page === 'importar' ? 'active' : '' ?>">
            <i class="fas fa-file-import w-4"></i> Importar datos
        </a>
        <?php endif; ?>
    </nav>

    <!-- Usuario -->
    <div class="p-3 border-t border-slate-600">
        <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-9 h-9 bg-slate-700 rounded-full flex items-center justify-center border border-slate-500">
                <i class="fas fa-user text-white text-sm"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-semibold truncate"><?= h($usuario_nombre) ?></p>
                <p class="text-slate-300 text-xs capitalize"><?= h($usuario_rol) ?></p>
            </div>
            <a href="logout.php" class="text-slate-200 hover:text-white p-1" title="Cerrar sesión">
                <i class="fas fa-sign-out-alt text-lg"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Main -->
<div class="min-h-screen flex flex-col lg:ml-64 transition-all duration-300">
    <!-- Topbar -->
    <header class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <button type="button"
                        id="sidebar-toggle"
                        @click="sidebarOpen = !sidebarOpen"
                        class="shrink-0 text-gray-500 hover:text-gray-700 p-2 -ml-2 rounded-lg hover:bg-gray-100 lg:hidden"
                        aria-label="Abrir menú">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <nav class="hidden md:flex text-sm text-gray-500 items-center gap-1 min-w-0">
                    <a href="index.php" class="hover:text-blue-600 shrink-0">Inicio</a>
                    <?php if (!empty($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $bc): ?>
                            <i class="fas fa-chevron-right text-xs shrink-0"></i>
                            <?php if (!empty($bc['url'])): ?>
                                <a href="<?= h($bc['url']) ?>" class="hover:text-blue-600 truncate"><?= h($bc['label']) ?></a>
                            <?php else: ?>
                                <span class="text-gray-800 font-medium truncate"><?= h($bc['label']) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </nav>
                <p class="md:hidden text-sm font-semibold text-gray-800 truncate">
                    <?= h($page_title ?? 'Inicio') ?>
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="hidden sm:inline text-xs text-gray-400"><?= date('d/m/Y H:i') ?></span>
                <span class="sm:hidden text-xs text-gray-400"><?= date('d/m/y') ?></span>
            </div>
        </div>
    </header>

    <!-- Page content -->
    <main class="flex-1 p-4 sm:p-6">
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="mb-4 p-4 rounded-lg <?= $_SESSION['flash']['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200' ?>">
                <i class="fas <?= $_SESSION['flash']['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= h($_SESSION['flash']['msg']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>
