<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?language=en&key=AIzaSyAbnCWDJwlWVfQW_yT1jqgOQwmFXURokKM">
    </script>
 
 <style>

#map_canvas {
  height: 100%;
  width: 100%;
  margin: 0px;
  padding: 0px
}
</style>


<?php

	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	/* Connect To Database*/
	
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from localidades where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$name=$rw['localidad'];
	$id_loc_gob_c=$rw['id_loc_gob_c']; //id Localidad en Presupuesto de Provincia
	$id_loc_padron=$rw['id_loc_padron']; //id Localidad en Padrón armado para escrutinio
	$cantidad_habitantes=$rw['cantidad_habitantes'];
	$demanda_habitacional=$rw['demanda_habitacional'];
	$latitud=$rw['latitud'];
	$longitud=$rw['longitud'];
	$modulos=$rw['modulos'];
	$cargo_elecciones=$rw['cargo_elecciones'];
	$lista_ganadora=$rw['lista_ganadora'];
	$seccion_padron=$rw['seccion_padron'];
	$circuito_default=$rw['circuito_default'];
	
	$usuario_editor=intval($_SESSION['usuario_editor']);
	
//	if($usuario_editor==1)
	 $disabled='';
//	else
//	$disabled='disabled="disabled"';
	  
	}
	}	
	else {exit;}
?>

 <div class="nav-tabs-custom">
   <ul class="nav nav-tabs">
    <li class="active"><a href="#detalles" data-toggle="tab">Detalles</a></li>
    <li><a href="#otros" data-toggle="tab">Otros</a></li>
    <li><a href="#ubicacion" data-toggle="tab">Ubicaci&oacute;n</a></li>
   </ul>
  <div class="tab-content">
   <div class="active tab-pane" id="detalles">

      <div class="form-group">
    	<label for="name" class="col-sm-3 control-label">Nombre</label>
  	  <div class="col-sm-6">
		<input type="text"  class="form-control" id="name" name="name" placeholder="Ingresa el Nombre" value="<?php echo $name;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
        
        <input type="hidden" value="<?php echo $id_loc_padron;?>" name="id_loc_padron" id="id_loc_padron">
        
	</div>
</div>

<div class="form-group">
	<label for="cantidad_habitantes" class="col-sm-3 control-label">Habitantes</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="cantidad_habitantes" name="cantidad_habitantes" placeholder="Ingresa Cantidad" value="<?php echo $cantidad_habitantes;?>" >
	</div>
    
    <label for="demanda_habitacional" class="col-sm-3 control-label">Demanda Casas</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="demanda_habitacional" name="demanda_habitacional" placeholder="Ingresa Cantidad" value="<?php echo $demanda_habitacional;?>" >
		
	</div>
    
    
</div>

  

    </div><!-- /.tab-pane -->
  
  <div class="tab-pane" id="otros">  
   <div class="form-group">
	<label for="modulos" class="col-sm-3 control-label">M&oacute;dulos</label>
	<div class="col-sm-4">
		<input type="text" class="form-control" id="modulos" name="modulos" placeholder="Ingresa los módulos" value="<?php echo $modulos;?>" <?php echo $disabled; ?>  required>
	</div>
  
  <label for="id_loc_gob_c" class="col-sm-2 control-label">Id Gob.</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="id_loc_gob_c" name="id_loc_gob_c" placeholder="Id de referenicia en Provincia" <?php echo $disabled; ?> value="<?php echo $id_loc_gob_c;?>">
	</div>  
   
    </div>  <!-- group -->  
    
     
    
 <div class="form-group">
	<label for="latitud" class="col-sm-3 control-label">Latitud</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="latitud" name="latitud" placeholder="Ingresa Latitud" value="<?php echo $latitud;?>" >
	</div>
    
    <label for="longitud" class="col-sm-3 control-label">Longitud</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="longitud" name="longitud" placeholder="Ingresa Longitud" value="<?php echo $longitud;?>" >
        
	</div>
      
</div>   <!-- group --> 

<div class="form-group">
	<label for="seccion_padron" class="col-sm-2 control-label">Seccion Padr&oacute;n</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="seccion_padron" name="seccion_padron" placeholder="Ingresa Secci&oacute;n" value="<?php echo $seccion_padron;?>" >
	</div>
    
    <label for="circuito_default" class="col-sm-2 control-label">Circuito</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="circuito_default" name="circuito_default" placeholder="Ingresa Circuito" value="<?php echo $circuito_default;?>" >
        
	</div>
    
    <label for="actualiza_default" class="col-sm-2 control-label">Actualiza Entidades?</label>
	<div class="col-sm-2">
		 <input type="checkbox" class="form-check-input" id="actualiza_default" name="actualiza_default">
   
        
	</div>
      
</div>   <!-- group -->     


<div class="form-group">
   	<label for="cargo_elecciones" class="col-sm-3 control-label"> Cargo Elecciones</label>
	<div class="col-sm-9">
	  <input type="text" class="form-control" id="cargo_elecciones" name="cargo_elecciones" placeholder="Ingresa los Cargos" <?php echo $disabled; ?> value="<?php echo $cargo_elecciones;?>" required />
	</div>
 </div>
 
<div class="form-group">
   	<label for="lista_ganadora" class="col-sm-3 control-label"> Lista Ganadora</label>
	<div class="col-sm-9">
		 <select class="form-control" name="lista_ganadora" id="lista_ganadora">
						<option value="0">Seleccione Lista Ganadora </option>
                        <option value="1" <?php if($lista_ganadora==1) echo "selected";?>>Frejupa</option>
                        <option value="2" <?php if($lista_ganadora==2) echo "selected";?>>Cambiemos</option>
                        <option value="4" <?php if($lista_ganadora==4) echo "selected";?>>Comunidad Organizada</option>
                        <option value="10" <?php if($lista_ganadora==10) echo "selected";?>>Junta Vecinal</option>
                       
         </select>               
	</div>
 </div>
 
    </div><!-- /.tab-pane -->
    
   <div class="tab-pane" id="ubicacion">
   
   <input id="lat" value="-37.0653398" />
<input id="long" value="-65.1397698" />
<input id="pasar" type="button" value="geocode" />
<input id="direccion" value="La Pampa, Argentina" />
 <div class="col-md-12 modal_body_map">
   <div id="map_canvas"></div> 
 </div>  	
      </div><!-- /.tab-pane --> 
  </div><!-- /.tab-content -->
 </div><!-- /.nav-tabs-custom -->

    
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