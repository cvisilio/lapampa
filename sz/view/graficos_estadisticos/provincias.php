<!DOCTYPE html>
<html>
  <head>
	<?php include("head.php");
	$grafico_id=mysqli_real_escape_string($con,(strip_tags($_GET['id'], ENT_QUOTES)));
	$sql_grafico=mysqli_query($con,"select * from graficos_estadisticos where  id_grafico='$grafico_id'");
	$count=mysqli_num_rows($sql_grafico);
	$rw_grafico=mysqli_fetch_array($sql_grafico);
	$titulo_grafico=$rw_grafico['titulo'];
	
	
	$graficos=$rw_grafico['ids_graficos_comparar'];
	$array_graficos=explode(",",$graficos);
	
	$indicador1=$array_graficos[0];
	$indicador2=$array_graficos[1];
	
	$series=$rw_grafico['serie_agrupadora'];
	$array_series=explode(",",$series);
		
	$serie1=$array_series[0];
	$serie2=$array_series[1];
	
	
	   
	
	?>
    <style type="text/css">
<!--
.Estilo1 {
	color: #999999;
	font-weight: bold;
}
.Estilo2 {color: #339966}
-->
    </style>
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
		<section class="content-header">
          
        </section>
		
		
		        <!-- Main content -->
        <section class="content">
          <!-- Info boxes -->
         

          <div class="row">
            <div class="col-md-12">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title"> <?php echo $titulo_grafico;?></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <p class="text-center">
                        <span class="Estilo1"><?php echo $serie1; ?></span><strong> - <span class="Estilo2"><?php echo $serie2; ?></span></strong> </p>
<div class="chart">
						<canvas id="barChart" style="height:380px"></canvas>
					</div>
                    </div><!-- /.col -->
                   
                  </div><!-- /.row -->
                </div><!-- ./box-body -->
                <div class="box-footer">
                  
                </div><!-- /.box-footer -->
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
				<p>No cuentas con los permisos necesario para acceder a este módulo.</p>
			</div>
		</section>		
		<?php
		}
		?>
      </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php //include("js.php");?>
  </body>
