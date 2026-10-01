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
    <title>Circles</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
		
    #map {
        width: 93%;
        height:600px;
    }
    
      /* Optional: Makes the sample page fill the window. */
    </style>
  
    <?php include("head.php");?>  
    
  </head>
  <body class="hold-transition <?php echo $skin;?> ">
	
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
        <?php 
		 if (isset($_GET['id'])){
   
       $id_indicador=intval($_GET['id']);
   
 } // if (isset($_GET['id']))   
      
		require_once ("config/db.php");
		require_once ("config/conexion.php");
		
	   $query_multiplicador =  mysqli_query($con,"SELECT multiplicador_radio_circulo, descripcion, titulo FROM indicadores where id='$id_indicador'");
	   $row_multiplicador = mysqli_fetch_array($query_multiplicador);
	   $multiplicador1=$row_multiplicador['multiplicador_radio_circulo'];
	   $descripcion_indicador=$row_multiplicador['descripcion'];
	   $titulo_indicador=$row_multiplicador['titulo'];
	 
		
		?>
		
        <section class="content-header">
          <div class="box">
                    
             <div class="container">
              <div id="map"></div>
              <div id="info"></div>
              
               <?php 
			echo "<h4><strong>". $titulo_indicador . "</strong></h4>";
			echo "<h4>". $descripcion_indicador . "</h4>";
			?>
            
            </div>  
            
           
            
         </div>  <!-- box --> 
		</section>
    </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>

  <?php 
  
   
	  
	   
	   $locations=array();
	   
     
		$query =  mysqli_query($con,"SELECT localidades.localidad,localidades.id,localidades.latitud ,localidades.longitud,localidades.para_indicadores_id, localidades.es_localidad FROM localidades where localidades.es_localidad_o_comision_fomento=1");
      			
		$primero=0;
		$cadena="";
		$sumando=1;
		$localidad[]="";
		
		$cantidad=mysqli_num_rows($query);
		
        while($row = mysqli_fetch_array($query)){
            $name = $row['localidad'];
			$id = $row['id'];
			
			$sumando++;
			
			$longitude = $row['longitud'];                              
            $latitude = $row['latitud'];
			
			$para_indicadores_id=mysqli_real_escape_string($con,(strip_tags($row['para_indicadores_id'], ENT_QUOTES)));
						
			$consulta= "select * from indicadores_provincias where provincia='".$para_indicadores_id."' and indicador='".$id_indicador."'";
							
			$sql_indicador=mysqli_query($con,$consulta);
			$rw=mysqli_fetch_array($sql_indicador);
			$count=mysqli_num_rows($sql_indicador);
			$lbl_region=$id;
					
					
			if($count >0)
			 {
			 $color1=$rw['color'];
			 $valor=$rw['valor']; //valor
			 if($valor == "")
			  $valor=0;
			 			  
			 $texto=$rw['text'];
			 if($valor==0)
			  $valor=$texto;
			 
			 $color1=substr($color1, 1);
			}
			else{
			 $color1="CCCCCC";
			 $valor="";
			}
			
					
			$localidad1="localidad_".$id;
			$cadena=$cadena . $localidad1.":{center:{ lat: ". $latitude . ", lng: " .$longitude . "},"; 
			$cadena=$cadena. "region:". $lbl_region.",";
			$cadena=$cadena. "valor:". $valor.",";
			$cadena=$cadena. "color1:'". "#". $color1 ."'";
					
			if ($sumando>$cantidad)
			 $cadena=$cadena ."}";
			else
			 $cadena=$cadena ."},";			
				
			
		}
		
	
      
	  
	  ?>  
  		 
   <script>
      // This example creates circles on the map, representing valors in North
      // America.
    
	   var multiplicador1= <?php echo $multiplicador1;?>;
	 	  	
      // First, create an object containing LatLng and valor for each city.
      var citymap = {
       <?php echo $cadena; ?>
      };

      function initMap() {
        // Create the map.
        var map = new google.maps.Map(document.getElementById('map'), {
         zoom: 7,
		 center: {lat: -37.251415, lng: -65.04338},
		 mapTypeId: 'terrain'
        });

        // Construct the circle for each value in citymap.
        // Note: We scale the area of the circle based on the valor.
        for (var city in citymap) {
          // Add the circle for this city to the map.
		 if(parseInt(citymap[city].valor) >0){
		  
          var cityCircle = new google.maps.Circle({
            strokeColor: citymap[city].color1,
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: citymap[city].color1,
            fillOpacity: 0.35,
			
            map: map,
            center: citymap[city].center,
            radius: Math.sqrt(citymap[city].valor) * multiplicador1
          });
		  
		 
		  var informacion = citymap[city].valor +""; //citymap[city].region + " : " +
		  
		  createClickableCircle(map, cityCircle, informacion); 
        
		}  
        
		}
		
		 google.maps.event.addDomListener(window, 'load', initialize);
		
      }
	  
	function createClickableCircle(map, circle, info){
       var infowindow =new google.maps.InfoWindow({
            content: info
        });  
        google.maps.event.addListener(circle, 'click', function(ev) {
            // alert(infowindow.content);
            infowindow.setPosition(circle.getCenter());
			//infoWindow.setContent(content);
            infowindow.open(map);
        });
 }  
	  
    </script>
    
    
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars(GOOGLE_MAPS_API_KEY, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap">      
    </script>    
     
  </body>
</html>