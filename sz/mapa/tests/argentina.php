<!DOCTYPE html>
<html lang="en">
<head>
   <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">
 
   <?php include("head.php");?>
    
      <?php 
	  
	//Inicia Control de Permisos
		  
	  include("mapa/tests/js/argentina.php");?>
    <meta charset="UTF-8">
    <title>Argentina</title>
   
    <script src="mapa/tests/js/snap.svg-min.js" type="text/javascript"></script>
    <!-- <script src="mapa/tests/js/argentina.js" type="text/javascript"> -->
    
    
    </script>
    
    <link rel="stylesheet" href="mapa/tests/css/argentina.css">
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
		
        <section class="content-header">
          <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                 
                  <div id="cuenta_parent"> <?php echo $titulo; ?> </div>             
                   
                </h3>
              
            </div>
        
            <div class="box-body">
				<div class="row">
                    <div class="col-md-12 col-sm-12">      
                      <div class="box-background">
                       <div class="box-body">
                       
                        <div class="container svgMapContainer">
                            <svg id="svgMap" viewBox="0 0 700 900" width="100%" height="100%" preserveAspectRatio= "xMinYMin meet"></svg>    
                        </div>
                    </div> <!-- box-body -->
                   </div> <!-- box-background -->
         
               </div> <!-- col-md-12 -->
              </div> <!-- row -->
           </div> <!-- box-body -->
         </div>  <!-- box --> 
		</section>
    </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>
  
  <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" />
 
  </body>
</html>      