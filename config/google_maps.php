<?php
/**
 * Carga la API key de Google Maps desde google_maps.local.php (no versionado).
 */
if (!defined('GOOGLE_MAPS_API_KEY')) {
	$local = __DIR__ . '/google_maps.local.php';
	if (is_readable($local)) {
		require $local;
	} else {
		define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: '');
	}
}
