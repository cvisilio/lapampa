<?php

require_once __DIR__ . '/xlsx.php';
require_once __DIR__ . '/SimpleXLS.php';

use Shuchkin\SimpleXLS;

function detectSpreadsheetFormat(string $path, ?string $originalName = null): string
{
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (in_array($ext, ['xls', 'xlsx'], true)) {
        return $ext;
    }

    if ($originalName !== null && $originalName !== '') {
        $extOriginal = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (in_array($extOriginal, ['xls', 'xlsx'], true)) {
            return $extOriginal;
        }
    }

    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        throw new RuntimeException('No se pudo abrir el archivo.');
    }
    $header = fread($handle, 8);
    fclose($handle);

    if ($header === false || strlen($header) < 4) {
        throw new RuntimeException('Archivo vacío o ilegible.');
    }

    if ($header === "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
        return 'xls';
    }

    if (str_starts_with($header, 'PK')) {
        return 'xlsx';
    }

    throw new RuntimeException('Formato no soportado. Use .xls o .xlsx.');
}

function readSpreadsheetFirstSheet(string $path, ?string $originalName = null): array
{
    $format = detectSpreadsheetFormat($path, $originalName);

    if ($format === 'xlsx') {
        return readXlsxFirstSheet($path);
    }

    $xls = SimpleXLS::parse($path);
    if (!$xls) {
        throw new RuntimeException('No se pudo leer el archivo .xls: ' . SimpleXLS::parseError());
    }

    return $xls->rows(0) ?: [];
}
