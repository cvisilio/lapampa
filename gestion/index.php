<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Dashboard';

// KPIs principales
$kpis = $db->query("
    SELECT
        (SELECT COUNT(*) FROM estrategias) AS total_estrategias,
        (SELECT COUNT(*) FROM programas) AS total_programas,
        (SELECT COUNT(*) FROM componentes) AS total_componentes,
        (SELECT COUNT(*) FROM acciones) AS total_acciones,
        (SELECT COUNT(*) FROM proyectos) AS total_proyectos,
        (SELECT COUNT(*) FROM proyectos WHERE estado = 'en_ejecucion') AS proyectos_activos,
        (SELECT COUNT(*) FROM proyectos WHERE estado = 'completado') AS proyectos_completados,
        (SELECT COUNT(*) FROM acciones WHERE estado = 'en_curso') AS acciones_en_curso,
        (SELECT COUNT(*) FROM acciones WHERE estado = 'completada') AS acciones_completadas,
        (SELECT COUNT(*) FROM funcionarios WHERE activo = 1) AS funcionarios_activos,
        (SELECT COALESCE(SUM(presupuesto_total),0) FROM proyectos) AS presupuesto_total,
        (SELECT COALESCE(SUM(presupuesto_ejecutado),0) FROM proyectos) AS presupuesto_ejecutado
")->fetch();

// Proyectos por estado
$estados_proy = $db->query("
    SELECT estado, COUNT(*) as total FROM proyectos GROUP BY estado ORDER BY total DESC
")->fetchAll();

// Acciones por organismo (top 8)
$por_organismo = $db->query("
    SELECT o.sigla, o.nombre_completo, o.color_hex,
           COUNT(DISTINCT p.id) as proyectos,
           COUNT(DISTINCT a.id) as acciones
    FROM organismos o
    LEFT JOIN acciones a ON a.organismo_responsable_id = o.id
    LEFT JOIN proyectos p ON p.organismo_id = o.id
    GROUP BY o.id
    HAVING proyectos > 0 OR acciones > 0
    ORDER BY proyectos DESC, acciones DESC
    LIMIT 8
")->fetchAll();

// Últimas novedades
$novedades = $db->query("
    SELECT n.*, f.nombre_apellido as funcionario_nombre
    FROM novedades n
    LEFT JOIN funcionarios f ON f.id = n.funcionario_id
    ORDER BY n.created_at DESC LIMIT 6
")->fetchAll();

// Proyectos recientes
$proyectos_recientes = $db->query("
    SELECT p.*, a.codigo as accion_codigo, o.sigla as organismo_sigla,
           o.color_hex, f.nombre_apellido as responsable
    FROM proyectos p
    JOIN acciones a ON a.id = p.accion_id
    LEFT JOIN organismos o ON o.id = p.organismo_id
    LEFT JOIN funcionarios f ON f.id = p.funcionario_responsable_id
    ORDER BY p.updated_at DESC LIMIT 5
")->fetchAll();

// Avance general del plan
$avance_plan = $db->query("
    SELECT 
        ROUND(AVG(porcentaje_avance), 1) as avance_promedio,
        COUNT(*) as total,
        SUM(CASE WHEN estado='completada' THEN 1 ELSE 0 END) as completadas
    FROM acciones
")->fetch();

include 'includes/header.php';
?>

<div class="space-y-6">

<!-- Título -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard General</h1>
        <p class="text-gray-500 text-sm mt-0.5">Plan Estratégico de La Pampa — Resumen ejecutivo</p>
    </div>
    <a href="proyectos.php?action=new" class="inline-flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors w-full sm:w-auto">
        <i class="fas fa-plus"></i> Nuevo Proyecto
    </a>
</div>

<!-- Indicadores rápidos -->
<div class="grid grid-cols-2 gap-4 max-w-xl">
    <a href="proyectos.php?estado=en_ejecucion"
       class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-green-200 hover:shadow-md transition-all group">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Proyectos activos</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int) $kpis['proyectos_activos'] ?></p>
                <p class="text-xs text-green-600 font-medium mt-2 group-hover:underline">Ver en ejecución</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-project-diagram text-green-600"></i>
            </div>
        </div>
    </a>

    <a href="acciones.php?estado=en_curso"
       class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition-all group">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Acciones en curso</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= (int) $kpis['acciones_en_curso'] ?></p>
                <p class="text-xs text-purple-600 font-medium mt-2 group-hover:underline">Ver en curso</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                <i class="fas fa-tasks text-purple-600"></i>
            </div>
        </div>
    </a>
</div>

<!-- KPIs fila 1 -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Estrategias</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $kpis['total_estrategias'] ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chess text-blue-600"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3"><?= $kpis['total_programas'] ?> programas · <?= $kpis['total_componentes'] ?> componentes</p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Acciones totales</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $kpis['total_acciones'] ?></p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-tasks text-purple-600"></i>
            </div>
        </div>
        <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
            <?php $pct_acc = $kpis['total_acciones'] > 0 ? round($kpis['acciones_completadas']/$kpis['total_acciones']*100) : 0; ?>
            <div class="bg-purple-500 h-1.5 rounded-full" style="width:<?= $pct_acc ?>%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1"><?= $kpis['acciones_completadas'] ?> completadas (<?= $pct_acc ?>%)</p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Proyectos</p>
                <p class="text-3xl font-bold text-gray-900 mt-1"><?= $kpis['total_proyectos'] ?></p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-project-diagram text-green-600"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            <span class="text-blue-600 font-medium"><?= $kpis['proyectos_activos'] ?> en ejecución</span>
            · <?= $kpis['proyectos_completados'] ?> completados
        </p>
    </div>

    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-gray-500 text-xs font-medium uppercase tracking-wider">Presupuesto ejecutado</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= formatPesos($kpis['presupuesto_ejecutado']) ?></p>
            </div>
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-amber-600"></i>
            </div>
        </div>
        <?php
        $pct_pres = $kpis['presupuesto_total'] > 0 ? round($kpis['presupuesto_ejecutado']/$kpis['presupuesto_total']*100) : 0;
        ?>
        <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5">
            <div class="bg-amber-500 h-1.5 rounded-full" style="width:<?= min($pct_pres,100) ?>%"></div>
        </div>
        <p class="text-xs text-gray-400 mt-1">de <?= formatPesos($kpis['presupuesto_total']) ?> total (<?= $pct_pres ?>%)</p>
    </div>
</div>

<!-- Fila 2: Gráfico + Avance -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <!-- Gráfico estados proyectos -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Proyectos por estado</h3>
        <canvas id="chartEstados" height="200"></canvas>
    </div>

    <!-- Avance del plan -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Avance general del plan</h3>
        <div class="flex items-center justify-center mb-4">
            <div class="relative w-36 h-36">
                <canvas id="chartAvance"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gray-900"><?= $avance_plan['avance_promedio'] ?>%</p>
                        <p class="text-xs text-gray-400">avance prom.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
                <span>Acciones completadas</span>
                <span class="font-semibold text-green-600"><?= $avance_plan['completadas'] ?> / <?= $avance_plan['total'] ?></span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>En curso</span>
                <span class="font-semibold text-blue-600"><?= $kpis['acciones_en_curso'] ?></span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>Funcionarios activos</span>
                <span class="font-semibold"><?= $kpis['funcionarios_activos'] ?></span>
            </div>
        </div>
    </div>

    <!-- Organismos más activos -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-4">Organismos con proyectos</h3>
        <div class="space-y-2.5">
            <?php foreach($por_organismo as $org): ?>
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-medium text-gray-700 truncate"><?= h($org['sigla']) ?></span>
                    <span class="text-gray-500"><?= $org['proyectos'] ?> proy · <?= $org['acciones'] ?> acc</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <?php $max_p = max(array_column($por_organismo,'proyectos')) ?: 1; ?>
                    <div class="h-1.5 rounded-full" style="width:<?= round($org['proyectos']/$max_p*100) ?>%;background-color:<?= h($org['color_hex']) ?>"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Fila 3: Últimos proyectos + Novedades -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    <!-- Proyectos recientes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Proyectos recientes</h3>
            <a href="proyectos.php" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Ver todos →</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($proyectos_recientes)): ?>
                <p class="px-5 py-8 text-center text-gray-400 text-sm">No hay proyectos cargados aún.</p>
            <?php else: ?>
            <?php foreach($proyectos_recientes as $p): ?>
            <a href="proyectos.php?id=<?= $p['id'] ?>" class="flex items-start gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors">
                <div class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0" style="background-color:<?= h($p['color_hex'] ?? '#6B7280') ?>"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate"><?= h($p['nombre']) ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?= h($p['accion_codigo']) ?> · <?= h($p['organismo_sigla'] ?? '—') ?></p>
                </div>
                <div class="flex-shrink-0 text-right">
                    <?= badgeEstado($p['estado']) ?>
                    <?php if ($p['porcentaje_avance'] > 0): ?>
                    <p class="text-xs text-gray-400 mt-1"><?= $p['porcentaje_avance'] ?>%</p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Novedades recientes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Últimas novedades</h3>
            <a href="novedades.php" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Ver todas →</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($novedades)): ?>
                <p class="px-5 py-8 text-center text-gray-400 text-sm">No hay novedades registradas aún.</p>
            <?php else: ?>
            <?php foreach($novedades as $n): ?>
            <div class="px-5 py-3.5">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-sm font-medium text-gray-900"><?= h($n['titulo']) ?></p>
                    <span class="text-xs text-gray-400 flex-shrink-0"><?= date('d/m/Y', strtotime($n['created_at'])) ?></span>
                </div>
                <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= h($n['descripcion']) ?></p>
                <p class="text-xs text-blue-600 mt-1"><?= h($n['funcionario_nombre'] ?? 'Sistema') ?> · <?= h($n['tipo']) ?></p>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>

<script>
// Gráfico: proyectos por estado
const estadosData = <?= json_encode(array_column($estados_proy, 'total')) ?>;
const estadosLabels = <?= json_encode(array_map(fn($e) => ucfirst(str_replace('_',' ',$e['estado'])), $estados_proy)) ?>;
const colores = ['#3B82F6','#10B981','#8B5CF6','#F59E0B','#EF4444','#6B7280'];

new Chart(document.getElementById('chartEstados'), {
    type: 'doughnut',
    data: {
        labels: estadosLabels,
        datasets: [{ data: estadosData, backgroundColor: colores, borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } }
    }
});

// Gráfico: avance
const avance = <?= $avance_plan['avance_promedio'] ?? 0 ?>;
new Chart(document.getElementById('chartAvance'), {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [avance, 100 - avance],
            backgroundColor: ['#3B82F6', '#E5E7EB'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true, cutout: '75%',
        plugins: { legend: { display: false }, tooltip: { enabled: false } }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
