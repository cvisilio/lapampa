<?php

 include('locations_model.php');
 
 $sql=mysqli_query($con,"select * from obras order by id");
  $opciones_obras="<option value='0'>Sin Especificar</option>";
 while ($rw=mysqli_fetch_array($sql)){
  $id=$rw['id'];
  $name=substr($rw['nombre_obra'],0,25);
 
  $name=mysqli_real_escape_string($con,(strip_tags($name, ENT_QUOTES)));;
  $name=strtolower($name);
  $name=utf8_encode($name);   
  $opciones_obras=$opciones_obras ."<option value='".$id ."'>". $name ."</option>";
 }
 
 $sql=mysqli_query($con,"select * from rubros order by id");
  $opciones_categorias="<option value=''>Selecciona</option>";
 while ($rw=mysqli_fetch_array($sql)){
  $id=$rw['id'];
  $name=substr($rw['name'],0,25);
  $name=mysqli_real_escape_string($con,(strip_tags($name, ENT_QUOTES)));;
  $name=strtolower($name);
  $name=utf8_encode($name);
  $cadena= $rw['modulos'];
  $modulos = explode(",", $cadena);
  if (in_array('15', $modulos))    
   $opciones_categorias=$opciones_categorias ."<option value='".$id ."'>". $name ."</option>";
 }
 

//get_unconfirmed_locations();exit;
?>
<!DOCTYPE html>
<html>
 
<head>

<style>
 #map {
        width: 93%;
        height:600px;
    }
	
    </style>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?language=en&key=AIzaSyAbnCWDJwlWVfQW_yT1jqgOQwmFXURokKM">
    </script>

 
  <?php include("head.php");?>  
   

