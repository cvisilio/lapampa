<?php
require_once 'includes/config.php';
require_once 'includes/localidades_match.php';
requireLogin();

$db = getDB();
$page_title = 'Localidades';
$breadcrumb = [['label' => 'Localidades']];

$deptColumns = $db->query("SHOW COLUMNS FROM departamentos")->fetchAll();
$deptColumnNames = array_map(static fn(array $col): string => $col['Field'], $deptColumns);
$deptLabelField = 'id';
foreach (['nombre', 'descripcion', 'departamento', 'detalle', 'denominacion', 'label'] as $candidate) {
    if (in_array($candidate, $deptColumnNames, true)) {
        $deptLabelField = $candidate;
        break;
    }
}
$deptNameExpr = $deptLabelField === 'id' ? 'CAST(id AS CHAR)' : "`{$deptLabelField}`";
$deptNameExprWithAlias = $deptLabelField === 'id' ? 'CAST(d.id AS CHAR)' : "d.`{$deptLabelField}`";
define('AGERET_XLSX', __DIR__ . '/InformesXLocalidad/CODIGOS_AGENTES_RETENCION.xlsx');

$localidadesColumns = $db->query('SHOW COLUMNS FROM localidades')->fetchAll();
$localidadesColumnNames = array_map(static fn(array $col): string => $col['Field'], $localidadesColumns);
if (!in_array('documento', $localidadesColumnNames, true)) {
    $db->exec("ALTER TABLE localidades ADD COLUMN documento VARCHAR(20) DEFAULT NULL AFTER intendente");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sincronizar_ageret'])) {
    requireRol('admin', 'editor');
    try {
        $stats = sincronizarAgeRetDesdeExcel($db, AGERET_XLSX);
        $msg = "AgeRet actualizado en {$stats['actualizados']} localidad(es).";
        if ($stats['sin_cambios'] > 0) {
            $msg .= " Sin cambios: {$stats['sin_cambios']}.";
        }
        if (!empty($stats['sin_coincidencia'])) {
            $msg .= ' Sin coincidencia: ' . implode(', ', array_slice($stats['sin_coincidencia'], 0, 8));
        }
        if (!empty($stats['coincidencias_dudosas'])) {
            $msg .= ' Coincidencias aproximadas: ' . implode(' | ', array_slice($stats['coincidencias_dudosas'], 0, 5));
        }
        $_SESSION['flash'] = ['type' => 'success', 'msg' => $msg];
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'No se pudo sincronizar AgeRet: ' . $e->getMessage()];
    }
    header('Location: localidades.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_localidad'])) {
    requireRol('admin', 'editor');

    $id = (int)($_POST['id'] ?? 0);
    $id_loc_gob_c = trim($_POST['id_loc_gob_c'] ?? '');
    $AgeRet = trim($_POST['AgeRet'] ?? '');
    $id_loc_padron = trim($_POST['id_loc_padron'] ?? '');
    $localidad = repararTextoLocalidad(trim($_POST['localidad'] ?? ''));
    $cantidad_habitantes = (int)($_POST['cantidad_habitantes'] ?? 0);
    $demanda_habitacional = trim($_POST['demanda_habitacional'] ?? '');
    $demanda_laboral = trim($_POST['demanda_laboral'] ?? '');
    $latitud = trim($_POST['latitud'] ?? '');
    $longitud = trim($_POST['longitud'] ?? '');
    $intendente = repararTextoLocalidad(trim($_POST['intendente'] ?? ''));
    $documento = trim($_POST['documento'] ?? '');
    $partido = trim($_POST['partido'] ?? '');
    $cantidad_concejales = (int)($_POST['cantidad_concejales'] ?? 0);
    $zona_am = trim($_POST['zona_am'] ?? '');
    $departamento_id = (int)($_POST['departamento_id'] ?? 0);
    $region_am = trim($_POST['region_am'] ?? '');

    if ($localidad === '') {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'La localidad es obligatoria.'];
        header('Location: localidades.php');
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $db->prepare("
                UPDATE localidades
                SET id_loc_gob_c = ?, AgeRet = ?, id_loc_padron = ?, localidad = ?, cantidad_habitantes = ?,
                    demanda_habitacional = ?, demanda_laboral = ?, latitud = ?, longitud = ?, intendente = ?,
                    documento = ?, partido = ?, cantidad_concejales = ?, zona_am = ?, departamento_id = ?, region_am = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $id_loc_gob_c !== '' ? $id_loc_gob_c : null,
                $AgeRet !== '' ? $AgeRet : null,
                $id_loc_padron !== '' ? $id_loc_padron : null,
                $localidad,
                $cantidad_habitantes ?: null,
                $demanda_habitacional !== '' ? $demanda_habitacional : null,
                $demanda_laboral !== '' ? $demanda_laboral : null,
                $latitud !== '' ? $latitud : null,
                $longitud !== '' ? $longitud : null,
                $intendente !== '' ? $intendente : null,
                $documento !== '' ? $documento : null,
                $partido !== '' ? $partido : null,
                $cantidad_concejales ?: null,
                $zona_am !== '' ? $zona_am : null,
                $departamento_id ?: null,
                $region_am !== '' ? $region_am : null,
                $id
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Localidad actualizada correctamente.'];
        } else {
            $stmt = $db->prepare("
                INSERT INTO localidades (
                    id_loc_gob_c, AgeRet, id_loc_padron, localidad, cantidad_habitantes,
                    demanda_habitacional, demanda_laboral, latitud, longitud, intendente,
                    documento, partido, cantidad_concejales, zona_am, departamento_id, region_am
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $id_loc_gob_c !== '' ? $id_loc_gob_c : null,
                $AgeRet !== '' ? $AgeRet : null,
                $id_loc_padron !== '' ? $id_loc_padron : null,
                $localidad,
                $cantidad_habitantes ?: null,
                $demanda_habitacional !== '' ? $demanda_habitacional : null,
                $demanda_laboral !== '' ? $demanda_laboral : null,
                $latitud !== '' ? $latitud : null,
                $longitud !== '' ? $longitud : null,
                $intendente !== '' ? $intendente : null,
                $documento !== '' ? $documento : null,
                $partido !== '' ? $partido : null,
                $cantidad_concejales ?: null,
                $zona_am !== '' ? $zona_am : null,
                $departamento_id ?: null,
                $region_am !== '' ? $region_am : null
            ]);
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Localidad creada correctamente.'];
        }
        header('Location: localidades.php');
    } catch (Throwable $e) {
        $_SESSION['flash'] = ['type' => 'error', 'msg' => 'No se pudo guardar la localidad: ' . $e->getMessage()];
        header('Location: localidades.php' . ($id > 0 ? '?edit=' . $id : '?nuevo=1'));
    }
    exit;
}

