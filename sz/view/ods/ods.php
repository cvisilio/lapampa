<?php 
if(!empty($_POST['ambito'])) //isset($_POST['submit']) && 
 $ambito=intval($_POST['ambito']); 
else
 $ambito=1; 

?>

<!DOCTYPE html>

<head>
   
   <?php include("head.php");
  	
   ?>
    
    <meta charset="UTF-8">
    <title>Objetivos</title>
    
   <link href="view/ods/ods_css_js/estilo_ods.css" rel="stylesheet" type="text/css">
   
   <script>
function indicadores1(id,objetivo,nombre_sub){
		var parametros = {"id":id,"nombre_sub":nombre_sub};
			$.ajax({
					url:'modal/editar/indicadores.php',
					data: parametros,
					 beforeSend: function(objeto){
					$("#loader").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
					    $(".modal-title").html("Objetivo: " + objetivo);
						$(".outer_div").html(data).fadeIn('slow');
						$("#loader").html("");
						
					}
				})
		}
</script>     
    
</head>
 <body class="hold-transition <?php echo $skin;?>" >
 
 <?php include("modal/indicadores.php"); ?>
	
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
      <?php if ($permisos_ver==1){?> 
        <!-- Content Header (Page header) -->
		
        <section class="content-header">
          <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                   
                </h3>
              
            </div>
        
            <div class="box-body">
				<div class="row">
                    <div class="col-md-12 col-sm-12">      
                      <div class="box-background">
                       <div class="box-body">
                       
                       <!-- desde acà -->
                <?php
				
				/*
				 // El siguiente código es para coregir caracteres especiales
				
				$query1=mysqli_query($con,"select * from ambitos_objetivos where id=2");
				while($row = mysqli_fetch_array($query1)){
				 $id=$row['id'];
				 $nombre= utf8_encode($row['descripcion_ambito']);
				
				 $sql = "UPDATE ambitos_objetivos SET descripcion_ambito='".$nombre."' WHERE id='".$id."' ";
				 $query_actualiza= mysqli_query($con,$sql);
				}
			*/		
								
				
				$sql="select objetivos_gestion.*,planes_gestion.plan,planes_gestion.descripcion_plan,planes_gestion.siglas, planes_gestion.nombre_sub from objetivos_gestion,planes_gestion where objetivos_gestion.ambito =planes_gestion.id and objetivos_gestion.ambito='$ambito'";
                $query_objetivos=mysqli_query($con,$sql);
				$row_objetivos1 = mysqli_fetch_array($query_objetivos);
				$descripcion_ambito_objetivos=$row_objetivos1['descripcion_plan'];
				$siglas=$row_objetivos1['siglas'];
				$nombre_ambito=$row_objetivos1['plan'];
								
				$num =mysqli_num_rows($query_objetivos);
				
						
				
				?>
               
                     <section id="objetivos">
                     <div class="container fuentes">
                           
                           <div class="row">
                                 <div class="modal-body" align="left">
                             
                          <form class="form-inline" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
                          
                      
                          <select id="ambito" name="ambito" class='form-control' onChange="this.form.submit()">
							
							<?php
							$sql1=mysqli_query($con,"select * from planes_gestion order by plan");                         
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id'];
							  $name=$rw1['plan'] . ": ". $rw1['siglas'];
							  if ($id==$ambito){$selected1="selected";}else{$selected1="";}
								?>
								<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>	
								<?php 
							}
						 	?>
						</select>
                       </form>
                                
                         </div> </div> 
                                                
                        
                        <div style="width:92%">
                        
                        <hr>
                           <?php 
						   $sql_archivos=mysqli_query($con,"select * from links where id_tabla=1 and id_registro='$ambito'");
						   while ($rw_archivos=mysqli_fetch_array($sql_archivos)){
							  $archivo=$rw_archivos['link'];
							  $titulo=$rw_archivos['titulo'];
							  
							  echo  '| ' .'<a href="'.$archivo.'" target="_blank">'.$titulo.'</a>';
							}
						   ?>
                           
                                <p><?php 
								if($num>0)
								 echo $descripcion_ambito_objetivos; 
								else
								 echo "No se han definido Objetivos a&uacute;n."; 
								 ?>
                                
                                </p>
                                </div>
                          <!-- PANELES -->
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="width:92%">
                    
                       <div class="objetivos">
                              <!-- goto: agregarObjetivo(); -->
                             
                           <?php
						   
						    
						    mysqli_data_seek($query_objetivos,0);
						   while($row_objetivos = mysqli_fetch_array($query_objetivos)){ 
						    $numero_objetivo = $row_objetivos['numero'];
							$id_objetivo = $row_objetivos['id'];
							$nombre_objetivo = $row_objetivos['objetivo'];
							$copete_objetivo = $row_objetivos['copete'];
							$descripcion_objetivo = $row_objetivos['descripcion'];
							$color1=$row_objetivos['color1'];
							$icono=$row_objetivos['icono'];
							$nombre_sub = $row_objetivos['nombre_sub']; // nombre sub se refiere a si le llamaron metas, objetivos específico u otro nombre a lo que tiene abajo. Ejejmplo: Objetivo y Meta; Objetivo y Objetivo Especifico
							
						   ?>   
                            <div class="panel panel-default"><div class="panel-heading obj<?php echo $numero_objetivo;?>" style="background-color:<?php echo $color1; ?> !important" role="tab" id="heading1"><h4 class="panel-title"><a class="text-uppercase in collapsed" role="button" id="btn<?php echo $numero_objetivo;?>" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $numero_objetivo;?>" aria-expanded="false" aria-controls="panel<?php echo $numero_objetivo;?>"><span class="caret rotar"></span> <?php echo $numero_objetivo . " ".$nombre_objetivo;  ?> </a></h4></div><div id="panel<?php echo $numero_objetivo;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?php echo $numero_objetivo;?>" aria-expanded="false" style="height: 0px;"><div class="panel-body"><div class="icon pull-left hidden-xs"><img src="<?php echo $icono;?>" width="200px" height="75px" alt="<?php echo $nombre_objetivo?>"></div><div class="icon-xs visible-xs"><img src="<?php echo $icono;?>" alt="<?php echo $nombre_objetivo?>" class="center-block"></div><h3>OBJETIVO <?php echo $numero_objetivo ." : ". $copete_objetivo?></h3><p><?php echo $descripcion_objetivo;?><br><br>
                               
                               <?php 
							   $sql="select * from metas_gestion where id_objetivo='$id_objetivo'";
                               $query_metas=mysqli_query($con,$sql);
							    while($row_metas = mysqli_fetch_array($query_metas)){
							     $codigo_meta=$row_metas['codigo_meta'];
								 $descripcion_meta=$row_metas['meta'];
								 $id_meta =$row_metas['id']; 
							    
							   ?>
                              
                                  <span class="list <?php echo $color1; ?>"><?php echo $codigo_meta; ?>)</span> <?php echo $descripcion_meta; ?> <a href="#" data-toggle="modal" data-target="#modal_update" onClick="indicadores1('<?php echo $id_meta;?>','<?php echo $nombre_objetivo;?>','<?php echo $nombre_sub;?>');"><i class='fa fa-bar-chart'>+</i></a> <br><br>
                                   <?php } ?>
                                 
                            </p></div></div></div>
                            
                              <?php } ?>
                            
                            </div>
                          </div>
                        </div>
                                        
                    </section>
              
                       <!-- hasta acá -->
                       
                        
                    </div> <!-- box-body -->
                   </div> <!-- box-background -->
         
               </div> <!-- col-md-12 -->
              </div> <!-- row -->
           </div> <!-- box-body -->
         </div>  <!-- box --> 
		        
        
        </section>
     
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
      <?php 
	 
	  include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>
    
  </body>
</html> 

