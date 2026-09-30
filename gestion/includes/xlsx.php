<?php
/**
 * Lectura básica de la primera hoja de un archivo .xlsx (sin dependencias externas).
 */

function readXlsxFirstSheet(string $path): array
{
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('No se pudo abrir el .xlsx como ZIP');
    }

    $shared = [];
    if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
        $sx = $zip->getFromIndex($idx);
        $xml = @simplexml_load_string($sx);
        if ($xml && isset($xml->si)) {
            foreach ($xml->si as $si) {
                $shared[] = xlsxExtractSiText($si);
            }
        }
    }

    $sheetPath = 'xl/worksheets/sheet1.xml';
    if ($zip->locateName($sheetPath) === false) {
        $rels = @simplexml_load_string($zip->getFromName('xl/workbook.xml'));
        if ($rels && isset($rels->sheets->sheet[0]['r:id'])) {
            $rid = (string) $rels->sheets->sheet[0]['r:id'];
            $wrels = @simplexml_load_string($zip->getFromName('xl/_rels/workbook.xml.rels'));
            if ($wrels) {
                foreach ($wrels->Relationship as $rel) {
                    if ((string) $rel['Id'] === $rid) {
                        $sheetPath = 'xl/' . ltrim((string) $rel['Target'], '/');
                        break;
                    }
                }
            }
        }
    }

    $sheetXml = $zip->getFromName($sheetPath);
    $zip->close();
    if ($sheetXml === false) {
        throw new RuntimeException('No se encontró la primera hoja en el xlsx');
    }

    $sheet = simplexml_load_string($sheetXml);
    if (!$sheet || !isset($sheet->sheetData)) {
        throw new RuntimeException('Hoja Excel inválida');
    }

    $sheet->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $rowNodes = $sheet->xpath('//m:sheetData/m:row') ?: [];

    $grid = [];
    foreach ($rowNodes as $row) {
        $cells = [];
        foreach ($row->c as $c) {
            $ref = (string) $c['r'];
            if (!preg_match('/^([A-Z]+)/', $ref, $m)) {
                continue;
            }
            $colIndex = xlsxColumnLettersToIndex($m[1]);
            $cells[$colIndex] = xlsxCellRawValue($c, $shared);
        }
        if ($cells === []) {
            continue;
        }
        ksort($cells);
        $maxCol = max(array_keys($cells));
        $line = [];
        for ($i = 0; $i <= $maxCol; $i++) {
            $line[$i] = $cells[$i] ?? '';
        }
        $grid[] = $line;
    }

    return $grid;
}

function xlsxExtractSiText(SimpleXMLElement $si): string
{
    if (isset($si->t)) {
        return (string) $si->t;
    }
    $parts = [];
    if (isset($si->r)) {
        foreach ($si->r as $r) {
            if (isset($r->t)) {
                $parts[] = (string) $r->t;
            }
        }
    }
    return implode('', $parts);
}

function xlsxColumnLettersToIndex(string $letters): int
{
    $n = 0;
    $len = strlen($letters);
    for ($i = 0; $i < $len; $i++) {
        $n = $n * 26 + (ord($letters[$i]) - 64);
    }
    return $n - 1;
}

function xlsxCellRawValue(SimpleXMLElement $c, array $shared): string
{
    $t = (string) $c['t'];
    $v = isset($c->v) ? (string) $c->v : '';
    if ($t === 's' && $v !== '') {
        $i = (int) $v;
        return $shared[$i] ?? '';
    }
    if ($t === 'inlineStr' && isset($c->is->t)) {
        return (string) $c->is->t;
    }
    return $v;
}
