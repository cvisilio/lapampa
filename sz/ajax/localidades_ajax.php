<?php
	session_start();
	
	/* Connect To Database*/
	require_once ("../config/db.php");
	require_once ("../config/conexion.php");
	//Inicia Control de Permisos
	include("../config/permisos.php");
	$user_id = $_SESSION['user_id'];
	$usuario_editor=$_SESSION['usuario_editor'];
	get_cadena($user_id);
	$modulo="Localidades";
	permisos($modulo,$cadena_permisos);
	//Finaliza Control de Permisos
	if (isset($_REQUEST["id"])){//codigo para eliminar 
	$id=$_REQUEST["id"];
	$id=intval($id);
	$eleccion_default=1; //eleccion con los candidatos ganadores (cuerpo concejo) almacenados en la tablas respectivas
	
	
	if ($permisos_eliminar==1){//Si cuenta por los permisos bien
	$query_validate=mysqli_query($con,"select * from obras where localidad='".$id."' or localidad2='".$id."'");
	$count=mysqli_num_rows($query_validate);
	if ($count==123){
			if($delete=mysqli_query($con, "DELETE FROM localidades WHERE id='$id'")){
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
			$msj="Error al eliminar los datos. La Localidad se encuentra vinculada con un dato";
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
	$query2 = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query2'], ENT_QUOTES)));
	$region = intval($_REQUEST['region']);
    $ordenar_por =0; 
	if(isset($_REQUEST['ordenar_por']))
	 $ordenar_por = mysqli_real_escape_string($con,(strip_tags($_REQUEST['ordenar_por'], ENT_QUOTES)));
 
	$tables="localidades";
	$campos="*";
	$sWhere=" localidad LIKE '%".$query."%'";
	if ($query2 >0){
	 $sWhere.=" and localidades.departamento_id = '".$query2."'";
	}

   if ($region >0){
	 $sWhere.=" and localidades.region_am = '".$region."'";
	} 	
	
	switch ($ordenar_por){
	 case 1:
	   $sWhere.=" order by es_localidad desc, localidad asc";
	   break;
	 case 2:
	  $sWhere.=" order by votantes_localidad desc";
	  break;
	 case 3:
	  $sWhere.=" order by cantidad_habitantes desc";
	  break;
	 case 4:
	  $sWhere.=" order by demanda_habitacional desc";
	  break;
	 case 5:
	  $sWhere.=" order by (demanda_habitacional / cantidad_habitantes) desc";
	  break;
	 case 6:
	  $sWhere.=" order by transferencias_2018 desc";
	  break;
	 case 7:
	  $sWhere.=" order by transferencias_2019 desc";
	  break;
	 case 8:
	  $sWhere.=" order by (transferencias_2019 / cantidad_habitantes) desc";
	  break;  
     case 9:
	  $sWhere.=" order by coparticipacion_2019 desc";
	  break;
	 case 10:
	  $sWhere.=" order by (coparticipacion_2019 / cantidad_habitantes) desc";
	  break; 
	  
	 default:	 
	   $sWhere.=" order by es_localidad desc, localidad asc";
	  break;
	
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
	
	// $count=mysqli_query($con,"select sum(Importe) AS Suma_Total_Importe from transferencias"); // habilitar esto y también parte del codigo donde graba para cuando se carguen las transferencias nuevas, una vez que se graben en campo se deshabilita. 
	// $rw_count=mysqli_fetch_array($count);
	// $Suma_Total_Importe= number_format($rw_count['Suma_Total_Importe'],"2",",",".");
	 
	 $sumas=mysqli_query($con,"select sum(demanda_habitacional) as suma_demanda_habitacional, sum(votantes_localidad) as suma_votantes_localidad, sum(votantes_localidad_2018) as suma_votantes_localidad_2018, sum(cantidad_habitantes) as suma_cantidad_habitantes, sum(transferencias_2018) as suma_total_transferencias_2018, sum(transferencias_2019) as suma_total_transferencias_2019, sum(coparticipacion_2019) as suma_total_coparticipacion_2019 from localidades"); //
	  $rw_suma=mysqli_fetch_array($sumas);
	  $suma_votantes_localidad=number_format($rw_suma['suma_votantes_localidad'],"0",",",".");
	  
	  $variacion_total_la_pampa=100-(($rw_suma['suma_votantes_localidad_2018'] * 100) / $rw_suma['suma_votantes_localidad']);
	  	  
	  $total_variacion_votantes_anterior=number_format($variacion_total_la_pampa,"2",",",".");
	  
	  $suma_cantidad_habitantes=number_format($rw_suma['suma_cantidad_habitantes'],"0",",",".");
	  $suma_demanda_habitacional=number_format($rw_suma['suma_demanda_habitacional'],"0",",",".");
	  
	  $total_porcentaje_demanda_habitacional=round($suma_demanda_habitacional * 100 / $suma_cantidad_habitantes,2);
	 $total_porcentaje_demanda_habitacional=number_format($total_porcentaje_demanda_habitacional,"2",",",".") . "%"; 
							 
     $suma_total_transferencias_2018= number_format($rw_suma['suma_total_transferencias_2018'],"0",",",".");							 	 
	 $suma_total_transferencias_2019= number_format($rw_suma['suma_total_transferencias_2019'],"0",",",".");
	 
	 $suma_transferencias_per_capita_2019=number_format($rw_suma['suma_total_transferencias_2019']/$rw_suma['suma_cantidad_habitantes'],"0",",",".");	
	 
	  $suma_total_coparticipacion_2019= number_format($rw_suma['suma_total_coparticipacion_2019'],"0",",",".");	
	  			
	  $suma_coparticipacion_per_capita_2019=round($rw_suma['suma_total_coparticipacion_2019'] / $rw_suma['suma_cantidad_habitantes'],0);
	  
	  
	  $suma_coparticipacion_per_capita_2019=number_format($suma_coparticipacion_per_capita_2019,"0",",",".");

	?>
	
	<div class="row">
      <hr>
                           <?php 
						   $sql_archivos=mysqli_query($con,"select * from links where id_tabla=2 and id_registro='$ambito'");
						   while ($rw_archivos=mysqli_fetch_array($sql_archivos)){
							  $archivo=$rw_archivos['link'];
							  $titulo=$rw_archivos['titulo'];
							  
							  echo  '| ' .'<a href="'.$archivo.'" target="_blank">'.$titulo.'</a>';
							}
						   ?>
		<div class="col-md-12">
			<div class="box">
				<div class="box-header with-border">
				<h3 class="box-title">Listado de Localidades</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
						
							<th><button type="button" class="btn btn-link" onclick="ordenar(1);">Localidad</button> </th>
                            
                             <th>  </th>
                            
                             
                              <th> <button type="button" class="btn btn-link" onclick="ordenar(1);">Partido</button></th>
                             
                             <th> 
                              
                             </th> 
                           
                             <th> <button type="button" class="btn btn-link" onclick="ordenar(1);">Tipo</button></th>
                              
                            
                            <!--<th align="right"><button type="button" class="btn btn-link" onclick="ordenar(7);">Dif. al Padr&oacute;n Ult.</button></th> -->
							<th align="right"><button type="button" class="btn btn-link" onclick="ordenar(2);">Vot.</button></th>
                            <th align="right"><button type="button" class="btn btn-link" onclick="ordenar(3);">Hab.</button></th>
                            
							<th align="right"><button type="button" class="btn btn-link" onclick="ordenar(4);">D. Hab.</button></th>
							<th align="right"><button type="button" class="btn btn-link" onclick="ordenar(5);">% Dem.</button></th>
                           <th align="right"><button type="button" class="btn btn-link" onclick="ordenar(6);">Tranf. 2018</button></th>
                            
                           <th align="right"><button type="button" class="btn btn-link" onclick="ordenar(7);">Tranf.  2019</button></th>
                            
                             <th align="right"><button type="button" class="btn btn-link" onclick="ordenar(8);">Tranf.x.Hab.2019</button></th>
                            
                             <th align="right"><button type="button" class="btn btn-link" onclick="ordenar(9);">Coparticip. 2019</button></th>
                             
                             
                             
                              <th align="right"><button type="button" class="btn btn-link" onclick="ordenar(10);">Cop.x.Hab.2019</button></th>
                            
                        
                            <th> </th>
                            
							
						</tr>
                        
                       <tr>
						
				     <td style="color:#FF0000"><strong> Totales..: </strong></td>
                            
                             <td> </td>
                              <td> </td>
                               <td> </td>
                              
                                <td> </td>
                         <!--   <td style="color:#FF0000" align="right"><strong><?php //echo  $total_variacion_votantes_anterior . "%"; ?></strong></td> -->
							<td style="color:#FF0000" align="right"><strong><?php echo  $suma_votantes_localidad ?></strong></td> 
                            <td style="color:#FF0000" align="right"><strong><?php echo   $suma_cantidad_habitantes ?></strong></td>
                            
							<td style="color:#FF0000" align="right"><strong><?php echo   $suma_demanda_habitacional ?></strong></td>
							<td style="color:#FF0000" align="right"><strong><?php echo   $total_porcentaje_demanda_habitacional ?></strong></td>
                            
                            <td  style="color:#FF0000" align="right"><strong>$ <?php echo $suma_total_transferencias_2018 ?></strong></td>
                            <td style="color:#FF0000" align="right"><strong>$ <?php echo $suma_total_transferencias_2019 ?></strong></td>
                            
                             <td style="color:#FF0000" align="right"><strong>$ <?php echo $suma_transferencias_per_capita_2019 ?></strong></td>
                            
                            <td style="color:#FF0000" align="right"><strong>$ <?php echo $suma_total_coparticipacion_2019 ?></strong></td>
                           
                            <td style="color:#FF0000" align="right"><strong>$ <?php echo $suma_coparticipacion_per_capita_2019 ?></strong></td>
                           
                         <td> </td>             
						
				    </tr>
                                                 
						<?php 
						$finales=0;
						
						while($row = mysqli_fetch_array($query)){	
							$id=$row['id'];
							$localidad=utf8_encode($row['localidad']);
							$localidad_aux=$row['localidad'];
							$cantidad_votantes=$row['votantes_localidad'];
							$cantidad_votantes_2018=$row['votantes_localidad_2018'];
					        $cantidad_habitantes=$row['cantidad_habitantes'];
							$demanda_habitacional=$row['demanda_habitacional'];
							$id_loc_gob_c=$row['id_loc_gob_c'];
							$latitud=$row['latitud'];
							$longitud=$row['longitud'];
							$lista_ganadora=$row['lista_ganadora'];
							$tipo_localidad=$row['es_localidad'];
							
							$tipo_localidad_label="";
							if($tipo_localidad==2)
							 $tipo_localidad_label="cf";
							elseif ($tipo_localidad==3)
							 $tipo_localidad_label="Loc";
														
							$candidatos=$row['candidatos'];
							list($L1,$L2,$L3,$L4,$L5,$L6,$L7,$L8,$L9,$L10)=explode(";",$candidatos);
							
							$lbl_proximo_partido="";
							$lbl_class_proximo_partido="";
							
							if ($lista_ganadora==150){
								$lbl_proximo_partido="Frejupa";
								list($xx,$intendente)=explode(":",$L1);
								
								$lbl_class_proximo_partido='label label-primary';
							}elseif ($lista_ganadora==149)  {
								$lbl_proximo_partido="Camb.";
								$lbl_class_proximo_partido='label label-warning';
								list($xx,$intendente)=explode(":",$L2);
							
							}elseif ($lista_ganadora==139)  {
								$lbl_proximo_partido="Com. Org.";
								$lbl_class_proximo_partido='label label-default';
								list($xx,$intendente)=explode(":",$L4);
							}elseif ($lista_ganadora==137 or $lista_ganadora==138 or $lista_ganadora==140 or $lista_ganadora==141 or $lista_ganadora==142 or $lista_ganadora==143 or $lista_ganadora==145 or $lista_ganadora==148 or $lista_ganadora==152)  {
								$lbl_proximo_partido="J.V.";
								$lbl_class_proximo_partido='label label-success';
								list($xx,$intendente)=explode(":",$L10);

							}
							
							$intendente="Pr&oacute;ximo Intendente de " . $localidad_aux ." : ". $intendente;
							
							$modulos=$row['modulos'];
							if ($demanda_habitacional > 0 and $cantidad_habitantes>0)
							{
							 $porcentaje_demanda_habitacional=round($demanda_habitacional * 100 / $cantidad_habitantes,2);
							 $label_porcentaje_demanda_habitacional=number_format($porcentaje_demanda_habitacional,"2",",",".") . "%";  
							
							}else
							$label_porcentaje_demanda_habitacional="-";
						    
						   if ($cantidad_votantes >0)	 
							{$votantes_porcentaje_variacion = 100-(100 * $cantidad_votantes_2018 / $cantidad_votantes);
							 $label_votantes_porcentaje_variacion=number_format($votantes_porcentaje_variacion,"2",",",".") . "%"; 
							 }
							else{
							 $label_votantes_porcentaje_variacion ="";
							}
							   
							//$count=mysqli_query($con,"select sum(Importe) AS Suma_Importe from transferencias where Loc='".$id_loc_gob_c."'" );
							//$rw_count=mysqli_fetch_array($count);
						//	$Suma_Importe=$rw_count['Suma_Importe'];
							
							$Importe_transferencia_2018=$row['transferencias_2018'];
							$Importe_transferencia_2019=$row['transferencias_2019'];
							$Importe_transferencia_per_capita_2019=round($Importe_transferencia_2019 / $cantidad_habitantes,0);
							$Importe_coparticipacion_2019=$row['coparticipacion_2019'];
							$Importe_coparticipacion_per_capita_2019=round($Importe_coparticipacion_2019 / $cantidad_habitantes,0);
							
						//	 $sql3 = "UPDATE localidades SET transferencias_2018='". $Suma_Importe."' WHERE id='".$id."'"; // esto es para actualizar las transeferencias al campo transferencias_2018 desde tabla transeferencias
    //$query3 = mysqli_query($con,$sql3);
							
							$finales++;
												  
						?>	
						<tr>
                       
							<td>
							
							<?php echo $localidad;?>
                            
                            </td>
                            
                            <td>
     <a href="#" data-toggle="modal" data-target="#myModal" data-lat='<?php echo $latitud; ?>' data-lng='<?php echo $longitud; ?>'><i class='fa fa-globe'></i></a>
        
   
    </td>
    
    <td align="center">
		<span class="<?php echo $lbl_class_proximo_partido;?>"><?php echo $lbl_proximo_partido;?></span>
							</td>
                         <td align="center"> 
						 <?php if ($permisos_ver==1 && $tipo_localidad>1){?>
									<a href="#" data-toggle="modal" data-target="#modal_update2" onclick="informacion1('<?php echo $id;?>','<?php echo $localidad;?>');"><i class='fa fa-info'>nfo</i></a>
            
									<?php } ?>
                                     </td> 
                            
                                  
                                 <td align="center"> <?php echo $tipo_localidad_label; ?> </td> 
                                       
    
                          <!--  <td align="right"><?php //echo $label_votantes_porcentaje_variacion;?></td> -->
                            <td align="right"><?php echo number_format($cantidad_votantes,"0",",",".");?></td>
							<td align="right"><?php echo number_format($cantidad_habitantes,"0",",",".");?></td>
							<td align="right"><?php echo number_format($demanda_habitacional,"0",",",".");?></td>
							<td align="right"><?php echo $label_porcentaje_demanda_habitacional;?></td>
							
                            <td align="right"><?php echo "$" . number_format($Importe_transferencia_2018,"0",",",".");?></td>
                              <td align="right"><?php echo "$" . number_format($Importe_transferencia_2019,"0",",",".");?></td> 
                             
                             <td align="right"><?php echo "$" . number_format($Importe_transferencia_per_capita_2019,"0",",",".");?></td>  
                              
                              <td align="right"><?php echo "$" . number_format($Importe_coparticipacion_2019,"0",",",".");?></td> 
                              
                               <td align="right"><?php echo "$" . number_format($Importe_coparticipacion_per_capita_2019,"0",",",".");?></td> 
                                
                              
                           
                          
							<td>
							<div class="btn-group pull-right">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Accion<span class="fa fa-caret-down"></span></button>
								<ul class="dropdown-menu">
							
								<?php if ($permisos_editar==1){?>
									<li><a href="#" data-toggle="modal" data-target="#modal_update" onclick="editar('<?php echo $id;?>','<?php echo $localidad;?>');"><i class='fa fa-edit'></i> Editar</a></li>
									<?php }
							   ?>
							  <li>  
                             <a href="edit_nota.php?id=<?php echo $id;?>&localidad=<?php echo $localidad;?>"><i class='fa fa-edit'></i> Nota</a>
                            </li>
							<?php
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
		  
