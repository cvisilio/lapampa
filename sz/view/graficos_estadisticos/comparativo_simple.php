<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE html>
<html>
  <head>
	<?php include("head.php");

    $cantidad_graficos=0;
       if (isset($_GET['id'])){
	    
		$grafico_id=mysqli_real_escape_string($con,(strip_tags($_GET['id'], ENT_QUOTES)));
	    $sql_grafico=mysqli_query($con,"select * from graficos_estadisticos where  id_grafico='$grafico_id'");
	    $rw_grafico=mysqli_fetch_array($sql_grafico);
	    $titulo_grafico=$rw_grafico['titulo'];
		
	    if($rw_grafico['ids_graficos_comparar']<>NULL)
		 $graficos=$rw_grafico['ids_graficos_comparar'];
		else
		 $graficos=$grafico_id; 
		 
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
		 
		 $array_etiquetas['grafico'. $indice][]=$serie_agrupadora; // primera columna de cada fila  se pone la serie. ejemplo 2020 
		 
		$sql=mysqli_query($con,"select * from valores_graficos_estadisticos where id_grafico='$grafico_id'");
		  
		  $lleva_decimal=1;
		  
		  while ($rw=mysqli_fetch_array($sql)){
			$nombre1=$rw['nombre'];
			
			//if (strpos($rw['valor'],'.') > 0)
			 $lleva_decimal=1;
			 
		    $valor1=floatval($rw['valor']);
			$data [$i+1][]= Array($nombre1,$valor1);
			
			if($cantidad_graficos==1) // guarda las eqtiquetas de los nombres una sola vez, se supone que todos los graficos a comparar tienen los mismos rubros (ejemplo: Cons. Meicas, Cons. Odont, Otras Const.)
			 { $array_etiquetas['etiquetas'][]= $nombre1; }
			
			$array_etiquetas['grafico'. $indice][]= $valor1;
		   	 
						
		   } //while
		  
		   
		   
		 } //if($grafico_id >0)
		 
	  }  // for i 	 
					
	}
	
	
		
	$datos_a_comparar[]= $array_etiquetas['etiquetas'];
 	
  	for($i=1; $i<=$longitud; $i++){
	 $datos_a_comparar[]= $array_etiquetas['grafico' . $i];
	}
 			
	if (!isset($_GET['id']) or $count!=1){
		header("location: index.php");
     }
	
	?>


    
    <script type="text/javascript">
     var datos_a_comparar = <?php echo json_encode($datos_a_comparar) ?>;
	 var titulos =<?php echo json_encode($titulo) ?>;
	  var descripciones =<?php echo json_encode($descripciones) ?>;
	 var cantidad_graficos=<?php echo json_encode($cantidad_graficos) ?>;
	 var tipos_graficos=<?php echo json_encode($tipos_graficos) ?>;
	 var titulo_grafico = <?php echo json_encode($titulo_grafico); ?>;
	 var lleva_decimal1= <?php echo intval($lleva_decimal); ?>
// ["apple","orange",1,false,null,true,8];
// access 4th element in array
   // alert( ar[1] ); // false
</script>
       
     <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
	     google.charts.load("current", {packages:["bar"], 'language': 'es'});
	   
		 
		 if (lleva_decimal1==0) 
		  google.charts.setOnLoadCallback(drawChart);
         else
	      google.charts.setOnLoadCallback(drawChart1);
		  
      function drawChart() {
        var data = google.visualization.arrayToDataTable(datos_a_comparar);
       
        var options = {
          hAxis: {format:'#,###.00'}, //#,###.00 para decimales
		  vAxis: {format:'#,###'},
          
          chart: {
          title: titulo_grafico,
         // subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		 
          }
			
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));
      
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
	  
	  function drawChart1() {
        var data = google.visualization.arrayToDataTable(datos_a_comparar);
       
        var options = {
          hAxis: {format:'#,###.00'}, //#,###.00 para decimales
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
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
		<?php if ($permisos_ver==1){?>
        
			
        <!-- Main content -->
        <section class="content" >
		 <div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border">
				     <h2 class="box-title"><?php // echo $descripciones[1];?></h2>
				</div><!-- /.box-header -->
				<div class="box-body">
                 <div class="table-responsive">
                
                  <div class="row">
                   <div class="col-md-12">
                  
					 <div id="columnchart_material" style="width: 100%; height: 400px;"></div>
                   </div> <!-- 12 -->
                  </div> <!-- row -->  
                 
                 </div>  <!-- table-responsive-->   
				</div><!-- /.box-body -->
				<div class="box-footer clearfix">
				  									
				</div>
			</div><!-- /.box -->
		</div><!-- /.col -->
	</div><!-- /.row -->				         
        </section><!-- /.content -->
		<?php 
		} else{
		?>	
		<section class="content">
			<div class="alert alert-danger">
				<h3>Acceso denegado! </h3>
				<p>No cuentas con los permisos necesario para acceder a este m&oacute;dulo.</p>
			</div>
		</section>		
		<?php
		}
		?>
      </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->

	<?php include("js.php");?>
	<script src="dist/js/VentanaCentrada.js"></script>
  </body>
</html>
