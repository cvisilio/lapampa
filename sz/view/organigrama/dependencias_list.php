<!DOCTYPE html>
<html>
  <head>
   
	<?php include("head.php");?>
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
       <div class="content-header">
         <h3 class="modal-title">Organigrama</h3>
      </div>
        <!-- Content Header (Page header) -->
		<?php if ($permisos_ver==1){?>
        <section class="content-header">
          <div class="box">
            <div class="box-header with-border">
              
                 
                  <div class="col-md-4">
                    <h3 class="box-title">
                     <div id="cuenta_parent"> </div> 
                   </h3>
                  </div> <!-- col-md -->
                 
                 <div class="col-md-1"> 
                <div> <button type="button" class="btn btn-primary" style="visibility:hidden" id="btn_pasar1" disabled >pasar a</button></div>
                
                  </div> <!-- col-md -->
                  
                  <div class="col-md-4"> 
                     <h3 class="box-title">
                   <div id="cuenta_parent2"> </div> 
                    </h3>
                   </div> <!-- col-md -->
                 
                    <div class="col-md-1">
                         
                           <div> 	<button style="visibility:hidden" class="btn btn-success" id="btn_confirmar_pasar" disabled type="button" onclick='pasar_cuenta();'><i class='fa fa-check'></i></button> </div>
                          
                       </div> <!-- col-md -->
                 
                 <div class="col-md-1">
                         
                           <div> 	<button style="visibility:hidden" class="btn btn-info" id="btn_cancelar" disabled type="button" onclick='cancelar();'><i class='fa fa-times'></i></button> </div>
                          
                       </div> <!-- col-md -->
                   
            </div>
        
            <div class="box-body">
				<div class="row">
                    <div class="col-md-12 col-sm-12">                      
                        
                     <div class="box-background">
                       <div class="box-body">
                        <div class="row">
                         <div class="col-md-4">
                          <label>Denominaci&oacute;n</label>
                          
                          <input type="text" class="form-control" disabled id='nombre' name="nombre"> 
                        
                        </div> <!-- col-md -->
                         
                         <div class="col-md-2">
                           <label>N&uacute;mero</label>
                          <input type="text" class="form-control" disabled id='codigo' name="codigo"> 
                          
                           </div> <!-- col-md -->
                          
                           <div class="col-md-2">
                           <label>Criterio 1</label>
                           <br>
                           <div align="center" class="panel panel-primary">
                          <label class="radio-inline"><input  type="radio" name="tipo_saldo" >Si</label>
<label class="radio-inline"><input  type="radio" name="tipo_saldo" >No</label> 
                          </div>
                           </div> <!-- col-md -->
                          
                           <div class="col-md-2">
                          <label>Criterio 2</label>
                          <div align="center" class="panel panel-primary">
                          
                         <label class="radio-inline"><input type="radio" id="" name="imputable" >S&iacute;</label>
<label class="radio-inline"><input type="radio"  name="imputable" >No</label> 
                          </div>
                          
                           </div> <!-- col-md --> 
             
                                               
                         <div class="col-md-1">
                          <label></label> 
                           <div> 	<button class="btn btn-success" id="btn_confirmar" disabled style="visibility:hidden" type="button" onclick='editar_cuenta();'><i class='fa fa-check'></i></button> </div>
                          
                          </div> <!-- col-md -->
                          
                           <div class="col-md-1">
                          <label></label> 
                           <div> 	<button style="visibility:hidden" class="btn btn-success" id="btn_confirmar_agregar" disabled type="button" onclick='agregar_cuenta();'><i class='fa fa-check'></i></button> </div>
                          
                          </div> <!-- col-md -->
                        
                        
                        
                          </div> <!-- row -->
					
                        
                    
					<input type='hidden' id='id' value='0'>
                    <input type='hidden' id='id2' value='0'> <!-- se almacenara el id de la cuenta a en caso de querer pasar una cuanta (id) hacia esta (id2) -->
                    			      
                  <div> <br> <?php if ($permisos_editar==1){?>
                          <button type="button"  class="btn btn-primary" onclick='agregar();'><i class='fa fa-plus'></i></button>
                          <button id="btn_editar" class="btn btn-warning" type="button" disabled onclick='editar();'><i class='fa fa-edit'></i></button> 
							 
							<button type="button" id="btn_eliminar" class="btn btn-danger" disabled onclick='eliminar_cuenta();'><i class='fa fa-minus'></i></button>
                            
                            <button type="button" id="btn_pasar" class="btn btn-dark" disabled onclick='pasar();'><i class='fa fa-exchange'></i></button>
                            
							<?php }?> </div> 
                
               <div id="div_agregar"> 
                              
               </div>
              	<div id="loader" class="text-center"></div>
						
           </div> <!-- box-body -->
         </div> <!-- box-background -->
         
         </div> <!-- col-md-12 -->
         </div> <!-- row -->
         </div> <!-- box-body -->
        </div>  <!-- box --> 
		</section>
		<!-- Main content -->
        	<div id="resultados_ajax"></div>
			<div class="outer_div"></div><!-- Datos ajax Final -->         
        
        <section class="content">
          <div class="row">
            <div class="col-md-6 col-sm-8">
              <div class="input-group">
                <input type="text" id="buscador_dependencia" class="form-control" placeholder="Buscar ministerio o dependencia">
                <span class="input-group-btn">
                  <button type="button" class="btn btn-default" onclick="buscarDependencia();">
                    <i class="fa fa-search"></i>
                  </button>
                </span>
              </div>
            </div>
          </div>
          <br>
		
          <div align="left" id="myDIV"></div>
          
          <div id="treeview"></div>
         
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
  
  <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" />
  <style>
    #treeview .search-result {
      color: #b30000 !important;
      font-weight: 700 !important;
    }
  </style>
  
  
    
	<script src="dist/js/VentanaCentrada.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.js"></script>
	
  </body>
