<?php
if (!defined('GOOGLE_MAPS_API_KEY')) {
	$__d = __DIR__;
	for ($__i = 0; $__i < 6; $__i++) {
		$__f = $__d . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'google_maps.php';
		if (is_readable($__f)) { require_once $__f; break; }
		$__d = dirname($__d);
	}
}
?>
<script>
    
 

 var verticesPoligono = [
        { lat: 41.05, lng: -4.79 },
        { lat: 40.39, lng: -6.09 },
        { lat: 39.29, lng: -5.85 },
        { lat: 38.39, lng: -4.09 },
        { lat: 38.94, lng: -2.59 },
        { lat: 40.09, lng: -3.12 },
        { lat: 40.95, lng: -3.99 }
      ];

 var poligono = new google.maps.Polygon({
        path: verticesPoligono,
        map: miMapa,
        strokeColor: 'rgb(255, 0, 0)',
        fillColor: 'rgb(255, 255, 0)',
        strokeWeight: 4,
      });
      
 var popup = new google.maps.InfoWindow();

      poligono.addListener('click', function (e) {
        popup.setContent('Contenido');
        popup.setPosition(e.latLng);
        popup.open(miMapa);
      });     
</script>	

 </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars(GOOGLE_MAPS_API_KEY, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap">
     
   
      
    </script>  