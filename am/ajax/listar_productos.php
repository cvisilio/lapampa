<?php
 require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	/* Connect To Database*/
	require_once ("../conexion.php");
	
	include '../classes/cart_compromisos.php';
    $cart_compromisos = new Cart_compromisos;
	
	 $cartItemsCompromisos = $cart_compromisos->contents();
	 
	 $total=0;
     foreach ($cartItemsCompromisos as $item) {
     $total+=$item["price"];
   	 }
	
   /*
	 // El siguiente c�digo es para corregir caracteres especiales
	$query1=mysqli_query($con,"select * from objetivos_motivos");
	while($row = mysqli_fetch_array($query1)){
	 $id=$row['id'];
	 $nombre= utf8_decode($row['motivo']);
	
	 $sql = "UPDATE objetivos_motivos SET motivo='".$nombre."' WHERE id='".$id."' ";
     $query_actualiza= mysqli_query($con,$sql);
		
	}
	*/
	
	
	
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));
    $mes_query = intval($_REQUEST['mes_query']); 
	$anio_query=intval($_REQUEST['anio_query']);
			 
	$tables="compromisos_am, localidades, objetivos_motivos";
	$campos="compromisos_am.*, localidades.localidad,objetivos_motivos.motivo";
	$sWhere=" compromisos_am.id_localidad=localidades.id and compromisos_am.afectacion=objetivos_motivos.id and (compromisos_am.observaciones LIKE '%".$query."%'";
	$sWhere.=" OR objetivos_motivos.motivo LIKE '%".$query."%'";
	$sWhere.=" OR localidades.localidad LIKE '%".$query."%'";
	$sWhere.=" OR compromisos_am.monto LIKE '".$query."%') ";
	
	$dia=1;
	$fecha = $anio_query . '-'. $mes_query.'-'.$dia;
	
	if($mes_query>0)
	 $sWhere.= " and (compromisos_am.fecha_final_transferencia >='$fecha' and compromisos_am.anio<=$anio_query and compromisos_am.mes<=$mes_query)";
	  
	$sWhere.=" order by compromisos_am.id desc";
	
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
					  <th> </th> 
                        <th>Localidad </th>
                        <th>Cuota </th>
                       <th class='text-center'>Afectacion</th>
						 <th class='text-center'>Mes/A&ntilde;o</th>
                        <th class='text-right'>Cuotas</th>
                        
                         <th class='text-right'>Total </th>
                        
						<th></th>
					</tr>
				</thead>
				<tbody>	
						<?php 
						$finales=0;
						while($row = mysqli_fetch_array($query)){
						  $mes=$row['mes'];
						  $anio=$row['anio'];
						  $cuotas=intval($row['cuotas']);
						  	
						// if(!($mes > $mes_query and $cuotas ==0 and $mes_query >0))
						  // {	
						    $id=$row['id'];
							$localidad=$row['id_localidad'];
							
							 $indice = md5((string) $id);
							 $id_en_array = isset($cartItemsCompromisos[$indice]['id'])
								 ? $cartItemsCompromisos[$indice]['id']
								 : null;

							 if ($id_en_array !== null && $id_en_array !== '') {
								 $checked = 'checked';
							 } else {
								 $checked = '';
							 }
							  
							
							$nombre_localidad=$row['localidad'];
							$motivo=ucfirst($row['motivo']);
							
							$monto=$row['monto'];
							$afectacion=$row['afectacion'];
							
							if($cuotas > 0)
							 {
							 $monto_transferir=round($monto / $cuotas,0);
							 $valor_cuota="$". number_format($monto_transferir,0,",",".");
							 }
							else
							 {
							 $monto_transferir=round($monto);
							 $valor_cuota="";
							 }
							$mes=$row['mes'];
							$anio=$row['anio'];
							$observaciones=$row['observaciones'];						
							$finales++;
						?>	
						<tr class="<?php echo $text_class;?>">
						    <td><input type="checkbox" <?php echo $checked; ?> id="check_<?php echo $id;?>" name="check_<?php echo $id;?>" value="0" <?php ?> onChange="if (this.checked==true) colocar_datos_array(<?php echo $id;?>,<?php echo $localidad;?>,<?php echo $afectacion;?>,<?php echo $cuotas;?>,<?php echo $monto_transferir;?>); else eliminar_datos_array(<?php echo $id;?>);"> </td>	                        
							<td ><?php echo $nombre_localidad;?></td>
                           
                            <td class='text-right'><?php echo $valor_cuota;?></td>		
							<td class='text-center' ><?php echo $motivo;?></td>
                             <td ><?php echo $mes ."/".$anio;?></td>
					        <td class='text-right'><?php echo $cuotas;?></td>
                             <td class='text-right'><?php echo "$". number_format($monto,0,",",".");?></td>		
							<td>
								<a href="#" data-target="#editProductModal" class="edit" data-toggle="modal" data-observaciones='<?php echo $observaciones;?>' data-localidad="<?php echo $localidad?>" data-monto="<?php echo $monto?>" data-afectacion="<?php echo $afectacion?>" data-cuotas="<?php echo $cuotas;?>" data-mes="<?php echo $mes;?>" data-anio="<?php echo $anio;?>" data-id="<?php echo $id; ?>"><i class="material-icons" data-toggle="tooltip" title="Editar" >&#xE254;</i></a>
								<a href="#deleteProductModal" class="delete" data-toggle="modal" data-id="<?php echo $id;?>"><i class="material-icons" data-toggle="tooltip" title="Eliminar">&#xE872;</i></a>
                    		</td>
						</tr>
						<?php 
					//	 }// !($mes > $mes_query and $cuotas ==0)
					
						} // while?>
                        <tr><td colspan="6"><div id="total"><?php  echo "Total Seleccionado: $" . number_format($total,0,",","."); ?> </div> </td> </tr>
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
<script>
 function colocar_datos_array(id_compromiso,id_localidad,id_motivo,cuotas,monto){
     
    $.ajax({
      url:"ajax/insertar_en_array_compromisos.php",
      type: "POST",
      data:"id_compromiso="+id_compromiso+"&id_localidad="+id_localidad+"&id_motivo="+ id_motivo+"&cuotas="+ cuotas+"&monto="+ monto,
      success: function(opciones){
	 $("#total").html(opciones);
	     }
    })
    
  } 

 function eliminar_datos_array(id_compromiso){
  
    $.ajax({
      url:"ajax/eliminar_en_array_compromisos.php",
      type: "POST",
      data:"id_compromiso="+id_compromiso,
      success: function(opciones){
       document.getElementById('check_'+id_compromiso).checked =false; 
	   $("#total").html(opciones);
		 }
    })
    
 
  }   
 </script> 