$q = trim($_GET['q'] ?? '');
$partido_filter = trim($_GET['partido'] ?? '');
$region_filter = trim($_GET['region_am'] ?? '');
$tipo_filter = (string) ($_GET['tipo_localidad'] ?? '');

$where = ['1=1'];
$params = [];

if ($q !== '') {
    $where[] = '(l.localidad LIKE ? OR l.intendente LIKE ? OR l.documento LIKE ?)';
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
}
if ($partido_filter !== '') {
    $where[] = 'l.partido = ?';
    $params[] = $partido_filter;
}
if ($region_filter !== '') {
    $where[] = 'l.region_am = ?';
    $params[] = $region_filter;
}
if ($tipo_filter !== '' && ctype_digit($tipo_filter)) {
    $where[] = 'l.tipo_localidad = ?';
    $params[] = (int) $tipo_filter;
}

$sql = "
    SELECT l.*, {$deptNameExprWithAlias} AS departamento_nombre
    FROM localidades l
    LEFT JOIN departamentos d ON d.id = l.departamento_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY l.localidad
    LIMIT 500
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$localidades = $stmt->fetchAll();

$tiposLocalidad = [
    1 => 'Otro',
    2 => 'Comisión de Fomento',
    3 => 'Localidad',
];

$partidos = $db->query("SELECT DISTINCT partido FROM localidades WHERE partido IS NOT NULL AND partido <> '' ORDER BY partido")->fetchAll();
$regiones = $db->query("SELECT DISTINCT region_am FROM localidades WHERE region_am IS NOT NULL AND region_am <> '' ORDER BY region_am")->fetchAll();
$departamentos = $db->query("SELECT id, {$deptNameExpr} AS nombre FROM departamentos ORDER BY nombre")->fetchAll();

