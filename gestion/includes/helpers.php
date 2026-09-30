<?php
/**
 * includes/helpers.php
 * Funciones de ayuda reutilizables para los módulos del sistema
 */

/**
 * Genera un <select> de organismos con opción vacía
 */
function selectOrganismos(
    string $name,
    int    $selected    = 0,
    string $placeholder = '— Seleccioná organismo —',
    string $class       = 'form-control'
): string {
    $db  = getDB();
    $org = $db->query("SELECT id, sigla, nombre_completo FROM organismos ORDER BY sigla")->fetchAll();

    $html  = "<select name=\"{$name}\" id=\"sel-{$name}\" class=\"{$class}\">";
    $html .= "<option value=\"\">{$placeholder}</option>";
    foreach ($org as $o) {
        $sel   = $selected == $o['id'] ? ' selected' : '';
        $html .= "<option value=\"{$o['id']}\"{$sel}>{$o['sigla']} — {$o['nombre_completo']}</option>";
    }
    $html .= '</select>';
    return $html;
}

/**
 * Genera un <select> de funcionarios, opcionalmente filtrado por organismo
 */
function selectFuncionarios(
    string $name,
    int    $selected   = 0,
    int    $organismo  = 0,
    string $class      = 'form-control'
): string {
    $db = getDB();

    if ($organismo) {
        $stmt = $db->prepare("SELECT f.id, f.nombre_apellido, f.cargo, o.sigla, o.id as org_id
                              FROM funcionarios f JOIN organismos o ON o.id=f.organismo_id
                              WHERE f.organismo_id = ? AND f.activo=1 ORDER BY f.nombre_apellido");
        $stmt->execute([$organismo]);
    } else {
        $stmt = $db->query("SELECT f.id, f.nombre_apellido, f.cargo, o.sigla, o.id as org_id
                            FROM funcionarios f JOIN organismos o ON o.id=f.organismo_id
                            WHERE f.activo=1 ORDER BY f.nombre_apellido LIMIT 500");
    }
    $funcs = $stmt->fetchAll();

    $html  = "<select name=\"{$name}\" id=\"sel-funcionario\" class=\"{$class}\">";
    $html .= '<option value="">— Seleccioná funcionario —</option>';
    foreach ($funcs as $f) {
        $sel   = $selected == $f['id'] ? ' selected' : '';
        $label = h($f['nombre_apellido']) . ' — ' . h($f['cargo']) . ' (' . h($f['sigla']) . ')';
        $html .= "<option value=\"{$f['id']}\" data-organismo=\"{$f['org_id']}\"{$sel}>{$label}</option>";
    }
    $html .= '</select>';
    return $html;
}

/**
 * Genera el <select> de acciones para vincular proyectos
 */
function selectAcciones(
    string $name,
    int    $selected = 0,
    string $class    = 'form-control'
): string {
    $db = getDB();
    $rows = $db->query("
        SELECT a.id, a.codigo, a.descripcion,
               comp.codigo as comp_c,
               prog.codigo as prog_c,
               e.codigo    as est_c
        FROM acciones a
        JOIN componentes comp ON comp.id = a.componente_id
        JOIN programas   prog ON prog.id = comp.programa_id
        JOIN estrategias e    ON e.id    = prog.estrategia_id
        ORDER BY a.codigo
    ")->fetchAll();

    $html  = "<select name=\"{$name}\" class=\"{$class}\">";
    $html .= '<option value="">— Seleccioná acción —</option>';

    $last_prog = '';
    foreach ($rows as $a) {
        if ($a['prog_c'] !== $last_prog) {
            if ($last_prog !== '') $html .= '</optgroup>';
            $html    .= "<optgroup label=\"{$a['est_c']} › {$a['prog_c']}\">";
            $last_prog = $a['prog_c'];
        }
        $sel   = $selected == $a['id'] ? ' selected' : '';
        $label = h($a['codigo']) . ' — ' . h(mb_substr($a['descripcion'], 0, 70));
        $html .= "<option value=\"{$a['id']}\"{$sel}>{$label}</option>";
    }
    if ($last_prog !== '') $html .= '</optgroup>';
    $html .= '</select>';
    return $html;
}

/**
 * Renderiza la barra de progreso HTML
 */
function progressBar(int $pct, string $color = 'blue', string $height = 'h-2'): string {
    $pct = max(0, min(100, $pct));
    $cls = match($color) {
        'green'  => 'bg-green-500',
        'red'    => 'bg-red-500',
        'yellow' => 'bg-yellow-400',
        'purple' => 'bg-purple-500',
        default  => 'bg-blue-500',
    };
    return "<div class=\"w-full bg-gray-100 rounded-full {$height}\">
              <div class=\"{$cls} {$height} rounded-full\" style=\"width:{$pct}%\"></div>
            </div>";
}

/**
 * Convierte número de segundos a texto legible
 */
function diasA(string $fecha): string {
    if (!$fecha) return '—';
    $diff = (int)((strtotime($fecha) - time()) / 86400);
    if ($diff < 0)  return "<span class=\"text-red-600 text-xs font-medium\">Vencido hace " . abs($diff) . " días</span>";
    if ($diff === 0) return "<span class=\"text-orange-500 text-xs font-medium\">Vence hoy</span>";
    if ($diff <= 7)  return "<span class=\"text-yellow-600 text-xs font-medium\">En {$diff} día(s)</span>";
    return "<span class=\"text-gray-500 text-xs\">En {$diff} días</span>";
}

/**
 * Devuelve el ícono FontAwesome para cada tipo de novedad
 */
function iconoNovedad(string $tipo): string {
    return match($tipo) {
        'proyecto'   => 'fa-project-diagram text-blue-500',
        'accion'     => 'fa-tasks text-purple-500',
        'componente' => 'fa-puzzle-piece text-indigo-500',
        'programa'   => 'fa-layer-group text-teal-500',
        'estrategia' => 'fa-chess text-gray-500',
        default      => 'fa-info-circle text-gray-400',
    };
}

/**
 * Redirigir con mensaje flash
 */
function redirectFlash(string $url, string $msg, string $tipo = 'success'): never {
    $_SESSION['flash'] = ['type' => $tipo, 'msg' => $msg];
    header('Location: ' . $url);
    exit;
}

/**
 * Paginar un array de resultados
 */
function paginar(array $items, int $pagina, int $porPagina = 25): array {
    $total   = count($items);
    $paginas = (int)ceil($total / $porPagina);
    $pagina  = max(1, min($pagina, max($paginas, 1)));
    $offset  = ($pagina - 1) * $porPagina;

    return [
        'items'    => array_slice($items, $offset, $porPagina),
        'pagina'   => $pagina,
        'paginas'  => $paginas,
        'total'    => $total,
        'desde'    => $total > 0 ? $offset + 1 : 0,
        'hasta'    => min($offset + $porPagina, $total),
    ];
}

/**
 * Renderiza controles de paginación
 */
function paginacion(array $info, string $baseUrl): string {
    if ($info['paginas'] <= 1) return '';

    $html = '<div class="flex items-center gap-1">';
    for ($i = 1; $i <= $info['paginas']; $i++) {
        $active = $i === $info['pagina'] ? 'bg-blue-700 text-white' : 'bg-white text-gray-600 border border-gray-300 hover:bg-gray-50';
        $sep    = str_contains($baseUrl, '?') ? '&' : '?';
        $html  .= "<a href=\"{$baseUrl}{$sep}pagina={$i}\" class=\"px-3 py-1.5 rounded text-xs font-medium {$active}\">{$i}</a>";
    }
    $html .= '</div>';
    return $html;
}