</html>
<script>
	
 function eliminar_cuenta(id)
		{
if(confirm('Esta acción  eliminará de forma permanente la dependencia \n\n Desea continuar?')){	
		var id=document.getElementById('id').value;		
		$.ajax({
        type: "GET",
        url: "view/organigrama/agregar_dependencia.php",
        data: "id="+id,
		 beforeSend: function(objeto){
		$("#loader").html("<img src='./img/ajax-loader.gif'>");
				  },
					success:function(data){
						$(".outer_div").html(data).fadeIn('slow');
						cargar();
						$("#loader").html("");
						window.setTimeout(function() {
						$(".alert").fadeTo(500, 0).slideUp(500, function(){
						$(this).remove();});}, 5000);
		
		}
			});

		}
}



function editar()
{
 document.getElementById('nombre').disabled = false;
 document.getElementById('codigo').disabled = false;
document.getElementsByName("tipo_saldo")[0].disabled = false;
 document.getElementsByName("tipo_saldo")[1].disabled = false;
 document.getElementsByName("imputable")[0].disabled = false;
 document.getElementsByName("imputable")[1].disabled = false;
 
 document.getElementById("btn_confirmar").style.visibility = "visible";
 document.getElementById("btn_confirmar").disabled= false;
 document.getElementById("btn_confirmar_agregar").style.visibility = "hidden";
 document.getElementById("btn_confirmar_agregar").disabled= true;
 
 
 document.getElementById('nombre').focus();	
}



function agregar()
{
 var name= document.getElementById('nombre').value;
 $("#cuenta_parent").html(name); 
 document.getElementById('nombre').value="";
 document.getElementById('nombre').disabled = false;
 document.getElementById('codigo').disabled = false;
 document.getElementsByName("tipo_saldo")[0].disabled = false;
 document.getElementsByName("tipo_saldo")[1].disabled = false;
 document.getElementsByName("imputable")[0].disabled = false;
 document.getElementsByName("imputable")[1].disabled = false;

 document.getElementById("btn_eliminar").disabled= true;
 document.getElementById("btn_editar").disabled= true;
 document.getElementById("btn_confirmar").style.visibility = "hidden";
 document.getElementById("btn_confirmar").disabled= true;
 document.getElementById("btn_confirmar_agregar").style.visibility = "visible";
 document.getElementById("btn_confirmar_agregar").disabled= false;
 
 
 document.getElementById('nombre').focus();	
}

