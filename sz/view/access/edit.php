<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: facturacionlp.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();

	/* Connect To Database*/

	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from users where user_id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$fullname=$rw['fullname'];
	$user_name=$rw['user_name'];
	$user_email=$rw['user_email'];
	$user_group_id=$rw['user_group_id'];
	$status=$rw['status'];
	$avatar=$rw['avatar'];
	$ministerio=$rw['ministerio'];
	$_SESSION['usuario_id']=$id;
	
	}
	}	
	else {exit;}	
?>

<!DOCTYPE html>
<html>
  <head>
	<?php include("head.php");?>
  
<style>
/* Fuerza visualización correcta del dropdown de autocomplete */
.ui-autocomplete {
    position: absolute;
    z-index: 9999 !important;
    background: #fff;
    border: 1px solid #ccc;
    max-height: 250px;
    overflow-y: auto;
    overflow-x: hidden;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    padding: 0;
    margin: 0;
    list-style: none;
}

.ui-menu-item {
    display: block !important;
    padding: 8px 12px !important;
    margin: 0 !important;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    line-height: 1.5 !important;
}

.ui-menu-item:last-child {
    border-bottom: none;
}

.ui-menu-item-wrapper {
    display: block !important;
    width: 100%;
    height: auto;
}
</style>
   
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
		  <h1><i class='fa fa-edit'></i> Editar Usuario</h1>
		
		</section>
		<!-- Main content -->
      
        <section class="content">
   
        <!-- /.col -->
         <div class="col-md-9">
		<form name="update_register" id="update_register" class="form-horizontal" method="post" enctype="multipart/form-data">
        
           
       <div class="nav-tabs-custom">
       
       
            <ul class="nav nav-tabs">
              <li class="active"><a href="#details" data-toggle="tab" aria-expanded="false">Datos Generales</a></li>
             <li class=""><a href="#usuarios_vinculados" data-toggle="tab" aria-expanded="false">Otros</a></li>
                          
            </ul>
    
     <div class="tab-content">
      
       <div id="resultados_ajax"></div>

         <div class="tab-pane active" id="details">
                
     

	  <div class="form-group">
		<label for="fullname" class="col-sm-3 control-label">Nombre completo</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Ingresa el nombre completo del usuario" value="<?php echo $fullname;?>" required>
			<input type="hidden" value="<?php echo $id;?>" name="user_id" id="user_id">
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="user_name" class="col-sm-3 control-label">Usuario</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Ingresa el usuario" value="<?php echo $user_name;?>" pattern="[a-zA-Z0-9]{4,64}" required>
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="user_email" class="col-sm-3 control-label">Email</label>
		<div class="col-sm-6">
		  <input type="text" class="form-control" id="user_email" name="user_email" placeholder="example@gmail.com" value="<?php echo $user_email;?>" required>
		</div>
	  </div>

	  <div class="form-group">
		<label for="user_group_id" class="col-sm-3 control-label">Grupo de permisos</label>
		<div class="col-sm-6">
			<select class="form-control" name="user_group_id" id="user_group_id">
				<?php
				$sql_grupos="select * from user_group";
				$query_grupos=mysqli_query($con,$sql_grupos);
				while ($rw_grupos=mysqli_fetch_array($query_grupos)){
					?>
					<option value="<?php echo $rw_grupos['user_group_id'];?>" <?php if ($user_group_id==$rw_grupos['user_group_id']){echo "selected";}else{echo"";}?>><?php echo $rw_grupos['name'];?></option>	
					<?php
				}
				?>
			</select> 
		</div>
	  </div>
	  
	  <div class="form-group">
		<label for="status" class="col-sm-3 control-label">Estado</label>
		<div class="col-sm-6">
		  <select class="form-control" name="status" id="status">
			<option value="1" <?php if ($status==1){echo "selected";}?>>Activo</option>
			<option value="2" <?php if ($status==2){echo "selected";}?>>Inactivo</option>
		  </select>
		</div>
	  </div>
    
     <div class="form-group">
     <label for="ministerio" class="col-sm-3 control-label">Ministerio</label>

                    <div class="col-sm-6">
                      <select class="form-control" name="ministerio" id="ministerio">
                      	<option value="">Seleccione Ministerio </option>					
						<?php 
							$sql=mysqli_query($con,"select * from ministerios");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=$rw['denominacion'];
							    if ($id==$ministerio){$selected1="selected";}else{$selected1="";}
							?>
                            
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?> </option>
							<?php
							}
						?>
					  </select>
                    </div> 
             </div>         

