<?php
if (!defined('GOOGLE_MAPS_API_KEY')) {
	$__d = __DIR__;
	for ($__i = 0; $__i < 6; $__i++) {
		$__f = $__d . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'google_maps.php';
		if (is_readable($__f)) { require_once $__f; break; }
		$__d = dirname($__d);
	}
}
?>
<!DOCTYPE html>
<html>
  <head>
	<?php include("head.php");?>
    
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?language=en&key=<?php echo htmlspecialchars(GOOGLE_MAPS_API_KEY, ENT_QUOTES, 'UTF-8'); ?>"> </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <script src="gm/script_google_maps_modal.js"></script>
 
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css">
   
 <style>
 
  #map-canvas img { max-width: none !important; } .gm-style-iw { width: 355px !important; top: 0px !important; left: 0px !important; background-color: #fff; box-shadow: 0 1px 6px rgba(178, 178, 178, 0.6); padding: 25px; border-radius: 2px 2px 10px 10px; } #iw-container { margin-bottom: 10px; } #iw-container .iw-title { font-family: 'Open Sans Condensed', sans-serif; font-size: 22px; font-weight: 400; padding: 10px; background-color: #ef8423; color: white; margin: 0; border-radius: 2px 2px 0 0; } #iw-container .iw-content { font-size: 13px; line-height: 18px; font-weight: 400; margin-right: 1px; padding: 15px 5px 20px 15px; max-height: 140px; overflow-y: auto; overflow-x: hidden; } .iw-subTitle { font-size: 16px; font-weight: 700; padding: 5px 0; }
 
 </style>  
    
  </head>
  <body class="hold-transition <?php echo $skin;?> sidebar-mini sidebar-collapse">
	<?php 
		if ($permisos_editar==1){
		 include("modal/agregar_localidades.php");
		 include("modal/editar_localidades.php");
     	}
	if ($permisos_ver==1)
	{	
	 include("modal/informacion_localidad.php");
		
	}	
	?>  
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
				<div class="row">
                    <div class="col-xs-2">
						<div class="input-group">
						  <input type="text" class="form-control" placeholder="Buscar por nombre" id='q' onKeyUp="load(1);">
						  <span class="input-group-btn">
							<button class="btn btn-default" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
						  </span>
						</div><!-- /input-group -->
                                            
						
					</div>
                    
                     <div class="col-xs-2"> <select id='departamento' class='form-control' onChange="load(1);">
							<option value="">Todos Departamentos </option>
							<?php
							$sql1=mysqli_query($con,"select * from departamentos order by departamento");
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id'];
							  $name=utf8_encode($rw1['departamento']);
							  
								?>
								<option value="<?php echo $id;?>"><?php echo $name;?></option>	
								<?php 
							}
							?>
						</select></div>
                        
                        <div class="col-xs-2"> <select id='region' class='form-control' onChange="load(1);">
							<option value="">Todas las Regiones </option>
							<?php
							for ($i=1;$i<=10;$i++){
							  $name="Region " .$i;
							  
								?>
								<option value="<?php echo $i;?>"><?php echo $name;?></option>	
								<?php 
							}
							?>
						</select></div>
                        
                    				
					<div class="col-xs-1">
						<div id="loader" class="text-center"></div>
						
					</div>
					<div class="col-xs-5 ">
						<div class="btn-group pull-right">
							<?php if ($permisos_editar==1){?>
							<button type="button" class="btn btn-default"  data-toggle="modal" data-target="#modal_register"><i class='fa fa-plus'></i> Nuevo</button>
							<?php }?>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Mostrar
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-right">
							  <li class='active' onclick='per_page(15);' id='15'><a href="#">15</a></li>
							  <li  onclick='per_page(25);' id='25'><a href="#">25</a></li>
							  <li onclick='per_page(50);' id='50'><a href="#">50</a></li>
							  <li onclick='per_page(100);' id='100'><a href="#">100</a></li>
							  <li onclick='per_page(1000000);' id='1000000'><a href="#">Todos</a></li>
							</ul>
						</div>
                    </div>
					<input type='hidden' id='per_page' value='15'>
			    </div>
		</section>
		<!-- Main content -->
        <section class="content">
           
            <!-- desde aca modal maps -->
           <!-- Modal -->
              <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 id="myModalLabel1">Mapa</h4> 
                    </div>
                    <div class="modal-body">
                   
                      <div class="row">
                        <div class="col-md-12 modal_body_content">
                          <p> <!-- ... --></p>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12 modal_body_map">
                          <div class="location-map" id="location-map">
                            <div style="width: 600px; height: 400px;" id="map_canvas"></div>
                          </div>
                         
                         
 
                          
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12 modal_body_end">
                          <p> <!-- ... --></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
           
           <!-- hasta acá modal maps -->
           
            
			<div id="resultados_ajax"></div>
			<div class="outer_div"></div><!-- Datos ajax Final --> 
          
            
                             
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
	<script src="dist/js/VentanaCentrada.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.js"></script>
	
  </body>
