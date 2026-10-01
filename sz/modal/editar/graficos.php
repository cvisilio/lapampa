<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	
	     $sql_grafico=mysqli_query();
		 $count=mysqli_num_rows($sql_grafico);
		 $rw_grafico=mysqli_fetch_array($sql_grafico);
		 
	
	---------------------------*/
	session_start();
	/* Connect To Database*/
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	if (isset($_GET["id"])){
	 $id=$_GET["id"];
	 $id=intval($id);
	 $sql="select * from graficos_estadisticos where  id_grafico='$id'";
	 $query=mysqli_query($con,$sql);
	 $num=mysqli_num_rows($query);
	 if ($num==1){
	  $rw=mysqli_fetch_array($query);
	  $titulo=$rw['titulo'];
	  $descripcion=$rw['descripcion'];
	  $columna1=$rw['columna1'];
	  $columna2=$rw['columna2'];
	  $tipo_grafico=$rw['tipo'];
	  $status=$rw['estado'];
	  $serie_agrupadora=$rw['serie_agrupadora'];
	  $tipo_comparativo=$rw['tipo_comparativo'];
	  $ids_graficos_comparar=$rw['ids_graficos_comparar'];
	  $indicador_gestion=$rw['indicador_gestion'];
	 
	 }
 	}	
	else {exit;}
?>
<div class="form-group">
	<label for="name" class="col-sm-3 control-label">Titulo.</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="name" name="name" placeholder="Ingresa el Titulo" value="<?php echo $titulo;?>" required>
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
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
	<label for="descripcion" class="col-sm-3 control-label">Subtitulo</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="Ingresa el Subtitulo" value="<?php echo $titulo;?>">
		
	</div>
</div>

<div class="form-group">
	<label for="serie_agrupadora" class="col-sm-3 control-label">Serie</label>
	<div class="col-sm-6">
		<input type="text" class="form-control" id="serie_agrupadora" name="serie_agrupadora" placeholder="Ingresa la Serie. Ejemplo 2015" value="<?php echo $serie_agrupadora;?>">
		
	</div>
</div>

    
<div class="form-group">
	<label for="tipo" class="col-sm-3 control-label">Estilo de Gr&aacute;fico</label>
	<div class="col-sm-6">
		<select class="form-control" name="tipo" id="tipo">
			<option value="PieChart" <?php if ($tipo_grafico=="PieChart"){echo "selected";}?>>Torta</option>
			<option value="ColumnChart" <?php if ($tipo_grafico=="ColumnChart"){echo "selected";}?>>Columnas</option>
           <option value="LineChart" <?php if ($tipo_grafico=="LineChart"){echo "selected";}?>>Lineas</option>
           <option value="AreaChart" <?php if ($tipo_grafico=="AreaChart"){echo "selected";}?>>Areas</option> 
		</select> 
		
	</div>
</div>



<div class="form-group">
	<label for="tipo_comparativo" class="col-sm-3 control-label">Comparativo</label>
	
    <div class="col-sm-6">
		<select class="form-control" name="tipo_comparativo" id="tipo_comparativo">
			<option value="" <?php if ($tipo_comparativo==""){echo "selected";}?>>Sin Comparar</option>
			<option value="estandar" <?php if ($tipo_comparativo=="estandar"){echo "selected";}?>>Estandar</option>
           <option value="provincias" <?php if ($tipo_comparativo=="provincias"){echo "selected";}?>>Provincias</option>
         </select>
	
    </div>

</div>

<div class="form-group">


	<label for="ids_graficos_comparar" class="col-sm-3 control-label">ids Comparar  
	y/o Mostrar</label>
	<div class="col-sm-6">

		<input type="text" class="form-control" id="ids_graficos_comparar" name="ids_graficos_comparar" placeholder="Ids separados por coma" value="<?php echo $ids_graficos_comparar;?>">
		
	</div>
</div>

<div class="form-group">
<label for="indicador_gestion" class="col-sm-2 control-label">Indicador Gesti&oacute;n</label>
	<div class="col-sm-10">
           <select style="width: 100% !important;" class="form-control select2" name="indicador_gestion" id="indicador_gestion">
       
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select indicadores_gestion.*,metas_gestion.codigo_meta from metas_gestion,indicadores_gestion where metas_gestion.id=indicadores_gestion.id_meta order by metas_gestion.id,indicadores_gestion.codigo_indicador");
							
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


<?php 

$sql1=mysqli_query($con,"select * from valores_graficos_estadisticos where id_grafico='$id'");
$i=1;
  while ($rw=mysqli_fetch_array($sql1)){
			$nombre=$rw['nombre'];
		    $valor=$rw['valor'];
			$id=$rw['id'];			
				
?>
<div class="form-group">
	<label class="col-sm-2 control-label">Nombre <?php echo $i; ?></label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="nombre_<?php echo $id; ?>" name="nombre_<?php echo $id; ?>" placeholder="nombre" value="<?php echo $nombre;?>">
		
	</div>
   
   <label class="col-sm-2 control-label">Valor <?php echo $i; ?></label>
	<div class="col-sm-3">
		<input type="text" class="form-control" id="valor_<?php echo $id; ?>" name="valor_<?php echo $id; ?>" placeholder="valor" value="<?php echo $valor;?>">
		
	</div> 
    
</div>

<?php $i++; } ?>