<div class="form-group">
	   <label for="id_imagen" class="col-sm-3 control-label">Imagen</label>
          <div class="col-sm-6">
             <div id="load_img">
               <img class="img-responsive" height="150px" width="150px" src="<?php echo $avatar;?>" alt="Imagen">
             </div>
             
             <div class="col-sm-2">
                      <input type="file" name="imagefile" id="imagefile" onChange="upload_image(<?php echo $id; ?>);">
              </div>
             
          </div>
  </div>
  
   <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                      <button type="submit" class="btn btn-primary actualizar_datos">Guardar datos</button>
                    </div>
                  </div>
  
  </div>  <!--tab-pane -->
   
    <!-- agregar otros usuarios vinculados a agenda desde acá
    
       <!-- /.tab-pane -->
               
               <div class="tab-pane" id="usuarios_vinculados">
               
                   <div class="box-body">
                      <div class="row">
                     
                     <div class="ui-widget"> 
                         <div class="col-md-2">
                             <input type="text"  class="form-control"  name="codigo_barra" id="codigo_barra" value="" placeholder="ID"  onBlur="if (pasoenter==0 && this.value !='') BuscarCodigo(this.value)" onFocus="pasoenter=0" >
                         </div>  
                           
                             <input type="hidden" id="product_id_complementos"> 
                           
                         <div class="col-md-8">      
                           <input id="descripcion" tabindex="1" name="descripcion" class="form-control" placeholder="Ingrese parte del nombre del usuario">
                          </div> 
                      
                     <div> 
                 
                <button onClick="agregar(document.getElementById('product_id_complementos').value)" class="btn btn-default" type="button"><i class='fa fa-plus'></i> Pasar</button>      
        
         <div id="resultados" class='col-md-12' style="margin-top:4px"></div><!-- Carga los datos ajax -->
        
         <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                      <button type="submit" class="btn btn-primary actualizar_datos">Guardar datos</button>
                    </div>
           </div>
           
          
                     
    </div>  <!--tab-pane -->
     </div>  <!--tab-content -->
      </div>  <!--nav-tabs-custom -->                   
  
    </form>
         
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
   </body> 
	<?php include("js.php");?>
       
</html>   
<script src="plugins/select2/select2.full.min.js"></script>
	<script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
   <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
     <link href="plugins/select2/select2.min.css" rel="stylesheet" />
      
           
 <script>

 
$(function() {
    $("#resultados").load("./ajax/agregar_usuarios_complementarios.php");

    $("#descripcion").autocomplete({
        appendTo: "body", // <-- Esto es clave
        source: function(request, response) {
            $.getJSON(
                "view/access/buscar_descripcion_usuarios.php",
                { term: request.term },
                response
            );
        },
        minLength: 2,
        select: function(event, ui) {
            event.preventDefault();
            $('#codigo_barra').val(ui.item.codigo);
            $('#descripcion').val(ui.item.descripcion);
            $('#product_id_complementos').val(ui.item.product_id);
        }
    });
});


function BuscarCodigo(id)
   { 
  		$.ajax({
        type: "POST",
        url: "view/access/buscar_codigo_usuarios.php",
        data: "id="+id,
		success: function(recibe){
		  var datos =  $.parseJSON(recibe);
		 
		  document.getElementById('descripcion').value=datos[0].descripcion;
		  document.getElementById('product_id_complementos').value=datos[0].product_id;
		  
		  // ver focus
			}
		});

	 }

 
 function agregar(id)
		{
				
		$.ajax({
        type: "POST",
        url: "./ajax/agregar_usuarios_complementarios.php",
        data: "id="+id,
		 beforeSend: function(objeto){
			$("#resultados").html("Mensaje: Cargando...");
		  },
        success: function(datos){
		$("#resultados").html(datos);
		
		  
		  document.getElementById('codigo_barra').value="";
		  document.getElementById('descripcion').value="";
		  document.getElementById('product_id_complementos').value="";
		 
		
		//return false;
		
		
		
		}
			});
			
			
			
		}	
	
 function eliminar(id)
		{
		
		$.ajax({
        type: "GET",
        url: "./ajax/agregar_usuarios_complementarios.php",
        data: "id="+id,
		 beforeSend: function(objeto){
			$("#resultados").html("Mensaje: Cargando...");
		  },
        success: function(datos){
		$("#resultados").html(datos);
		
		}
			});

		}


$( "#update_register" ).submit(function( event ) {
  $('#actualizar_datos').attr("disabled", true);
 var parametros = $(this).serialize();
	 $.ajax({
			type: "POST",
			url: "./ajax/modificar/usuario.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax").html("Enviando...");
			  },
			success: function(datos){
				$("#resultados_ajax").html(datos);
				$('.actualizar_datos').attr("disabled", false);
				window.setTimeout(function() {
				$(".alert").fadeTo(500, 0).slideUp(500, function(){
				$(this).remove();});}, 5000);
		  }
	});
  event.preventDefault();
});

</script>