<!DOCTYPE html>
<html>
  <head>
 
	<?php include("head.php");?>
     
      <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
   
	
  </head>
  <body class="hold-transition <?php echo $skin;?> sidebar-mini sidebar-collapse" >
   
   <?php
	   
	  if ($permisos_editar==1){
		include("modal/agregar_obra.php");
		include("modal/editar_obra.php");
		//include("movimientos_clientes/movimientos_clientes.php");
		//include("modal/cobro_cliente.php");
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
                       
                        
						  <input type="text" class="form-control" placeholder="Buscar..." id='q' onKeyUp="load(1);">
						  <span class="input-group-btn">
							<button class="btn btn-default" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
						  </span>
						</div><!-- /input-group -->
						
					</div>
					<div class="col-xs-2"> <select id='localidad1' class='form-control' onChange="load(1);">
							<option value="">Todas las Localidad </option>
							<?php
							$sql1=mysqli_query($con,"select * from localidades order by localidad");
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id'];
							  $name=utf8_encode($rw1['localidad']);
							  $cadena= $rw1['modulos'];
							  $modulos = explode(",", $cadena);
							  if (in_array('12', $modulos)) 
								 {
								?>
								<option value="<?php echo $id;?>"><?php echo $name;?></option>	
								<?php }
							}
							?>
						</select></div>
                       
                       <div class="col-xs-2"> <select id='estado1' class='form-control' onChange="load(1);">
							<option value="">Todos los Estado </option>
							<?php
							$sql1=mysqli_query($con,"select * from estados_obras");
							while ($rw1=mysqli_fetch_array($sql1)){
								?>
								<option value="<?php echo $rw1['id_estado']?>"><?php echo $rw1['estado'];?></option>	
								<?php
							}
							?>
						</select></div>
                        
                         <div class="col-xs-2"> <select id='ministerio1' class='form-control' onChange="load(1);">
							<option value="">Todos Ministerios </option>
							<?php
							$sql1=mysqli_query($con,"select * from ministerios order by denominacion");
							while ($rw1=mysqli_fetch_array($sql1)){
							    $cadena= $rw1['modulos'];
								$modulos = explode(",", $cadena);
							   if (in_array('12', $modulos)) 
							    {
								?>
								<option value="<?php echo $rw1['id']?>"><?php echo $rw1['denominacion'];?></option>	
								<?php
							  } //if
							 } //while
							?>
						</select></div>
                        
                        
					<div class="col-xs-1">
						<div id="loader" class="text-center"></div>
						
					</div>
					<div class="col-xs-3 ">
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
	$(function() {
	  load(1);
	  //datepicker
		$('.datepicker').datepicker({
			format: 'dd/mm/yyyy',
			endDate: '+360d',
			autoclose: true
		});
		

	});
	function load(page){
		var query=$("#q").val();
		var localidad1=$("#localidad1").val();
		var estado1=$("#estado1").val();
		var ministerio1=$("#ministerio1").val();
		var per_page=$("#per_page").val();
		var parametros = {"action":"ajax","page":page,"query":query,"localidad1":localidad1,"estado1":estado1,"ministerio1":ministerio1,"per_page":per_page};
		$("#loader").fadeIn('slow');
		$.ajax({
			url:'./ajax/obras_ajax.php',
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
			if(confirm('Esta acción eliminará de forma permanente la obra \n\n Desea continuar?')){
				var page=1;
				var query=$("#q").val();
				var localidad=$("#localidad").val();
				var per_page=$("#per_page").val();
				
				var parametros = {"action":"ajax","page":page,"query":query,"localidad":localidad,"per_page":per_page,"id":id};
				
				$.ajax({
					url:'./ajax/obras_ajax.php',
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
				url: "ajax/registro/agregar_obras.php",
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
			url: "ajax/modificar/obra.php",
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


function editar(id){
		var parametros = {"action":"ajax","id":id};
			$.ajax({
					url:'modal/editar/obras.php',
					data: parametros,
					 beforeSend: function(objeto){
					$("#loader2").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
						$(".outer_div2").html(data).fadeIn('slow');
						$("#loader2").html("");
						send_update();
					}
				})
		}

 
		
</script>

