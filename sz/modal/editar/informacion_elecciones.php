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
	$eleccion=5;
	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from localidades where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	 $rw=mysqli_fetch_array($query);
	 $name=$rw['localidad'];
	 $lista_ganadora=$rw['lista_ganadora'];
	 $id_loc_padron=$rw['id_loc_padron'];
	 $tipo_localidad=$rw['es_localidad'];
	 
	 
       $cantidad_graficos=0;
      
	
	  $sql="select resultados_elecciones.intendente,resultados_elecciones.intendente,resultados_elecciones.lista,listas_elecciones.numero_lista_provincial,listas_elecciones.agrupacion_politica_provincial,listas_elecciones.color,listas_elecciones.abreviatura_provincial from resultados_elecciones,listas_elecciones where resultados_elecciones.lista <>100 and resultados_elecciones.lista <>101 and  resultados_elecciones.lista=listas_elecciones.id_lista and resultados_elecciones.localidad='$id_loc_padron' and resultados_elecciones.eleccion='$eleccion' order by resultados_elecciones.intendente desc limit 3";
	 $query_resultados=mysqli_query($con,$sql);
		 
	 $j=0;
	 $resultado=0;
	 $lista="";
	 
	 while ($rw_resultados=mysqli_fetch_array($query_resultados)){
	
	  $lista1=$rw_resultados['numero_lista_provincial'] . "(". $rw_resultados['abreviatura_provincial'] . ")";
	  $resultado=$rw_resultados['intendente'];
	  $color =$rw_resultados['color'];
	  $lista=$rw_resultados['lista'];
	 
	   }
	 	
	
	
	    $graficos=$rw_grafico['ids_graficos_comparar'];
	    
		$array_graficos=explode(",",$graficos);
		
		$data = Array (); // inicializo array datos
		
		$longitud = count($array_graficos);
        $titulo = Array();
		$tipos_graficos= Array();
		$descripciones= Array();
		//Recorro todos los elementos
		
		$array_etiquetas['etiquetas'][]='Periodos'; // Podria ser año, meses, etc. es l aprimera fila primera columna y se ubica como leyenda abajo de las series agrupadoras
		
		$cantidad_graficos=0;
		for($i=0; $i<$longitud; $i++){
		
		 $grafico_id=intval($array_graficos[$i]);
		 
		 if($grafico_id >0){
		 $cantidad_graficos++; 
		 $sql_grafico=mysqli_query($con,"select * from graficos_estadisticos where  id_grafico='$grafico_id'");
		 $count=mysqli_num_rows($sql_grafico);
		 $rw_grafico=mysqli_fetch_array($sql_grafico);
		 $titulo[$i+1]=$rw_grafico['titulo'];
		 $descripciones[$i+1]=$rw_grafico['descripcion'];
		 $nombre=$rw_grafico['columna1'];
		 $valor=$rw_grafico['columna2'];
		 $tipos_graficos[$i+1]=$rw_grafico['tipo'];
		 $serie_agrupadora=$rw_grafico['serie_agrupadora'];
		 $series[$i+1]=$serie_agrupadora;
		 
		 $data [$i+1][] = Array ($nombre,$valor); // cabeceras ehemplo: Nombre, Valor
		 
		 $indice=$i+1;
		 
		 $array_etiquetas['grafico'. $indice][]=$serie_agrupadora; // primera columna de cada fila  se pone la serie. ejemplo 2015 
		 
		$sql=mysqli_query($con,"select * from valores_graficos_estadisticos where id_grafico='$grafico_id'");
		  
		  while ($rw=mysqli_fetch_array($sql)){
			$nombre1=$rw['nombre'];
		    $valor1=intval($rw['valor']);
			$data [$i+1][]= Array($nombre1,$valor1);
			
			if($cantidad_graficos==1) // guarda las eqtiquetas de los nombres una sola vez, se supone que todos los graficos a comparar tienen los mismos rubros (ejemplo: Cons. Meicas, Cons. Odont, Otras Const.)
			 { $array_etiquetas['etiquetas'][]= $nombre1; }
			
			$array_etiquetas['grafico'. $indice][]= $valor1;
		   	 
						
		   } //while
		  
		   
		   
		 } //if($grafico_id >0)
		 
	  }  // for i 	 
					
			
	$datos_a_comparar[]= $array_etiquetas['etiquetas'];
 	
  	for($i=1; $i<=$longitud; $i++){
	 $datos_a_comparar[]= $array_etiquetas['grafico' . $i];
	}
 			
	if (!isset($_GET['id']) or $count!=1){
		header("location: index.php");
     }

}

}	
	?>

    <script type="text/javascript">
     var datos_a_comparar = <?php echo json_encode($datos_a_comparar) ?>;
	 var titulos =<?php echo json_encode($titulo) ?>;
	  var descripciones =<?php echo json_encode($descripciones) ?>;
	 var cantidad_graficos=<?php echo json_encode($cantidad_graficos) ?>;
	 var tipos_graficos=<?php echo json_encode($tipos_graficos) ?>;
	 var titulo_grafico = <?php echo json_encode($titulo_grafico); ?>
	
// ["apple","orange",1,false,null,true,8];
// access 4th element in array
   // alert( ar[1] ); // false
</script>
       
     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
     
         google.charts.load("current", {packages:["bar"], 'language': 'es'});
	  google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(datos_a_comparar);

        var options = {
		  hAxis: {format:'#,###'},
		  vAxis: {format:'#,###'},
          chart: {
          title: titulo_grafico,
         // subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		 
          }
			
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
      
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
    
 
       <div class="row">
            <div class="col-md-12">
         		 <div id="columnchart_material" style="width: 100%; height: 500px;"></div>
            </div> <!-- 12 -->
       </div> <!-- row -->  
                 
 
  