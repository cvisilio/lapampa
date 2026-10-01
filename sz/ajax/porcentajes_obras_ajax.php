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
	
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
     
    $sql="SELECT round(sum(monto_actual),2) as suma_total FROM obras";
	$query = mysqli_query($con,$sql);
	$row = mysqli_fetch_array($query);
	$monto_total=$row['suma_total']; 
    
	$por=" Ministerios";
	
	$tables="obras, ministerios";
	$campos="denominacion,round(sum(monto_actual),2) as suma,count(denominacion) as cantidad";
	$sWhere="ministerios.id=obras.ministerio and obras.estado <> 6 GROUP BY ministerio order by denominacion";		
	include 'pagination.php'; //include pagination file
	//pagination variables
	$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
	$per_page = intval($_REQUEST['per_page']); //how much records you want to show
	$adjacents  = 4; //gap between pages after number of adjacents
	$offset = ($page - 1) * $per_page;
	//Count the total number of row in your table*/
	$count_query   = mysqli_query($con,"SELECT $campos FROM $tables where $sWhere");
	
	$numrows = mysqli_num_rows($count_query);
	
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
				<h3 class="box-title">Porcentaje de Obras por <?php echo $por;?> </h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<table class="table table-condensed table-hover table-striped">
						<tr>
						
                            <td><strong>Ministerio </strong> </td>
                            <td><strong>Porcentaje en Pesos</strong></td>
							<td align="right"><strong>Monto Total</strong></td>
							<td align="right"><strong>Cantidad Obras</strong></td>
                      
						</tr>
						<?php 
						$finales=0;
						
						while($row = mysqli_fetch_array($query)){
						  $porcentaje=round($row['suma'] * 100 / $monto_total,3);
						  $porcentaje=number_format($porcentaje,3,",",".");	
						  $denominacion=$row['denominacion'];
						  $suma=number_format($row['suma'],2,",",".");
						  $cantidad=$row['cantidad'];	
							
							$finales++;
						?>	
						<tr>
                      
							<td><?php echo $denominacion;?></td>
							<td align="right"><?php echo $porcentaje .  "%";?></td>
                            <td align="right"><?php echo "$".$suma;?></td>
                            <td align="right"><?php echo $cantidad;?></td>
                        
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
		  