</html>

 <!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <!-- ChartJS 1.0.1 -->
    <script src="plugins/chartjs/Chart.min.js"></script>
    <!-- FastClick -->
    <script src="plugins/fastclick/fastclick.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js"></script>
    <script>
      $(function () {
        /* ChartJS
         * -------
         * Here we will create a few charts using ChartJS
         */

        //--------------
        //- AREA CHART -
        //--------------
        var areaChartData = {
          labels: ["Bs. As.", "Catamarca", "Chaco", "Chubut", "CABA", "Córdoba", "Corrientes","Entre Ríos","Formosa","Jujuy","La Pampa","La Rioja","Mendoza","Misiones","Neuquén","Río Negro","Salta","San Juan","San Luis","Santa Cruz","Santa Fe","Sgo. del Estero","T. del Fuego","Tucumán"],
          datasets: [
            {
              label: "2017",
              fillColor: "rgba(210, 214, 222, 1)",
              strokeColor: "rgba(210, 214, 222, 1)",
              pointColor: "rgba(210, 214, 222, 1)",
              pointStrokeColor: "#c1c7d1",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(220,220,220,1)",
              data: [<?php echo obtener_indicador_provincia($indicador1,"buenos-aires");?>, <?php echo obtener_indicador_provincia($indicador1,"catamarca");?>, <?php echo obtener_indicador_provincia($indicador1,"chaco");?>, <?php echo obtener_indicador_provincia($indicador1,"chubut");?>, <?php echo obtener_indicador_provincia($indicador1,"caba-big");?>, <?php echo obtener_indicador_provincia($indicador1,"cordoba");?>, <?php echo obtener_indicador_provincia($indicador1,"corrientes");?>,<?php echo obtener_indicador_provincia($indicador1,"entre-rios");?>,<?php echo obtener_indicador_provincia($indicador1,"formosa");?>,<?php echo obtener_indicador_provincia($indicador1,"jujuy");?>,<?php echo obtener_indicador_provincia($indicador1,"la-pampa");?>,<?php echo obtener_indicador_provincia($indicador1,"la-rioja");?>,<?php echo obtener_indicador_provincia($indicador1,"mendoza");?>,<?php echo obtener_indicador_provincia($indicador1,"misiones");?>,<?php echo obtener_indicador_provincia($indicador1,"neuquen");?>,<?php echo obtener_indicador_provincia($indicador1,"rio-negro");?>,<?php echo obtener_indicador_provincia($indicador1,"salta");?>,<?php echo obtener_indicador_provincia($indicador1,"san-juan");?>,<?php echo obtener_indicador_provincia($indicador1,"san-luis");?>,<?php echo obtener_indicador_provincia($indicador1,"santa-cruz");?>,<?php echo obtener_indicador_provincia($indicador1,"santa-fe");?>,<?php echo obtener_indicador_provincia($indicador1,"santiago-del-estero");?>,<?php echo obtener_indicador_provincia($indicador1,"tierra-del-fuego");?>,<?php echo obtener_indicador_provincia($indicador1,"tucuman");?>]
            },
            {
              label: "2018",
              fillColor: "rgba(60,141,188,0.9)",
              strokeColor: "rgba(60,141,188,0.8)",
              pointColor: "#3b8bba",
              pointStrokeColor: "rgba(60,141,188,1)",
              pointHighlightFill: "#fff",
              pointHighlightStroke: "rgba(60,141,188,1)",
              data: [<?php echo obtener_indicador_provincia($indicador2,"buenos-aires");?>, <?php echo obtener_indicador_provincia($indicador2,"catamarca");?>, <?php echo obtener_indicador_provincia($indicador2,"chaco");?>, <?php echo obtener_indicador_provincia($indicador2,"chubut");?>, <?php echo obtener_indicador_provincia($indicador2,"caba-big");?>, <?php echo obtener_indicador_provincia($indicador2,"cordoba");?>, <?php echo obtener_indicador_provincia($indicador2,"corrientes");?>,<?php echo obtener_indicador_provincia($indicador2,"entre-rios");?>,<?php echo obtener_indicador_provincia($indicador2,"formosa");?>,<?php echo obtener_indicador_provincia($indicador2,"jujuy");?>,<?php echo obtener_indicador_provincia($indicador2,"la-pampa");?>,<?php echo obtener_indicador_provincia($indicador2,"la-rioja");?>,<?php echo obtener_indicador_provincia($indicador2,"mendoza");?>,<?php echo obtener_indicador_provincia($indicador2,"misiones");?>,<?php echo obtener_indicador_provincia($indicador2,"neuquen");?>,<?php echo obtener_indicador_provincia($indicador2,"rio-negro");?>,<?php echo obtener_indicador_provincia($indicador2,"salta");?>,<?php echo obtener_indicador_provincia($indicador2,"san-juan");?>,<?php echo obtener_indicador_provincia($indicador2,"san-luis");?>,<?php echo obtener_indicador_provincia($indicador2,"santa-cruz");?>,<?php echo obtener_indicador_provincia($indicador2,"santa-fe");?>,<?php echo obtener_indicador_provincia($indicador2,"santiago-del-estero");?>,<?php echo obtener_indicador_provincia($indicador2,"tierra-del-fuego");?>,<?php echo obtener_indicador_provincia($indicador2,"tucuman");?>]
            }
          ]
        };
        //-------------
        //- BAR CHART -
        //-------------
        var barChartCanvas = $("#barChart").get(0).getContext("2d");
        var barChart = new Chart(barChartCanvas);
        var barChartData = areaChartData;
        barChartData.datasets[1].fillColor = "#00a65a";
        barChartData.datasets[1].strokeColor = "#00a65a";
        barChartData.datasets[1].pointColor = "#00a65a";
        var barChartOptions = {
          //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
		  scaleBeginAtZero: true,
          //Boolean - Whether grid lines are shown across the chart
          scaleShowGridLines: true,
          //String - Colour of the grid lines
          scaleGridLineColor: "rgba(0,0,0,.05)",
          //Number - Width of the grid lines
          scaleGridLineWidth: 1,
          //Boolean - Whether to show horizontal lines (except X axis)
          scaleShowHorizontalLines: true,
          //Boolean - Whether to show vertical lines (except Y axis)
          scaleShowVerticalLines: true,
          //Boolean - If there is a stroke on each bar
          barShowStroke: true,
          //Number - Pixel width of the bar stroke
          barStrokeWidth: 1,
          //Number - Spacing between each of the X value sets
          barValueSpacing: 5,
          //Number - Spacing between data sets within X values
          barDatasetSpacing: 1,
		  
          //String - A legend template
          legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
          //Boolean - whether to make the chart responsive
          responsive: true,
          maintainAspectRatio: true,
		  	  		  
		  
        };

        barChartOptions.datasetFill = false;
        barChart.Bar(barChartData, barChartOptions);
      });
    </script>


	

