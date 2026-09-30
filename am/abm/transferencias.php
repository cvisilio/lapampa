<div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                  
                  <?php 
				  $transferencias_menu=1;
				  include("menu.php");?>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js">//siempre tiene que ir este script primero, antes de poner el imput range</script>
   
                   
                </div>
              </div>  
              
                <div class="col-sm-2">
				  <a href="#addProductModal" class="btn btn-danger" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Nueva Trans.</span></a>
				</div>
               
                <div class="col-xs-3">
						<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						 </div>
                         <?php $fecha_actual = date("d-m-Y"); ?>
						  <input type="text" class="form-control pull-right" value="<?php echo date("d/m/Y",strtotime($fecha_actual."")).' - '.date('d/m/Y');?>" id="range" name="range" readonly>                       
						</div><!-- /input-group - 1 month-->
					</div>
                   <div class="col-xs-2"> 
                    <button class="btn btn-info" type="button" onClick="exportar_pdf();">
                               <span class="glyphicon glyphicon-print"></span>
                         </button>
                      <button class="btn btn-info" type="button" onClick="exportar_csv();">
                               <span class="glyphicon glyphicon-save"></span>
                         </button>

                  <button class="btn btn-info" type="button" onClick="exportar_csv_por_motivos();">
                               <span class="glyphicon glyphicon-tags"></span>
                         </button>        
                   </div>
            
            <div class='col-sm-2 pull-left'>
             <div class="input-group">
              <select class="form-control" name="motivo" id="motivo" onchange="load(1);">
                   <option value="">Todos Motivos </option>
							<?php
							$sql1=mysqli_query($con,"select * from objetivos_motivos order by motivo");
							while ($rw1=mysqli_fetch_array($sql1)){
							  $id=$rw1['id'];
							  $name=$rw1['motivo'];
							  
								?>
								<option value="<?php echo $id;?>"><?php echo $name;?></option>	
						
                            <?php } ?>
                            </select>
              </div>
            </div>
			<div class='col-sm-3 pull-right'>
				<div id="custom-search-input">
                            <div class="input-group">
                            
                            
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
   
  
   <script src="formularios/js/select2/select2.full.min.js"></script>
   
   <link href="formularios/js/select2/select2.min.css" rel="stylesheet" />
   
	<!-- Edit Modal HTML -->
	<?php include("html/modal_add_transferencias.php");?>
	<!-- Edit Modal HTML -->
	<!-- Delete Modal HTML -->
	<?php include("html/modal_delete.php");?>
    <?php include("html/modal_delete_transferencias_seleccion.php");?>
    <link rel="stylesheet" href="formularios/daterangepicker/daterangepicker-bs3.css" />
    <script src="formularios/daterangepicker/daterangepicker.js"></script>
   <script src="js/VentanaCentrada.js"></script>
   
    
 <script>
 
  function exportar_pdf()
  {
  var query=$("#q").val();
  var daterange=$("#range").val();
  var motivo=$("#motivo").val();
  			
   VentanaCentrada('ajax/pdf_transferencias_cargadas.php?query='+query+'&daterange='+daterange+'&motivo='+motivo+'&action=ajax','Nueva factura','','1024','768','true');

  }
  
  function exportar_csv()
  {
  var query=$("#q").val();
  var daterange=$("#range").val();
  var motivo=$("#motivo").val();
  			
   VentanaCentrada('ajax/csv_transferencias_cargadas.php?query='+query+'&daterange='+daterange+'&motivo='+motivo+'&action=ajax','Nueva factura','','1024','768','true');

  } 

 function exportar_csv_por_motivos()
  {
  var query=$("#q").val();
  var daterange=$("#range").val();
  var motivo=$("#motivo").val();
  			
   VentanaCentrada('ajax/csv_transferencias_cargadas_por_motivos.php?query='+query+'&daterange='+daterange+'&motivo='+motivo+'&action=ajax','Nueva factura','','1024','768','true');

  } 

 
 $(function() {
 		$(".select2").select2();
	
		  load(1);
	    
		$('.select2').select2({
        dropdownParent: $('#addProductModal')
        });
					
		
	
   $('#range').daterangepicker({
		"timePicker": true,
       // "startDate":  moment().startOf('hour').add(-8, 'hour'),
       // "endDate":moment().startOf('hour'), 
		"locale": {
        "format": "MM/DD/YYYY hh:mm A",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "Desde",
        "toLabel": "Hasta",
        "customRangeLabel": "Custom",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        "firstDay": 1
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "opens": "right"
});	
		  

 	});		  
			
 function load(page){
			var query=$("#q").val();
			var daterange=$("#range").val();
			var motivo=$("#motivo").val();
			var per_page=10;
			var parametros = {"action":"ajax","page":page,'query':query,'range':daterange,'motivo':motivo,'per_page':per_page};
			$("#loader").fadeIn('slow');
			$.ajax({
				url:'./ajax/listar_transferencias.php',
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
					url: "./ajax/editar_transferencia.php",
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
		
		/*
		$('#addProductModal').on('hidden.bs.modal', '.modal', function () {
          $(this).removeData('bs.modal');
		  $(this).find('.modal-content').empty();
         });
		*/
		
		$( "#add_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/guardar_transferencia.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					load(1);
					$('#addProductModal').modal('hide');
				    //location.reload();
				  }
			});
		  event.preventDefault();
		});
		
		$( "#delete_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/eliminar_transferencia.php",
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
			  
		
	  $( "#delete_product_seleccion" ).submit(function( event ) {
		//  var parametros = $(this).serialize();
				  
		     var seleccionados = [];
			  $("input[name='check2[]']:checked").each(function ()
			  
			  {
			  
		        seleccionados.push(parseInt(this.value));
			   
			  
			  });
			  //valido que seleccione una opcion minimo
			 /* if(seleccionados.length == 0){
				alert('Seleccione al menos una opcion');
				return false;
			  }
			 */ 
			var dataString = 'seleccion1='+ seleccionados; 		  
		  		 
			$.ajax({
					type: "GET",
					url: "./ajax/eliminar_transferencia_seleccion.php",
					data:dataString,
				 
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
					alert(datos);
					load(1);
					
					$('#deleteProductModalSeleccion').modal('hide');
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
        cache: true
		
		
		
    },
    minimumInputLength: 2
	
})


</script>