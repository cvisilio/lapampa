<div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                  <?php 
				  $indices_menu=1;
				  $anio1=date('Y');
				  include("menu.php");?>
                </div>
              </div>  
                    
					<div class="col-sm-4">
						<a href="#addProductModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Nuevo</span></a>
					</div>
                       
			<div class='col-sm-8 pull-right'>
				<div id="custom-search-input">
                    <div class="input-group col-md-12">
                      <div class="col-md-4">     
                            <select class="form-control" name="ley_query" id="ley_query" onchange="load(1);">
                         <option value="">Ley (Origen)</option>
					        <?php 
							$sql=mysqli_query($con,"select * from leyes order by abreviatura");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['abreviatura'];
								 ?>
							?>
							<option value="<?php echo $id;?>"><?php echo $name;?></option>
							<?php
							}
						?>
                            </select>
                        </div>
                                
                            
                               <div class="col-md-3">
                                 <input class="form-control" type="number" value="<?php echo $anio1; ?>"  name="anio_query" id="anio_query" />
                             </div> 
                             
                             
                              
                              <div class="input-group col-md-5">     
                                <input type="text" class="form-control" placeholder="Buscar"  id="q" onKeyUp="load(1);" />
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
   
  
	<!-- Edit Modal HTML -->
	<?php include("html/modal_add_indices1.php");?>
	<!-- Edit Modal HTML -->
	<?php include("html/modal_edit_indices.php");?>
	<!-- Delete Modal HTML -->
	<?php include("html/modal_delete.php");?>
    
 <script>
		$(function() {
			load(1);
		});
		
	 function load(page){
			var query=$("#q").val();
			var ley_query=$("#ley_query").val();
			var anio_query=$("#anio_query").val();
			var per_page=10;
			var parametros = {"action":"ajax","page":page,'query':query,'ley_query':ley_query,'anio_query':anio_query,'per_page':per_page};
			$("#loader").fadeIn('slow');
			$.ajax({
				url:'./ajax/listar_indices.php',
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
		  var indice = button.data('indice') 
		  $('#edit_indice').val(indice)
		  var localidad = button.data('localidad') 
		  $('#edit_localidad').val(localidad)
          var ley= button.data('ley1') 
		  $('#edit_ley').val(ley)
          var anio = button.data('anio1') 
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
					url: "./ajax/editar_indice.php",
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
					url: "./ajax/guardar_indice.php",
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
					url: "./ajax/eliminar_indice.php",
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