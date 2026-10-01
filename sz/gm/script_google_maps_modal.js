$(document).ready(function() {
 // var map = null;
  var myMarker;
  var myLatlng;
  
  
  function initializeGMap(lat, lng) {
    myLatlng = new google.maps.LatLng(lat, lng);

    var myOptions = {
      zoom: 12,
      zoomControl: true,
      center: myLatlng,
	  streetViewControl:false, 
      mapTypeId: google.maps.MapTypeId.ROADMAP,
    };

   var map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);

    myMarker = new google.maps.Marker({
      position: myLatlng,
    });
    myMarker.setMap(map);
  }

  // Re-init map before show modal
  $('#myModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    initializeGMap(button.data('lat'), button.data('lng'));
    $('#location-map').css('width', '100%');
    $('#map_canvas').css('width', '100%');
  });

  // Trigger map resize event after modal shown
  $('#myModal').on('shown.bs.modal', function() {
     $("#myModal").show(); 										   
    google.maps.event.trigger(map, 'resize');
	map.setCenter(new google.maps.LatLng(-37.0653398, -65.1397698)); 
    map.setCenter(myLatlng);
	
  });
});// JavaScript Document

