<?php 
       if (isset($_GET['id'])){
		$product_id=intval($_GET['id']);
		$sql_product=mysqli_query($con,"select * from datos where  id='$product_id'");
		$count=mysqli_num_rows($sql_product);
		$rw_product=mysqli_fetch_array($sql_product);
		$titulo=$rw_product['titulo'];
		$rubro=$rw_product['rubro'];
		$ministerio=$rw_product['ministerio'];
		$tipo=$rw_product['tipo'];
		$dato=$rw_product['dato'];
		$image_path=$rw_product['image_path'];
		$iddependiente = $rw_product['iddependiente'];
		$rubro = $rw_product['rubro'];
		$ambito = $rw_product['ambito'];
		$orden = $rw_product['orden'];
		$copete=$rw_product['copete'];
			
	}
	if (!isset($_GET['id']) or $count!=1){
		header("location: datos.php");
     }
	
	?>

<!DOCTYPE html>

<html>
  <head>
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
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
		<?php if ($permisos_ver==1){?>
        
			
        <!-- Main content -->
        <section class="content" >
		 <div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border">
				     <h2 class="box-title"><?php echo $titulo;?></h2>
				</div><!-- /.box-header -->
				<div class="box-body">
                 <div class="table-responsive">
					<?php echo $dato;?>
                 </div>   
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
	
	



