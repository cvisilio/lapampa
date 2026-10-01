<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	$usuario_editor=$_SESSION['usuario_editor'];
	get_cadena($user_id);
	$modulo="Datos";
	
	/*
	if ($user_id==2)
	{
	 // El siguiente código es para corregir caracteres especiales
	$query1=mysqli_query($con,"select * from obras");
	while($row = mysqli_fetch_array($query1)){
	 $id=$row['id'];
	 $nombre= utf8_encode($row['nombre_obra']);
	 $observaciones = utf8_encode($row['observaciones']);

	 $sql = "UPDATE obras SET nombre_obra='".$nombre."',observaciones='".$observaciones."' WHERE id='".$id."' ";
     $query_actualiza= mysqli_query($con,$sql);
	}
	
	}
	*/
	
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	//$query_validate=mysqli_query($con,"select id from acciones_obra where obra_id='".$id."'");
	//$count=mysqli_num_rows($query_validate);
	 $count=0;
		if ($count==0)
		{
			if($delete=mysqli_query($con, "DELETE FROM obras WHERE id='$id'")){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar los datos ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
		}
		else
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. ELa obra se encuentra vinculada al modulo de Acciones Obras";
			$classM="alert alert-error";
			$times="&times;";
		}
		
	} else {//No cuenta con los permisos
		$aviso="Acceso denegado!";
		$msj="No cuentas con los permisos necesario para acceder a este m?dulo.";
		$classM="alert alert-error";
		$times="&times;";
	}
}
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
    $localidad1=$_REQUEST['localidad1'];
	$estado1=$_REQUEST['estado1'];
	$ministerio1=$_REQUEST['ministerio1'];
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));
	$tables="obras, ministerios, estados_obras";
	$campos="obras.*,ministerios.denominacion,estados_obras.estado,estados_obras.id_estado,estados_obras.color";
	$campos_suma="sum(monto_adjudicado) as suma_monto_adjudicado,sum(monto_actual) as suma_monto_actual, sum(presupuesto_estimado) as suma_presupuesto_estimado, sum(cantidad) as suma_cantidad";
	$sWhere=" obras.ministerio=ministerios.id and obras.estado=estados_obras.id_estado and (nombre_obra LIKE '%".$query."%' or observaciones LIKE '%".$query."%')";
	
	if ($localidad1 >0){
	 
		$sWhere.=" and (obras.localidad = '".$localidad1."' or obras.localidad2 = '".$localidad1."')";
		
	}
	
	if ($estado1 >0){
	 
		$sWhere.=" and obras.estado = '".$estado1."'";
	}
	
	if ($ministerio1 >0){
	 
		$sWhere.=" and obras.ministerio = '".$ministerio1."'";
	}

   //$ordenar_ultimas_modificadas=0;	
   if($usuario_editor==1)
    $sWhere.=" order by obras.fecha_modificado desc";
   elseif ($estado1 >0)
    $sWhere.=" order by obras.numero_prioridad,obras.localidad";
   else
  	$sWhere.=" order by obras.estado,obras.fecha_finalizada";
	
	$suma_monto_actual=0;
    $suma_monto_adjudicado=0;
	$query_suma = mysqli_query($con,"SELECT $campos_suma FROM  $tables where $sWhere");
	$row_suma= mysqli_fetch_array($query_suma);
	$suma_monto_adjudicado= $row_suma['suma_monto_adjudicado'];
	$suma_monto_actual= $row_suma['suma_monto_actual'];
	$suma_presupuesto_estimado = $row_suma['suma_presupuesto_estimado'];
	
	$suma_cantidad=$row_suma['suma_cantidad'];
	
	if ($estado1 ==6){
	 $suma_monto_adjudicado=$suma_presupuesto_estimado;
	 $suma_monto_actual=$suma_presupuesto_estimado;
	}
	
	include 'pagination.php'; //include pagination file
	//pagination variables
	$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
	$per_page = intval($_REQUEST['per_page']); //how much records you want to show
	$adjacents  = 4; //gap between pages after number of adjacents
	$offset = ($page - 1) * $per_page;
	//Count the total number of row in your table*/
	$count_query   = mysqli_query($con,"SELECT count(*) AS numrows FROM $tables where $sWhere ");
	if ($row= mysqli_fetch_array($count_query)){$numrows = $row['numrows'];}
	else {echo mysqli_error($con);}
	$total_pages = ceil($numrows/$per_page);
	$reload = './permisos.php';
	//main query to fetch the data
	$query = mysqli_query($con,"SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
	//loop through fetched data
	
	if (isset($_REQUEST["id"])){
	?>
			<div class="<?php echo $classM;?>">
				<button type="button" class="close" data-dismiss="alert"><?php echo $times;?></button>
				<strong><?php echo $aviso?> </strong>
				<?php echo $msj;?>
			</div>	
	<?php
		}
	
	if ($numrows>0){

	?>
	
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border">
				<h3 class="box-title">Listado de Obras (Certificado a Junio de 2020)</h3>
			  </div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
						    <?php if($usuario_editor==1){ ?>
                             <th>Nro. </th>
                             <th>Modific&oacute; </th>
                            <?php } ?>
                            <?php if($estado1==6){ ?>
                            <th>Prioridad </th>
                            <?php } ?> 
                            <th>Nombre de la Obra y Empresa </th>
							<th>Ministerio </th>
							<th>Localidad/es</th>
                           
                            
                            <th>Estado </th>
                            
                            <?php if($ministerio1==21 || $ministerio1==17){ ?>
                            <th>Cant.</th>
                            <?php } ?> 
                            <th>Fecha</th>
                            <th align="center">Avance</th>
                          	<th align="right" >$ Adjudicado</th>
                            <th align="right">$ Actual</th>
                           
							
							<th></th>
						</tr>
						<?php 
						$finales=0;
					
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$expediente=$row['expediente'];
							$name=$row['nombre_obra'];
							$id_estado=$row['id_estado'];
							$name=strtolower($name);
							$observaciones=$row['observaciones'];
							$cantidad=$row['cantidad'];
						
							$obra_y_empresa=$name . $observaciones;
																				
							$ministerio=$row['denominacion'];
							$estado=$row['estado'];
							
							$lbl_status=$estado;
							$lbl_class='label label-' .$row['color'];
							
							$localidad1=$row['localidad'];
							
							$sql_localidad=mysqli_query($con,"select localidad from localidades where id='$localidad1'");
							$rw=mysqli_fetch_array($sql_localidad);
							$localidad1=utf8_encode($rw['localidad']);
							
							$localidad2=$row['localidad2'];
							if($localidad2 > 0)
							 {
							 $localidad2=$row['localidad2'];
							 $sql_localidad=mysqli_query($con,"select localidad from localidades where id='$localidad2'");
							$rw=mysqli_fetch_array($sql_localidad);
							$localidad2="/".utf8_encode($rw['localidad']);
							}
							else
							{ $localidad2="";}
							
							$localidad=$localidad1 . $localidad2;
							$empresa_adjudicataria=$row['empresa_adjudicataria_1'];
							$fecha_finalizada=$row['fecha_finalizada'];
							
							//if($fecha_finalizada <>""){ 
							//list($date2)=explode("-",$fecha_finalizada);
							//list($Y,$m,$d)=explode("-",$date2);}
							//else
							$Y=substr($fecha_finalizada,0,4);
							if($Y=='0000')
							 $Y="-";
						 
							$fecha_finalizada=$Y; ////$d."-".$m."-" .$Y
							
							//$fecha_a_licitar=$row['fecha_a_licitar'];
							
							//list($date1)=explode("-",$fecha_a_licitar);
							//list($Y,$m,$d)=explode("-",$date1);
							//$fecha_a_licitar=$d."-".$m."-" .$Y;
							
							if($usuario_editor==1){
							 $fecha_modificado=$row['fecha_modificado'];
							 list($date,$hora)=explode(" ",$fecha_modificado);
							 list($Y,$m,$d)=explode("-",$date);
							 $fecha_modificado=$d."-".$m."-" .$Y;
							}
							//$fecha_apertura=$row['fecha_apertura'];
							if ($row['porcentaje_avance'] >0)
							 $porcentaje_avance=$row['porcentaje_avance']. "%";
							else
							 $porcentaje_avance="-"; 
							$presupuesto_oficial=$row['presupuesto_oficial'];
							$mes_base=$row['mes_base'];
							 
							$monto_adjudicado=number_format($row['monto_adjudicado'],0,",",".");
							$monto_actual=number_format($row['monto_actual'],0,",",".");
							
							
							if ($id_estado ==6) // cuando es AnteProyecto, tomo la columna Monto_Adjudicado para poner el Presupuesto-Estimado del AnteProyecto (por cuestión de espacios nomás)               
							 $monto_adjudicado=number_format($row['presupuesto_estimado'],2,",",".");
							
							$partida_contable=$row['partida_contable'];
							$cuenta=$row['cuenta'];
							$subclase=$row['subclase'];
							$finalidad_y_funcion=$row['finalidad_y_funcion'];
							
							$numero_prioridad=$row['numero_prioridad'];
							
							
							//$sql_contacto=mysqli_query($con,"select first_name, last_name, phone, email from contacts where client_id='$id'");
							//$rw=mysqli_fetch_array($sql_contacto);
							//$contact=$rw['first_name']." ".$rw['last_name'];
							
							$finales++;
						?>	
						<tr>
                            <?php if($usuario_editor==1){ ?>
                             <th><?php echo $finales . ". ";?> </th>
                             <th><?php echo $fecha_modificado;?> </th>
                            <?php } ?>
                             <?php if($estado1==6){ ?>
                            <td><?php echo $numero_prioridad;?></td>
                             <?php } ?>
							<td><?php echo $obra_y_empresa;?></td>
							<td><?php echo $ministerio;?></td>
                            <td><?php echo $localidad;?></td>
                         <td>
								<span class="<?php echo $lbl_class;?>"><?php echo $lbl_status;?></span>
							</td>
                            
                            <?php if($ministerio1==21 || $ministerio1==17){ ?>
                            <td><?php if($cantidad>0) echo $cantidad;?> </td>
                            <?php } ?> 
                            
                             <td align="center"><?php echo $fecha_finalizada;?></td>
                             <td align="center"><?php echo $porcentaje_avance;?></td>
                          	<td align="right"><?php echo "$". $monto_adjudicado;?> </td>
                            <td align="right"><?php echo "$". $monto_actual;?></td>
                           
							
							<td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Acciones <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
                          <?php 
									 if ($permisos_editar==1){?>
									<li><a href="#" data-toggle="modal" data-target="#modal_update" onclick="editar('<?php echo $id;?>');"><i class='fa fa-edit'></i> Editar</a></li>
                                                                      
									<?php }
									if ($permisos_eliminar==1){
									?>
									<li><a href="#" onclick="eliminar('<?php echo $id;?>')"><i class='fa fa-trash'></i> Borrar</a></li>
									<?php }?>
								</ul>
							</div><!-- /btn-group -->
                    		</td>
						</tr>
						<?php }?>
                        
                       <tr> <td align="right" colspan="6"><?php if ($estado1 <>6){echo  " Nota: No se suman los montos de AnteProyectos | ";} ?>Total: </td> <td align="right"><?php echo "$". number_format($suma_monto_adjudicado,2,",","."); ?> </td><td align="right"><?php echo "$".  number_format($suma_monto_actual,2,",","."); ?> </td> <td> </td>  </tr>	
               
                <?php if($ministerio1==21){ ?>
                  <tr> <td colspan="9"><?php echo " Cantidad : ". number_format($suma_cantidad,0,",",".");?> </td></tr>	
                <?php } ?> 
                            
            
                       	
					</table>
				</div><!-- /.box-body -->
				<div class="box-footer clearfix">
				
				<?php 
				$inicios=$offset+1;
				$finales+=$inicios -1;
				echo "Mostrando $inicios al $finales de $numrows registros. ";
				
				echo paginate($reload, $page, $total_pages, $adjacents);?>
					
				</div>
			</div><!-- /.box -->
		</div><!-- /.col -->
	</div><!-- /.row -->	
	<?php	
	}	
}
?>          
		  
