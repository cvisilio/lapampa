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
	$sql="select * from obras where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	$row=mysqli_fetch_array($query);
	$id=$row['id'];
	$expediente=$row['expediente'];
	$nombre_obra1=$row['nombre_obra'];
	$observaciones=$row['observaciones'];
	$ministerio=$row['ministerio'];
	$estado=$row['estado'];
 	$localidad1=$row['localidad'];
	$localidad2=$row['localidad2'];
	$empresa_adjudicataria=$row['empresa_adjudicataria_1'];
	
	if($row['fecha_finalizada']!='0000-00-00')
	$fecha_finalizada=date('d/m/Y', strtotime($row['fecha_finalizada']));
	else
	 $fecha_finalizada="";
	if($row['fecha_a_licitar'] !='0000-00-00') 
	$fecha_licitar=date('d/m/Y', strtotime($row['fecha_a_licitar']));
	else
	$fecha_licitar="";
	if($row['fecha_apertura'] !='0000-00-00')  
	 $fecha_apertura=date('d/m/Y', strtotime($row['fecha_apertura']));
	else
	 $fecha_apertura="";
	$porcentaje_avance=$row['porcentaje_avance'];
    $presupuesto_oficial=$row['presupuesto_oficial'];
	$mes_base=$row['mes_base'];
	$monto_adjudicado=$row['monto_adjudicado'];
    $partida_contable=$row['partida_contable'];
	$cuenta=$row['cuenta'];
	$subclase=$row['subclase'];
	$finalidad_y_funcion=$row['finalidad_y_funcion'];
	$monto_actual=$row['monto_actual'];
	$cantidad=$row['cantidad'];
	
	//$cantidad_nuevos=$row['cantidad_nuevos'];
	 
	}
	}	
	else {exit;}
