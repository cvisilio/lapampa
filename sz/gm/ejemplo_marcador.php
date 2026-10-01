
 <style>
 html,
body,
#map_canvas {
  height: 100%;
  width: 100%;
  margin: 0px;
  padding: 0px
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?language=en&key=AIzaSyAbnCWDJwlWVfQW_yT1jqgOQwmFXURokKM">
    </script>
<input id="lat" value="-37.0653398" />
<input id="long" value="-65.1397698" />
<input id="pasar" type="button" value="geocode" />
<input id="direccion" value="La Pampa, Argentina" />
<div id="map_canvas" style="border: 2px solid #3872ac;"></div> 	


<script>
var lat = null;
var lng = null;
var map = null;
var geocoder = null;
var marker = null;
var myListener = null;

jQuery(document).ready(function() {
  lat = jQuery('#lat').val();
  lng = jQuery('#long').val();
  jQuery('#pasar').click(function() {
    codeAddress();
    return false;
  });
  initialize();
});

function initialize() {

  geocoder = new google.maps.Geocoder();

  if (lat != '' && lng != '') {
    var latLng = new google.maps.LatLng(lat, lng);
  } else {
    var latLng = new google.maps.LatLng(-37.0653398, -65.1397698);
  }
  var myOptions = {
    center: latLng,
    zoom: 7,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

  marker = new google.maps.Marker({
    map: map,
    position: latLng,
    draggable: true
  });
  google.maps.event.addListener(marker, 'dragend', function() {
    updatePosition(marker.getPosition());
  });
  updatePosition(latLng);
  google.maps.event.addListener(map, 'click', function(event) {
    if (marker) {
      marker.setPosition(event.latLng)
    } else {
      marker = new google.maps.Marker({
        map: map,
        position: event.latLng,
        draggable: true
      });
    }
    updatePosition(event.latLng);
  });

}

function codeAddress() {

  var address = document.getElementById("direccion").value;
  geocoder.geocode({
    'address': address
  }, function(results, status) {

    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
      marker.setPosition(results[0].geometry.location);
      updatePosition(results[0].geometry.location);

      google.maps.event.addListener(marker, 'dragend', function() {
        updatePosition(marker.getPosition());
      });
    } else {
      alert("No podemos encontrar la direccion, error: " + status);
    }
  });
}

function updatePosition(latLng) {

  jQuery('#lat').val(latLng.lat());
  
  jQuery('#long').val(latLng.lng());
  
 }
 </script>