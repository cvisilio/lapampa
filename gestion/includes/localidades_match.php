<?php
/**
 * Coincidencia de nombres de localidad y sincronización de AgeRet.
 */

require_once __DIR__ . '/xlsx.php';
require_once __DIR__ . '/text_encoding.php';

function convertirNumerosPalabrasLocalidad(string $texto): string
{
    $map = [
        'VEINTICINCO' => '25',
        'VEINTICUATRO' => '24',
        'VEINTITRES' => '23',
        'VEINTIDOS' => '22',
        'VEINTIUNO' => '21',
        'VEINTE' => '20',
        'DIECINUEVE' => '19',
        'DIECIOCHO' => '18',
        'DIECISIETE' => '17',
        'DIECISEIS' => '16',
        'QUINCE' => '15',
        'CATORCE' => '14',
        'TRECE' => '13',
        'DOCE' => '12',
        'ONCE' => '11',
        'DIEZ' => '10',
        'NUEVE' => '9',
        'OCHO' => '8',
        'SIETE' => '7',
        'SEIS' => '6',
        'CINCO' => '5',
        'CUATRO' => '4',
        'TRES' => '3',
        'DOS' => '2',
        'UNO' => '1',
    ];

    uksort($map, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));

    foreach ($map as $palabra => $numero) {
        $texto = preg_replace('/\b' . preg_quote($palabra, '/') . '\b/u', $numero, $texto) ?? $texto;
    }

    return $texto;
}

function aliasesNombreLocalidadExcel(): array
{
    return [
        'TOMAS M ANCHORENA' => 'T M DE ANCHORENA',
        'TOMAS MANUEL DE ANCHORENA' => 'T M DE ANCHORENA',
        // En retenciones/distribución el Excel trae el nombre largo; en BD es "General Campos" (id 37)
        'GENERAL MANUEL J CAMPOS' => 'GENERAL CAMPOS',
        'GENERAL MANUEL JOSE CAMPOS' => 'GENERAL CAMPOS',
    ];
}

function aplicarAliasNombreLocalidad(string $needle): string
{
    $aliases = aliasesNombreLocalidadExcel();
    return $aliases[$needle] ?? $needle;
}

