<?php

function repararTextoLocalidad(string $texto): string
{
    if (str_contains($texto, 'Ã') || str_contains($texto, 'â')) {
        $fixed = @iconv('UTF-8', 'ISO-8859-1//IGNORE', $texto);
        if ($fixed !== false && $fixed !== '') {
            $texto = $fixed;
        }
    }

    return $texto;
}

function nombreLocalidad(mixed $texto): string
{
    return repararTextoLocalidad((string) ($texto ?? ''));
}

function textoLegible(mixed $texto): string
{
    return repararTextoLocalidad((string) ($texto ?? ''));
}

function repararTextosEnFilasLocalidad(PDO $db): array
{
    $campos = [
        'localidad',
        'intendente',
        'partido',
        'demanda_habitacional',
        'demanda_laboral',
        'zona_am',
        'region_am',
    ];
    $stmt = $db->query('SELECT id, ' . implode(', ', $campos) . ' FROM localidades');
    $sets = implode(' = ?, ', $campos) . ' = ?';
    $update = $db->prepare("UPDATE localidades SET {$sets} WHERE id = ?");
    $actualizados = 0;

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $id = (int) $fila['id'];
        $valores = [];
        $huboCambio = false;

        foreach ($campos as $campo) {
            $original = (string) ($fila[$campo] ?? '');
            $corregido = $original === '' ? $original : repararTextoLocalidad($original);
            $valores[] = $corregido !== '' ? $corregido : null;
            if ($corregido !== $original) {
                $huboCambio = true;
            }
        }

        if ($huboCambio) {
            $valores[] = $id;
            $update->execute($valores);
            $actualizados++;
        }
    }

    return ['actualizados' => $actualizados];
}

/** @deprecated Use repararTextosEnFilasLocalidad() */
function repararNombresLocalidadesEnBd(PDO $db): array
{
    return repararTextosEnFilasLocalidad($db);
}
