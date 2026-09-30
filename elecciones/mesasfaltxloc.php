<?php
if (!isset($_SESSION)) {
  session_start();
}

?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<?php

 require_once('Connections/conexionUsuarios.php'); 


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
if (isset($_POST['menu'])) {
  $colname_mesasfaltxloc = intval($_POST['menu']);
}


$query_mesasfaltxloc = "SELECT mesas.*,entidades.Establecimiento, localidades.Localidad FROM mesas, entidades,localidades WHERE Escrutada = 'N' and mesas.CodigoEscuela=entidades.Id and mesas.CodigoLocalidad = localidades.id_loc_padron and mesas.CodigoLocalidad = '$colname_mesasfaltxloc' ORDER BY mesas.Id";

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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Mesas Faltantes por Localidad</title>
<style type="text/css">
<!--
.Estilo2 {
	font-size: 18px;
	font-weight: bold;
	color: #003399;
}
.Estilo3 {font-size: 20px}
.Estilo4 {font-size: 20px; font-weight: bold; color: #003399; }
.Estilo5 {color: #0066FF}
-->
</style>
</head>

<body>
<table class="table">
  <tr>
    <th width="18%" scope="row"><div align="left" class="Estilo2 Estilo3">
      <div align="center"><a href="cargarmesas.php">(Volver)</a></div>
    </div></th>
    <td width="82%"><div align="center" class="Estilo4">
        <?php  echo $totalRows_mesasfaltxloc;?> 
    -de-<?php echo $TotalMesas;?> ( <?php echo $Porcentaje;?> % Mesas Faltantes)</div></td>
  </tr>
</table>
<form id="form1" name="form1" method="post" action="">
  <table class="table" >
    <tr>
      <th width="16%" scope="row"><div align="center"><strong>Mesa</strong></div></th>
      <td width="35%"><div align="center"><strong>Escuela</strong></div></td>
      <td width="33%"><div align="center"><strong>Localidad</strong></div></td>
    </tr>
    <?php do { ?>
      <tr>
        <th scope="row"><?php echo $row_mesasfaltxloc['Mesa']; ?></th>
         <td><?php echo $row_mesasfaltxloc['Establecimiento']; ?></td>
        <td><?php echo $row_mesasfaltxloc['Localidad']; ?></td>
      </tr>
      <?php } while ($row_mesasfaltxloc = mysqli_fetch_assoc($mesasfaltxloc)); ?>
  </table>
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($mesasfaltxloc);
?>
