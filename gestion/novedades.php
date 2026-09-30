<?php
require_once 'includes/config.php';
requireLogin();

$db = getDB();
$page_title = 'Novedades';
$breadcrumb = [['label' => 'Novedades / Bitácora']];

// Guardar novedad
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_novedad'])) {
    requireRol('admin','editor');

    $tipo     = $_POST['tipo'] ?? 'proyecto';
    $ref_id   = (int)($_POST['referencia_id'] ?? 0);
    $titulo   = trim($_POST['titulo'] ?? '');
    $desc     = trim($_POST['descripcion'] ?? '');
    $func_id  = (int)($_POST['funcionario_id'] ?? 0) ?: ($_SESSION['usuario_id'] ? null : null);

    if ($titulo && $desc && $ref_id) {
        $db->prepare("INSERT INTO novedades (tipo, referencia_id, titulo, descripcion, funcionario_id) VALUES (?,?,?,?,?)")
           ->execute([$tipo, $ref_id, $titulo, $desc, $func_id ?: null]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Novedad registrada correctamente.'];
    }
    header('Location: novedades.php');
    exit;
}

$modo_form = !empty($_GET['nuevo']);

// Para selects del form
$proyectos_sel = $db->query("SELECT id, nombre FROM proyectos ORDER BY nombre")->fetchAll();
$acciones_sel  = $db->query("SELECT id, codigo, descripcion FROM acciones ORDER BY codigo LIMIT 100")->fetchAll();
$funcionarios  = $db->query("SELECT f.id, f.nombre_apellido, o.sigla FROM funcionarios f JOIN organismos o ON o.id=f.organismo_id WHERE f.activo=1 ORDER BY f.nombre_apellido")->fetchAll();

// Listado de novedades
$novedades = $db->query("
    SELECT n.*, f.nombre_apellido as funcionario_nombre, f.cargo as funcionario_cargo,
           o.sigla as organismo_sigla
    FROM novedades n
    LEFT JOIN funcionarios f ON f.id = n.funcionario_id
    LEFT JOIN organismos o ON o.id = f.organismo_id
    ORDER BY n.created_at DESC
    LIMIT 100
")->fetchAll();

include 'includes/header.php';
?>

<div class="space-y-5">
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Novedades / Bitácora</h1>
        <p class="text-gray-500 text-sm">Registro cronológico de avances y eventos</p>
    </div>
    <?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
    <button onclick="document.getElementById('form-novedad').classList.toggle('hidden')"
            class="inline-flex items-center gap-2 bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        <i class="fas fa-plus"></i> Nueva novedad
    </button>
    <?php endif; ?>
</div>

<!-- Formulario nueva novedad -->
<?php if (in_array($_SESSION['rol']??'',['admin','editor'])): ?>
<div id="form-novedad" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 <?= $modo_form ? '' : 'hidden' ?>">
    <h2 class="font-bold text-gray-800 mb-5">Registrar novedad</h2>
    <form method="POST" class="space-y-4">
        <input type="hidden" name="guardar_novedad" value="1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de referencia</label>
                <select name="tipo" id="sel_tipo" onchange="updateRef(this.value)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="proyecto" <?= ($_GET['tipo']??'proyecto')==='proyecto'?'selected':'' ?>>Proyecto</option>
                    <option value="accion" <?= ($_GET['tipo']??'')==='accion'?'selected':'' ?>>Acción</option>
                    <option value="programa">Programa</option>
                    <option value="estrategia">Estrategia</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Referencia</label>
                <select name="referencia_id" id="sel_ref"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Seleccioná —</option>
                    <?php $pre_ref = (int)($_GET['ref'] ?? 0); ?>
                    <?php foreach($proyectos_sel as $p): ?>
                    <option value="<?= $p['id'] ?>" class="opt-proyecto" <?= $pre_ref==$p['id']?'selected':'' ?>>
                        <?= h(mb_substr($p['nombre'],0,60)) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                <input type="text" name="titulo" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="Ej: Reunión de avance, Informe mensual, Obra iniciada...">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción <span class="text-red-500">*</span></label>
                <textarea name="descripcion" rows="4" required
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                          placeholder="Describí el avance, evento o novedad registrada..."></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Funcionario que reporta</label>
                <select name="funcionario_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Opcional —</option>
                    <?php foreach($funcionarios as $fn): ?>
                    <option value="<?= $fn['id'] ?>"><?= h($fn['nombre_apellido']) ?> (<?= h($fn['sigla']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                <i class="fas fa-save mr-2"></i> Guardar novedad
            </button>
            <button type="button" onclick="document.getElementById('form-novedad').classList.add('hidden')"
                    class="border border-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-50">
                Cancelar
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Timeline de novedades -->
<div class="space-y-3">
<?php if (empty($novedades)): ?>
<div class="bg-white rounded-xl py-16 text-center shadow-sm border border-gray-100">
    <i class="fas fa-newspaper text-4xl text-gray-200 mb-3 block"></i>
    <p class="text-gray-400">No hay novedades registradas aún.</p>
</div>
<?php else: ?>
<?php
$tipos_color = [
    'proyecto'   => 'blue',
    'accion'     => 'purple',
    'componente' => 'indigo',
    'programa'   => 'teal',
    'estrategia' => 'gray',
];
foreach($novedades as $nv):
$color = $tipos_color[$nv['tipo']] ?? 'blue';
?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex gap-4">
    <div class="flex-shrink-0">
        <div class="w-9 h-9 rounded-full bg-<?= $color ?>-100 flex items-center justify-center">
            <i class="fas fa-<?= $nv['tipo']==='proyecto'?'project-diagram':($nv['tipo']==='accion'?'tasks':'layer-group') ?> text-<?= $color ?>-600 text-sm"></i>
        </div>
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-semibold text-gray-900 text-sm"><?= h($nv['titulo']) ?></p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs px-2 py-0.5 bg-<?= $color ?>-50 text-<?= $color ?>-700 rounded-full font-medium capitalize"><?= h($nv['tipo']) ?> #<?= $nv['referencia_id'] ?></span>
                    <?php if ($nv['funcionario_nombre']): ?>
                    <span class="text-xs text-gray-400">
                        <i class="fas fa-user mr-1"></i><?= h($nv['funcionario_nombre']) ?>
                        <?php if ($nv['organismo_sigla']): ?>(<?= h($nv['organismo_sigla']) ?>)<?php endif; ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <span class="text-xs text-gray-400 flex-shrink-0"><?= date('d/m/Y H:i', strtotime($nv['created_at'])) ?></span>
        </div>
        <p class="text-gray-600 text-sm mt-2 leading-relaxed"><?= nl2br(h($nv['descripcion'])) ?></p>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>
</div>

<?php include 'includes/footer.php'; ?>
