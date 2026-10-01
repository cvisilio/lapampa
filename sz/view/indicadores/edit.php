<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	
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
		  <h1><i class='fa fa-edit'></i>Editar Indicador </h1>
		
		</section>
		<!-- Main content -->
        <section class="content">
		<div class="row">
        
            <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			          
        
		 <div id="resultados_ajax"></div>
     
       <div class="col-sm-12">
        <br> 
        
			<form name="update_register" id="update_register" class="form-horizontal" method="post" enctype="multipart/form-data">     

<?php	
	/* Connect To Database*/
		
 if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	
	$sql="select indicadores.titulo,indicadores.indicador_gestion,indicadores.ambito,indicadores.multiplicador_radio_circulo, indicadores.descripcion,indicadores_provincias.text, indicadores_provincias.color, indicadores_provincias.url, indicadores_provincias.provincia,indicadores.accion from indicadores, indicadores_provincias where indicadores.id=indicadores_provincias.indicador and indicadores_provincias.indicador='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	
  if ($num>0){
    $i=0;
	while($rw = mysqli_fetch_array($query)){
	   
		if($i==0)
		{ 
			$titulo=$rw['titulo'];
	        $descripcion=$rw['descripcion'];
			$i=1;
			$region=$rw['ambito'];
			$multiplicador1=$rw['multiplicador_radio_circulo'];
			$indicador_gestion=$rw['indicador_gestion'];
			$id_accion=$rw['accion'];
		}	
	 $provincia=$rw['provincia'];
	 $text=$rw['text'];
	 $color=$rw['color'];
	 $url=$rw['url'];
	 
	 
	 
?>	

 
	 
<?php	 echo '<div class="form-group">
	<label for="name" class="col-sm-2 control-label">'. $provincia .'</label>
	<div class="col-sm-2">
		<input type="text" class="form-control" id="'.$provincia.'" name="'.$provincia.'" placeholder="Ingresa valor o descripci&oacute;n" value="'.$text.'">
		
	</div>
	
	<label for="color1" class="col-sm-2 control-label" style="color:'. $color.'">Color</label>
	<div class="col-sm-2">
		<input style="color:'. $color.'" type="text" class="form-control" id="color_'.$provincia.'" name="color_'.$provincia.'" placeholder="Ingresa color #xxxxxx" value="'.$color.'">	</div>
		
		<label for="url" class="col-sm-1 control-label">url</label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="link_'.$provincia.'" name="link_'.$provincia.'" placeholder="Ingresa una url" value="'.$url.'">	</div>

</div>'; 
	 
	
	
	}
 }
}	
	else {exit;}
?>


<div class="form-group">
	<label for="name" class="col-sm-2 control-label">Degradado</label>
	<div class="col-sm-1">
		<input type="checkbox" class="custom-control-input" id="degradado" name="degradado">
        
	</div>
    <label for="color_degradado" class="col-sm-2 control-label">Color Degradado</label>
	<div class="col-sm-3">
    <input class="jscolor" id="color_degradado" name="color_degradado" readonly value="BD285F">
   
    </div>
    
  <label for="multiplicador_radio_circulo" class="col-sm-2 control-label">Multip. Radio Circulo</label>
	<div class="col-sm-2">
    <input type="text" class="form-control" id="multiplicador_radio_circulo" name="multiplicador_radio_circulo" placeholder="Ingresa Constante Radio" value="<?php echo $multiplicador1;?>" required>
    </div> 
    
</div>

<div class="form-group">
	<label for="name" class="col-sm-2 control-label">T&iacute;tulo</label>
	<div class="col-sm-10">
		<input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ingresa el T&iacute;tulo" value="<?php echo $titulo;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
        
        <input type="hidden" value="<?php echo $region;?>" name="region" id="region">
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
								if ($indicador_gestion==$id1){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
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
								if ($id_accion==$id1){$selected1="selected";}else{$selected1="";}
							 ?>
							<option value="<?php echo $id1;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
		
	</div>

</div>


<div class="form-group">
	<label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n</label>
	<div class="col-sm-10">
		<textarea id="descripcion" cols="10" name="descripcion" class="form-control"><?php echo $descripcion;?></textarea>
		
	</div>
</div>

 <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                      <button type="submit" class="btn btn-primary actualizar_datos">Guardar datos</button>
                    </div>
                  </div>

</form></div></div>

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


<script>

$( "#update_register" ).submit(function( event ) {
  $('.actualizar_datos').attr("disabled", true);
   var editor1 =  nicEditors.findEditor('descripcion').getContent();
  
   var parametros = $(this).serialize() + '&editor1=' + editor1;
	 $.ajax({
			type: "POST",
			url: "ajax/modificar/indicadores.php",
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
   });
	
</script>