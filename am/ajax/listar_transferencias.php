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
    $daterange = mysqli_real_escape_string($con,(strip_tags($_REQUEST['range'], ENT_QUOTES)));
	$motivo =intval($_REQUEST['motivo']);
	$tables="transferencias, localidades, objetivos_motivos";
	$campos="transferencias.*, localidades.localidad,objetivos_motivos.motivo";
	$sWhere=" transferencias.id_localidad=localidades.id and transferencias.afectacion=objetivos_motivos.id and (transferencias.observaciones LIKE '%".$query."%'";
	$campos_suma="sum(transferencias.monto) as suma_montos";
	$sWhere.=" OR objetivos_motivos.motivo LIKE '%".$query."%'";
	$sWhere.=" OR localidades.localidad LIKE '%".$query."%'";
	$sWhere.=" OR transferencias.monto LIKE '".$query."%') ";
	
	if($motivo >0)
	 $sWhere.=" and objetivos_motivos.id ='$motivo'";
	
	if (!empty($daterange)){
		list ($f_inicio,$f_final)=explode(" - ",$daterange);//Extrae la fecha inicial y la fecha final en formato espa?ol
		list ($dia_inicio,$mes_inicio,$anio_inicio)=explode("/",$f_inicio);//Extrae fecha inicial 
		$fecha_inicial="$anio_inicio-$mes_inicio-$dia_inicio 00:00:00";//Fecha inicial formato ingles
		list($dia_fin,$mes_fin,$anio_fin)=explode("/",$f_final);//Extrae la fecha final
		$fecha_final="$anio_fin-$mes_fin-$dia_fin 23:59:59";
		
		$sWhere .= " and transferencias.fecha_registro between '$fecha_inicial' and '$fecha_final' ";
	}
	
	$sWhere.=" order by localidades.localidad";
	
	
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
    $query_suma = mysqli_query($con,"SELECT $campos_suma FROM  $tables where $sWhere");
 	$row_suma = mysqli_fetch_array($query_suma);
	$suma_montos=$row_suma['suma_montos'];
	//loop through fetched data
	


		
	
	if ($numrows>0){
		
	?>
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
                  <!--  <th> </th> -->
						<th>Localidad </th>
						<th class='text-right'>Monto </th>
						<th class='text-center'>Afectacion</th>
                        <th class='text-center'>Fecha</th>
						<th class='text-right'>Cuota</th>
                        
						<th></th>
					</tr>
				</thead>
				<tbody>	
						<?php 
						$finales=0;
						
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$localidad=$row['id_localidad'];
							$nombre_localidad=$row['localidad'];
							$motivo=ucfirst($row['motivo']);
							$monto=$row['monto'];
							$afectacion=$row['afectacion'];
							$cuotas=$row['cuotas'];
							$observaciones=$row['observaciones'];
							$fecha_registro=$row['fecha_registro'];
							list($date,$hora)=explode(" ",$fecha_registro);
							list($Y,$m,$d)=explode("-",$date);
							$fecha=$d."-".$m."-".$Y;		
							
							$finales++;
						?>	
						<tr class="<?php echo $text_class;?>">
							<!--<td><input type="checkbox" name="check2[<?php echo $id;?>]" value="<?php echo $id;?>"> </td> -->
							<td ><?php echo $nombre_localidad;?></td>
                            <td class='text-right'><?php echo "$". number_format($monto,0,",",".");?></td>
							<td class='text-left' ><?php echo $motivo;?></td>
                            <td class='text-left' ><?php echo $fecha;?></td>
					        <td class='text-right'><?php echo $cuotas;?></td>		
							<td>
								
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
									echo ". Total $". number_format($suma_montos,0,",",".")
								?>
						</td>
						</tr>
                      <!--   <tr><td colspan="2">Para todos los seleccionados : </td> <td align="left" colspan="2"> <a href="#deleteProductModalSeleccion" class="delete" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Eliminar">&#xE872;</i>Eliminar</a> </td> <td> </td><td></td> </tr> --> 
				</tbody>			
			</table>
		</div>	

	
	
	<?php	
	}	
}

}
?>          
