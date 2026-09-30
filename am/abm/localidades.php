<html>
<head> 

 <script src="js/VentanaCentrada.js"></script>
 <script src="formularios/js/select2/select2.full.min.js"></script>
 <link href="formularios/js/select2/select2.min.css" rel="stylesheet" />
 
</head>
<body>
<div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                  <?php 
				  $localidades_menu=1;
				  include("menu.php");?>
                </div>
            
            </div>
            
            	<div class="col-sm-4">
						 <a href="#GuardarSelecciontModal" class="btn btn-danger" data-toggle="modal"><i class="material-icons">save</i> <span>Grabar</span></a>
					</div>
             
                                     
			<div class='col-sm-4 pull-right'>
				<div id="custom-search-input">
                       
                            <div class="input-group col-md-12">
                             
                                <input type="text" class="form-control" placeholder="Buscar"  id="q1" onKeyUp="load(1);" />
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
</body>
</html>   
  
	
   
     <?php include("html/modal_add_transferencias_temporal.php");?>
     <?php include("html/modal_add_compromisos_temporal.php");?>
    <?php include("html/modal_guardar_seleccion.php");?> 
    
 <script>
     var time = new Date().getTime();
     $(document.body).bind("mousemove keypress", function(e) {
         time = new Date().getTime();
     });

     function refresh() {
         if(new Date().getTime() - time >= 60000) 
             window.location.reload(true);
         else 
             setTimeout(refresh, 10000);
     }

     setTimeout(refresh, 10000);
</script>     
	
 <script>
 
 $('#GuardarSelecciontModal').on('show.bs.modal', function (event) {
		 // var button = $(event.relatedTarget) // Button that triggered the modal
		 // var id = button.data('id') 
		 // $('#delete_id').val(id)
		 
		})
 
$( "#guardar_seleccion" ).submit(function( event ) {
   $('#guardar_datos').attr("disabled", true);
   var parametros="";
   $.ajax({
	type: "POST",
	url: "./ajax/guardar_transferencias_seleccionasdas.php",
	data: parametros,
	beforeSend: function(objeto){
	$("#resultados").html("Grabando...");
	  },
	 success: function(datos){
	 window.open('ajax/pdf_transferencias_seleccionasdas.php', '_blank');	
	 $('#guardar_datos').attr("disabled", false);
	 $("#resultados").html(datos);
	 load(1);
	 $('#GuardarSelecciontModal').modal('hide');
	 }
	});
  
 
	
 event.preventDefault();
});
    
   $(function() {
         
		$(".select2").select2();
		  load(1);
	   
		$('.select2').select2({
         dropdownParent: $('#AgregarTransferenciaTemporalModal')
	    });	  
		  
	 });
		
	 function load(page){
	  
			var query1=$("#q1").val();
			var per_page=80;
			var parametros = {"action":"ajax","page":page,'query1':query1,'per_page':per_page};
			$("#loader").fadeIn('slow');
			$.ajax({
				url:'./ajax/listar_localidades.php',
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
		
					
		$( "#add_product" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "POST",
					url: "./ajax/insertar_en_array.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html("");
										
				    load(1);
					$('#AgregarTransferenciaTemporalModal').modal('hide');
					
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
	
});
		
		$( "#add_product2" ).submit(function( event ) {
		  var parametros = $(this).serialize();
			$.ajax({
					type: "GET",
					url: "./ajax/insertar_en_array_compromisos.php",
					data: parametros,
					 beforeSend: function(objeto){
						$("#resultados").html("Enviando...");
					  },
					success: function(datos){
					$("#resultados").html(datos);
				    load(1);
					$('#AgregarCompromisoTemporalModal').modal('hide');
					//location.reload();
				  }
			});
		  event.preventDefault();
		});
		
 
 
			
</script>   
	