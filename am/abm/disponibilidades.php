<div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                  <?php 
				  $disponibilidades_menu=1;
				  include("menu.php");?>
                </div>
              </div>  
                    
					<div class="col-sm-4">
						<a href="#addProductModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Nuevo</span></a>
					</div>
                       
			<div class='col-sm-4 pull-right'>
				<div id="custom-search-input">
                            <div class="input-group col-md-12">
                                <input type="text" class="form-control" placeholder="Buscar"  id="q" onKeyUp="load(1);" />
                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="button" onClick="load(1);">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div>
                </div>
			</div>
			<div class='clearfix'></div>
			<hr>
			<div id="loader"></div><!-- Carga de datos ajax aqui -->
			<div id="resultados"></div><!-- Carga de datos ajax aqui -->
			<div class='outer_div'></div><!-- Carga de datos ajax aqui -->
            
			
        </div>
    </div>
   
  
	<!-- Edit Modal HTML -->
	<?php include("html/ modal_add_disponibilidades1.php");?>
	<!-- Edit Modal HTML -->
	<?php include("html/modal_edit_disponibilidades.php");?>
	<!-- Delete Modal HTML -->
	<?php include("html/modal_delete.php");?>
    
      
 <script>
		$(function() {
			load(1);
		});
		
	 function load(page){
			var query=$("#q").val();
			var per_page=10;
			var parametros = {"action":"ajax","page":page,'query':query,'per_page':per_page};
			$("#loader").fadeIn('slow');
			$.ajax({
				url:'./ajax/listar_disponibilidades.php',
				data: parametros,
				 beforeSend: function(objeto){
				$("#loader").html("Cargando...");
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
		
	$('#editProductModal').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var monto = button.data('monto') 
		  $('#edit_monto').val(monto)
		  var partida = button.data('partida') 
		  $('#edit_partida').val(partida)
		  var ley = button.data('ley') 
		  $('#edit_ley').val(ley)
		  var anio = button.data('anio') 
		  $('#edit_anio').val(anio)
		 
		  var id = button.data('id') 
		  $('#edit_id').val(id)
		  
		})
		
		$('#deleteProductModal').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var id = button.data('id') 
		  $('#delete_id').val(id)
		})
		
		
		$( "#edit_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/editar_disponibilidad.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					load(1);
					$('#editProductModal').modal('hide');
				  }
			});
		  event.preventDefault();
		});
		
		
		$( "#add_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/guardar_disponibilidad.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					load(1);
					$('#addProductModal').modal('hide');
				  }
			});
		  event.preventDefault();
		});
		
		$( "#delete_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/eliminar_disponibilidad.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					load(1);
					$('#deleteProductModal').modal('hide');
				  }
			});
		  event.preventDefault();
		});

</script>