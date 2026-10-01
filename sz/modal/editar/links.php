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
	$sql="select * from links where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$rw=mysqli_fetch_array($query);
	$tabla=$rw['id_tabla'];
	$link=$rw['link'];
	$titulo=$rw['titulo'];
	$tipo=$rw['tipo'];
	$id_registro=$rw['id_registro'];
	$resumen=$rw['resumen'];
	}
	}	
	else {exit;}
?>

 
    
 <div class="form-group">
	<label for="link" class="col-sm-2 control-label">link a archivo</label>
	<div class="col-sm-7">
		<input type="text" class="form-control" id="link" name="link" placeholder="Ingresa el link a archivo" value="<?php echo $link;?>" required>
        
        
		<input type="hidden" value="<?php echo $id;?>" name="id" id="id">
	</div>
 </div>
 
 <div class="form-group">
	<label for="titulo" class="col-sm-2 control-label">Titulo</label>
	<div class="col-sm-9">
     <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ingresa el titulo" value="<?php echo $titulo;?>" required>
   </div>
   </div> 
   
   <div class="form-group">
                    <label for="resumen1" class="col-sm-2 control-label">Resumen</label>
                    <div class="col-sm-10">
				      <textarea class="form-control" name="resumen1" style="width: 100%;" id="resumen1"><?php echo $resumen;?></textarea>
                    </div>
                  </div>

    
  <div class="form-group">     
          
            <label for="id_tabla" class="col-sm-2 control-label"> lugar que aparece </label>

                    <div class="col-sm-6">
                      <select class="form-control" name="id_tabla" id="id_tabla">
						<option value="">Selecciona</option>
                        
						<?php 
							$sql=mysqli_query($con,"select * from tablas order by tabla");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name= $rw['tabla'];
								if ($tabla==$id){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
                    
    <label for="id_registro" class="col-sm-2 control-label">Id Registro</label>
	<div class="col-sm-2">
     <input type="text" class="form-control" id="id_registro" name="id_registro" placeholder="Ingresa el registro de la tabla (ejemplo podria parecer de acuerdo a alguna seleccion de un select)" value="<?php echo $id_registro;?>" >
   </div>          
	</div> 