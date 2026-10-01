<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Planes_Programas_Proyectos";
	
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
	$query_validate=mysqli_query($con,"select id_meta from proyectos_gestion where id_programa='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM programas_gestion WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. El Programa se encuentra vinculado con un Proyecto";
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
	$plan = intval($_REQUEST['plan']);
	$tables="planes_gestion,programas_gestion";
	$campos="planes_gestion.plan,programas_gestion.*";
	$sWhere="planes_gestion.id = programas_gestion.id_plan and programa LIKE '%".$query."%'";
	
	if ($plan >0){
	 $sWhere.=" and programas_gestion.id_plan = '".$plan."'";
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
				<h3 class="box-title">Listado de Programas</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th>Nro.</th>
							<th>Programa </th>
							<th>Nº de Proyectos</th>
							<th>Plan</th>
                            <th>Links</th>
							<th></th>
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$numero=$row['codigo_programa'];
							$name=$row['programa'];
							$plan=$row['plan'];
							
							$count=mysqli_query($con,"select count(*) AS num_prod from proyectos_gestion where id_programa='".$id."'" );
							$rw_count=mysqli_fetch_array($count);
							$num_prod=$rw_count['num_prod'];
														
							$finales++;
						?>	
						<tr>
							<td><?php echo $numero;?></td>
							<td><?php echo $name;?></td>
							<td><?php echo number_format($num_prod,0);?></td>
						
							<td><?php echo $plan;?></td>
                             <td> <?php 
						   $sql_archivos=mysqli_query($con,"select * from links where id_tabla=2 and id_registro='$numero'");
						   while ($rw_archivos=mysqli_fetch_array($sql_archivos)){
							  $archivo=$rw_archivos['link'];
							  $titulo=$rw_archivos['titulo'];
							  
							  echo  '| ' .'<a href="'.$archivo.'" target="_blank">'.$titulo.'</a>';
							}
						   ?> </td>
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
		  
