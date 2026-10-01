<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Objetivos_Metas_Indicadores";
	
	/*
	 // El siguiente código es para coregir caracteres especiales
	
	$query1=mysqli_query($con,"select * from ambitos_objetivos where id=2");
	while($row = mysqli_fetch_array($query1)){
	 $id=$row['id'];
	 $nombre= utf8_encode($row['name']);
	
	 $sql = "UPDATE ambitos_objetivos SET name='".$nombre."' WHERE id='".$id."' ";
     $query_actualiza= mysqli_query($con,$sql);
	}
		
 */	
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	//$query_validate=mysqli_query($con,"select indicador_gestion from indicadores where indicador_gestion='".$id."'");
	$count=0; //mysqli_num_rows($query_validate);
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM acciones WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. La aaci&oacute;n se encuentra vinculado a un indicador";
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
	$objetivo = intval($_REQUEST['objetivo']);
	$meta= intval($_REQUEST['meta']);
	$tables="acciones, metas_gestion, objetivos_gestion";
	$campos="acciones.*,metas_gestion.meta, objetivos_gestion.objetivo";
	$sWhere="acciones.id_meta=metas_gestion.id and metas_gestion.id_objetivo=objetivos_gestion.id and nombre LIKE '%".$query."%'";
	
	if ($meta >0){
	 $sWhere.=" and acciones.id_meta = '".$meta."'";
	} 
	
	if ($objetivo >0){
	 $sWhere.=" and objetivos_gestion.id = '".$objetivo."'";
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
				<h3 class="box-title">Listado de Acciones</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
                           <!-- <th>Id</th> -->
                            <th>Nro.</th>
							<th>Acci&oacute;n</th>
							<th align="right">Meta</th>
                            <th align="right">Estado</th>
							<th></th>
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$name=$row['nombre'];
							$proyecto=$row['id_proyecto'];
							$responsable=$row['responsable'];
							$meta=$row['meta'];
							//$meta=substr($meta,0,50)."...";
							$estado=$row['estado'];
							$numero_accion=$row['numero_accion'];
							if ($estado==1){
								$lbl_estado="Terminada";
								$lbl_class='label label-primary';
							}else {
								$lbl_estado="En Curso";
								$lbl_class='label label-success';
							}
				     		$finales++;
						?>	
                                               
						<tr>
                           <!-- <td><?php // echo $id;?>)</td> -->
                            <td><?php echo $numero_accion;?>)</td>
							<td><?php echo $name;?></td>
                           
							<td><?php echo $meta;?></td>
                         
                           <td><span class="<?php echo $lbl_class;?>"><?php echo $lbl_estado;?></span></td>
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
		  
