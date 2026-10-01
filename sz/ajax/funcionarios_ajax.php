<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Funcionarios";
	
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
	$query_validate=mysqli_query($con,"select id_funcionario from necesidades_problemas where id_funcionario='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM funcionarios WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. El Funcionario se encuentra vinculado con un Proyecto";
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
	$ministerio = intval($_REQUEST['ministerio']);
	$poder= intval($_REQUEST['poder']);
	$localidad= intval($_REQUEST['localidad1']);
	$tables="funcionarios,ministerios,cargos_poderes_estado";
	$campos="funcionarios.*,ministerios.denominacion,cargos_poderes_estado.cargo";
	$sWhere="funcionarios.ministerio = ministerios.id and funcionarios.id_cargo = cargos_poderes_estado.id and funcionarios.nombre LIKE '%".$query."%'";
	
	if ($ministerio >0){
	 $sWhere.=" and funcionarios.ministerio = '".$ministerio."'";
	} 
	
	if ($poder >0){
	 $sWhere.=" and funcionarios.poder= '".$poder."'";
	} 	
	
	if ($localidad >0){
	 $sWhere.=" and funcionarios.localidad= '".$localidad."'";
	} 		
	
	if ($localidad >0)
	  $sWhere.=" order by funcionarios.lista,funcionarios.posicion";
	else
	 $sWhere.=" order by ministerios.denominacion,funcionarios.nombre"; 
	 
	 
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
				<h3 class="box-title">Listado de Funcionarios</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th>Funcionario </th>
                            <th>Localidad </th>
							<th>Ministerio</th>
                             <th>Poder</th>
                             <th>Lista</th>
                             <th>Elecci&oacute;n</th>
                             <th>Pos.</th>
                            <th>Cargo</th>
							<th>Télefono</th>
                            <th>Problemas Resueltos</th>
                            <th>P. No R.</th>
                            <th>Proyectos Terminados</th>
                            <th>P. en Curso</th>
                           
							<th></th>
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){
						    $terminados1=0;
							$terminados2=0;
							$resueltos1=0;
							$resueltos2=0;	
							$id=$row['id'];
							$name=$row['nombre'];
							$ministerio=$row['denominacion'];
							$telefono=$row['telefono'];
							$cargo=$row['cargo'];
							$poder=$row['poder'];
							$posicion=$row['posicion'];
							$lista=$row['lista'];
							$eleccion=$row['eleccion'];
							$descripcion_lista="";
							$localidad=$row['localidad'];
							
							$descripcion_lista="";
							$descripcion_eleccion="";
							
							if($lista >0)
							{
							 $sql0=mysqli_query($con,"select * from listas_elecciones where listas_elecciones.id_lista='$lista'");
							 $rw_sql0 = mysqli_fetch_array($sql0);
							 $descripcion_lista=utf8_decode($rw_sql0['abreviatura_provincial']);
							}
							
							if($eleccion >0)
							{
							 $sql0=mysqli_query($con,"select * from elecciones where elecciones.id_eleccion='$eleccion'");
							 $rw_sql0 = mysqli_fetch_array($sql0);
							 $descripcion_eleccion=utf8_decode($rw_sql0['tipo']);
							}
							
							if($localidad >0)
							{
							 $sql0=mysqli_query($con,"select * from localidades where id_loc_padron='$localidad'");
							 $rw_sql0 = mysqli_fetch_array($sql0);
							 $localidad=$rw_sql0['localidad'];
							}
							
							$count=mysqli_query($con,"select count(*) AS num_prod,resuelto from necesidades_problemas where id_funcionario='".$id."' group by resuelto");
							while($rw_count = mysqli_fetch_array($count)){
							 $resuelto=$rw_count['resuelto'];
							 if ($resuelto==1)
							  $resueltos1=$rw_count['num_prod'];
							 else
							  $resueltos2=$rw_count['num_prod'];
							}
							
							$count2=mysqli_query($con,"select count(*) AS num_prod,estado from proyectos_gestion where id_funcionario='".$id."' group by estado");
							while($rw_count2 = mysqli_fetch_array($count2)){
							 $estado_proyecto=$rw_count2['estado'];
							 if ($estado_proyecto==1)
							  $terminados1=$rw_count2['num_prod'];
							 else
							  $terminados2=$rw_count2['num_prod'];
							}
							
							switch ($poder) {
                              case 1:
							   $detalle_poder="Ejecutivo";
							   break;
							  case 2:
							   $detalle_poder="Legislativo";
							   break;
							  case 3:
							    $detalle_poder="Judicial";
							   break;
							  case 4:
							   $detalle_poder="Municipal";
							   break;
							   }
							  
																				
							$finales++;
						?>	
						<tr>
						
							<td><?php echo $name;?></td>
                            <td><?php echo $localidad;?></td>
                            <td><?php echo $ministerio;?></td>
                            <td><?php echo $detalle_poder;?></td>
                            <td><?php echo $descripcion_lista;?></td>
                            <td><?php echo $descripcion_eleccion;?></td>
                            <td><?php echo $posicion;?></td>
                            <td><?php echo $cargo;?></td>
							<td><?php echo $telefono;?></td>
                            <td align="center">
                            <?php if($resueltos1 >0)
							 { ?>
                            <a href="#" data-toggle="modal" data-target="#modal_update2" onclick="informacion_necesidades_problemas('<?php echo $id;?>','1','<?php echo $name;?>');"> <?php echo number_format($resueltos1,0);?></a>
							<?php } ?>
                            </td>
                             <td align="center">
                             <?php if($resueltos2 >0)
							 { ?>
                            <a href="#" data-toggle="modal" data-target="#modal_update2" onclick="informacion_necesidades_problemas('<?php echo $id;?>','2','<?php echo $name;?>');"> <?php echo number_format($resueltos2,0);?></a>
                            <?php } ?>
                            </td>
                            
                           <td align="center">
                            <?php if($terminados1 >0)
							 { ?>
                            <a href="#" data-toggle="modal" data-target="#modal_update2" onclick="informacion_proyectos('<?php echo $id;?>','1','<?php echo $name;?>');"> <?php echo number_format($terminados1,0);?></a>
							<?php } ?>
                            </td>
                           <td align="center">
                           <?php if($terminados2 >0)
							 { ?>
                            <a href="#" data-toggle="modal" data-target="#modal_update2" onclick="informacion_proyectos('<?php echo $id;?>','2','<?php echo $name;?>');"> <?php echo number_format($terminados2,0);?></a>
                            <?php } ?>
                            </td>
                            
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
		  
