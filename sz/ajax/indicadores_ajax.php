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
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	$query_validate=mysqli_query($con,"select mapa from datos where mapa='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM indicadores WHERE id='$id'")){
			    $delete=mysqli_query($con, "DELETE FROM indicadores_provincias WHERE indicador='$id'");
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
			$msj="Error al eliminar los datos. El indicador se encuentra vinculado con un dato";
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
	$tables="indicadores";
	$campos="*";
	$sWhere=" titulo LIKE '%".$query."%'";
	
	
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
				<h3 class="box-title">Listado de Indicadores</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th>ID</th>
							<th>Indicador </th>
                            <th>Descripci&oacute;n </th>
                            <th>Ambito </th>
                            <th>Mapa </th>
                            <th>Link </th>
                            <th> </th>
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$tipo_poligono=$row['tipo_poligono'];
							$titulo=substr($row['titulo'],0,35) . "...";
							$descripcion=$row['descripcion'];
							$link=$row['link'];
							$ambito=$row['ambito'];
							if($ambito==1)
							 {
							  $lbl_ambito="Nacional";
							  $lbl_class='label label-info'; 
							}
							else
							 {$lbl_ambito="Provincial";
							 $lbl_class='label label-success'; 
							 
							 }
							$finales++;
						?>	
						<tr>
							<td><?php echo $id;?></td>
							<td><?php echo $titulo;?></td>
							
							<td><?php echo $descripcion;?></td>
                            <td> <span class="<?php echo $lbl_class;?>"><?php echo $lbl_ambito;?></span></td>
                            
                            <td>
                            <?php if($ambito==1){ ?>
                            <a href="mapas.php?id=<?php echo $id;?>"> <i class="glyphicon glyphicon-globe"></i></a>
                            <?php } else {
							?>
                            
                            <a href="indicadores_locales.php?id=<?php echo $id;?>"> <i class="glyphicon glyphicon-globe"></i></a>
                            
                            
                             <a href="indicadores_locales_circulos.php?id=<?php echo $id;?>"> <i class="glyphicon glyphicon-map-marker"></i></a>
                                                         							
							<?php } ?>
                            </td>
                            
                             <td>
                            <?php if($link<>"") { ?>
							 <a href="<?php echo $link;?>" target="_blank"> <i class="glyphicon glyphicon-link"></i></a>                            <?php } ?>
                             </td>
                            
                            <td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Acciones <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
									<?php if ($permisos_editar==1){?>
									<li><a href="edit_indicador.php?id=<?php echo $id;?>"><i class='fa fa-edit'></i> Editar</a></li>
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
		  
