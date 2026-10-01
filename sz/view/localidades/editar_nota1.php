<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	/* Connect To Database*/
	
	
	//require_once ("../../config/db.php");
	//require_once ("../../config/conexion.php");
	$user_id = $_SESSION['user_id'];
    $fecha=date("d-m-Y H:i"). ": ";
  if (isset($_GET["id"])){
	$id_localidad=$_GET["id"];
	$localidad=$_GET["localidad"];
	$id_localidad=intval($id_localidad);
	$sql="select * from anotaciones where id_localidad='$id_localidad' and usuarios='$user_id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	$anotacion="";
	$recordatorio="";
	$ultima_actualizacion="";
	if ($num==1){
	 $rw=mysqli_fetch_array($query);
	 $anotacion=$rw['anotacion'];
	 $id_anotacion=$rw['id'];
	 $id_localidad=$rw['id_localidad'];
	 $recordatorio=$rw['recordatorio'];
	 
	 $ultima_actualizacion=$rw['ultima_actualizacion'];
	}
	else
	{
	  
	$recordatorio="";
	$sql1 = "INSERT INTO anotaciones (recordatorio, anotacion,id_localidad,usuarios) VALUES('".$recordatorio."','".$fecha."','".$id_localidad."','".$user_id."')";
	$query1=mysqli_query($con,$sql1);
	}
?>
   
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
        <!-- Content Header (Page header) -->
		<?php if ($permisos_ver==1){?>
        <section class="content-header">
		  <h1><i class='fa fa-edit'></i> Notas de <?php echo " ". $localidad; ?> </h1>
		
		</section>
		<!-- Main content -->
        <section class="content">
		<div class="row">
        
        <div class="col-md-12">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        
        
        
		 <div id="resultados_ajax"></div>
       <div class="box-body">
        <div class="col-md-12">
			<form name="update_register" id="update_register" class="form-horizontal" method="post" enctype="multipart/form-data">
			  <div class="form-group">
                        <label for="expediente" class="col-sm-2 control-label">Recordatotio</label>
                        <div class="col-sm-10">
         
     <input type="text" class="form-control"  id="recordatorio" name="recordatorio" placeholder="Ingresa un recordatotio si lo desea" value="<?php echo $recordatorio;?>">
     
     	<input type="hidden" value="<?php echo $id_localidad;?>" name="id_localidad" id="id_localidad">
        <input type="hidden" value="<?php echo $user_id;?>" name="user_id" id="user_id">
     
     </div>
     
     
     
     </div>
     
     
     
      
    <div class="form-group">
          <div class="col-sm-12">
   <textarea class="form-control" name="anotacion" id="anotacion" rows="15" cols="80" ><?php echo $anotacion ."\r\n".$fecha;?> 
   </textarea>
  
    </div></div>
           
       <div class="form-group">
                        <label for="expediente" class="col-sm-3 control-label">Actualizaci&oacute;n</label>
           <div class="col-sm-3">
    
         <?php 
		 if($ultima_actualizacion<> "")
		  $ultima_actualizacion= date('d/m/y H:m:s', strtotime($ultima_actualizacion));
	     ?> <input type="text" class="form-control" readonly="readonly" value="<?php  echo $ultima_actualizacion; ?> " />
	          
      </div>
       
       
                    <div class="col-sm-offset-2 col-sm-4">
                      <button type="submit" class="btn btn-primary actualizar_datos">Guardar datos</button>
                    </div>
                  </div>
      
 <?php } ?>
		   
		  </form>
               
        </div>   <!-- /.col 12 -->
      
       </div>   <!-- /.Box Body -->
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
	<?php include("js.php");?>
     
  <script>
		$( "#update_register" ).submit(function( event ) {
		  $('.actualizar_datos').attr("disabled", true);
		  var parametros = $(this).serialize();
		  $.ajax({
				type: "POST",
				url: "./ajax/modificar/notas.php",
				data: parametros,
				 beforeSend: function(objeto){
					$("#resultados_ajax").html("Mensaje: Cargando...");
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
      
