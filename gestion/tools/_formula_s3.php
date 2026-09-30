<?php
require __DIR__ . '/../includes/config.php';

$db = getDB();
$importe3 = 3633498;
$sumOtros = 13844630;
$remun = 41124591;

$k = 1379671.09 / $sumOtros;
echo "k from excel = $k\n";
echo "0.83/0.11*0.0132036 = " . (0.83/0.11*0.0132036) . "\n";
echo "0.83/0.11*0.048 = " . (0.83/0.11*0.048) . "\n";

$col2A = $remun * 0.17 + $sumOtros * (0.83/0.11*0.048);
$col2B = $remun * 0.17 + $importe3 + $sumOtros * $k;
$col2C = $remun * 0.17 + $importe3 + $sumOtros * (0.83/0.11*0.0132036);

echo "col2A (otros*0.362): $col2A\n";
echo "col2B (importe3 + otros*k): $col2B\n";
echo "col2C: $col2C\n";

// DB values ageret 986
$importe3db = 3633500;
$col2db = $remun * 0.17 + $importe3db + $sumOtros * (0.83/0.11*0.0132036);
echo "col2C with db importe3: $col2db\n";

// Maybe adic = remun*0.17 + sum ALL conceptos raw except use transformed for 3?
$all = $importe3 + $sumOtros;
$col2D = $remun * 0.17 + $importe3 + ($sumOtros / 0.11) * 0.83 * 0.017;
echo "col2D otros/0.11*0.83*0.017: $col2D\n";

$col2E = $remun * 0.17 + ($importe3 + $sumOtros) * 0.136;
echo "col2E all*0.136: $col2E\n";

// remun*0.292
echo "col2 remun*0.291898: " . ($remun * 0.291898) . "\n";
