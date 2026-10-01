<?php 
  
 $region=intval($_GET['id']);
 
//$region=1;

$sql="select regiones_mapa.* from regiones_mapa where region='$region'";

$query=mysqli_query($con,$sql);
$count=mysqli_num_rows($query);

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
        <script src="././jscolor/jscolor.js"></script>
        
         <script type="text/javascript" src="https://js.nicedit.com/nicEdit-latest.js"></script>
    <script>   bkLib.onDomLoaded(function() { nicEditors.allTextAreas() }); //Textarea enriquecido </script>  
        
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
		  <h1><i class='fa fa-edit'></i>Nuevo Indicador </h1>
		
		</section>
		<!-- Main content -->
        <section class="content">
		<div class="row">
        
            <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			Color Referencia: <input class="jscolor" readonly value="ab2567">
           
        
		 <div id="resultados_ajax"></div>
     
       <div class="col-sm-12">
        <br> 
			<form name="new_register" id="new_register" class="form-horizontal" method="post" enctype="multipart/form-data">


     
	  
      <div class="form-group">
		<label for="name" class="col-sm-2 control-label">T&iacute;tulo</label>
		<div class="col-sm-10">
		  <input type="text" class="form-control"  id="titulo" name="titulo" placeholder="Ingresa el T&iacute;tulo" required>
			 
		</div>
	  </div>
    
<div class="form-group">
<label for="indicador_gestion" class="col-sm-2 control-label">Indicador Gesti&oacute;n</label>
	<div class="col-sm-10">
       <select class="form-control" name="indicador_gestion" id="indicador_gestion">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select indicadores_gestion.*,metas_gestion.codigo_meta from metas_gestion,indicadores_gestion where metas_gestion.id=indicadores_gestion.id_meta order by metas_gestion.codigo_meta,indicadores_gestion.codigo_indicador");
							
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['codigo_indicador'].": ".$rw['nombre_indicador'];
							 ?>
							<option value="<?php echo $id1;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
		
	</div>

</div>
  
<div class="form-group">
<label for="id_accion" class="col-sm-2 control-label">Acci&oacute;n</label>
	<div class="col-sm-10">
       <select class="form-control" name="id_accion" id="id_accion">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select acciones.*,metas_gestion.codigo_meta from metas_gestion,acciones where metas_gestion.id=acciones.id_meta order by metas_gestion.codigo_meta,acciones.numero_accion");
							
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['numero_accion'].": ".$rw['nombre'];
							 ?>
							<option value="<?php echo $id1;?>"><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
		
	</div>

</div>
        
	  
<div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n</label>
	<div class="col-sm-10">
		<textarea id="descripcion" cols="10" name="descripcion" class="form-control"></textarea>
        
         <input type="hidden" value="<?php echo $region;?>" name="region1" id="region1">
		
	</div>
</div>

<?php 
   
	while($rw = mysqli_fetch_array($query)){
     $provincia=$rw['id'];
	 $color=$rw['color_default'];
	 $url=$rw['url'];
	 	 	  
	  echo '<div class="form-group">
	<label for="name" class="col-sm-2 control-label">'. $provincia .'</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="'.$provincia.'" name="'.$provincia.'" placeholder="Ingresa valor o descripci&oacute;n" value="1">
		
	</div>
	
	<label for="name" class="col-sm-2 control-label">Color</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="color_'.$provincia.'" name="color_'.$provincia.'" placeholder="Ingresa color #xxxxxx" value="'.$color.'">
		
	</div>
	
	<label for="url" class="col-sm-1 control-label">url</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="link_'.$provincia.'" name="link_'.$provincia.'" placeholder="Ingresa una url" value="'.$url.'">	</div>
	
</div>'; 
	 
	 }

?>

 <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                      <button type="submit" class="btn btn-primary actualizar_datos">Guardar datos</button>
                    </div>
                  </div>

</form>

</div></div>

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
$('#new_register').submit(function( event ) {
   
	 $('#actualizar_datos').attr("disabled", true);
	 var editor1 =  nicEditors.findEditor('descripcion').getContent();
     var parametros = $(this).serialize() + '&editor1=' + editor1;
	
		 $.ajax({
				type: "POST",
				url: "ajax/registro/agregar_indicadores.php",
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
				
			  }
		});
	  event.preventDefault();
 
})
</script>

