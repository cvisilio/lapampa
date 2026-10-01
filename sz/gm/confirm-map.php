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
    var map;
    var marker;
    var infowindow;
    var red_icon =  'http://maps.google.com/mapfiles/ms/icons/red-dot.png' ;
    var purple_icon =  'http://maps.google.com/mapfiles/ms/icons/purple-dot.png' ;
    var locations = <?php get_all_locations() ?>;

    function initMap() {
        var lapampa = {lat: -37.0653398, lng: -65.1397698};
        infowindow = new google.maps.InfoWindow();
        map = new google.maps.Map(document.getElementById('map'), {
            center: lapampa,
            zoom: 7
        });


        var i ; var confirmed = 0;
        for (i = 0; i < locations.length; i++) {

            marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map,
            icon :   locations[i][4] === '1' ?  red_icon  : purple_icon,
            html: document.getElementById('form')
           });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
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
    }

    function saveData() {
        var confirmed = document.getElementById('confirmed').checked ? 1 : 0;
        var id = document.getElementById('id').value;
        var url = 'gm/locations_model.php?confirm_location&id=' + id + '&confirmed=' + confirmed ;
        downloadUrl(url, function(data, responseCode) {
            if (responseCode === 200  && data.length > 1) {
                infowindow.close();
                window.location.reload(true);
            }else{
                infowindow.setContent("<div style='color: purple; font-size: 25px;'>Errores</div>");
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

<div style="display: none" id="form">
    <table class="map1">
        <tr>
            <input name="id" type='hidden' id='id'/>
            <td><a>Descripcion:</a></td>
            <td><textarea disabled id='description' placeholder='Description'></textarea></td>
            
        </tr>
        
        <tr>
            <td><b>Confirma ?:</b></td>
            <td><input id='confirmed' type='checkbox' name='confirmed'></td>
        </tr>

        <tr><td></td><td><input type='button' value='Guardar' onclick='saveData()'/></td></tr>
    </table>
</div>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?language=en&key=AIzaSyAbnCWDJwlWVfQW_yT1jqgOQwmFXURokKM&callback=initMap">
</script>
</body>
</html>