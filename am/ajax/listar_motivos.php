<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	/* Connect To Database*/
	require_once ("../conexion.php");
	
	//if ($user_id==2)
 	//{
	 // El siguiente código es para corregir caracteres especiales
 	/* 
	$query1=mysqli_query($con,"select * from objetivos_motivos");
	while($row = mysqli_fetch_array($query1)){
	 $id=$row['id'];
	 $nombre= utf8_encode($row['motivo']);
	// $observaciones = utf8_encode($row['observaciones']);

	 $sql = "UPDATE objetivos_motivos SET motivo='".$nombre."' WHERE id='".$id."' ";
	 //,observaciones='".$observaciones."'
     $query_actualiza= mysqli_query($con,$sql);
	
	} //while
 	
	*/
 //	} // if user_id==2
	

	
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));

	$tables="objetivos_motivos";
	$campos="objetivos_motivos.*";
	$sWhere.=" objetivos_motivos.motivo LIKE '%".$query."%'";
	
	$sWhere.=" order by objetivos_motivos.motivo";
	
	
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
	//main query to fetch the data
	$query = mysqli_query($con,"SELECT $campos FROM  $tables where $sWhere LIMIT $offset,$per_page");
	//loop through fetched data

	if ($numrows>0){
		
	?>
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Motivo</th>
					                        
						<th></th>
					</tr>
				</thead>
				<tbody>	
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$motivo=ucfirst($row['motivo']);
							$finales++;
						?>	
						<tr class="<?php echo $text_class;?>">
							<td class='text-left' ><?php echo $motivo;?></td>
							<td class='text-right' >
								<a href="#" data-target="#editProductModal" class="edit" data-toggle="modal" data-motivo1="<?php echo $motivo?>" data-id="<?php echo $id; ?>"><i class="material-icons" data-toggle="tooltip" title="Editar" >&#xE254;</i></a>
								<a href="#deleteProductModal" class="delete" data-toggle="modal" data-id="<?php echo $id;?>"><i class="material-icons" data-toggle="tooltip" title="Eliminar">&#xE872;</i></a>
                    		</td>
						</tr>
						<?php }?>
						<tr>
							<td colspan='6'> 
								<?php 
									$inicios=$offset+1;
									$finales+=$inicios -1;
									echo "Mostrando $inicios al $finales de $numrows registros";
									echo paginate( $page, $total_pages, $adjacents);
								?>
							</td>
						</tr>
				</tbody>			
			</table>
		</div>	
	<?php	
	}	
}

}
?>   