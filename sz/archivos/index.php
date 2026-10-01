<!DOCTYPE html>
<html lang="es">
 <head>
   <?php include("head.php");?>  
  <title>Inicio</title>
 </head>
 
<!-- body-->
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
		
        <section class="content-header">
         <div class="box">
                    
        <div class="container">
       
       
        
        <div class="row">
                                                       
           <?php 
		 // define('FM_EMBED', true);
         
		 //define('FM_SELF_URL', $_SERVER['tinyfilemanager.php']); //PHP_SELF
         //include('tinyfilemanager.php');
          
			define('FM_EMBED', true); 
			define('FM_SELF_URL', $_SERVER['PHP_SELF']);
			require 'tinyfilemanager.php';
		   
		   ?>
           
           		
            
        </div>
        <!-- /.row -->
		
	</div>
  </div>          
    
		</section>
    </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>
   
  </body>
  
</html> 
