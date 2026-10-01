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
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAbnCWDJwlWVfQW_yT1jqgOQwmFXURokKM&callback=initMap">
     
   
      
    </script>  