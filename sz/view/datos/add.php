<?php
session_start();	

	$target_dir="img/productos/product.png";
	$inser=mysqli_query($con,"INSERT INTO datos (titulo, rubro, ministerio, tipo, dato, image_path, iddependiente, ambito,orden) VALUES ('', '1', '0', '1', '', '$target_dir', '0', '1', '0')");
	echo mysqli_error($con);
	
	$sql_product=mysqli_query($con,"select id from datos order by id desc limit 0,1");
	$rw_product=mysqli_fetch_array($sql_product);
	$product_id=$rw_product['id'];
	$_SESSION['product_id']=$product_id; 	
?>
<!DOCTYPE html>
<html>
  <head>
  
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js">
    </script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
   <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
	<?php 
	
	
	include("head.php");?>
    <script type="text/javascript" src="https://js.nicedit.com/nicEdit-latest.js"></script>
    <script>bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>
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
		  <h1><i class='fa fa-edit'></i> Agregar nuevo dato</h1>
		
		</section>
		<!-- Main content -->
        <section class="content">
		<div class="row">
		
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			<div id="load_img">
              <img class="img-responsive" src="img/productos/product.png" alt="Bussines profile picture">
			  </div>

              <h3 class="profile-username text-center"></h3>

              <p class="text-muted text-center mail-text"></p>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          
        </div>
        <!-- /.col -->
        <div class="col-md-9">
		<form name="update_register" id="update_register" class="form-horizontal" method="post" enctype="multipart/form-data">
		
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#details" data-toggle="tab" aria-expanded="false">Detalles del dato</a></li>
             <li class=""><a href="#otros_detalles" data-toggle="tab" aria-expanded="false">Otros detalles</a></li>
              <li class=""><a href="#tablas" data-toggle="tab" aria-expanded="false">Tablas</a></li>
              
            </ul>
            <div class="tab-content">
              <div id="resultados_ajax"></div>

              <div class="tab-pane active" id="details">
                 <input type="hidden"  id="product_id" name="product_id"  value="<?php echo $product_id;?>" >
                   <div class="form-group">
                    <label for="rubro" class="col-sm-2 control-label">Rubro</label>

                    <div class="col-sm-2">
                      <select class="form-control" name="rubro" id="rubro" required>						
						<?php 
					        $sql=mysqli_query($con,"select * from rubros");
				         
						 	while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['name'];
							    if ($id==1){$selected1="selected";}else{$selected1="";}
								$cadena= $rw['modulos'];
  								$modulos = explode(",", $cadena);
                                 if (in_array('2', $modulos)){    
							?>
                            
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?> </option>
							<?php
							}
							}
						?>
					  </select>
                    </div>
                  
                   <label for="ministerio" class="col-sm-2 control-label">Ministerio</label>

                    <div class="col-sm-2">
                      <select class="form-control" name="ministerio" id="ministerio" required>						
						<?php 
							$sql=mysqli_query($con,"select * from ministerios");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['denominacion'];
							    if ($id==6){$selected1="selected";}else{$selected1="";}
							?>
                            
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?> </option>
							<?php
							}
						?>
					  </select>
                    </div>
                    
                    <label for="ambito" class="col-sm-2 control-label">Ambito</label>

                    <div class="col-sm-2">
                      <select class="form-control" name="ambito" id="ambito" required>						
						                           
							<option value="1" >Global </option>
                            <option value="2" >Ministerios </option>
                            <option value="4" >Detalle </option>
							
					  </select>
                    </div>
                  
                  </div>
                  
                   

                  <div class="form-group ">
                   
					<label for="titulo" class="col-sm-2 control-label">T&iacute;tulo</label>

                    <div class="col-sm-10">
                      <input type="text" value="" class="form-control" id="titulo" name="titulo" >
                    
                    </div>
                  </div>
                  
                  <div class="form-group ">
                   
					<label for="titulo" class="col-sm-2 control-label">Copete</label>

                    <div class="col-sm-10">
                      <textarea  id="copete" cols="10" name="copete" class="form-control"></textarea>
                    
                    </div>
                  </div>
                  
                  
				  <div class="form-group">
                    <label for="dato" class="col-sm-2 control-label">Dato</label>

                    <div class="col-sm-10">
                    <textarea  id="editor1" name="editor1" rows="10"></textarea>
				  </div>	
				  </div>
                  
                   <div class="form-group">
                    <label for="status" class="col-sm-2 control-label">Estado</label>
                    <div class="col-sm-6">
                        <select class="form-control" name="status" id="status">
                            <option value="1" selected >Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                  </div>
           		  
                  <div class="form-group">
                    <label for="image" class="col-sm-2 control-label">Imagen</label>

                    <div class="col-sm-6">
                      <input type="file" name="imagefile" id="imagefile" onChange="upload_image(<?php echo $product_id; ?>);">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                      <button type="submit" class="btn btn-primary actualizar_datos">Guardar datos</button>
                    </div>
                  </div>
               
              </div>
              <!-- /.tab-pane -->
			   <div class="tab-pane" id="otros_detalles">
                  <div class="form-group ">
                   
					<label for="mapa" class="col-sm-2 control-label">Indicador Mapa</label>

                    <div class="col-sm-10">
                      <select class="form-control" name="mapa" id="mapa" required>
                       <option value="0">Sin Indicador</option>						
						<?php 
					        $sql=mysqli_query($con,"select * from indicadores");
				         
						 	while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['titulo'];
							    
							?>
                            
							<option value="<?php echo $id;?>"><?php echo $name;?> </option>
							<?php
							}
						?>
					  </select>
                    </div>
                  </div>
                  
                  <div class="form-group ">
                   
					<label for="mapa2" class="col-sm-2 control-label">Indicador Mapa 2</label>

                    <div class="col-sm-10">
                      <select class="form-control" name="mapa2" id="mapa2" required>
                       <option value="0">Sin Indicador</option>						
						<?php 
					        $sql=mysqli_query($con,"select * from indicadores");
				         
						 	while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['titulo'];
							    
							?>
                            
							<option value="<?php echo $id;?>"><?php echo $name;?> </option>
							<?php
							}
						?>
					  </select>
                    </div>
                  </div>
                  
                   <div class="form-group ">
                   
					<label for="grafico1" class="col-sm-2 control-label">Graficos</label>

                    <div class="col-sm-10">
                    
                    
                       <input type="text" value="" class="form-control" id="graficos" name="graficos" >
                    </div>
                  </div>
                  
                </div>   
               <!-- /.tab-pane -->
               
               <div class="tab-pane" id="tablas">
                   
               
               </div> <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
		  </form>
        </div>
        <!-- /.col -->
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
	<?php include("js.php");?>

	<script>
 	
 function upload_image(id){
			    var tabla = "datos";
				var campo = "image_path";
				var carpeta = "productos";
				
				var inputFileImage = document.getElementById("imagefile");
				var file = inputFileImage.files[0];
				if( (typeof file === "object") && (file !== null) )
				{
					$("#load_img").text('Cargando...');	
					var data = new FormData();
					data.append('imagefile',file);
					data.append('id',id);
					data.append('tabla',tabla);
					data.append('campo',campo);
					data.append('carpeta',carpeta);
															
					$.ajax({
						url: "ajax/imagen_ajax.php",        // Url to which the request is send
						type: "POST",             // Type of request to be send, called as method
						data: data, 			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
						contentType: false,       // The content type used when sending data to the server.
						cache: false,             // To unable request pages to be cached
						processData:false,        // To send DOMDocument or non processed data file it is set to false
						success: function(data)   // A function to be called if request succeeds
						{
							$("#load_img").html(data);
							
						}
					});	
				}
				
				
			}
    </script>
    

    
		<script>
		$( "#update_register" ).submit(function( event ) {
		  $('.actualizar_datos').attr("disabled", true);
		  var editor1 = nicEditors.findEditor('editor1').getContent();
		 
		   var product_id=$("#product_id").val();
		   var rubro=$("#rubro").val();
		   var ministerio=$("#ministerio").val();
		   var ambito=$("#ambito").val();
		   var titulo=$("#titulo").val();
		   var mapa=$("#mapa").val();
		   var mapa2=$("#mapa2").val();
		   var status=$("#status").val();
		   var graficos=$("#graficos").val();
		   var copete = nicEditors.findEditor('copete').getContent();
		   var parametros = {"editor1":editor1,"product_id":product_id,'rubro':rubro,'ministerio':ministerio,'ambito':ambito,'titulo':titulo,'copete':copete,'mapa':mapa,'mapa2':mapa2,'status':status,'graficos':graficos};
		   
		  $.ajax({
				type: "POST",
				url: "./ajax/modificar/dato.php",
				data: parametros,
				 beforeSend: function(objeto){
					$("#resultados_ajax").html("Mensaje: Cargando...");
				  },
				success: function(datos){
				$("#resultados_ajax").html(datos);
				$('.actualizar_datos').attr("disabled", false);
				load(1);
				window.setTimeout(function() {
				$(".alert").fadeTo(500, 0).slideUp(500, function(){
				$(this).remove();});}, 5000);
				
			  }
		});		
		  event.preventDefault();
		});
	</script>
	
	
    
 

  </body>
</html>
