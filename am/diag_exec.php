<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function can_exec() {
  if (!function_exists('exec')) return false;
  $disabled = ini_get('disable_functions');
  return !($disabled && stripos($disabled, 'exec') !== false);
}

echo "<pre>";
echo "exec habilitada: " . (can_exec() ? "SI\n" : "NO\n");

@exec('which pdftotext 2>&1', $out1, $ret1);
echo "which pdftotext -> exit=$ret1\n" . implode("\n", $out1) . "\n\n";

@exec('pdftotext -v 2>&1', $out2, $ret2);
echo "pdftotext -v -> exit=$ret2\n" . implode("\n", $out2) . "\n\n";

@exec('which qpdf 2>&1', $out3, $ret3);
echo "which qpdf -> exit=$ret3\n" . implode("\n", $out3) . "\n\n";

phpinfo(INFO_GENERAL);
echo "</pre>";

