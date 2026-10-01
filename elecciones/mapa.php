<?php
if (!defined('GOOGLE_MAPS_API_KEY')) {
	$__d = __DIR__;
	for ($__i = 0; $__i < 6; $__i++) {
		$__f = $__d . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'google_maps.php';
		if (is_readable($__f)) { require_once $__f; break; }
		$__d = dirname($__d);
	}
}
 if (!isset($_SESSION)) {
  session_start();
}

if (!$_SESSION['MM_Username'])
 exit;

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
       
    <meta charset="utf-8">
  
    <title>Mapa con ultima elección</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
      * element that contains the map. */
	.EstiloListas {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

    .EstiloVotos1 {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

    .EstiloVotos {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

    .Estilo152 {font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000;font-size: 20px}
    .Estilo_Selector {font-size: 16px}

    .cabeceras {font-family: Verdana, Arial, Helvetica, sans-serif; color:#666666;font-size: 22px}

    .cabecera_logo {font-size: 14px; font-weight: bold; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif; color:#ffffff;}
	
   
    #map {
        width: 100%;
        height:600px;
    }
    
      /* Optional: Makes the sample page fill the window. */
    </style>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
  <script src="jQuery/jQuery-3.5.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
</head>

 <body>
	
   <?php 
     
	include('Connections/conexionUsuarios.php');
	
		$ganadores=array();
		$ganadores=[];
		$escrutadas=[];
		
		$SumaGobernadorL1=0;
		$SumaGobernadorL2=0;
		$SumaGobernadorL3=0;
		$SumaGobernadorL5=0;
		$SumaGobernadorL6=0;
		$SumaGobernadorL7=0;
		
		$SumaL1I=0;
		$SumaL2I=0;
		$SumaL3I=0;
		$SumaL4I=0;
		$SumaL5I=0;
		$SumaL6I=0;
		
		
		$sel_consulta="select CodigoLocalidad, sum(L1I) as SumaL1I,sum(L1DP) as SumaL1DP, sum(L1G) as  SumaL1G,sum(L2I) as SumaL2I,sum(L2DP) as SumaL2DP, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I,sum(L3DP) as SumaL3DP, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I,sum(L4DP) as SumaL4DP, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I,sum(L5DP) as SumaL5DP, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I,sum(L6DP) as SumaL6DP, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I,sum(L7DP) as SumaL7DP, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I,sum(L8DP) as SumaL8DP, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I,sum(L9DP) as SumaL9DP, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I,sum(L10DP) as SumaL10DP, sum(L10G) as SumaL10G, count(*) as cantidad_escrutadas from mesas where Escrutada='S' group by mesas.CodigoLocalidad";
	   $query=mysqli_query($con,$sel_consulta);
       while($row = mysqli_fetch_array($query)){
		 
		for ($i=0;$i<=9;$i++){
          $Totales1[$i]["suma_gobernador"]=0;
		  $Totales1[$i]["partido"]=0;
         }
		 
		 $id=$row['CodigoLocalidad'];
		
		 $Totales1[0]['suma_gobernador']= $row['SumaL1I']; //SumaL1I
		 $Totales1[0]['partido']= 1;
		 $Totales1[1]['suma_gobernador']= $row['SumaL2I']; //SumaL1I
		 $Totales1[1]['partido']= 2;
		 $Totales1[2]['suma_gobernador']= $row['SumaL3I']; //SumaL1I
		 $Totales1[2]['partido']= 3;
		 $Totales1[3]['suma_gobernador']= $row['SumaL5I']; //SumaL1I
		 $Totales1[3]['partido']= 5;
		 $Totales1[4]['suma_gobernador']=$row['SumaL6I']; //SumaL1I
		 $Totales1[4]['partido']= 6;
		 $Totales1[5]['suma_gobernador']=$row['SumaL7I']; //SumaL1I
		 $Totales1[5]['partido']= 7;
		 
		 $SumaGobernadorL1=$SumaGobernadorL1+$row['SumaL1G'];
		 $SumaGobernadorL2=$SumaGobernadorL2+$row['SumaL2G'];
		 $SumaGobernadorL3=$SumaGobernadorL3+$row['SumaL3G'];
		 $SumaGobernadorL5=$SumaGobernadorL5+$row['SumaL5G'];
		 $SumaGobernadorL6=$SumaGobernadorL6+$row['SumaL6G'];
		 $SumaGobernadorL7=$SumaGobernadorL7+$row['SumaL7G'];
		 
		 $SumaL1I=$SumaL1I+$row['SumaL1I'];
		 $SumaL2I=$SumaL2I+$row['SumaL2I'];
		 $SumaL3I=$SumaL3I+$row['SumaL3I'];
		 $SumaL4I=$SumaL4I+$row['SumaL4I'];
		 $SumaL5I=$SumaL5I+$row['SumaL5I'];
		 $SumaL6I=$SumaL6I+$row['SumaL6I'];
				 
		 foreach ($Totales1 as $key => $row1) {
           $aux[$key] = $row1['suma_gobernador'];
          }
 
          array_multisort($aux, SORT_DESC, $Totales1); 		 
		 	
		 $ganadores[$id]=$Totales1[0]['partido'];
		 $escrutadas[$id]=$row['cantidad_escrutadas'];
		
		} // while
		
	$TotalGobernador=$SumaGobernadorL1+$SumaGobernadorL2+$SumaGobernadorL3+$SumaGobernadorL5+$SumaGobernadorL6+$SumaGobernadorL7;	
	$PorcentajeL1=round(($SumaGobernadorL1/$TotalGobernador) * 100,0);
	$PorcentajeL2=round(($SumaGobernadorL2/$TotalGobernador) * 100,0);
	$PorcentajeL3=round(($SumaGobernadorL3/$TotalGobernador) * 100,0);
	
	

	?>
    
   
   
   <div align="center" style="width:96%;margin-left:2%;border:inset;border-color:#E9ECEF">
    <div align="center">
   
   <!-- <p align="center" class="Estilo8"><a target='_blank' href="https://eleccionesgenerales2019.lapampa.gob.ar/scripts/cgiip.exe/WService=Elecciones/EReLocCowr.htm?vPartido=1&SelectLocalidad=84&User=">Ver Resultados en Tribunal Electoral (clic aqu&iacute;)</a></p> -->
 <table width="100%" height="81" border="0">
 
   <tr>
     <td height="77" colspan="2" align="left" bgcolor="#016EA6"><p><img src="fotos/escudo_la_pampa.png" width="65" height="70" style="margin-top:4px; margin-left:5px; margin-right:15px; float:left" /></p>
       <p><span class="cabecera_logo">Elecciones <?php echo "2023";?></span></p>
       <p><span class="cabecera_logo"> 14 de mayo de <?php echo "2023";?> - Escrutinio Provisorio.</span></p>        </td> 
       
       <td width="51%" colspan="2" align="right" bgcolor="#016EA6"><p><img src="fotos/Escudo_del_Partido_Justicialista.png" width="54" height="67" style="margin-top:2px; margin-right:5px; margin-bottom:2px; float:left" /></p>
       
       <p><span class="cabecera_logo"> Datos de Toda La Provincia de La Pampa &nbsp;&nbsp;</span></p>      
       <p>
     
       <span class="cabecera_logo"> <i class="fa fa-user"></i><?php echo $_SESSION['MM_Username']." |  "; ?></span> 
       <span class="cabecera_logo"><a target="_self" style="color:#39FF14; margin-right:15px;" href="verestadistica.php">Inicio  </a></span>
      
       
       </td>  
   </tr>
  </table>
  
    <div class="row">
    <div class="col-md-8"> 
  
   <div style="margin-top:1px; margin-bottom:1px" id="map"></div>
    <div id="info"></div>
    
       <table width="100%">
          
        
        <tr><td> <img src="fotos/Ziliotto.jpg" width="100" height="100" style="float:left; margin-right:5px" >
        <p><strong style="font-size:22px">FREJUPA (Ziliotto - Mayoral)</strong>  </p>
      
        <p><strong style="font-size:22px"> 
           <?php  if($SumaGobernadorL1>0) echo number_format($SumaGobernadorL1,0,'','.') . " Votos (". $PorcentajeL1; ?>%) </strong></p>
          </td>
        </tr>
        
        <tr> 
        <td><div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $PorcentajeL1; ?>%">
            </div> 
           </div> 
           </td>
        </tr>
  
    <tr>  
   <tr><td> <img src="fotos/Berhongaray.png" width="100" height="100" style="float:left; margin-right:5px"><p><strong style="font-size:22px"> JXC (Berhongaray - Testa) </strong></p><p><strong style="font-size:22px"> 
             <?php  if($SumaGobernadorL2>0) echo number_format($SumaGobernadorL2,0,'','.'). " Votos (". $PorcentajeL2; ?>%) </strong></p></td>
    </tr>
  
   <td> <div class="progress">
        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo $PorcentajeL2; ?>"
  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $PorcentajeL2; ?>%"> 
            </div>
           </div>
         </td>
       </tr> 
      
   <tr><td> <img src="fotos/Tierno.png" width="100" height="100" style="float:left; margin-right:5px">
   <p><strong style="font-size:22px"> Com. Org. (Tierno - Winschel)</strong></p><p><strong style="font-size:22px"> 
             <?php   if($SumaGobernadorL3>0) echo number_format($SumaGobernadorL3,0,'','.'). " Votos (". $PorcentajeL3; ?>%) </strong></p></td>
    </tr>
  
   <td> <div class="progress">
        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $PorcentajeL3; ?>"
  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $PorcentajeL3; ?>%"> 
            </div>
           </div>
         </td>
       </tr> 
    
    </table> 
    
        
  </div> <!-- column 8--> 
  
   <div class="col-md-4"> 
     
  
  <table class="table"><tr> <td bgcolor="#11285C"> <span style="color:#FFFFFF"> &nbsp; FREJUPA &nbsp;</span> </td> <td bgcolor="#FF6600"> <span style="color:#FFFFFF"> &nbsp; JxC </span> </td> <td bgcolor="#00CCCC"> <span style="color:#FFFFFF"> &nbsp; Com. Org. &nbsp;</span> </td> 
     <td bgcolor="#2D572C">  <span style="color:#FFFFFF"> &nbsp;Partido Vecinal</span> </td> 
     </tr>
    </table>
  
  <table class="table"> 
     
  
 <?php  
 $sel_consulta =  mysqli_query($con,"SELECT localidades.*,listas_elecciones.color2,listas_elecciones.abreviatura_provincial from localidades, listas_elecciones where es_localidad_o_comision_fomento=1 and localidades.lista_ganadora_proxima = listas_elecciones.id_lista");
  $cantidad_en_columna=0;
     
     $columna=1;	   
  while($row = mysqli_fetch_array($sel_consulta)){
	
	  $nombre_localidad=utf8_encode($row['localidad']);
	  $intendente_proximo=utf8_encode($row['intendente_proximo']);
	  $abreviatura_provincial=$row['abreviatura_provincial'];
	  $id= $row['id'];
	  $id_loc_padron=$row['id_loc_padron']; 
	  $color=$row['color2'];
	
     if ($columna==1) 
	  echo "<tr>"; //se abre la primera fila

?>
     <td>  <a href="#" data-toggle="modal" style="font-weight:bold; color:<?php echo $color;?>" data-target="#myModal" onClick="informacion1('<?php echo $id;?>','<?php echo $nombre_localidad;?>');"><?php echo $nombre_localidad;?></a> <a href="#" data-toggle="modal" style="font-weight:bold; color:#CCCCCC" data-target="#myModal" onClick="informacion2('<?php echo $id;?>','<?php echo $nombre_localidad;?>');">&nbsp;<i class="fa fa-history" aria-hidden="true"></i></a> </td> 
   <?php 
   
    if (($columna%3)==0)
      {
       echo "</tr>"; // la fila solo se cierra despues de la segunda columna
       $columna=1;
      }
    else
     $columna++; //incrementamos el valor en uno, ahora columna = 2
  } 
 ?>

 </tr></table>



<!-- Modal <td><strong style="color:<?php // echo $color;?>"><?php // echo $intendente_proximo; ?> </strong> </td> -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title">Gobiernos Locales 2023-2027</h4> 
      </div>
      <div class="modal-body">
        <div id="resultados_ajax"> </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>
  
  
 
  <!--  <a href="#" data-toggle="modal" data-target="#exampleModalCenter" onclick="informacion1('<?php // echo $id;?>');"> ... </a> -->
   
   
   </div> <!-- column 4-->  
  </div>      
         
  <div style="background:#E9ECEF; height:10px"> </div>
 <div style="background:#016EA6; height:10px"> </div>
 <br />
 
 <span>Copyright &copy; <?php echo date('Y')?> - Partido Justicialista La Pampa </span> 
 </div></div>                          
              
     
  <?php 
       
	   
	 		
	   $locations=array();
     
	  
		$query =  mysqli_query($con,"SELECT poligonos_localidades.*, localidades.localidad,localidades.para_indicadores_id, localidades.es_localidad,localidades.lista_ganadora,localidades.id_loc_padron,localidades.cantidad_mesas,localidades.lista_ganadora_proxima FROM localidades, poligonos_localidades where poligonos_localidades.id=localidades.id");
       
		$cadena_colores="";
		$cadena_nombres=[];
		
		$primero=0;
				
			
        while($row = mysqli_fetch_array($query)){
            $name = $row['localidad'];
			$id = $row['id'];
			$cadena_nombres[$id]=$row['localidad'];
			$nombres_localidades[$id]=$name;
			$Idlocalidad=$row['id_loc_padron'];
			$eleccion=10;
			$cantidad_mesas=$row['cantidad_mesas'];
						
			$id_loc_padron=$row['id_loc_padron'];
			$lista_ganadora_proxima=$row['lista_ganadora_proxima'];
						
			
			$localidad1=mysqli_real_escape_string($con,(strip_tags($row['para_indicadores_id'], ENT_QUOTES)));
			
			$longitude = $row['longitud'];                              
            $latitude = $row['latitud'];
            $poligono=$row['poligono'];
						
						   
			   switch($lista_ganadora_proxima) {
            	 case 150:
			        $color1='0000FF'; //azul 
				 	 break;
				 case 149:
					  $color1='FF6600'; // amarillo
					   break;
				  case 139:
					  $color1='00CCCC'; // Celeste
					   break;	   
				 default:
					  $color1='00FF33';
					   break;	
				 }
				
			
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
			mapTypeId: 'satellite'
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
      url: 'trae_datos_concejales.php', //trae_datos_localidad.php, trae el historial de elecciones pasadas. Y el trae_datos_localidad_actual trae datos eleccion actual
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

<script>


function informacion1(id_localidad,nombre_localidad){
  
  $.ajax({
	  type: "POST",
      url: 'trae_datos_concejales.php', //trae datos del intendente, concejales y del resultado de la última elección
      data: {"id_localidad":id_localidad},
      success: function(data) {
	   $(".modal-title").html("Cuerpo Colegiados 2023-2027");
      $("#resultados_ajax").html(data); 
	  }
   }
 );
		
}

function informacion2(id_localidad,nombre_localidad){
  
  $.ajax({
	  type: "POST",
      url: 'trae_datos_localidad.php', //trae_datos_localidad.php, trae el historial de elecciones pasadas. Y el trae_datos_localidad_actual trae datos eleccion actual
      data: {"id_localidad":id_localidad},
      success: function(data) {
	  $(".modal-title").html("Historial Elecciones");
      $("#resultados_ajax").html(data); 
	  }
   }
 );
		
}

</script>
 
  </body>
</html>