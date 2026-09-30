<?php
/**
 * Copia el bloque completo `funcionarios` desde gestion_gubernamental.sql hacia funcionarios.sql.
 * Uso: php tools/sync_funcionarios_sql.php
 */
declare(strict_types=1);

$root = dirname(__DIR__);
$src = $root . '/gestion_gubernamental.sql';
$dst = $root . '/funcionarios.sql';

if (!is_readable($src)) {
    fwrite(STDERR, "No se encuentra $src\n");
    exit(1);
}

$lines = file($src, FILE_IGNORE_NEW_LINES);
if ($lines === false) {
    exit(1);
}

$start = $end = null;
foreach ($lines as $i => $line) {
    if (str_contains($line, 'Estructura de tabla para la tabla `funcionarios`')) {
        $start = $i;
    }
    if ($start !== null && $i > $start && str_contains($line, 'Estructura de tabla para la tabla `hitos`')) {
        $end = $i;
        break;
    }
}
if ($start === null || $end === null || $end <= $start) {
    fwrite(STDERR, "No se localizó el bloque funcionarios..hitos en el dump.\n");
    exit(1);
}

// Quitar líneas vacías y el separador `-- --------------------------------------------------------` previo a hitos
$chunk = array_slice($lines, $start, $end - $start);
while ($chunk !== [] && trim((string) end($chunk)) === '') {
    array_pop($chunk);
}
if ($chunk !== [] && str_contains((string) end($chunk), '--------------------------------------------------------')) {
    array_pop($chunk);
}
while ($chunk !== [] && trim((string) end($chunk)) === '') {
    array_pop($chunk);
}

$core = implode("\n", $chunk);

$header = <<<'HDR'
-- phpMyAdmin SQL Dump (fragmento: solo tabla `funcionarios`)
-- Sincronizado desde gestion_gubernamental.sql — estructura con `organismo_id` (FK a organismos.id).
-- Importar con `organismos` ya cargados en la misma base.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `funcionarios`;

HDR;

$footer = <<<'FTR'

ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_funcionarios_organismo` (`organismo_id`);

ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6145;

ALTER TABLE `funcionarios`
  ADD CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`organismo_id`) REFERENCES `organismos` (`id`);

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
FTR;

$out = $header . $core . $footer;
file_put_contents($dst, $out);
echo "OK: funcionarios.sql sincronizado (" . strlen($out) . " bytes)\n";
