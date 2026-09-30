<?php
declare(strict_types=1);
$src = __DIR__ . '/../funcionarios.sql';
$dst = __DIR__ . '/../funcionarios_import_safe.sql';
$sql = file_get_contents($src);
if ($sql === false) { fwrite(STDERR, "No se pudo leer $src\n"); exit(1);} 
$sql = preg_replace(
    '/\) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;/',
    ",\n  PRIMARY KEY (`id`),\n  KEY `idx_funcionarios_organismo` (`organismo_id`)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    $sql,
    1
);
$sql = preg_replace(
    '/ALTER TABLE `funcionarios`\s+ADD PRIMARY KEY \(`id`\),\s+ADD KEY `idx_funcionarios_organismo` \(`organismo_id`\);\s*/s',
    "",
    $sql,
    1
);
file_put_contents($dst, $sql);
echo "OK: generado $dst\n";