?>


 <div class="form-group">
                        <label for="expediente" class="col-sm-3 control-label">Expediente</label>
                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="expediente"  name="expediente"  value="<?php echo $expediente; ?>">
                        </div>
                        
                        <label for="cantidad" class="col-sm-3 control-label">Cantidad</label>
                        <div class="col-sm-3">
                          <input type="text" class="form-control" id="cantidad"  name="cantidad"  value="<?php echo $cantidad; ?>">
                        </div>
                        
                      </div>
         
                    <div class="form-group">
                        <label for="nombre_obra" class="col-sm-3 control-label">Nombre de la Obra</label>
                        <div class="col-sm-9">
                          
                          <textarea id="nombre_obra" cols="10" name="nombre_obra" class="form-control" ><?php echo $nombre_obra1;?></textarea>
                          
                          <input type="hidden" value="<?php echo $id;?>" name="id" id="id">
                        </div>
                      </div>
                      
					  <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Estado</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="estado" id="estado" required>
						<option value="">Selecciona Estado</option>
						<?php 
							$sql=mysqli_query($con,"select * from estados_obras");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id_estado'];
								$name=$rw['estado'];
							 if ($id==$estado){$selected1="selected";}else{$selected1="";}
							
							?>
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                        </div>
                       
                       <label for="ministerio" class="col-sm-3 control-label">Ministerio</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="ministerio" id="ministerio" required>
						<option value="">Selecciona Ministerio</option>
						<?php
							$sql1=mysqli_query($con,"select * from ministerios order by denominacion");
							while ($rw1=mysqli_fetch_array($sql1)){
							    $id=$rw1['id'];
								if ($id==$ministerio){$selected1="selected";}else{$selected1="";}
								$cadena= $rw1['modulos'];
								$modulos = explode(",", $cadena);
							   if (in_array('12', $modulos)) 
							    {
								?>
								<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $rw1['denominacion'];?></option>	
								<?php
							  } //if
							 } //while
							?>
					  </select>
                        </div>
                       
                     </div>
                     
                   <div class="form-group">
                      <label for="fecha_licitar" class="col-sm-2 control-label">Licitada</label>
                    <div class="col-sm-4">
                   	         <div class="input-group">
                                            <input type="text" class="form-control datepicker" name="fecha_licitar" id="fecha_licitar"  value="<?php echo $fecha_licitar;?>">

                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                    </div>
                    </div>
                   
                    <label for="fecha_finalizada" class="col-sm-2 control-label">Finalizada</label>
                      <div class="col-sm-4">
                   	  <div class="input-group">
                                            <input type="text" class="form-control datepicker" name="fecha_finalizada" id="fecha_finalizada"  value="<?php echo $fecha_finalizada;?>" >

                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                    </div>
                    </div>           
                   </div> 
                        
                     
                    <div class="form-group">
                    <label for="localidad" class="col-sm-3 control-label">Localidad 1</label>

                    <div class="col-sm-3">
                      <select class="form-control" name="localidad" id="localidad" required>
						<option value="">Seleccione Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=utf8_encode($rw['localidad']);
								if ($id==$localidad1){$selected1="selected";}else{$selected1="";} 
								 ?>
							?>
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>
                    
                     <label for="localidad2" class="col-sm-3 control-label">Localidad 2</label>

                    <div class="col-sm-3">
                      <select class="form-control" name="localidad2" id="localidad2">
						<option value="">Seleccione Localidad</option>
						<?php 
							$sql=mysqli_query($con,"select * from localidades order by localidad");                  
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['id'];
								$name=utf8_encode($rw['localidad']);
								if ($id==$localidad2){$selected1="selected";}else{$selected1="";} 
								 ?>
							?>
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>      
                          
                   </div>  
                      
					<div class="form-group">
                    <label for="empresa" class="col-sm-3 control-label">Empresa</label>

                    <div class="col-sm-4">
                      <select class="form-control" name="empresa" id="empresa">
						<option value="">Selecciona Empresa</option>
						
					  </select>
                    </div>
                    <label for="porcentaje_avance" class="col-sm-2 control-label">Avance</label>

                    <div class="col-sm-3">
                     <div class="input-group">
						  
                      <input type="text" class="form-control" id="porcentaje_avance" name="porcentaje_avance" value="<?php echo $porcentaje_avance;?>" >
                         <div class="input-group-addon">
							<i class="fa fa-percent"><strong>%</strong></i>
						  </div>
                      </div>
                    </div>            
                   </div>
                 
                 <div class="form-group">
                        <label for="presupuesto_oficial" class="col-sm-3 control-label">Presupuesto Oficial</label>
                        <div class="col-sm-4">
                          <div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-usd"></i>
						  </div>
						  <input type="text" class="form-control" id="presupuesto_oficial" name="presupuesto_oficial" pattern="\d+(\.\d{2})?"  value="<?php echo $presupuesto_oficial;?>" >
						</div>
                        </div>
                         <label for="mes_base" class="col-sm-3 control-label">Mes Base</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="mes_base"  name="mes_base" value="<?php echo $mes_base;?>">
                        </div>
                      </div> 
                 
                   <div class="form-group">
                    <label for="monto_adjudicado" class="col-sm-2 control-label">Monto Adjudicado</label>

                    <div class="col-sm-4">
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-usd"></i>
						  </div>
						  <input type="text" class="form-control" id="monto_adjudicado" name="monto_adjudicado" pattern="\d+(\.\d{2})?" value="<?php echo $monto_adjudicado;?>" >
						</div>
                    </div>
                    
                    <label for="monto_actual" class="col-sm-2 control-label">Monto Actual</label>
                         <div class="col-sm-4">
						<div class="input-group">
						  <div class="input-group-addon">
							<i class="fa fa-usd"></i>
						  </div>
						  <input type="text" class="form-control" id="monto_actual" name="monto_actual" pattern="\d+(\.\d{2})?" value="<?php echo $monto_actual;?>" >
						</div>
                    </div>
                   </div> 
                    
                   <div class="form-group">
                        <label for="partida_contable" class="col-sm-3 control-label">Part. Contable</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="partida_contable"  name="partida_contable" value="<?php echo $partida_contable;?>">
                        </div>
                         <label for="cuenta" class="col-sm-2 control-label">Cuenta</label>
                        <div class="col-sm-1">
                          <input type="text" class="form-control" id="cuenta"  name="cuenta" value="<?php echo $cuenta;?>">
                        </div>
                          <label for="subclase" class="col-sm-2 control-label">SubClase</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="subclase"  name="subclase" value="<?php echo $subclase;?>">
                        </div>
                      </div>     
                       
			  <div class="form-group">
                        <label for="observaciones" class="col-sm-3 control-label">Observaciones</label>
                        <div class="col-sm-9">
                        <textarea  id="observaciones" name="observaciones"><?php echo $observaciones;?></textarea>
                        </div>
                      </div>

<script>
 $(function() {
		 //datepicker
		$('.datepicker').datepicker({
			format: 'dd/mm/yyyy',
			endDate: '+360d',
			autoclose: true
		});
	});	
 </script>    