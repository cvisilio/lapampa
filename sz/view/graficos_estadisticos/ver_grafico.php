<?php 
  $cantidad_graficos=0;
       if (isset($_GET['id'])){
		$graficos=mysqli_real_escape_string($con,(strip_tags($_GET['id'], ENT_QUOTES)));;
		$array_graficos=explode(",",$graficos);
		
		$data = Array (); // inicializo array datos
		
		$longitud = count($array_graficos);
        $titulo = Array ();
		$tipos_graficos= Array ();
		$descripciones= Array ();
		//Recorro todos los elementos
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
		 
		
		
		$data [$i+1][] = Array ($nombre,$valor); // cabeceras ehemplo: Nombre, Valor
		
		$sql=mysqli_query($con,"select * from valores_graficos_estadisticos where id_grafico='$grafico_id'");
		
		  while ($rw=mysqli_fetch_array($sql)){
			$nombre1=$rw['nombre'];
		    $valor1=intval($rw['valor']);
			
			$data [$i+1][]= Array($nombre1,$valor1);
						
		   } //while
		  
		   
		   
		 } //if($grafico_id >0)
		 
	  }  // for i 	 
					
	}
	
	
    		
	if (!isset($_GET['id']) or $count!=1){
		header("location: index.php");
     }
	
	?>

<!DOCTYPE html>

<html>
  <head>
	<?php include("head.php");?>
    <script type="text/javascript">
     var datos = <?php echo json_encode($data) ?>;
	 var titulos =<?php echo json_encode($titulo) ?>;
	  var descripciones =<?php echo json_encode($descripciones) ?>;
	 var cantidad_graficos=<?php echo json_encode($cantidad_graficos) ?>;
	 var tipos_graficos=<?php echo json_encode($tipos_graficos) ?>;
	
// ["apple","orange",1,false,null,true,8];
// access 4th element in array
   // alert( ar[1] ); // false
</script>
       
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"], 'language': 'es'});
      //google.charts.setOnLoadCallback(drawChart);
	 
	  
	 
     google.charts.setOnLoadCallback(function() { 
	  for (var i = 1; i <= cantidad_graficos; i++) {
        drawChart1(i);
	   }  
	   	  
	  });
	 
	  
      function drawChart1(numero) {
        var data = google.visualization.arrayToDataTable(
		datos[numero]         
       );

        var options = {
          title: titulos[numero],
		  //subtitle: descripcioes[numero],
		//  legend: 'none', // Agregar esto para Mostrar Nombres en vez de %
        //  pieSliceText: 'label', // Agregar esto para Mostrar Nombres en vez de %
        //  pieStartAngle: 100, //Agregar esto para Mostrar Nombres en vez de %
         is3D: true,
		 hAxis: {format:'#'},
		 vAxis: {format:'#'},
		  
        };
		 
        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'+numero));
		
        chart.draw(data, options);
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
                 <?php 
				 $filas = ceil($cantidad_graficos/2);
				 $j=1;
				 for($i=1; $i<=$filas; $i++){ 
				  $nombre1="piechart_3d" .$j;
				  $j++;
				  $nombre2="piechart_3d" .$j;
				  $j++;
				 ?>
                  <div class="row">
                   <div class="col-md-12">
                    <div class="col-md-6">
					  <div id="<?php echo $nombre1; ?>" style="width: 100%; height: 400px;"></div>
                     </div>
                    <div class="col-md-6"> 
                     <div id="<?php echo $nombre2; ?>" style="width: 100%; height: 400px;"></div>
                    </div>
                   </div> <!-- 12 -->
                  </div> <!-- row -->  
                <?php } ?>   
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