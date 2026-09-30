<?php
if (!isset($_SESSION)) {
  session_start();
}
?>


<?php
 require_once('Connections/conexionUsuarios.php'); 

 $Partido[1]["color"]= "#0066CC"; // Azul PJ
 $Partido[2]["color"]=  "#CC0099"; // Violeta - Milei
 $Partido[3]["color"]=  "#CCCCCC";
 $Partido[4]["color"]=  "#CCCCCC";
 $Partido[5]["color"]=  "#CCCCCC";
 
$MM_authorizedUsers = "1,2,3,4";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php

$colname_mesasfaltxloc = "-1";
if (isset($_GET['menu'])) {
  $colname_mesasfaltxloc = intval($_GET['menu']);
}


$query_mesasfaltxloc = "SELECT mesas.*,entidades.Establecimiento, localidades.Localidad FROM mesas, entidades,localidades WHERE Escrutada = 'S' and mesas.CodigoEscuela=entidades.Id and mesas.CodigoLocalidad = localidades.id_loc_padron and mesas.CodigoLocalidad = '$colname_mesasfaltxloc' ORDER BY mesas.Id";

$mesasfaltxloc=mysqli_query($con,$query_mesasfaltxloc);
$row_mesasfaltxloc=mysqli_fetch_array($mesasfaltxloc);

$totalRows_mesasfaltxloc = mysqli_num_rows($mesasfaltxloc);

 $Idlocalidad =$colname_mesasfaltxloc;
 
 $cadena = "select * from mesas WHERE CodigoLocalidad='$Idlocalidad'";

 $mesasxloc =mysqli_query($con,$cadena);
 $TotalMesas =  mysqli_num_rows($mesasxloc);
  
 if ($totalRows_mesasfaltxloc >0){
  $Porcentaje = ($totalRows_mesasfaltxloc * 100) / $TotalMesas;
  $Porcentaje = round($Porcentaje,2);
  }
 else{
  $Porcentaje = 0;
 }  

?>
<style type="text/css">
<!--
.Estilo2 {
	font-size: 18px;
	font-weight: bold;
	color: #003399;
}
.Estilo4 {font-size: 14px; font-weight: bold; color: #003399; }
.Estilo5 {color: #0066FF}
-->
</style>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.0.0/dist/tf.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


<table class="table">
  <tr>
   
    <td><div align="center" class="Estilo4">
        <?php  echo $totalRows_mesasfaltxloc;?> 
    -de-<?php echo $TotalMesas;?> ( <?php echo $Porcentaje;?> %) Mesas Escrutadas</div></td>
  </tr>
</table>

  <table class="table">
    <tr>
     
      <td><div align="center"><strong>Escuela</strong></div></td>
      <th scope="row"><div align="center"><strong>Mesa</strong></div></th>
      <th scope="row"><div align="center"><strong>PJ General</strong></div></th>
      <th scope="row"><div align="center"><strong>PJ Balotaje</strong></div></th>
      <th scope="row"><div align="center"><strong>LLA General.</strong></div></th>
      <th scope="row"><div align="center"><strong>LLA Balotaje</strong></div></th>
    </tr>
    <?php 
	$total_pj_anterior=0;
	$total_pj_general=0;
	$total_jxc_anterior=0;
	$total_jxc_general=0;
	$filas=0;
	$cadena_x1="";
	$cadena_y1="";
			
	do { 
	 
	 $Mesa=$row_mesasfaltxloc['Mesa'];    
	 
	 $sql="select * from mesas_generales_presidente_2023 where Mesa='$Mesa'";
	 
	 $query_resultados=mysqli_query($con,$sql);
	 $row_resultados=mysqli_fetch_array($query_resultados);
	 $Codigo_Localidad_Mesa_Anterior=$row_resultados['CodigoLocalidad'];
	 
	 $suma_pj_anterior=$row_resultados['L3G'];
	 $suma_pj_general=$row_mesasfaltxloc['L1G'];
	 
	 $suma_jxc_anterior=$row_resultados['L1G'];
	 $suma_jxc_general=0;
	 
	 $suma_libertad_avanza_anterior=$row_resultados['L4G'];
	 $suma_libertad_avanza_general=$row_mesasfaltxloc['L2G'];
	 
	 $total_pj_anterior+=$suma_pj_anterior;
	 $total_pj_general+=$suma_pj_general;
	 $total_jxc_anterior+=$suma_jxc_anterior;
	 $total_jxc_general+=$suma_jxc_general;
	 $total_libertad_avanza_anterior+=$suma_libertad_avanza_anterior;
	 $total_libertad_avanza_general+=$suma_libertad_avanza_general;
	 
	 // para determinar quién gana hasta ahora con las mesas escrutadas
		  
	if($suma_pj_general > $suma_jxc_general) 
	 {
	  $t = $suma_pj_general;
      $color_ganador=$Partido[1]["color"];
	 }
	else 
	 {
	 $t = $suma_jxc_general;
	 $color_ganador=$Partido[2]["color"];
	 }
	
	if($t > $suma_libertad_avanza_general)  
	  { 
	 //  $mayor = $t;
	   $color_ganador=  $color_ganador;
	  }
	else 
	  { 
	 //  $mayor = $suma_libertad_avanza_general;
	   $color_ganador= $Partido[2]["color"];
	  }
	// hasta acá determina quién gana
	
	 $filas++;
			
	?>
    <tr>
      <td><?php echo $row_mesasfaltxloc['Establecimiento']; ?></td>
      <td><?php echo "Mesa Nro: ". $row_mesasfaltxloc['Mesa']; ?></td>
      <th scope="row" style="color:<?php echo $Partido[1]['color']; ?>" ><div align="center"><?php echo $suma_pj_anterior; ?></div></th>
      <th scope="row" style="color:<?php echo $Partido[1]['color']; ?>" ><div align="center"><?php echo $suma_pj_general; ?></div></th>
         
      <th scope="row" style="color:<?php echo $Partido[2]['color']; ?>" ><div align="center"><?php echo $suma_libertad_avanza_anterior; ?></div></th>
      <th scope="row" style="color:<?php echo $Partido[2]['color']; ?>" ><div align="center"><?php echo $suma_libertad_avanza_general; ?></div></th>
            
     <?php $iguales="No"; 
	    if ($Codigo_Localidad_Mesa_Anterior==$Idlocalidad)
	      $iguales="Si";
	 ?>
         
    </tr>
    <?php 
	 }
	 while ($row_mesasfaltxloc = mysqli_fetch_assoc($mesasfaltxloc))  ?>
    <tr>
      <td colspan="2" align="right"><?php echo "Total : "; ?></td>
      <th scope="row" style="color:<?php echo $Partido[1]['color']; ?>" ><div align="center"><?php echo number_format($total_pj_anterior,0,'','.'); ?></div></th>
      <th scope="row" style="color:<?php echo $Partido[1]['color']; ?>" ><div align="center"><?php echo  number_format($total_pj_general,0,'','.'); ?></div></th>
           
       <th scope="row" style="color:<?php echo $Partido[2]['color']; ?>" ><div align="center"><?php echo  number_format($total_libertad_avanza_anterior,0,'','.'); ?></div></th>
      <th scope="row" style="color:<?php echo $Partido[2]['color']; ?>" ><div align="center"><?php echo  number_format($total_libertad_avanza_general,0,'','.'); ?></div></th>
    </tr>
  </table>

<?php
 // valores a calcular
 ?>