$edit_localidad = null;
if (isset($_GET['edit']) && $_GET['edit'] !== '') {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $stmtEdit = $db->prepare("SELECT * FROM localidades WHERE id = ? LIMIT 1");
        $stmtEdit->execute([$editId]);
        $edit_localidad = $stmtEdit->fetch() ?: null;
    }
}

$urlExportLocalidades = 'modulos/exportar.php?' . http_build_query(array_filter([
    'tipo' => 'localidades',
    'q' => $q !== '' ? $q : null,
    'partido' => $partido_filter !== '' ? $partido_filter : null,
    'region_am' => $region_filter !== '' ? $region_filter : null,
    'tipo_localidad' => $tipo_filter !== '' ? $tipo_filter : null,
], static fn($v) => $v !== null));

include 'includes/header.php';
?>

<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Localidades</h1>
            <p class="text-gray-500 text-sm"><?= count($localidades) ?> resultado(s)</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= h($urlExportLocalidades) ?>"
               class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </a>
            <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'editor'])): ?>
            <form method="POST" onsubmit="return confirm('¿Sincronizar AgeRet desde CODIGOS_AGENTES_RETENCION.xlsx?')">
                <input type="hidden" name="sincronizar_ageret" value="1">
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-purple-200 text-purple-700 hover:bg-purple-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-sync-alt"></i> Sincronizar AgeRet
                </button>
            </form>
            <a href="localidades.php?nuevo=1"
               class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus"></i> Nueva localidad
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                <input type="text" name="q" value="<?= h($q) ?>" placeholder="Localidad, intendente o documento..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Partido</label>
                <select name="partido" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <?php foreach ($partidos as $p): ?>
                    <option value="<?= h($p['partido']) ?>" <?= $partido_filter === $p['partido'] ? 'selected' : '' ?>>
                        <?= h($p['partido']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Región AM</label>
                <select name="region_am" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas</option>
                    <?php foreach ($regiones as $r): ?>
                    <option value="<?= h($r['region_am']) ?>" <?= $region_filter === $r['region_am'] ? 'selected' : '' ?>>
                        <?= h($r['region_am']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                <select name="tipo_localidad" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <?php foreach ($tiposLocalidad as $tipoId => $tipoLabel): ?>
                    <option value="<?= (int) $tipoId ?>" <?= $tipo_filter === (string) $tipoId ? 'selected' : '' ?>>
                        <?= h($tipoLabel) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-800">
                <i class="fas fa-search mr-1"></i> Filtrar
            </button>
            <a href="localidades.php" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">Limpiar</a>
        </form>
    </div>

    <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'editor']) && (!empty($_GET['nuevo']) || $edit_localidad)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-800 mb-4"><?= $edit_localidad ? 'Editar localidad' : 'Nueva localidad' ?></h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="guardar_localidad" value="1">
            <input type="hidden" name="id" value="<?= (int)($edit_localidad['id'] ?? 0) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID Gob C</label>
                <input type="text" name="id_loc_gob_c" value="<?= h($edit_localidad['id_loc_gob_c'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">AgeRet (código agente)</label>
                <input type="text" name="AgeRet" value="<?= h($edit_localidad['AgeRet'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ID Padrón</label>
                <input type="text" name="id_loc_padron" value="<?= h($edit_localidad['id_loc_padron'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Localidad <span class="text-red-500">*</span></label>
                <input type="text" name="localidad" required value="<?= h(nombreLocalidad($edit_localidad['localidad'] ?? '')) ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad habitantes</label>
                <input type="number" name="cantidad_habitantes" min="0" value="<?= h($edit_localidad['cantidad_habitantes'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad concejales</label>
                <input type="number" name="cantidad_concejales" min="0" value="<?= h($edit_localidad['cantidad_concejales'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Demanda habitacional</label>
                <input type="text" name="demanda_habitacional" value="<?= h($edit_localidad['demanda_habitacional'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Demanda laboral</label>
                <input type="text" name="demanda_laboral" value="<?= h($edit_localidad['demanda_laboral'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Intendente</label>
                <input type="text" name="intendente" value="<?= h(textoLegible($edit_localidad['intendente'] ?? '')) ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                <input type="text" name="documento" value="<?= h($edit_localidad['documento'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="DNI del intendente">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Partido</label>
                <input type="text" name="partido" value="<?= h($edit_localidad['partido'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zona AM</label>
                <input type="text" name="zona_am" value="<?= h($edit_localidad['zona_am'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Región AM</label>
                <input type="text" name="region_am" value="<?= h($edit_localidad['region_am'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departamento ID</label>
                <select name="departamento_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Seleccioná departamento —</option>
                    <?php foreach ($departamentos as $d): ?>
                    <option value="<?= (int)$d['id'] ?>" <?= (int)($edit_localidad['departamento_id'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>>
                        <?= h($d['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Latitud</label>
                <input type="text" name="latitud" value="<?= h($edit_localidad['latitud'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Longitud</label>
                <input type="text" name="longitud" value="<?= h($edit_localidad['longitud'] ?? '') ?>"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-3 flex gap-3 pt-2">
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                    <i class="fas fa-save mr-2"></i> Guardar
                </button>
                <a href="localidades.php" class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Foto</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Localidad</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Intendente</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Documento</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Partido</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Habitantes</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Departamento</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Región AM</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider text-center">Tipo</th>
                        <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'editor'])): ?>
                        <th class="px-4 py-3"></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                <?php if (empty($localidades)): ?>
                    <tr>
                        <td colspan="<?= in_array($_SESSION['rol'] ?? '', ['admin', 'editor']) ? '10' : '9' ?>" class="px-4 py-8 text-center text-gray-400">
                            No hay localidades para mostrar.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($localidades as $l): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <img src="https://lapampaperonista.com.ar/elecciones/fotos_intendentes/<?= (int)$l['id'] ?>.png"
                                 alt="Foto de <?= ($l['intendente'] ?? '') !== '' ? h(textoLegible($l['intendente'])) : h(nombreLocalidad($l['localidad'])) ?>"
                                 class="w-12 h-12 rounded-full object-cover border border-gray-200 bg-gray-100"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span class="w-12 h-12 rounded-full border border-gray-200 bg-gray-100 text-gray-300 items-center justify-center hidden">
                                <i class="fas fa-user"></i>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900"><?= h(nombreLocalidad($l['localidad'])) ?></p>
                        </td>
                        <td class="px-4 py-3 text-gray-700"><?= ($l['intendente'] ?? '') !== '' ? h(textoLegible($l['intendente'])) : '—' ?></td>
                        <td class="px-4 py-3 text-gray-700 font-mono text-xs"><?= ($l['documento'] ?? '') !== '' ? h($l['documento']) : '—' ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= ($l['partido'] ?? '') !== '' ? h(textoLegible($l['partido'])) : '—' ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= $l['cantidad_habitantes'] !== null ? number_format((int)$l['cantidad_habitantes'], 0, ',', '.') : '—' ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= h($l['departamento_nombre'] ?? ($l['departamento_id'] ?? '—')) ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= h($l['region_am'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-center text-gray-700">
                            <?= h($tiposLocalidad[(int)($l['tipo_localidad'] ?? 0)] ?? '—') ?>
                        </td>
                        <?php if (in_array($_SESSION['rol'] ?? '', ['admin', 'editor'])): ?>
                        <td class="px-4 py-3 text-right">
                            <a href="localidades.php?edit=<?= (int)$l['id'] ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-colors shadow-sm"
                               title="Editar localidad">
                                <i class="fas fa-pen-to-square text-xs"></i>
                                Editar
                            </a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
