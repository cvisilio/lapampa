<?php 
 if (isset($_SESSION['user_id'])){
 
 $sWhere1="";
$busqueda1 ="Buscar..";
if(!empty($_POST['buscar']))
   {
   
    $busqueda1 = mysqli_real_escape_string($con,(strip_tags($_POST['buscar'], ENT_QUOTES)));
    $sWhere1=" where datos.dato LIKE '%".$busqueda1."%'";
		  
   } 
  
   
?>
        <!-- Logo -->
        <a href="index.php" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>SZ</b>|Gesti&oacute;n</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>SZ </b>| Gesti&oacute;n </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          
           <form role="search" method="post" style="width:50%; margin-top:7px; margin-left:100px" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
            <div class="input-group">
              <input type="text" name="buscar" id="buscar" class="form-control" placeholder="<?php echo $busqueda1; ?>">
                            
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default">
                    <span class="glyphicon glyphicon-search"></span>
                    </button>
                  
                     <a style="margin-left:20px" class="btn btn-warning" target="_blank"  href="https://pilquen.lapampa.gob.ar/scripts/cgiip.exe/WService=Pilquen/Login.htm" role="button">Pilquen</a>
                     
                      <a style="margin-left:10px" class="btn btn-success" target="_self" href="http://www.lapampaperonista.com.ar/sz/localidades.php" role="button">Localidades</a>
                      
                      <a style="margin-left:10px" class="btn btn-danger" target="_self" href="http://www.lapampaperonista.com.ar/sz/agenda.php" role="button">Agenda</a>
                      
                      
               
                </span>
            
           
                        
                 
           </div>
           
               
            
        </form>    
              
        </nav>
        
               
	 <?php } ?>		