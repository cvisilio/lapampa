<?php
	session_start();
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	get_cadena($user_id);
	$modulo="Datos";
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	 $count=0;
	if ($count==0){
			if($delete=mysqli_query($con, "DELETE FROM datos WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. El dato se encuentra vinculado con otro dato";
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
	$tables="datos, rubros";
	$campos="datos.id,datos.titulo,datos.ministerio,datos.tipo,datos.dato,datos.image_path,datos.iddependiente,datos.ambito,datos.orden,datos.created_at,datos.fecha_modificado,datos.status,rubros.name";
	$sWhere="datos.rubro=rubros.id"; //and rubros.status=1
	$sWhere.=" and datos.dato LIKE '%".$query."%'";
	//$sWhere.=" and products.product_code LIKE '%".$query2."%'";
	$sWhere.=" order by datos.id desc";
	
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
				<h3 class="box-title">Listado de Datos</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
							<th class='text-center'>Imagen</th>
							<th>Modificado </th>
							<th>Titulo </th>
                            <th>Estado</th>
							<th>Ambito </th>
							<th class='text-center'>Rubro</th>
                        					
						</tr>
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$dato_id=$row['id'];
						
							$fecha_modificado= date('d/m/Y', strtotime($row['fecha_modificado']));
							//$model=$row['model'];
							$titulo=$row['titulo'];
							$rubro_name=$row['name'];
							$tipo=$row['tipo'];
							$ambito=$row['ambito'];
							$image_path=$row['image_path'];
						    $status=$row['status'];
							
							if ($status==1){
								$lbl_status="Activo";
								$lbl_class='label label-primary';
							}else {
								$lbl_status="Inactivo";
								$lbl_class='label label-danger';
							}
						   
									                    
							if ($ambito==1){
								$lbl_ministerio="Global";
								$lbl_class_ministerio='label label-success';
							}elseif ($ambito==2)  {
								$lbl_ministerio="Ministerio";
								$lbl_class_ministerio='label label-info';
							
							}elseif ($ambito==3)  {
								$lbl_ministerio="Detalle";
								$lbl_class_ministerio='label label-default';
							}
													
													
							$finales++;
						?>	
						<tr>
							<td class='text-center'>
							<a href="ver_dato.php?id=<?php echo $dato_id;?>">	<img src="<?php echo $image_path;?>" alt="Product Image" class='img-rounded' width="60"></a>
							</td>
							<td><?php echo $fecha_modificado;?></td>
						
                             <td><a href="ver_dato.php?id=<?php echo $dato_id;?>"><?php echo $titulo;?></a> </td>
                            <td>
								<span class="<?php echo $lbl_class;?>"><?php echo $lbl_status;?></span>
							</td>
                            
							<td>
								<span class="<?php echo $lbl_class_ministerio;?>"><?php echo $lbl_ministerio;?></span>
							</td>
							<td><?php echo $rubro_name;?></td>
							<td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Acciones <span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
									<?php if ($permisos_editar==1){?>
									 <li><a href="edit_dato.php?id=<?php echo $dato_id;?>"><i class='fa fa-edit'></i> Editar</a></li>
									<?php }
									
									
									if ($permisos_eliminar==1){
									?>
									<li><a href="#" onclick="eliminar('<?php echo $dato_id;?>')"><i class='fa fa-trash'></i> Borrar</a></li>
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
		  
