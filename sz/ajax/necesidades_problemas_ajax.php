<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Necesidades_Problemas";
	
	/* // El siguiente código es para coregir caracteres especiales
	$query1=mysqli_query($con,"select * from metas_gestion");
	while($row = mysqli_fetch_array($query1)){
	 $id=$row['id'];
	 $meta1= utf8_encode($row['meta']);
	
	 $sql = "UPDATE metas_gestion SET meta='".$meta1."' WHERE id='".$id."' ";
     $query_actualiza= mysqli_query($con,$sql);
	}
			
	*/
	
	
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	$query_validate=mysqli_query($con,"select * from proyectos_gestion necesidades_problemas where id='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM necesidades_problemas WHERE id='$id'")){
				$aviso="Bien hecho!";
				$msj="Datos eliminados satisfactoriamente.";
				$classM="alert alert-success";
				$times="&times;";	
			}else{
				$aviso="Aviso!";
				$msj="Error al eliminar ".mysqli_error($con);
				$classM="alert alert-error";
				$times="&times;";					
			}
	}
	else 
		{
			$aviso="Aviso!";
			$msj="Error al eliminar los datos. La Necesidad Problema se encuentra vinculado con un Proyecto";
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
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));
	$programa = intval($_REQUEST['programa']);
	$localidad = intval($_REQUEST['localidad']);
	$tables="necesidades_problemas";
	$campos="necesidades_problemas.*";
	$sWhere="nombre LIKE '%".$query."%'";
	
	if ($programa >0){
	 $sWhere.=" and necesidades_problemas.id_programa = '".$programa."'";
	} 	
	
	if ($localidad >0){
	 $sWhere.=" and necesidades_problemas.localidad = '".$localidad."'";
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
				<h3 class="box-title">Listado de Necesidades Problemas</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th>Nro.</th>
							<th>Necesidad/Problema </th>
                        	<th>Tipo</th>
							<th>Localidad</th>
                            <th>Resuelto</th>
                            <th>Avance</th>
							<th></th>
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$tipo=$row['tipo'];
							if($tipo==1)
							 $tipo="Necesidad";
							else
							 $tipo="Problema";
							 
							$resuelto=$row['resuelto'];
							if ($resuelto==1){
								$lbl_resuelto="Si";
								$lbl_class='label label-primary';
							}else {
								$lbl_resuelto="No";
								$lbl_class='label label-danger';
							}
							
							$avance=$row['avance'];
					
							$name=$row['nombre'];
							$programa=$row['id_programa'];
							$id_localidad=$row['localidad'];
							
							$sql_localidad=mysqli_query($con,"select * from localidades where id='$id_localidad'");
							$rw_localidad=mysqli_fetch_array($sql_localidad);
							$nombre_localidad=utf8_encode($rw_localidad['localidad']);
							
																					
							$finales++;
						?>	
						<tr>
							<td><?php echo $id;?></td>
							<td><?php echo $name;?></td>
							<td><?php echo $tipo;?></td>
						
							<td><?php echo $nombre_localidad;?></td>
                            <td><span class="<?php echo $lbl_class;?>"><?php echo $lbl_resuelto;?></span></td>
                            <td><?php echo $avance;?>%</td>
							<td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Acciones <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
									<?php if ($permisos_editar==1){?>
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
		  