function agregar_cuenta()
{
      var id=0;
	  id=document.getElementById('id').value;
	  var codigo=document.getElementById('codigo').value;
	  var nombre=document.getElementById('nombre').value;
	  
	  var tipo_saldo="D";
	  var imputable=0;
	  
	  if(document.getElementsByName("tipo_saldo")[0].checked)
       tipo_saldo= "D";
      
	  if(document.getElementsByName("tipo_saldo")[1].checked)
       tipo_saldo= "H";
      
	  	  
      if(document.getElementsByName("imputable")[0].checked)
       imputable= 1;
      
	  if(document.getElementsByName("imputable")[1].checked)
       imputable= 0;
	 
	 
	  	
      $.ajax({
      type: "POST",
      url: "view/organigrama/agregar_dependencia.php",
      data: "id="+id+"&codigo="+codigo+"&nombre="+nombre+"&tipo_saldo="+tipo_saldo+"&imputable="+imputable,
	  beforeSend: function(objeto){
	  $("#loader").html("Enviando...");
	  document.getElementById("btn_confirmar_agregar").disabled= true;
				  },
					success:function(data){
						$(".outer_div").html(data).fadeIn('slow');
						cargar();
						$("#loader").html("");
						window.setTimeout(function() {
						$(".alert").fadeTo(500, 0).slideUp(500, function(){
						$(this).remove();});}, 5000);
		
		}
			});



}

function editar_cuenta()
{
      var id=document.getElementById('id').value;
	  var codigo=document.getElementById('codigo').value;
	  var nombre=document.getElementById('nombre').value;
	  
	  var tipo_saldo;
	  var imputable;
	  
	  if(document.getElementsByName("tipo_saldo")[0].checked)
       tipo_saldo= "D";
      
	  if(document.getElementsByName("tipo_saldo")[1].checked)
       tipo_saldo= "H";
      
	  	  
      if(document.getElementsByName("imputable")[0].checked)
       imputable= 1;
      
	  if(document.getElementsByName("imputable")[1].checked)
       imputable= 0;
 	   	  
	  	  	
      $.ajax({
      type: "POST",
      url: "view/organigrama/editar_dependencia.php",
      data: "id="+id+"&codigo="+codigo+"&nombre="+nombre+"&tipo_saldo="+tipo_saldo+"&imputable="+imputable,
	  beforeSend: function(objeto){
	 $("#loader").html("Enviando...");
	  document.getElementById("btn_confirmar").disabled= true;
	  },
	  success:function(data){
	  $(".outer_div").html(data).fadeIn('slow');
	  cargar();
	  $("#loader").html("");
	  window.setTimeout(function() {
	  $(".alert").fadeTo(500, 0).slideUp(500, function(){
	  $(this).remove();});}, 5000);
	  
		  
		}
			});



}	


function pasar()
{


 var name= document.getElementById('nombre').value;
 $("#cuenta_parent").html(name);
 document.getElementById("btn_eliminar").disabled= true;
 document.getElementById("btn_editar").disabled= true;
 document.getElementById("btn_confirmar").style.visibility = "hidden";
 document.getElementById("btn_confirmar").disabled= true;
 document.getElementById("btn_confirmar_agregar").style.visibility = "hidden";
 document.getElementById("btn_confirmar_agregar").disabled= true;
  
 document.getElementById("btn_pasar1").style.visibility = "visible";
 document.getElementById("btn_pasar1").disabled= false;
  
}

function pasar_cuenta()
{
    
      var id=0;
	  id=document.getElementById('id').value;
	  var id2=0;
	  id2=document.getElementById('id2').value;
	 
	 if (!id > 0)
	  {
	  alert("Origen no puede estar vacio");
	  return;
	  
	  }
	
	 if (!id2 > 0)
	  {
	  alert("Destino no puede estar vacio");
	  return;
	  
	  } 
	
	 if(confirm("Está seguro de querer pasar la cuenta " + 	 $("#cuenta_parent").text() + " a la cuenta " + $("#cuenta_parent2").text())){; 
	
	 	
      $.ajax({
      type: "POST",
      url: "view/organigrama/pasar_cuenta.php",
      data: "id="+id+"&id2="+id2,
	  beforeSend: function(objeto){
	  $("#loader").html("Enviando...");
	  document.getElementById("btn_confirmar_pasar").disabled= true;
	  document.getElementById("btn_cancelar").style.visibility = "visible";
	  document.getElementById("btn_cancelar").disabled= false;
				  },
					success:function(data){
						$(".outer_div").html(data).fadeIn('slow');
						cargar();
						$("#loader").html("");
						window.setTimeout(function() {
						$(".alert").fadeTo(500, 0).slideUp(500, function(){
						$(this).remove();});}, 5000);
		
		}
			});

 }

}
	
