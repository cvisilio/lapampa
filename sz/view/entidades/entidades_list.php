<!DOCTYPE html>
<html>
  <head>
	<?php include("head.php");?>
  </head>
  <body class="hold-transition <?php echo $skin;?> sidebar-mini">
	<?php 
		if ($permisos_editar==1){
		include("modal/agregar_entidades.php");
		include("modal/editar_entidades.php");
		include("modal/ajuste_mesas.php");
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
                    <div class="col-xs-3">
						<div class="input-group">
						  <input type="text" class="form-control" placeholder="Buscar por nombre" id='q' onKeyUp="load(1);">
                     
						  <span class="input-group-btn">
							<button class="btn btn-default" type="button" onclick='load(1);'><i class='fa fa-search'></i></button>
						  </span>
						</div><!-- /input-group -->
						
					</div>
					<div class="col-xs-3"> <select id='localidad1' class='form-control' onChange="load(1);">
							<option value="">Todas las Localidades </option>
							<?php
							$sql1=mysqli_query($con,"select * from localidades order by localidad");
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id_loc_padron'];
							  $name=utf8_encode($rw1['localidad']);
							 
								?>
								<option value="<?php echo $id;?>"><?php echo $name;?></option>	
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
                            <button type="button"  onclick="export_mesas_csv();" class="btn btn-default"><i class='fa fa-file-excel-o'></i> Mesas CSV</button>
                            
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
	});
	function load(page){
		var query=$("#q").val();
		var localidad1=$("#localidad1").val();
		var per_page=$("#per_page").val();
		var parametros = {"action":"ajax","page":page,'query':query,'localidad1':localidad1,'per_page':per_page};
		$("#loader").fadeIn('slow');
		$.ajax({
			url:'./ajax/entidades_ajax.php',
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
			if(confirm('Esta acción eliminará de forma permanente el rubro \n\n Desea continuar?')){
				var page=1;
				var query=$("#q").val();
				var per_page=$("#per_page").val();
				var parametros = {"action":"ajax","page":page,"query":query,"per_page":per_page,"id":id};
				
				$.ajax({
					url:'./ajax/entidades_ajax.php',
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
				url: "ajax/registro/agregar_entidades.php",
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
			url: "ajax/modificar/entidades.php",
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





</script>
<script>
		function editar(id){
			var parametros = {"action":"ajax","id":id};
			$.ajax({
					url:'modal/editar/entidades.php',
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

 function ajuste_numero_mesas(id){
            
			var parametros = {"action":"ajax","id":id,"tabla":"entidades"};
			$.ajax({
					url:'modal/editar/ajuste_numero_mesas.php',
					data: parametros,
					 beforeSend: function(objeto){
					$("#loader2").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
						$(".outer_div2").html(data).fadeIn('slow');
						$("#loader2").html("");
						send_update_numero_mesas();
					}
				})
		}
 
 function send_update_numero_mesas(){
  $('#update_register1').validator().on('submit', function (e) {
  if (e.isDefaultPrevented()) {
    // handle the invalid form...
  } else {
     $('#actualizar_datos').attr("disabled", true);
 var parametros = $(this).serialize();
     var maximocantidadmesas=50;
	 var desde =$("#desde").val();
	 var hasta =$("#hasta").val();
	 var diferencia = hasta - desde;
	 
	 alerta = 0;
	 if (diferencia >=maximocantidadmesas)
	  alerta =1
	 
	var graba=1;
   if (alerta ==1)
    {	 
	if (confirm("Grabar más de " +maximocantidadmesas + " mesas")) {
	  graba = 1;
	} else {
	  graba=0;
	}  
   }	
  
  if(graba==1)    	
	{ 
	 $.ajax({
			type: "POST",
			url: "ajax/modificar/ajuste_numero_mesas.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax").html("Enviando...");
			  },
			success: function(datos){
			$("#resultados_ajax").html(datos);
			$('#actualizar_datos').attr("disabled", false);
			//load(1);
			window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove();});}, 5000);
			$('#modal_update1').modal('hide');
		  }
	});
   } // if graba==1	
  event.preventDefault();
  }
})
}					

function export_mesas_csv(){
		VentanaCentrada('exportar_escuelas_mesas_csv.php','','400','300','true');
	}				
		
</script>