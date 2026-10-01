<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	/* Connect To Database*/
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from proyectos_gestion where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$proyecto=$rw['proyecto'];
	$numero=$rw['codigo_proyecto'];
	$programa=$rw['id_programa'];
	$indicadores_gestion=$rw['indicadores_gestion'];
	$funcionario=$rw['id_funcionario'];
	$avance=$rw['avance'];
	$estado=$rw['estado'];
	}
	}	
	else {exit;}
?>
 <div class="form-group">
	<label for="proyecto" class="col-sm-2 control-label">Proyecto</label>
	<div class="col-sm-10">
		<input type="text" class="form-control" id="proyecto" name="proyecto" placeholder="Ingresa el Proyecto" value="<?php echo $proyecto;?>" required>
        
        
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="numero" class="col-sm-2 control-label">C&oacute;digo Proyecto</label>
	<div class="col-sm-4">
        
     <input type="text" class="form-control" id="numero" name="numero" placeholder="Ingresa el Código del Proyecto" value="<?php echo $numero;?>" required>
     
    
	</div>
 </div>
 
  <div class="form-group">
	   <label for="id_programa" class="col-sm-2 control-label">Programa</label>
          <div class="col-sm-8">
                      <select class="form-control" name="id_programa" id="id_programa" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from programas_gestion order by codigo_programa");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['codigo_programa'] . ": ". $rw['programa'];
								if ($programa==$id1){$selected1="selected";}else{$selected1="";}
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
	   <label for="id_funcionario" class="col-sm-2 control-label">Funcionario</label>
          <div class="col-sm-10">
                      <select class="form-control" name="id_funcionario" id="id_funcionario">
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select funcionarios.nombre,funcionarios.id,ministerios.denominacion from funcionarios,ministerios where funcionarios.ministerio=ministerios.id order by ministerios.denominacion, funcionarios.nombre");
							while ($rw=mysqli_fetch_array($sql)){
								$id1=$rw['id'];
								$name=$rw['denominacion'] . ": ". $rw['nombre'];
								if ($funcionario==$id1){$selected1="selected";}else{$selected1="";}
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
	<label for="indicadores_gestion" class="col-sm-2 control-label">Indicadores Gesti&oacute;n</label>
	<div class="col-sm-4">
        
     <input type="text" class="form-control" id="indicadores_gestion" name="indicadores_gestion" placeholder="Ingrese codigo indicadores separados por coma" value="<?php echo $indicadores_gestion;?>" >
     
    
	</div>
 </div>
 
 <div class="form-group">  
    <label for="estado" class="col-sm-2 control-label">Estado</label>
          <div class="col-sm-3">
                      <select class="form-control" name="estado" id="estado" required>
						<option value="">Selecciona</option>
						
							<option value="1" <?php if ($estado==1)echo ' selected="selected"';else 'selected=""'; ?>> Terminado </option>
                            <option value="2" <?php if ($estado==2)echo ' selected="selected"';else 'selected=""'; ?>> En Curso </option>
						
					  </select>
    
                    </div>      
   </div>
   
   
 <div class="form-group">
	<label for="avance" class="col-sm-2 control-label">Avance</label>
	<div class="col-sm-3">
        
     <input type="text" class="form-control" id="avance" name="avance" placeholder="Ingrese el avance" value="<?php echo $avance;?>" >
     
    
	</div>
 </div>
 
 <div class="form-goup">
 
<h4>Localidades Afectadas</h4>
  
  <div style="margin-top: 10px; margin-bottom: 10px;">Hacer doble clic sobre una opcion para agregarla</div>
<div style="display: inline-block; margin-right: 20px;">Localidades<br/>
 
  <select id="opciones_disponibles" size="10" style="width: 200px;">
     <?php $sql=mysqli_query($con,"select * from localidades where es_localidad_o_comision_fomento=1 order by localidad");                  
		while ($rw=mysqli_fetch_array($sql)){
		 $id1=$rw['id'];
		 $name=utf8_encode($rw['localidad']);
		?>
		<option value="<?php echo $id1;?>"><?php echo $name;?></option> 
       
       <?php } ?> 
       
    </select>
</div>
<div style="display: inline-block; margin-right: 20px;">Localidades Agregadas<br/>

    <select class="form-control" id="opciones_agregadas" name="opciones_agregadas" size="10" style="width: 200px;">
     <?php $sql2=mysqli_query($con,"select localidades.* from localidades,localidades_proyectos where localidades_proyectos.id_localidad=localidades.id and localidades_proyectos.id_proyecto='$id' order by localidad");                  
		while ($rw=mysqli_fetch_array($sql2)){
		 $id1=$rw['id'];
		 $name=utf8_encode($rw['localidad']);
		?>
      <option value="<?php echo $id1;?>"><?php echo $name;?></option> 
       
       <?php } ?>   
    
    </select>
</div>
      
 </div>
 
 <script>
 
 $(document).ready(function () {
    //agregar opciones
    $('#opciones_disponibles option').dblclick(function () {
        var valor = $(this).val(); //valor de la opcion
        var texto = $(this).text(); //texto de la opcion

        //verificar si la opcion no esta agregada antes de agregarla
        if ($('#opciones_agregadas option[value="' + valor + '"]').length === 0) {
            //agregar la opcion
            $('#opciones_agregadas').append($('<option>', {
                value: valor,
                text: texto
            }));
        }
    });

    //remover opciones
    $(document).on('dblclick', '#opciones_agregadas option', function () {
        var valor = $(this).val(); //valor de la opcion

        //eliminar la opcion con valor X
        $('#opciones_agregadas option[value="' + valor + '"]').remove();
    });

});
	
// http://jsfiddle.net/hugolizama/839rjbtn/
    	   
 
 </script>