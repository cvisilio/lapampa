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
	$nombre_sub=mysqli_real_escape_string($con,(strip_tags($_GET["nombre_sub"],ENT_QUOTES)));
	$id=intval($id);
	
	
	$sql="select * from metas_gestion,objetivos_gestion where metas_gestion.id_objetivo =objetivos_gestion.id and metas_gestion.id='$id'";
	$query_metas=mysqli_query($con,$sql);
	$rw_metas=mysqli_fetch_array($query_metas);
	
	  $meta=$rw_metas['meta'];
	  $color_cabecera=$rw_metas['color2'];
	  
	  ?>
	   <div class="panel panel-<?php echo $color_cabecera;?>">
       <div class="panel-heading">
     
       <?php 
	   
	     echo $nombre_sub .": " . $meta;
	   
	  
	  ?>
      </div>
      
  
      <div class="panel-body">
   
      
      <?php
	  $lbl_class="";//'label label-'."dark";
	  $sql="select * from indicadores_gestion where id_meta='$id'";
	  $query_indicadores=mysqli_query($con,$sql);
	  
	  while ($rw_indicadores=mysqli_fetch_array($query_indicadores)){
	   $codigo_indicador= $rw_indicadores['codigo_indicador'];
	   $id_indicador= $rw_indicadores['id'];
	   $nombre_indicador=$rw_indicadores['nombre_indicador'];
	   $link1=$rw_indicadores['link1'];
	   $open_target=$rw_indicadores['open_target1'];
	   if ($open_target==1)
	    $target="_self";
	   else
	  	$target="_blank";
		   
	   $sql2="select * from graficos_estadisticos where indicador_gestion='$id_indicador'";
	   $query2=mysqli_query($con,$sql2);
	   
	   $cadena_graficos="";
	   
	   // cada indicador de gestion puede tener asaociados graficos e indicadores estadísticos
	     $bandera=0;
		 while($row = mysqli_fetch_array($query2)){	
		   
		   if($bandera>0)
		    $cadena_graficos=$cadena_graficos."|";
		   
		   $bandera++;
		   
		   $id=$row['id_grafico'];
		   $titulo=substr($row['titulo'],0,35) . "...";
		   //$descripcion=$row['descripcion'];
		   $tipo=$row['tipo'];
		   $tipo_comparativo=$row['tipo_comparativo'];
		   $ids_graficos_comparar=$row['ids_graficos_comparar'];
		   
		    if($tipo_comparativo=="provincias")
			 $href_grafico= "camparativo_indicadores.php?id=". $id;
		    elseif ($tipo_comparativo=="estandar")
			 $href_grafico= "camparativo_simple.php?id=". $id;
			else{
			   if ($ids_graficos_comparar <> NULL)
			   $id=$ids_graficos_comparar;
			   $href_grafico= "ver_grafico.php?id=". $id;
			    }
			 
			 if($tipo=="PieChart")
			  {$tipo_label="Torta";
			    $icono="adjust";
		      }
		     
			 if($tipo=="ColumnChart")
			   {$tipo_label="Columnas";
		        $icono="stats";
			   }
		
	            $cadena_graficos= '<a href="'.$href_grafico.'" title="'.$titulo .'"> <i class="glyphicon glyphicon-'. $icono.'"></i>'; 
				if($tipo_comparativo=="provincias") { 
				  $cadena_graficos= $cadena_graficos. '<i class="glyphicon glyphicon-globe"></i>';
				  }
				
				$cadena_graficos= $cadena_graficos. '</a>';
				
			} //While graficos
	   
	      
		  // Indicadores en mapa (Nacional y Provincial
	   $sql3="select * from indicadores where indicador_gestion='$id_indicador'";
	   $query3=mysqli_query($con,$sql3);
	 	 
	   $cadena_mapa_local_zonas="";
	   $cadena_mapa_nacional="";
	   $cadena_mapa_local_circulos="";
	   
		  while($row_mapas = mysqli_fetch_array($query3)){
		    
			  if($bandera>0)
		        $cadena_mapa_nacional=$cadena_mapa_nacional."|";
		    
			$bandera++;
				
		    $id=$row_mapas['id'];
		    $tipo_poligono=$row_mapas['tipo_poligono'];
		    $titulo=substr($row_mapas['titulo'],0,35) . "...";
		   // $descripcion=$row_mapas['descripcion'];
		    $link=$row_mapas['link'];
		    $ambito=$row_mapas['ambito'];
		   if($ambito==1)
		    {
			 $lbl_ambito="Nacional";
			 $lbl_class2='label label-info'; 
		    }
			else
			 {$lbl_ambito="Provincial";
              $lbl_class2='label label-success'; 
			}
			
			?>
            
			<?php if($ambito==1){ 
                     $cadena_mapa_nacional= $cadena_mapa_nacional. '<a href="mapas.php?id='. $id.'" title="'.$titulo .'"> <i class="glyphicon glyphicon-globe"></i></a>';
                   } 
				    else {
					 $cadena_mapa_local_zonas=$cadena_mapa_local_zonas .'<a href="indicadores_locales.php?id='.$id.'" title="'.$titulo .'"> <i class="glyphicon glyphicon-globe"></i></a>';
                     $cadena_mapa_local_circulos=$cadena_mapa_local_circulos.'<a href="indicadores_locales_circulos.php?id='.$id.'" title="'.$titulo .'"> <i class="glyphicon glyphicon-certificate"></i></a>';
                 
                   } 
		  
		} 
		  // While Indicadores Mapas 
	  
	  $plus= '<a href="/sz/indicadores.php" title="Nuevo Indicador"> Agregar Indicador</a>';
	  
	  if($link1 <>"")
	    $link1=' : ' . '<a href="'.$link1.'" title="'.$link1 .'" target="'.$target .'"> <i class="glyphicon glyphicon-link"></i></a>';
	  	  
	   
	   echo "<h4 class='". $lbl_class ."'>". "<strong>".$codigo_indicador . ": " ."</strong>" . $nombre_indicador . $cadena_graficos. $cadena_mapa_nacional .  $cadena_mapa_local_zonas.$cadena_mapa_local_circulos. $link1 ."</h4>" ;
	
	   	 
	 }  //While 
	 ?> 
        	 
       </div> <!--  <div class="panel-body">-->
       
        <div class="panel-footer">
        			       
	   <?php echo $plus;  ?></div> 
    
    
 <div class="panel-body">
   
      
      <?php
	  $lbl_class="";//'label label-'."dark";
	  $sql="select * from acciones where id_meta='$id'";
	  $query_indicadores=mysqli_query($con,$sql);
	  
	  while ($rw_indicadores=mysqli_fetch_array($query_indicadores)){
	   $numero_accion= $rw_indicadores['numero_accion'];
	   $nombre_accion= $rw_indicadores['nombre'];
	  
	   echo "<h4 class='". $lbl_class ."'>". "<strong>".$numero_accion . ": " ."</strong>" . $nombre_accion ."</h4>" ;
	  }
	  ?>
      
   </div>         
       
       
 </div> <!--  <div class="panel panel-cabecera"> -->
 
 
<?php } ?>