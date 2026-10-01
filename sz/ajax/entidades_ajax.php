<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Entidades";
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	//$query_validate=mysqli_query($con,"select entidad from obras where entidad ='".$id."'");
	//$count=mysqli_num_rows($query_validate);
	if ($count==20){
			if($delete=mysqli_query($con, "DELETE FROM entidades WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. Se encuentra vinculado con otro dato";
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
	$tables="entidades, localidades";
	$campos="entidades.*, localidades.localidad";
	$sWhere=" entidades.CodigoLocalidad=localidades.id_loc_padron and Establecimiento LIKE '%".$query."%' ";
	
	if ($localidad1 >0){
	 
		$sWhere.=" and entidades.CodigoLocalidad = '".$localidad1."'";
		
	}
	
	$sWhere.=" order by Establecimiento";
	
	/*
	if ($user_id==2)
	{
	 // El siguiente código es para corregir caracteres especiales
	$query1=mysqli_query($con,"select * from entidades");
	while($row = mysqli_fetch_array($query1)){
	 $id=$row['Id'];
	 $nombre= utf8_encode($row['DomicilioEstablecimiento']);
	
	 $sql = "UPDATE entidades SET DomicilioEstablecimiento='".$nombre."' WHERE Id='".$id."' ";
     $query_actualiza= mysqli_query($con,$sql);
	}
	}
	*/
	
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
				<h3 class="box-title">Listado de Entidades</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							
							<th>Entidad </th>
                            <th>Localidad </th>
                            <th>Rubro </th>
							<th>Nº de Mesas</th>
                            <th>Tel&eacute;fono</th>
                            
							<th>Estado</th>
							
							<th></th>
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['Id'];
							$name=$row['Establecimiento'];
							$id_rubro=$row['id_rubro'];
							$Telefono_Referencia=$row['Telefono_Referencia'];
																	
							$nombre_localidad=$row['localidad'];
							
							/*La consulta de abajo para obtener nombre del rubro*/
							$consulta_nombre=mysqli_query($con,"select name AS nombre_rubro from rubros where status= 1 and id='".$id_rubro."'" );
							$rw_nombre_rubro=mysqli_fetch_array($consulta_nombre);
							$nombre_rubro=$rw_nombre_rubro['nombre_rubro'];
							/*La consulta de abajo para obtener la cantidad de artículos del SubRubro*/
							
							$count=mysqli_query($con,"select count(*) AS num_mesas, MAX(Mesa) as Maximo, MIN(Mesa) as Minimo from mesas where CodigoEscuela='".$id."'" );
							$rw_count=mysqli_fetch_array($count);
							$num_mesas=$rw_count['num_mesas'];
							
							$status=$row['status'];
							if ($status==1){
								$lbl_status="Activo";
								$lbl_class='label label-success';
							}else {
								$lbl_status="Inactivo";
								$lbl_class='label label-danger';
							}
							$finales++;
							
							 $desde_hasta=$rw_count['Minimo'] . " : " . $rw_count['Maximo'];
						?>	
						<tr>
						
							<td><?php echo $name;?></td>
                            <td><?php echo utf8_encode($nombre_localidad);?></td>
                            <td><?php echo $nombre_rubro;?></td>
							<td><?php echo number_format($num_mesas,0);?></td>
                            	<td><?php echo  $Telefono_Referencia;?></td>
							<td>
								<span class="<?php echo $lbl_class;?>"><?php echo $lbl_status;?></span>
							</td>
						
							<td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Acciones <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
									<?php if ($permisos_editar==1){?>
									<li><a href="#" data-toggle="modal" data-target="#modal_update" onclick="editar('<?php echo $id;?>');"><i class='fa fa-edit'></i> Editar</a></li>
                               <?php }
							   
								 if ($permisos_editar==1){?>
									<li><a href="#" data-toggle="modal" data-target="#modal_update1" onclick="ajuste_numero_mesas('<?php echo $id;?>');"><i class='fa fa-edit'></i> Ajustar Nº Mesas</a></li>
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
		  
