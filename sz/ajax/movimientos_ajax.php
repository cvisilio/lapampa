<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Compromisos";
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	//$query_validate=mysqli_query($con,"select estado_movimiento from movimientos_localidades where subrubro ='".$id."'");
	//$count=mysqli_num_rows($query_validate);
	 $count=0;
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM movimientos_localidades WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. El Movimiento se encuentra vinculado con un dato";
			$classM="alert alert-error";
			$times="&times;";
		}	
		
	} else {//No cuenta con los permisos
		$aviso="Acceso denegado!";
		$msj="No cuentas con los permisos necesario para acceder a este módulo.";
		$classM="alert alert-error";
		$times="&times;";
	}
}
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
    $localidad1=$_REQUEST['localidad1'];
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));
	$tables="movimientos_localidades";
	$campos="*";
	$sWhere=" detalle LIKE '%".$query."%'";
	
	if ($localidad1 >0){
	 
		$sWhere.=" and (movimientos_localidades.id_localidad = '".$localidad1."')";
		$sWhere_suma=" and (movimientos_localidades.id_localidad = '".$localidad1."')";
		
	}
	
	$sWhere.=" order by id desc";
	
	$suma=mysqli_query($con,"select sum(valor) as suma from movimientos_localidades");
	$suma1=mysqli_fetch_array($suma);
	$suma_montos=$suma1['suma'];
		
	
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
				<h3 class="box-title">Listado de Compromisos </h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th>ID</th>
							<th>Compromiso</th>
                            <th>Localidad </th>
							<th>Monto</th>
							<th>Estado</th>
							<th>Agregado</th>
							<th></th>
						</tr>
                        <tr>
							<td> </td>
							<td></td>
                            <th style="color:#FF0000">Total..: </th>							<th align="center" style="color:#FF0000"> <?php echo " $" . number_format($suma_montos,"0",",",".");?></th>
							<td></td>
							<td></td>
							<td></td>
						</tr>
                        
                       
                        
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$name=$row['detalle'];
							$valor=$row['valor'];
							$id_localidad=$row['id_localidad'];
							$date_added=$row['fecha_movimiento'];
							/*La consulta de abajo para obtener nombre del rubro*/
							$consulta_nombre=mysqli_query($con,"select localidad as nombre_localidad from localidades where id='".$id_localidad."'" );
							$rw_nombre=mysqli_fetch_array($consulta_nombre);
						
							$nombre_localidad=utf8_encode($rw_nombre['nombre_localidad']);
							/*La consulta de abajo para obtener la cantidad de artículos del SubRubro*/
														
							list($date,$hora)=explode(" ",$date_added);
							list($Y,$m,$d)=explode("-",$date);
							$fecha=$d."-".$m."-".$Y;
							$status=$row['estado_movimiento'];
							if ($status==0){
								$lbl_status="En Curso";
								$lbl_class='label label-success';
							}else {
								$lbl_status="AnteProyecto";
								$lbl_class='label label-info';
							}
							$finales++;
						?>	
						<tr>
							<td><?php echo $id;?></td>
							<td><?php echo $name;?></td>
                            <td><?php echo $nombre_localidad;?></td>
							<td><?php echo "$". number_format($valor,"0",",",".");?></td>
							<td>
								<span class="<?php echo $lbl_class;?>"><?php echo $lbl_status;?></span>
							</td>
							<td><?php echo $fecha;?></td>
							<td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Acciones <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
									<?php if ($permisos_editar==1){?>
									<li><a href="#" data-toggle="modal" data-target="#modal_update" onclick="editar('<?php echo $id;?>');"><i class='fa fa-edit'></i> Editar</a></li>
                               <?php }
							   
								 /*if ($permisos_editar==1){?>
									<li><a href="#" data-toggle="modal" data-target="#modal_update1" onclick="ajuste_precios('<?php echo $id;?>');"><i class='fa fa-edit'></i> Ajustar Precios</a></li>
									<?php } */
									    
									
								if ($permisos_eliminar==1){
									?>
									<li><a href="#" onclick="eliminar('<?php echo $id;?>')"><i class='fa fa-trash'></i> Borrar</a></li>
									<?php }?>
								</ul>
							</div><!-- /btn-group -->
                    		</td>
						</tr>
						<?php }?>		
					</table>
				</div><!-- /.box-body -->
				<div class="box-footer clearfix">
				
				<?php 
				$inicios=$offset+1;
				$finales+=$inicios -1;
				echo "Mostrando $inicios al $finales de $numrows registros";
				echo paginate($reload, $page, $total_pages, $adjacents);?>
					
				</div>
			</div><!-- /.box -->
		</div><!-- /.col -->
	</div><!-- /.row -->	
	<?php	
	}	
}
?>          
		  