</html>



	<script>
	function ver_intendente(intendente){
	 alert(intendente);
	}
	
	
	
	$(function() {
		load(1);
			
	});
	function load(page){
		var query=$("#q").val();
		var query2=$("#departamento").val();
		var region=$("#region").val();
		var per_page=$("#per_page").val();
		var parametros = {"action":"ajax","page":page,'query':query,'query2':query2,'region':region,'per_page':per_page};
		$("#loader").fadeIn('slow');
		$.ajax({
			url:'./ajax/localidades_ajax.php',
			data: parametros,
			 beforeSend: function(objeto){
			$("#loader").html("<img src='./img/ajax-loader.gif'>");
		  },
			success:function(data){
				$(".outer_div").html(data).fadeIn('slow');
				$("#loader").html("");
			
			}
		})
	}
	
	function per_page(valor){
		$("#per_page").val(valor);
		load(1);
		$('.dropdown-menu li' ).removeClass( "active" );
		$("#"+valor).addClass( "active" );
	}

	
	</script>

		<script>
		function eliminar(id){
			if(confirm('Esta acción  eliminará de forma permanente el localidad \n\n Desea continuar?')){
				var page=1;
				var query=$("#q").val();
				var query2=$("#departamento").val();
					
				var per_page=$("#per_page").val();
				var parametros = {"action":"ajax","page":page,"query":query,'query2':query2,"per_page":per_page,"id":id};
				
				$.ajax({
					url:'./ajax/localidades_ajax.php',
					data: parametros,
					 beforeSend: function(objeto){
					$("#loader").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
						$(".outer_div").html(data).fadeIn('slow');
						$("#loader").html("");
						window.setTimeout(function() {
						$(".alert").fadeTo(500, 0).slideUp(500, function(){
						$(this).remove();});}, 5000);
					}
				})
			}
		}
	</script>
	



<script>
$('#new_register').validator().on('submit', function (e) {
  if (e.isDefaultPrevented()) {
    // handle the invalid form...
  } else {
    $('#guardar_datos').attr("disabled", true);
	 var parametros = $(this).serialize();
		 $.ajax({
				type: "POST",
				url: "ajax/registro/agregar_localidades.php",
				data: parametros,
				 beforeSend: function(objeto){
					$("#resultados_ajax").html("Enviando...");
				  },
				success: function(datos){
				$("#resultados_ajax").html(datos);
				$('#guardar_datos').attr("disabled", false);
				load(1);
				window.setTimeout(function() {
				$(".alert").fadeTo(500, 0).slideUp(500, function(){
				$(this).remove();});}, 5000);
				$('#modal_register').modal('hide');
			  }
		});
	  event.preventDefault();
  }
})
</script>

<script>
function send_update(){
$('#update_register').validator().on('submit', function (e) {
  if (e.isDefaultPrevented()) {
    // handle the invalid form...
  } else {
     $('#actualizar_datos').attr("disabled", true);
 	 
  var parametros = $(this).serialize();
	 $.ajax({
			type: "POST",
			url: "ajax/modificar/localidades.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax").html("Enviando...");
			  },
			success: function(datos){
			$("#resultados_ajax").html(datos);
			$('#actualizar_datos').attr("disabled", false);
			
			load(1);
			window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove();});}, 5000);
			$('#modal_update').modal('hide');
		  }
	});
  event.preventDefault();
  }
})
}


 function editar(id,localidad){
		var parametros = {"action":"ajax","id":id};
			$.ajax({
					url:'modal/editar/localidades.php',
					data: parametros,
					 beforeSend: function(objeto){
					$("#loader2").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
					    $(".modal-title").html("Localidad: " + localidad);
						$(".outer_div2").html(data).fadeIn('slow');
						$("#loader2").html("");
						send_update();
					}
				})
		}

 function informacion1(id,localidad){
		var parametros = {"action":"ajax","id_localidad":id};
			$.ajax({
					url:'../elecciones/trae_datos_concejales.php',
					 type: "POST",
					//modal/editar/informacion_localidad.php anterior
					data: parametros,
					 beforeSend: function(objeto){
					$("#loader3").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
					    $(".modal-title").html("Localidad: " + localidad);
						$(".outer_div3").html(data).fadeIn('slow');
						$("#loader3").html("");
						send_update();
					}
				})
		}
		
 
function ordenar(ordenar_por){
		var query=$("#q").val();
		var query2=$("#departamento").val();
		var per_page=$("#per_page").val();
		var parametros = {"action":"ajax","page":1,'query':query,'query2':query2,'per_page':per_page,"ordenar_por":ordenar_por};
		$("#loader").fadeIn('slow');
		$.ajax({
			url:'./ajax/localidades_ajax.php',
			data: parametros,
			 beforeSend: function(objeto){
			$("#loader").html("<img src='./img/ajax-loader.gif'>");
		  },
			success:function(data){
				$(".outer_div").html(data).fadeIn('slow');
				$("#loader").html("");
			}
		})
	}
	

		
</script>

