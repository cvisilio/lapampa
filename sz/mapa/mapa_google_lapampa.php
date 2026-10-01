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
    <title>Indicadores Locales</title>
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
      
      <?php 
	   if (isset($_GET['id'])){
   
   $id_indicador=intval($_GET['id']);
   
 } // if (isset($_GET['id']))   
  
     
	require_once ("config/db.php");
	require_once ("config/conexion.php");
	
	if($id_indicador==10)
		{
		$ganadores=array();
		$ganadores=[];
		$sel_consulta="select CodigoLocalidad, sum(L1I) as SumaL1I, sum(L1G) as  SumaL1G,sum(L2I) as SumaL2I, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10G) as SumaL10G from mesas where Escrutada='S' group by mesas.CodigoLocalidad";
		$query=mysqli_query($con,$sel_consulta);
        while($row = mysqli_fetch_array($query)){
		for ($i=0;$i<=4;$i++){
          $Totales1[$i]["suma_gobernador"]=0;
		  $Totales1[$i]["partido"]=0;
         }
		 
		 $id=$row['CodigoLocalidad'];
		 $Totales1[0]['suma_gobernador']= $row['SumaL1G'];
		 $Totales1[0]['partido']= 1;
		 $Totales1[1]['suma_gobernador']= $row['SumaL2G'];
		 $Totales1[1]['partido']= 2;
		 $Totales1[2]['suma_gobernador']= $row['SumaL3G'];
		 $Totales1[2]['partido']= 3;
		 $Totales1[3]['suma_gobernador']= $row['SumaL4G'] + $row['SumaL5G']+$row['SumaL6G']+ $row['SumaL7G']+ $row['SumaL8G'];
		 $Totales1[3]['partido']= 4;
		 $Totales1[4]['suma_gobernador']=$row['SumaL9G']+ $row['SumaL10G'];
		 $Totales1[4]['partido']= 5;
		 
		 foreach ($Totales1 as $key => $row1) {
           $aux[$key] = $row1['suma_gobernador'];
          }
 
          array_multisort($aux, SORT_DESC, $Totales1); 		 
		 	
		 $ganadores[$id]=$Totales1[0]['partido'];
		} // while
		
		} // if ==10
	
	 $query_indicador =  mysqli_query($con,"SELECT descripcion, titulo FROM indicadores where id='$id_indicador'");
	   $row_indicador = mysqli_fetch_array($query_indicador);
	   $descripcion_indicador=$row_indicador['descripcion'];
	   $titulo_indicador=$row_indicador['titulo'];
	   
	  ?>
      
      <div class="content-wrapper">
       
        <!-- Content Header (Page header) -->
		
        <section class="content-header">
          <div class="box">
                    
             <div class="container">
              <div id="map"></div>
              <div id="info"></div>
            </div>  
           
            <?php 
			echo "<h4>". $titulo_indicador . "</h4>";
			echo $descripcion_indicador;
			//echo print_r($ganadores);
			?> 
            
         </div>  <!-- box --> 
		</section>
    </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>

  <?php 
       
	   
	 		
	   $locations=array();
     
		$query =  mysqli_query($con,"SELECT poligonos_localidades.*, localidades.localidad,localidades.para_indicadores_id, localidades.es_localidad,localidades.lista_ganadora,localidades.id_loc_padron FROM localidades, poligonos_localidades where poligonos_localidades.id=localidades.id");
       
		$cadena_colores="";
		$cadena_nombres=[];
		
		$primero=0;
        while($row = mysqli_fetch_array($query)){
            $name = $row['localidad'];
			$id = $row['id'];
			$cadena_nombres[$id]=$row['localidad'];
			$nombres_localidades[$id]=$name;
			$lista_ganadora=$row['lista_ganadora'];
			$id_loc_padron=$row['id_loc_padron'];
			
			$localidad1=mysqli_real_escape_string($con,(strip_tags($row['para_indicadores_id'], ENT_QUOTES)));
			
			$longitude = $row['longitud'];                              
            $latitude = $row['latitud'];
            $poligono=$row['poligono'];
					
			$consulta= "select color from indicadores_provincias where provincia='".$localidad1."' and indicador='".$id_indicador."'";
			
			$sql_indicador=mysqli_query($con,$consulta);
			$rw=mysqli_fetch_array($sql_indicador);
			$count=mysqli_num_rows($sql_indicador);
					
			if($count >0)
			 {
			 $color1=$rw['color'];
			 $color1=substr($color1, 1);
			}
			else{
			 $color1=$row['color_default'];
			}
			
			if($id_indicador==10)
			 {
			
			 if (array_key_exists($id_loc_padron, $ganadores))
			  {
			   $partido_ganador_actual=$ganadores[$id_loc_padron];
			   
			    switch($partido_ganador_actual) {
            	 case 1:
			        $color1='CC0099';
					break;
				 case 2:
					$color1='FF0000';
					break;
				 case 3:
				    if($lista_ganadora==2)
				     $color1='0000FF';
				   else
					 $color1='003366';
				 	 break;
				 case 4:
				   if($lista_ganadora==1)
				     $color1='FF0000';
				   else
					  $color1='FF6600'; 
					   break;
				 case 5:
					  $color1='333333';
					   break; 	  
				 default:
					  $color1='d9d9d9';
				 }
			     }
			   else
			    {
				$color1='d9d9d9';
				}	 
			   }
			
			/*
			#0000FF	blue
			#0000FFFF	opaque blue
			#0000FF80	50% opaque blue
			#0000FF00	completely transparent blue
			*/
			
			$cadena_colores=$cadena_colores . "_" .$id. "_".$color1;
			
		       	 
			/* Each row is added as a new array */
		  if ($row['poligono'] <> NULL)	
            {
			$coordenadas[$id]=explode(",",$row['poligono']);
	        $nombres[$id]=$color1;//$row['color_default'];
			
		
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
			zoom: 7,
			center: {lat: -37.251415, lng: -65.04338},
			streetViewControl: false,
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
   
  
  localidad<?php echo $k; ?>.addListener('click', function(event){
   	   
	 infoWindow = new google.maps.InfoWindow();
	 
	  $.ajax({
	  type: "POST",
      url: 'mapa/trae_datos_localidad.php',
      data: {"id_localidad":<?php echo $k; ?>},
      success: function(data) {
       infoWindow.setContent(data);
       infoWindow.setPosition(event.latLng);   
       infoWindow.open(map);
	  },
      error: function() {
        alert('No se pudo obtener datos');
      }
   }
);

	
});
   
   
  <?php } ?> 
 
  
  
}


  
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo htmlspecialchars(GOOGLE_MAPS_API_KEY, ENT_QUOTES, 'UTF-8'); ?>&callback=initMap">      
    </script>
 

  </body>
</html>