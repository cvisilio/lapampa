<?php

function obtener_datos($rubro,$buscar1,$ministerio_usuario1){
	global $con;
	  
	 $sWhere="";
	 if ($buscar1 <>"" and $buscar1<>"Buscar..")
	  $sWhere=" and (datos.titulo LIKE '%".$buscar1."%' or datos.dato LIKE '%".$buscar1."%')";
	
	$sql=mysqli_query($con,"select datos.*,rubros.muestra_copete,rubros.muestra_solo_a_ministerio from datos, rubros where datos.rubro=rubros.id and datos.status=1 and datos.ambito=1 and datos.rubro ='$rubro'". $sWhere." order by datos.rubro desc limit 0,10");
	while ($rw=mysqli_fetch_array($sql)){
	    $id=$rw['id'];
		$titulo=$rw['titulo'];
		$dato=$rw['dato'];
		$rubro=$rw['rubro'];
		$copete=$rw['copete'];
		$mapa=$rw['mapa'];
		$mapa2=$rw['mapa2'];
		$graficos=$rw['graficos'];
		$ministerio=$rw['ministerio'];
		$tipo_mapa_que_muestra=$rw['tipo_mapa_que_muestra'];// (como quiere mostrar el mapa, ppintando region o círculos)
		$tipo_mapa_que_muestra2=$rw['tipo_mapa_que_muestra2'];	
		$muestra_copete=$rw['muestra_copete'];
		$muestra_solo_a_ministerio=$rw['muestra_solo_a_ministerio'];
	    $pasa = 1;
		
		if ($muestra_solo_a_ministerio==1 && $ministerio_usuario1<>$ministerio)
	  	    $pasa=0;
	
	   if($pasa==1)
	    {	 	
		?>
        <thead> 
	<tr>
        <th><a href="ver_dato.php?id=<?php echo $id;?>"><?php echo $titulo;?></a>
        <?php if ($mapa>0){ 
          $sql_indicador=mysqli_query($con,"select * from indicadores where id='$mapa'");
		  $rw_indicador=mysqli_fetch_array($sql_indicador);
		  
		  $ambito_mapa=$rw_indicador['ambito']; // si es provincial (2) o nacional (1)
		  if($ambito_mapa==1)
		  {
		  ?>
          | <a href="mapas.php?id=<?php echo $mapa;?>"> <i class="glyphicon glyphicon-globe"></i></a>      <?php
		  } 
          else
            {
			switch ($tipo_mapa_que_muestra) {
				case 0:
					?>
                    <a href="indicadores_locales.php?id=<?php echo $mapa;?>"> <i class="glyphicon glyphicon-globe"></i></a>
             <a href="indicadores_locales_circulos.php?id=<?php echo $mapa;?>"> <i class="glyphicon glyphicon-map-marker"></i></a>
					<?php
					break;
				case 1:
					?>
                     <a href="indicadores_locales.php?id=<?php echo $mapa;?>"> <i class="glyphicon glyphicon-globe"></i></a>
            
                    <?php
					break;
				case 2:
					?>
                     <a href="indicadores_locales_circulos.php?id=<?php echo $mapa;?>"> <i class="glyphicon glyphicon-map-marker"></i></a>
                    <?php
					break;
              } //swicht
		    
			 
			 }//else
			        
	      } //if $mapa >0 ?>
          
       <?php if ($mapa2>0){ 
          $sql_indicador=mysqli_query($con,"select * from indicadores where id='$mapa2'");
		  $rw_indicador=mysqli_fetch_array($sql_indicador);
		  
		  $ambito_mapa2=$rw_indicador['ambito']; // si es provincial (2) o nacional (1)
		  if($ambito_mapa2==1)
		  {
		  ?>
          | <a href="mapas.php?id=<?php echo $mapa2;?>"> <i class="glyphicon glyphicon-globe"></i></a>      <?php
		  } 
          else
            {
			switch ($tipo_mapa_que_muestra2) {
				case 0:
					?>
                    <a href="indicadores_locales.php?id=<?php echo $mapa2;?>"> <i class="glyphicon glyphicon-globe"></i></a>
             <a href="indicadores_locales_circulos.php?id=<?php echo $mapa2;?>"> <i class="glyphicon glyphicon-map-marker"></i></a>
					<?php
					break;
				case 1:
					?>
                     <a href="indicadores_locales.php?id=<?php echo $mapa2;?>"> <i class="glyphicon glyphicon-globe"></i></a>
            
                    <?php
					break;
				case 2:
					?>
                     <a href="indicadores_locales_circulos.php?id=<?php echo $mapa2;?>"> <i class="glyphicon glyphicon-map-marker"></i></a>
                    <?php
					break;
              } //swicht
		    
			 
			 }//else
			        
	      } //if $mapa2>0 ?> 
          
         
         <?php if ($graficos<>""){
		  $pos = strpos($graficos, ",");
         
		  if($pos === false)
		   {
		   $graficos=intval($graficos);  
		   $sql_grafico=mysqli_query($con,"select * from graficos_estadisticos where  id_grafico='$graficos'");
		  $rw_grafico=mysqli_fetch_array($sql_grafico);
		  $tipo_comparativo=$rw_grafico['tipo_comparativo'];
		  
		  $ids_graficos_comparar=$rw_grafico['ids_graficos_comparar'];
		  $id=$rw_grafico['id_grafico'];
		  $titulo=substr($rw_grafico['titulo'],0,35);
		 
		 if ($tipo_comparativo=="provincias")
		  {$href_grafico= "camparativo_indicadores.php?id=". $id;}
		 elseif ($tipo_comparativo=="estandar")
		  {$href_grafico= "camparativo_simple.php?id=". $id;}
		 else{
		    if ($ids_graficos_comparar <> NULL)
			 $id=$ids_graficos_comparar;
		   
		    $href_grafico= "ver_grafico.php?id=". $id;
		   }
		 }  
	else
		  $href_grafico= "ver_grafico.php?id=". $graficos; 	 		  
          ?>
          
		 | <a href="<?php echo $href_grafico;?>" title="<?php echo $titulo;?>"> <i class="glyphicon glyphicon-stats"></i></a>
         
		 <?php } ?>
             
         
        </th>
        </tr>
        </thead>
     
     <tbody>   
     </tr>
    <?php if ($muestra_copete ==1){?> 
     <tr>   
        <td><?php echo $copete;?></td>
      
        
    </tr>
    <?php }?>
    </tbody>
   
  <!--<h1 style="border-top-style: solid;border-top-color: coral;"></h1> -->
    
		<?php
    
	} // if $pasa==1)
    
	} //while
 	
}


function obtener_indicador_provincia($indicador,$provincia){
	global $con;
	
	$sql=mysqli_query($con,"select text from indicadores_provincias where indicador = '$indicador' and provincia= '$provincia'");
	$rw=mysqli_fetch_array($sql);
	echo $valor=$rw['text'];
	}
	
?>