<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	/* Connect To Database*/
	
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	
	if (isset($_GET["id"])){
	$tipo=intval($_GET["tipo"]);
	$id=intval($_GET["id"]);
	$sql="select * from necesidades_problemas where id_funcionario='$id' and resuelto='$tipo'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num>0){
	 if($tipo==1)
	  {
	  $color_ganador="success";
	  $lbl_resuelto="Resueltos";
	  }
	 else
	   {
	  $color_ganador="danger";
	  $lbl_resuelto="No Resueltos";
	  }
	 
	 ?>
      
      <div class="panel panel-<?php echo $color_ganador;?>">
       <div class="panel-heading">
     
       <?php
	    
	  $texto_cabecera=$num." Necesidades/Problemas " . $lbl_resuelto; 
	  echo $texto_cabecera;
	  	  
	  ?>
      </div>
      
      <div class="panel-body">
      <div class="table-responsive">
        <table class="table table-striped">
         <tr> 
          <th> Id </th> <th> Problema/Necesidad </th> <th> Avance </th> <th> Localidad </th>
          </tr>
      <?php
	  
	    while ($rw=mysqli_fetch_array($query)){
		  if($tipo==2 && $rw['avance'] > 0)
		    $avance= $rw['avance']. "%";
		  else
		   $avance="";
		   
		  $id= $rw['id']; 
		  $localidad=$rw['localidad'];  
		  
		  $sql_localidad=mysqli_query($con,"select localidad from localidades where id='".$localidad."'");
		 $rw_localidad = mysqli_fetch_array($sql_localidad);
         $localidad=utf8_encode($rw_localidad['localidad']); 
		  	      
	        echo "<tr><td>". $id ."</td><td>".$rw['nombre'] ."</td> <td>". $avance ."</td><td>". $localidad . "</td></tr>";
	  
	    }
	
	 ?>
       </table>
       </div>
        <div class="panel-footer">
     </div>
 <?php }
 }
 ?>	   