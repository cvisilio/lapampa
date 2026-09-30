<div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
               
                <?php 
				$compromisos_menu=1;
				$anio1=date('Y');
				$mes1=date('n');
				include("menu.php");?>
               </div>
            </div>
            
             <div class="col-sm-4">
						<a href="#addProductModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Nuevo Compromiso</span></a>
					</div>
            
			<div class='col-sm-7 pull-right'>
				<div id="custom-search-input">
                   <div class="col-md-12">
                     
                         <div class="col-md-3">     
                            <select class="form-control" name="mes_query" id="mes_query" onchange="load(1);">
                             <option value="">Mes</option>
					        <?php for($i=1;$i<=12;$i++){
                               $selected="";
                               if($i==$mes1) 
                               $selected=" selected=selected";?>
                               
                              <option value="<?php echo $i; ?>" <?php echo $selected?>><?php echo $i; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                        
                         <div class="col-md-3">
                           <input class="form-control" type="number" value="<?php echo $anio1; ?>"  name="anio_query" id="anio_query" />
                         </div> 
                        
                        <div class="input-group col-md-6">   
                          <input type="text" class="form-control" placeholder="Buscar"  id="q1" onKeyUp="load(1);" />
                                <span class="input-group-btn">
                                    <button class="btn btn-info" type="button" onClick="load(1);">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                            </div>
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
   
   <script src="formularios/js/select2/select2.full.min.js"></script>
   <link href="formularios/js/select2/select2.min.css" rel="stylesheet" />
   
   
	<!-- Edit Modal HTML -->
	<?php include("html/modal_add_compromisos.php");?>
	<!-- Edit Modal HTML -->
	<?php include("html/modal_edit_compromisos.php");?>
	<!-- Delete Modal HTML -->
	<?php include("html/modal_delete.php");?>
    
        
 <script>
 
    $(function() {
		$(".select2").select2();
		  load(1);
	    
		$('.select2').select2({
        dropdownParent: $('#addProductModal')
    });	  
		  
		});
		
 function load(page){
 		var query=$("#q1").val();
		var mes_query=$("#mes_query").val();
		var anio_query=$("#anio_query").val();
			var per_page=20;
			var parametros = {"action":"ajax","page":page,'query':query,'mes_query':mes_query,'anio_query':anio_query,'per_page':per_page};
			$("#loader").fadeIn('slow');
			$.ajax({
				url:'./ajax/listar_productos.php',
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
		  var localidad = button.data('localidad') 
		  $('#edit_localidad').val(localidad)
		  var monto = button.data('monto') 
		  $('#edit_monto').val(monto)
		  var afectacion = button.data('afectacion') 
		  $('#edit_afectacion').val(afectacion)
		  var cuotas = button.data('cuotas') 
		  $('#edit_cuotas').val(cuotas)
		  var observaciones = button.data('observaciones') 
		  $('#edit_observaciones').val(observaciones)
		  var mes_inicio = button.data('mes') 
		  $('#edit_mes_inicio').val(mes_inicio)
          var anio_inicio = button.data('anio') 
		  $('#edit_anio_inicio').val(anio_inicio)

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
		   var id=$('#edit_id').val();
			$.ajax({
					type: "POST",
					url: "./ajax/editar_producto.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					eliminar_datos_array(id)
					load(1);
					$('#editProductModal').modal('hide');
					
				  }
			});
		  event.preventDefault();
		});
		
		
		$( "#add_product" ).submit(function( event ) {
		  $('#guardar_datos').attr("disabled", true);
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/guardar_producto.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					$('#guardar_datos').attr("disabled", false);
					load(1);
					$('#addProductModal').modal('hide');
					//location.reload();
				  }
			});
		  event.preventDefault();
		  
		});
		
		$( "#delete_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
		   var id=$('#delete_id').val();
			$.ajax({
					type: "POST",
					url: "./ajax/eliminar_producto.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					load(1);
					eliminar_datos_array(id);
					$('#deleteProductModal').modal('hide');
				  }
			});
		  event.preventDefault();
		});

  $(".select2").select2({        
    ajax: {
        url: "ajax/motivos_select2.php",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term // search term
            };
        },
        processResults: function (data) {
            // parse the results into the format expected by Select2.
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data
            return {
                results: data
            };
        },
        cache: false
		
		
		
    },
    minimumInputLength: 2
	
})		

</script>