<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	/* Connect To Database*/
	require_once ("../conexion.php");

	
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));

	$tables="disponibilidades_x_leyes, leyes";
	$campos="disponibilidades_x_leyes.*, leyes.abreviatura";
	$sWhere.=" disponibilidades_x_leyes.id_ley =leyes.id and leyes.abreviatura LIKE '%".$query."%'";
	
	$sWhere.=" order by leyes.abreviatura";
	
	
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
						<th>Ley (origen)</th>
                        <th align="center">Monto Disponible</th>
                        <th>A&ntilde;o</th>
                        <th>Partida</th>
					                        
						<th></th>
					</tr>
				</thead>
				<tbody>	
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$id_ley=$row['id_ley'];
							$ley=$row['abreviatura'];
							$monto=$row['monto_disponible'];
							$anio=$row['anio'];
							$partida=$row['partida'];
							
							$finales++;
						?>	
						<tr class="<?php echo $text_class;?>">
						
                            <td class='text-left' ><?php echo $ley;?></td>
                            <td class='text-right' ><?php echo "$".number_format($monto,0,",",".");?></td>
                            <td class='text-left' ><?php echo $anio;?></td>
                            <td class='text-left' ><?php echo $partida;?></td>
                            
                            
							<td class='text-right' >
								<a href="#" data-target="#editProductModal" class="edit" data-toggle="modal" data-monto="<?php echo $monto?>" data-anio="<?php echo $anio?>" data-ley="<?php echo $id_ley?>" data-partida="<?php echo $partida?>" data-id="<?php echo $id; ?>"><i class="material-icons" data-toggle="tooltip" title="Editar" >&#xE254;</i></a>
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