$(document).ready(function(){
 
 cargar();
 
});


function cancelar(){
    $("#cuenta_parent").html("");
	$("#cuenta_parent2").html("");
	document.getElementById("id").value =0;
	document.getElementById("id2").value =0;
	
	document.getElementById("btn_confirmar_pasar").style.visibility = "hidden";
    document.getElementById("btn_confirmar_pasar").disabled= true;
	
	document.getElementById("btn_pasar1").disabled= true;
	document.getElementById("btn_pasar1").style.visibility = "hidden";
	
	document.getElementById("btn_cancelar").style.visibility = "hidden";
    document.getElementById("btn_cancelar").disabled= true;
 
}

function cargar(){

$.ajax({ 
   url: "view/organigrama/dependencia.php",
   method:"POST",
   dataType: "json",       
   success: function(data)  
   {
   document.getElementById("btn_eliminar").disabled= true;
   document.getElementById("btn_editar").disabled= true;
  $('#treeview').treeview({data: data});
  $('#treeview').off('nodeSelected').on('nodeSelected', function(event, data) {
   
   if (document.getElementById("btn_pasar1").disabled== false)
    {
	
	document.getElementById("id2").value = data.id; // aculto
    $("#cuenta_parent2").html(data.name);
	document.getElementById("btn_confirmar_pasar").style.visibility = "visible";
    document.getElementById("btn_confirmar_pasar").disabled= false;
	
	 document.getElementById("btn_cancelar").style.visibility = "visible";
	 document.getElementById("btn_cancelar").disabled= false;
		
	}
   else   
	{
	document.getElementById("id").value = data.id; // aculto
	$("#cuenta_parent").html("");
	$("#cuenta_parent2").html("");
	document.getElementById("id2").value =0;
	
	document.getElementById("btn_confirmar_pasar").style.visibility = "hidden";
    document.getElementById("btn_confirmar_pasar").disabled= true;
	
	document.getElementById("btn_pasar1").disabled= true;
	document.getElementById("btn_pasar1").style.visibility = "hidden";
	
	 document.getElementById("btn_cancelar").style.visibility = "hidden";
	 document.getElementById("btn_cancelar").disabled= true;
	}
	
	document.getElementById("nombre").value = data.name;
	document.getElementById("codigo").value = data.codigo;
	if(data.tipo_saldo=="D")
	 document.getElementsByName("tipo_saldo")[0].checked = true;
	else
	document.getElementsByName("tipo_saldo")[1].checked = true;
	
	if(data.imputable==1)
	 document.getElementsByName("imputable")[0].checked = true;
	else
	document.getElementsByName("imputable")[1].checked = true;
			
	$("#loader").html("");	
		
	document.getElementById('nombre').disabled = true;
    document.getElementById('codigo').disabled = true;
    document.getElementsByName("tipo_saldo")[0].disabled = true;
	document.getElementsByName("tipo_saldo")[1].disabled = true;
    document.getElementsByName("imputable")[0].disabled = true;
	document.getElementsByName("imputable")[1].disabled = true;

    	
	document.getElementById("btn_eliminar").disabled= false;
    document.getElementById("btn_editar").disabled= false;
	document.getElementById("btn_pasar").disabled= false;
	document.getElementById("btn_confirmar").disabled= true;
    document.getElementById("btn_confirmar").style.visibility = "hidden";
	document.getElementById("btn_confirmar_agregar").style.visibility = "hidden";
	document.getElementById("btn_confirmar_agregar").disabled= true;

   
	});
   buscarDependencia();
   }   
 });


}

function buscarDependencia(){
 var texto = $("#buscador_dependencia").val();
 $("#treeview").treeview('clearSearch');

 if (!texto || $.trim(texto) === "") {
  return;
 }

 var resultados = $("#treeview").treeview('search', [texto, {
  ignoreCase: true,
  exactMatch: false,
  revealResults: true
 }]);

 if (resultados && resultados.length > 0) {
  var primerResultado = document.querySelector("#treeview .search-result");
  if (primerResultado && typeof primerResultado.scrollIntoView === "function") {
   primerResultado.scrollIntoView({ behavior: "smooth", block: "center" });
  }
 }
}

</script>