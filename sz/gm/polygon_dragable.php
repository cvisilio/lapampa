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
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Draggable Polygons</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <div id="info"></div>
  <?php 
   $locations=array();
        $uname="root";
        $pass="";
        $servername="localhost";
        $dbname="sz";
        $db=new mysqli($servername,$uname,$pass,$dbname);
        $query =  $db->query("SELECT poligonos_localidades.*, localidades.localidad, localidades.es_localidad FROM localidades, poligonos_localidades where poligonos_localidades.id=localidades.id");
        //$number_of_rows = mysql_num_rows($db);  
        //echo $number_of_rows;
		$cadena_colores="";
		$primero=0;
        while( $row = $query->fetch_assoc() ){
            $name = $row['localidad'];
			$id = $row['id'];
            $longitude = $row['longitud'];                              
            $latitude = $row['latitud'];
            $poligono=$row['poligono'];
			$color=$row['color_default'];
			$cadena_colores=$cadena_colores . "_" .$id. "_".$color; 
        	 
			/* Each row is added as a new array */
		  if ($row['poligono'] <> NULL)	
            {
			$coordenadas[$id]=explode(",",$row['poligono']);
	        $nombres[$id]=$row['color_default'];
		
		    }
		}
			
  ?> 
 
	 <?php 
	 $localidad[]="";
	
	 foreach ($coordenadas as $k => $v) { 
	   $cantidad=count($coordenadas[$k]);
	   $cadena="";
	    $localidad[$k]="";
		for ($i=0; $i < $cantidad-1; $i+=2)
		 {
		  $cadena=$cadena ."{ lat: ". $coordenadas[$k][$i+1] . ", lng: " .$coordenadas[$k][$i] . "}";
		  if($i < $cantidad-2) $cadena=$cadena . ",";
		  
		 }
	  $localidad[$k]=$cadena;
	  }	
	    
		 ?>
         
     <script>
	
		 
	   function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 5,
			center: {lat: -38.060297, lng: -65.098312},
			mapTypeId: 'terrain'
		  });

  // Define the LatLng coordinates for the polygon's path.
   
  
  // Construct the polygon.
 <?php 

  foreach ($localidad as $k => $v) { 
 
  $buscado= "_". $k ."_";
  $posicion= strpos($cadena_colores, $buscado);
  $color1= substr($cadena_colores,$posicion + strlen($buscado),6); 
  $color2=$color1;
  ?>
 
 
   var localidad<?php echo $k; ?> = new google.maps.Polygon({
     paths: [<?php echo $localidad[$k]; ?>],
     strokeColor: '#<?php echo $color1; ?>',
     strokeOpacity: 0.8,
	 fillColor:  '#<?php echo $color1; ?>',
     strokeWeight: 2,
    
	// editable: true,
   //  draggable: true,
     fillOpacity: 0.35
  });
  <?php } ?> 
 <?php 
  
 foreach ($localidad as $k => $v) { ?>   
 
  localidad<?php echo $k; ?>.setMap(map); 
  <?php } ?> 
  
}
   
  	        // Construct a draggable blue triangle with geodesic set to false.
  	
	/* function getPolygonCoords() {
		var len = azul.getPath().getLength();
		var htmlStr = "";
		for (var i = 0; i < len; i++) {
		 htmlStr += azul.getPath().getAt(i).toUrlValue(5) + "<br>";
		}
		document.getElementById('info').innerHTML = htmlStr;
		
     }	
		*/
	//getPolygonCoords();	
		
  
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars(GOOGLE_MAPS_API_KEY, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap">
     
   
      
    </script>
 

  </body>
</html>