</head>
<!-- body-->
<body class="hold-transition <?php echo $skin;?> sidebar-mini">
	
    <div class="wrapper">
      <header class="main-header">
		<?php include("main-header.php");?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
		<?php include("main-sidebar.php");?>
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
       
        <!-- Content Header (Page header) -->
		
        <section class="content-header">
          <div class="box">
                    
             <div class="container">
              <div id="map"></div>
            </div>  
            
         </div>  <!-- box --> 
		</section>
    </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>
    
  
    <script>
	

        /**
         * Create new map
		 
	Rosa:  pink-dot.png
    Amarillo: yellow-dot.png
    purpura: purple-dot.png
	Azul: blue-dot.png
	Verde: green-dot.png
         */
		 
        var infowindow;
        var map;
        var red_icon =  'http://maps.google.com/mapfiles/ms/icons/red-dot.png';
        var purple_icon =  'http://maps.google.com/mapfiles/ms/icons/purple-dot.png';
        var locations = <?php get_confirmed_locations() ?>;
        var myOptions = {
            zoom: 7,
            center: new google.maps.LatLng(-37.0653398, -65.1397698),
            mapTypeId: 'roadmap'
        };
	
		
        map = new google.maps.Map(document.getElementById('map'), myOptions);

        /**
         * Global marker object that holds all markers.
         * @type {Object.<string, google.maps.LatLng>}
         */
        var markers = {};

        /**
         * Concatenates given lat and lng with an underscore and returns it.
         * This id will be used as a key of marker to cache the marker in markers object.
         * @param {!number} lat Latitude.
         * @param {!number} lng Longitude.
         * @return {string} Concatenated marker id.
         */
        var getMarkerUniqueId= function(lat, lng) {
            return lat + '_' + lng;
        };

        /**
         * Creates an instance of google.maps.LatLng by given lat and lng values and returns it.
         * This function can be useful for getting new coordinates quickly.
         * @param {!number} lat Latitude.
         * @param {!number} lng Longitude.
         * @return {google.maps.LatLng} An instance of google.maps.LatLng object
         */
        var getLatLng = function(lat, lng) {
            return new google.maps.LatLng(lat, lng);
        };

        /**
         * Binds click event to given map and invokes a callback that appends a new marker to clicked location.
         */
        var addMarker = google.maps.event.addListener(map, 'click', function(e) {
		   
            var lat = e.latLng.lat(); // lat of clicked point
            var lng = e.latLng.lng(); // lng of clicked point
            var markerId = getMarkerUniqueId(lat, lng); // an that will be used to cache this marker in markers object.
            var marker = new google.maps.Marker({
                position: getLatLng(lat, lng),
                map: map,
				
                animation: google.maps.Animation.DROP,
                id: 'marker_' + markerId,
                html: "    <div id='info_"+markerId+"'>\n" +
                "        <table class=\"map1\">\n" +
                "            <tr>\n" +
                "                <td><a>Description:</a></td>\n" +
                "                <td><textarea  id='manual_description' placeholder='Description'></textarea></td></tr>\n" +  "            <tr>\n" +
				"                <td><a>Obra:</a></td>\n" +
				"                <td><select id='id_obra'><?php echo $opciones_obras;?></select></td></tr>\n" +      "            <tr>\n" +
				"                <td><a>Categoria:</a></td>\n" +
				"                <td><select id='id_categoria'><?php echo $opciones_categorias;?></select></td></tr>\n"  +
				"            <tr><td></td><td><input type='button' value='Guardar' onclick='saveData("+lat+","+lng+")'/></td></tr>\n" +
                "        </table>\n" +
                "    </div>"
            });
            markers[markerId] = marker; // cache marker in markers object
            bindMarkerEvents(marker); // bind right click event to marker
            bindMarkerinfo(marker); // bind infowindow with click event to marker
        });

        /**
         * Binds  click event to given marker and invokes a callback function that will remove the marker from map.
         * @param {!google.maps.Marker} marker A google.maps.Marker instance that the handler will binded.
         */
        var bindMarkerinfo = function(marker) {
            google.maps.event.addListener(marker, "click", function (point) {
                var markerId = getMarkerUniqueId(point.latLng.lat(), point.latLng.lng()); // get marker id by using clicked point's coordinate
                var marker = markers[markerId]; // find marker
                infowindow = new google.maps.InfoWindow();
                infowindow.setContent(marker.html);
                infowindow.open(map, marker);
                // removeMarker(marker, markerId); // remove it
            });
        };

        /**
         * Binds right click event to given marker and invokes a callback function that will remove the marker from map.
         * @param {!google.maps.Marker} marker A google.maps.Marker instance that the handler will binded.
         */
        var bindMarkerEvents = function(marker) {
            google.maps.event.addListener(marker, "rightclick", function (point) {
                var markerId = getMarkerUniqueId(point.latLng.lat(), point.latLng.lng()); // get marker id by using clicked point's coordinate
                var marker = markers[markerId]; // find marker
                removeMarker(marker, markerId); // remove it
            });
        };

        /**
         * Removes given marker from map.
         * @param {!google.maps.Marker} marker A google.maps.Marker instance that will be removed.
         * @param {!string} markerId Id of marker.
         */
        var removeMarker = function(marker, markerId) {
            marker.setMap(null); // set markers setMap to null to remove it from map
            delete markers[markerId]; // delete marker instance from markers object
        };


        /**
         * loop through (Mysql) dynamic locations to add markers to map.
         */
        var i ; var confirmed = 0;
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon :   locations[i][4] === '1' ?  red_icon  : purple_icon,
                html: "<div>\n" +
                "<table class=\"map1\">\n" +
                "<tr>\n" +
                "<td><a>Description:</a></td>\n" +
                "<td><textarea disabled id='manual_description' placeholder='Description'>"+locations[i][3]+"</textarea></td></tr>\n" +
                "</table>\n" +
                "</div>"
            });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow = new google.maps.InfoWindow();
                    confirmed =  locations[i][4] === '1' ?  'checked'  :  0;
                    $("#confirmed").prop(confirmed,locations[i][4]);
                    $("#id").val(locations[i][0]);
                    $("#description").val(locations[i][3]);
                    $("#form").show();
                    infowindow.setContent(marker.html);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }

        /**
         * SAVE save marker from map.
         * @param lat  A latitude of marker.
         * @param lng A longitude of marker.
         */
        function saveData(lat,lng) {
            var description = document.getElementById('manual_description').value;
			var id_obra = document.getElementById('id_obra').value;
			var id_categoria = document.getElementById('id_categoria').value;
            var url = 'gm/locations_model.php?add_location&description=' + description+ '&id_obra=' + id_obra + '&id_categoria=' + id_categoria + '&lat=' + lat + '&lng=' + lng;
            downloadUrl(url, function(data, responseCode) {
                if (responseCode === 200  && data.length > 1) {
                    var markerId = getMarkerUniqueId(lat,lng); // get marker id by using clicked point's coordinate
                    var manual_marker = markers[markerId]; // find marker
                    manual_marker.setIcon(purple_icon);
                    infowindow.close();
                    infowindow.setContent("<div style=' color: purple; font-size: 25px;'> Esperando la confirmaci&oacute;n del administrador!!</div>");
                    infowindow.open(map, manual_marker);

                }else{
                    console.log(responseCode);
                    console.log(data);
                    infowindow.setContent("<div style='color: red; font-size: 25px;'>Errores</div>");
                }
            });
        }

        function downloadUrl(url, callback) {
            var request = window.ActiveXObject ?
                new ActiveXObject('Microsoft.XMLHTTP') :
                new XMLHttpRequest;

            request.onreadystatechange = function() {
                if (request.readyState == 4) {
                    callback(request.responseText, request.status);
                }
            };

            request.open('GET', url, true);
            request.send(null);
        }


    </script>
</body>
  
</html>   