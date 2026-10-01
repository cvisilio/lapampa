 
<form class="form-horizontal" method="post" id="new_register" name="new_register" >
<!-- Modal -->
<div class="modal fade" id="modal_register" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Gr&aacute;fico</h4>
      </div>
      <div class="modal-body">
	  
      <div class="form-group">
	<label for="name" class="col-sm-3 control-label">Titulo</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="name" name="name" placeholder="Ingresa el Titulo" value="" required>
		
	</div>
</div>

<div class="form-group">
	<label for="descripcion" class="col-sm-3 control-label">Subtitulo</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ingresa el subtitulo" value="">
		
	</div>
</div>

<div class="form-group">
	<label for="serie_agrupadora" class="col-sm-3 control-label">Serie</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="serie_agrupadora" name="serie_agrupadora" placeholder="Ingresa la Serie. Ejemplo 2015" value="">
		
	</div>
</div>

<div class="form-group">
	<label for="tipo" class="col-sm-3 control-label">Estilo de Gr&aacute;fico</label>
	<div class="col-sm-6">
		<select class="form-control" name="tipo" id="tipo">
			<option value="PieChart">Torta</option>
			<option value="ColumnChart">Columnas</option>
           <option value="LineChart">Lineas</option>
           <option value="AreaChart">Areas</option> 
		</select> 
		
	</div>
</div>

<div class="form-group">
	<label for="tipo_comparativo" class="col-sm-3 control-label">Comparativo</label>
 	 <div class="col-sm-6">
    
		 <select class="form-control" name="tipo_comparativo" id="tipo_comparativo">
		
        	<option value="">Sin Comparar</option>
			<option value="estandar">Estandar</option>
           <option value="provincias">Provincias</option>
         </select>  
   
	</div>

</div>


<div class="form-group">
	
    <label for="ids_graficos_comparar" class="col-sm-3 control-label">ids Comparar 
	y/o Mostrar</label>
    
	<div class="col-sm-6">
		
        <input type="text" class="form-control" id="ids_graficos_comparar" name="ids_graficos_comparar" placeholder="Ids separados por coma" value="">
        
		
	</div>
</div>

<div class="form-group">
<label for="indicador_gestion" class="col-sm-2 control-label">Indicador Gesti&oacute;n</label>
	<div class="col-sm-7">
       <select class="form-control" name="indicador_gestion" id="indicador_gestion">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select indicadores_gestion.*,metas_gestion.codigo_meta from metas_gestion,indicadores_gestion where metas_gestion.id=indicadores_gestion.id_meta order by metas_gestion.id,indicadores_gestion.codigo_indicador");
							
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


<?php 
$cantidad=10;
for($i=1; $i<=$cantidad; $i++){
   
?>
<div class="form-group">
	<label for="nombre<?php echo $i; ?>" class="col-sm-2 control-label">Nombre <?php echo $i; ?></label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="nombre<?php echo $i; ?>" name="nombre<?php echo $i; ?>" placeholder="nombre" value="">
		
	</div>
   
   <label for="valor<?php echo $i; ?>" class="col-sm-2 control-label">Valor <?php echo $i; ?></label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="valor<?php echo $i; ?>" name="valor<?php echo $i; ?>" placeholder="valor" value="">
		
	</div> 
    
</div>

<?php  } ?>
  

	 
	  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="submit" id="guardar_datos" class="btn btn-primary">Registrar</button>
      </div>
    </div>
  </div>
</div>
</form>