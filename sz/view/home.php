<!DOCTYPE html>
<html>
  <head>
  
	<?php include("head.php");?>
   
  </head>
  <body class="hold-transition <?php echo $skin;?> sidebar-mini sidebar-collapse">
    <div class="wrapper">

      <header class="main-header">
		<?php include("main-header1.php");
		
		?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
		<?php include("main-sidebar.php");?>
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
		<?php if ($permisos_ver==1){?>
		<!-- <section class="content-header">
          <h1>
            Panel de Control
            <small>Version 1.0</small>
          </h1>
          <ol class="breadcrumb">
            <li class="active"><i class="fa fa-dashboard"></i> Inicio</li>
            
          </ol>
        </section> -->
		
		                
		        <!-- Main content include('agenda/index.php');-->
        <section class="content">
          <!-- Info boxes -->
                    
         
          <div class="row">
         
            <!-- Left col -->
            <div class="col-md-12">
              <!-- TABLE: Datos Globales -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Económico</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="table-responsive">
                    <table class="table no-margin" width="100%">
                                           
                        <?php 
						obtener_datos(1,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                </div><!-- /.box-body -->
               
              </div><!-- /.box -->
            </div><!-- /.col -->
            

            <div class="col-md-12">
 
              <!-- Listado Estadisticos -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Social</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
				<div class="table-responsive">
                    <table class="table no-margin">
                                           
                        <?php 
						obtener_datos(3,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                 
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->

          <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <div class="col-md-12">
              <!-- TABLE: Datos Globales -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Educativo</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
               
                   <div class="box-body">
				<div class="table-responsive">
                    <table class="table no-margin">
                                           
                        <?php 
						obtener_datos(4,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                 
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
            

            <div class="col-md-12">
 
              <!-- Listado Estadisticos -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Salud</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
				<div class="table-responsive">
                    <table class="table no-margin">
                                           
                        <?php 
						obtener_datos(5,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                </div><!-- /.box-body -->
               
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
          
           <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <div class="col-md-12">
              <!-- TABLE: Datos Globales -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Infraestuctura</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" ><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
               
                   <div class="box-body">
				<div class="table-responsive">
                    <table class="table no-margin">
                                           
                        <?php 
						obtener_datos(6,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                 
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
            

            <div class="col-md-12">
 
              <!-- Listado Estadisticos bg-primary, bg-success, .bg-info, .bg-warning, and .bg-danger-->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Art&iacute;culos de Inter&eacute;s</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
				<div class="table-responsive">
                    <table class="table no-margin">
                                           
                        <?php 
						obtener_datos(18,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                </div><!-- /.box-body -->
               
              </div><!-- /.box -->
            </div><!-- /.col -->
            
          </div><!-- /.row -->
          
            <!-- Main row -->
          <div class="row">
            <!-- Left col -->
            <div class="col-md-12">
              <!-- TABLE: Datos Globales -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"><strong class="bg-primary">Consultas Frecuentes</strong></h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse" ><i class="fa fa-minus"></i></button>
                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                  </div>
                </div><!-- /.box-header -->
               
                   <div class="box-body">
				<div class="table-responsive">
                    <table class="table no-margin">
                                           
                        <?php 
						obtener_datos(21,$busqueda1,$ministerio_usuario);
						?>
                     
                    </table>
                  </div><!-- /.table-responsive -->
                 
                </div><!-- /.box-body -->
                
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div>  
          
          
          
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
    

	