function esMatchFuzzyLocalidadInvalido(string $needle, string $candidato, float $score): bool
{
    if ($score >= 90) {
        return false;
    }

    $palabrasNeedle = preg_split('/\s+/', $needle, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $palabrasCand = preg_split('/\s+/', $candidato, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if (count($palabrasNeedle) <= count($palabrasCand)) {
        return false;
    }

    $todasEnNeedle = true;
    foreach ($palabrasCand as $palabra) {
        if (!in_array($palabra, $palabrasNeedle, true)) {
            $todasEnNeedle = false;
            break;
        }
    }

    return $todasEnNeedle;
}

function normalizarNombreLocalidad(string $texto): string
{
    $texto = repararTextoLocalidad($texto);
    $texto = mb_strtoupper(trim($texto), 'UTF-8');
    $texto = str_replace(
        ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
        ['A', 'E', 'I', 'O', 'U', 'U', 'N'],
        $texto
    );
    $texto = str_replace(['.', ',', '-', "'"], ' ', $texto);
    $texto = preg_replace('/\s+/', ' ', $texto) ?? $texto;
    $texto = convertirNumerosPalabrasLocalidad($texto);

    $reemplazos = [
        'GRAL ' => 'GENERAL ',
        'GOB ' => 'GOBERNADOR ',
        'INT ' => 'INTENDENTE ',
        'COL ' => 'COLONIA ',
        'COR ' => 'CORONEL ',
        'EDO ' => 'EDUARDO ',
        'EMB ' => 'EMBAJADOR ',
        'ING ' => 'INGENIERO ',
        'ALG ' => 'ALGARROBO ',
        'T M DE ' => 'T M DE ',
        'T. M. DE ' => 'T M DE ',
        ' STA ' => ' SANTA ',
        ' STA.' => ' SANTA',
    ];
    foreach ($reemplazos as $buscar => $poner) {
        $texto = str_replace($buscar, $poner, $texto);
    }

    $texto = trim(preg_replace('/\s+/', ' ', $texto) ?? $texto);

    return aplicarAliasNombreLocalidad($texto);
}

function buscarLocalidadPorNombre(PDO $db, string $nombreExcel, array &$cache, float $minScore = 75): ?array
{
    $needle = normalizarNombreLocalidad($nombreExcel);
    if ($needle === '') {
        return null;
    }

    if ($cache === []) {
        $cache = $db->query('SELECT id, localidad, AgeRet FROM localidades ORDER BY localidad')->fetchAll();
    }

    foreach ($cache as $loc) {
        $candidato = normalizarNombreLocalidad((string) $loc['localidad']);
        if ($candidato === $needle) {
            return ['id' => (int) $loc['id'], 'nombre' => $loc['localidad'], 'score' => 100.0];
        }
    }

    foreach ($cache as $loc) {
        $candidato = normalizarNombreLocalidad((string) $loc['localidad']);
        if (str_contains($candidato, $needle) || str_contains($needle, $candidato)) {
            $lenNeedle = mb_strlen($needle);
            $lenCand = mb_strlen($candidato);
            if ($lenNeedle >= 4 && $lenCand >= 4) {
                return ['id' => (int) $loc['id'], 'nombre' => $loc['localidad'], 'score' => 95.0];
            }
        }
    }

    $best = null;
    $bestScore = 0.0;
    foreach ($cache as $loc) {
        $candidato = normalizarNombreLocalidad((string) $loc['localidad']);
        similar_text($needle, $candidato, $score);
        if ($score > $bestScore) {
            $bestScore = $score;
            $best = $loc;
        }
    }

    if ($best && $bestScore >= $minScore) {
        $candidatoBest = normalizarNombreLocalidad((string) $best['localidad']);
        if (esMatchFuzzyLocalidadInvalido($needle, $candidatoBest, $bestScore)) {
            return null;
        }

        return [
            'id' => (int) $best['id'],
            'nombre' => $best['localidad'],
            'score' => round($bestScore, 1),
        ];
    }

    return null;
}

function detectarColumnasAgentesRetencion(array $headerRow): array
{
    $map = [];
    foreach ($headerRow as $i => $cell) {
        $k = normalizarNombreLocalidad((string) $cell);
        if ($k === 'CODIGO' || str_contains($k, 'CODIGO')) {
            $map['codigo'] = $i;
        } elseif ($k === 'LOCALIDAD' || str_contains($k, 'LOCALIDAD')) {
            $map['localidad'] = $i;
        }
    }
    return $map;
}

function sincronizarAgeRetDesdeExcel(PDO $db, string $path): array
{
    if (!is_readable($path)) {
        throw new RuntimeException('No se encuentra el archivo: ' . $path);
    }

    $rows = readXlsxFirstSheet($path);
    if (count($rows) < 2) {
        throw new RuntimeException('El Excel no contiene datos.');
    }

    $headerIndex = 0;
    $cols = detectarColumnasAgentesRetencion($rows[0]);
    if (count($cols) < 2) {
        for ($i = 0; $i < min(count($rows), 5); $i++) {
            $try = detectarColumnasAgentesRetencion($rows[$i]);
            if (count($try) >= 2) {
                $headerIndex = $i;
                $cols = $try;
                break;
            }
        }
    }

    if (count($cols) < 2) {
        throw new RuntimeException('El Excel debe tener columnas codigo y localidad.');
    }

    $dataRows = array_slice($rows, $headerIndex + 1);
    $stmtUpdate = $db->prepare('UPDATE localidades SET AgeRet = ? WHERE id = ?');

    $cache = [];
    $actualizados = 0;
    $sinCambios = 0;
    $sinCoincidencia = [];
    $coincidenciasDudosas = [];

    foreach ($dataRows as $row) {
        $codigo = trim((string) ($row[$cols['codigo']] ?? ''));
        $nombreExcel = trim((string) ($row[$cols['localidad']] ?? ''));
        if ($codigo === '' || $nombreExcel === '') {
            continue;
        }

        $match = buscarLocalidadPorNombre($db, $nombreExcel, $cache);
        if (!$match) {
            $sinCoincidencia[] = $nombreExcel;
            continue;
        }

        if ($match['score'] < 90) {
            $coincidenciasDudosas[] = "{$nombreExcel} → {$match['nombre']} ({$match['score']}%)";
        }

        $stmtUpdate->execute([$codigo, $match['id']]);
        if ($stmtUpdate->rowCount() > 0) {
            $actualizados++;
        } else {
            $sinCambios++;
        }

        foreach ($cache as &$loc) {
            if ((int) $loc['id'] === $match['id']) {
                $loc['AgeRet'] = $codigo;
                break;
            }
        }
        unset($loc);
    }

    return [
        'actualizados' => $actualizados,
        'sin_cambios' => $sinCambios,
        'sin_coincidencia' => $sinCoincidencia,
        'coincidencias_dudosas' => $coincidenciasDudosas,
    ];
}
