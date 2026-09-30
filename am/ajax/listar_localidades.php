<?php
   require_once("../classes/Login.php");
   $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	
	/* Connect To Database*/
	require_once ("../conexion.php");
	include '../classes/cart.php';
	
	$cart = new Cart;
	
	$cartItems = $cart->contents();
	 
	 $total=0;
     foreach ($cartItems as $item) {
     $total+=$item["price"];
   	 }
	
	include '../classes/cart_compromisos.php';
    $cart_compromisos = new Cart_compromisos;
	
	 $cartItemsCompromisos = $cart_compromisos->contents();
	 
	 $total_compromisos=0;
     foreach ($cartItemsCompromisos as $item) {
     $total_compromisos+=$item["price"];
   	 } 
	 
	 $total=$total+$total_compromisos;
	  
	
$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
if($action == 'ajax'){
	$query1 = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query1'], ENT_QUOTES)));

	$tables="localidades";
	$campos="localidades.*";
	$sWhere=" localidades.localidad LIKE '%".$query1."%'";
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
	//loop through fetched data
	
	if ($numrows>0){
		
	?>
    
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
					   <th></th> 
                        <th>Localidad </th>
                     	<th>Transferencias x Compromisos </th>
                        <th> </th>
                         <th>Total TxC </th> 
                        <th>Otras Transferencias </th>
                        <th></th>
                        <th>Total otras</th> 
                         <th>Total a Transferir </th> 
                      <th> </th>
                        <th></th>
					</tr>
				</thead>
				<tbody>	
						<?php 
						$finales=0;
						
						while($row = mysqli_fetch_array($query)){
						    $subtotal_compromisos[$id_localidad]=0;
							$subtotal_otros_compromisos[$id_localidad]=0;	
							$id_localidad=$row['id'];
							$nombre_localidad=$row['localidad'];
						 					    
							$finales++;
						?>	
						<tr class="<?php echo $text_class;?>">
							 <td> </td>
							<td ><?php echo $nombre_localidad;?></td>
                            
                           <td ><select class="form-control"  name="id_compromiso_<?php echo $id_localidad;?>" id="id_compromiso_<?php echo $id_localidad;?>">
						   
                         <?php  
						       
								foreach ($cartItemsCompromisos as $item) {
							     $id_en_array=$item["id_compromiso"];
								 $motivo=$item["id_motivo"];
								 $monto=$item["price"];
								 
								if($id_en_array==$id_localidad)
								 {
								 $subtotal_compromisos[$id_localidad]=$subtotal_compromisos[$id_localidad]+$monto;
								 $sql=mysqli_query($con,"select * from objetivos_motivos where id=$motivo");
								 $rw=mysqli_fetch_array($sql);
						 		 $name=$rw['motivo'] . "($" .number_format($monto,0,",",".").")";
								 ?>
								  <option selected="selected" value="<?php echo $id_en_array; ?>"><?php echo $name; ?></option>							<?php } } ?>	
					  </select></td>
                       <td>
                       <a href="#" onclick="llamar_modal_compromiso(<?php echo $id_localidad; ?>);"> 
                                                                  
                       <i class="material-icons" data-toggle="tooltip" title="Agregar">&#xE147;</i></a> </td>
                      
                           <td> 
                           <input type="text" name="subtotal_compromisos_<?php echo $id_localidad;?>" id="subtotal_compromisos_<?php echo $id_localidad;?>"  class="form-control" value="<?php $subtotal=$subtotal_compromisos[$id_localidad] +0; echo number_format($subtotal,0,",",".");?>" disabled="disabled"> </td>
                           
                            <td ><select class="form-control" name="afectacion_<?php echo $id_localidad;?>" id="afectacion_<?php echo $id_localidad;?>">
                            <!--onChange="if (this.value !='') colocar_datos_array(<?php //echo $id_localidad;?>);" -->
                            
						 <?php  
						      
						      foreach ($cartItems as $item) {
						        $id_en_array=$item["id_localidad"];
								$id_motivo_en_array=$item["id_motivo"];
								$monto=$item["price"];
								
								 
								if($id_en_array==$id_localidad)
								 {
								 $subtotal_otros_compromisos[$id_localidad]=$subtotal_otros_compromisos[$id_localidad]+$monto;
								 $sql=mysqli_query($con,"select * from objetivos_motivos where id=$id_motivo_en_array");
								 $rw=mysqli_fetch_array($sql);
						 		 $name=$rw['motivo'] . "($" .number_format($monto,0,",",".").")";
								 ?>
								  <option selected="selected" value="<?php echo $id_en_array; ?>"><?php echo $name; ?></option>							<?php } } ?>	
					  </select></td>
                      <td> <a href="#" onclick="llamar_modal_transferencia(<?php echo $id_localidad; ?>);"><i class="material-icons" data-toggle="tooltip" title="Agregar">&#xE147;</i></a> </td>
                          
                           
                           <td > <input type="text"  name="subtotal_otros_compromisos_<?php echo $id_localidad;?>" id="subtotal_otros_compromisos_<?php echo $id_localidad;?>" class="form-control" value="<?php echo $subtotal_otros_compromisos[$id_localidad];?>" disabled="disabled">   </td> <!--onChange="if (this.value !='') colocar_datos_array(<?php echo $id_localidad;?>);" -->
                         							                            
                            <td><?php
							$subtotal=$subtotal_otros_compromisos[$id_localidad] + $subtotal_compromisos[$id_localidad];        if($subtotal>0)
							echo "$". number_format($subtotal,0,",",".");?> </td>
                             
                           <td> <?php if($subtotal>0){ ?><a href=""  class="delete" onclick="eliminar_datos_array(<?php echo $id_localidad; ?>);"><i class="material-icons" data-toggle="tooltip" title="Eliminar">&#xE872;</i></a>  <?php } ?>      </td>                   
                           
                    	</tr>
						<?php }?>
						<tr><td colspan="6"><?php  echo "Total a transferir: $" . number_format($total,0,",","."); ?> </td> </tr>
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

 
function colocar_datos_compromiso(id_compromiso,id_localidad,id_motivo,cuota,monto){
   
   $.ajax({
      url:"ajax/insertar_en_array_compromisos.php",
      type: "POST",
      data:"id_compromiso="+id_compromiso+"&id_localidad="+id_localidad+"&id_motivo="+ id_motivo+"&cuota="+ cuota+"&monto="+ monto,
      success: function(opciones){
	 //$("#total").html(opciones);
	     }
    })
    
  }
 
 function colocar_datos_array(id_localidad){
   
   var cuota=document.getElementById('subtotal_compromisos_'+id_localidad).value;
   var monto=document.getElementById('monto_'+id_localidad).value;
   var compromiso = document.getElementById("id_compromiso_"+id_localidad);
   var id_compromiso = compromiso.options[compromiso.selectedIndex].value;
   var motivo = document.getElementById("afectacion_"+id_localidad);
   var id_motivo = motivo.options[motivo.selectedIndex].value;
     
    $.ajax({
      url:"ajax/insertar_en_array.php",
      type: "POST",
      data:"id_compromiso="+id_compromiso+"&id_localidad="+ id_localidad+"&id_motivo="+ id_motivo+"&cuota="+ cuota+"&monto="+ monto,
      success: function(opciones){
	    
         document.getElementById('check_'+id_localidad).checked =true; 	 
	     }
    })
    
  } 
  
 function llamar_modal_transferencia(id_localidad)
 {
 
 $.fn.modal.Constructor.prototype.enforceFocus = function() {};
 
 $('#AgregarTransferenciaTemporalModal').modal({show:true});
 
 $('#id_localidad1').val(id_localidad);
 $('#localidad').val(id_localidad);

 }
 
 function llamar_modal_compromiso(id_localidad)
 {

 $('#monto2').val('');
 $.fn.modal.Constructor.prototype.enforceFocus = function() {};
 
 $('#AgregarCompromisoTemporalModal').modal({show:true});
 
  $.ajax({
      url:"./ajax/compromisos_localidad_select.php",
      type: "POST",
      data:"id="+id_localidad,
      success: function(opciones){
        $("#id_compromiso").html(opciones);
		
	    $("#id_compromiso").change(); //La función change se encuentra en modal_add_compromisos_temporal.php
         }
    })
 
 
 $('#id_localidad2').val(id_localidad);
 $('#localidad2').val(id_localidad);
 
 	
 }

 
 
 function eliminar_datos_array(id_localidad){
        
    $.ajax({
      url:"ajax/eliminar_en_array.php",
      type: "POST",
      data:"id_localidad="+id_localidad,
      success: function(opciones){
	   //location.reload();
		 load(1);
		 }
    }) 
  
  //alert(id_localidad);
  
  } 
 
  
    
  </script>  
		  
