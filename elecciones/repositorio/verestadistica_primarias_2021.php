<?php
if (!isset($_SESSION)) {
  session_start();
  $_SESSION['todos']=1; //0
}

if(isset($_POST['submit']))
{
 if ($_SESSION['todos']==1)
  $_SESSION['todos']=1; //0
 else
  $_SESSION['todos']=1;
  
} 

 $alto_fila = 46;
 $alto_fila_interna = 34;

include('Connections/conexionUsuarios.php');

 $TotalesG=0;
 $TotalesI=0;
 $PorcentajeMesasEscrutadas=0; 

 $Listas[1]["nombre"]="Solana - Zomoza / Vanini-Toselli"; 
 $Listas[2]["nombre"]="P&eacute;rez Garc&iacute;a - Ber&oacute;n / Pilcic - Bustamante";  
 $Listas[3]["nombre"]="Bensus&aacute;n - Alonso / Mar&iacute;n - Rauschenberger"; 
 $Listas[4]["nombre"]="Altolaguirre - Gette / P&eacute;rez - Coli";
 $Listas[5]["nombre"]="Teysseire - Pantanali / Deanna - Kenny"; 
 $Listas[6]["nombre"]="Kroneberger - Huala / Maquieyra - Ramis";
 $Listas[7]["nombre"]="Casado - Moreno / Bertone - Romero";
 $Listas[8]["nombre"]="Leher - Montoya / Serradell - Lovera";
 $Listas[9]["nombre"]="Cabiati - Baleani / Lupardo - Mrongowius";
 $Listas[10]["nombre"]="G&oacute;mez - Busch / Montigel - R&iacute;os";
 
 $Listas[1]["partido"]="P.S. Vamos con Vos";
 $Listas[2]["partido"]="Movimiento al Socialismo";
 $Listas[3]["partido"]="Frente de Todos";
 $Listas[4]["partido"]="";
 $Listas[5]["partido"]="";
 $Listas[6]["partido"]="";
 $Listas[7]["partido"]="";
 $Listas[8]["partido"]="";
 $Listas[9]["partido"]="";
 $Listas[10]["partido"]="";
 
 $Listas[1]["lista"]="50 - V";
 $Listas[2]["lista"]="200 - 1A";
 $Listas[3]["lista"]="501 - A";
 $Listas[4]["lista"]="502 - A";
 $Listas[5]["lista"]="502 - B";
 $Listas[6]["lista"]="502 - C";
 $Listas[7]["lista"]="502 - D";
 $Listas[8]["lista"]="502 - E";
 $Listas[9]["lista"]="503 - 1A";
 $Listas[10]["lista"]="503 - 10R";
 
 $Listas[1]["cargos"]="D,S";
 $Listas[2]["cargos"]="D,S";
 $Listas[3]["cargos"]="D,S";
 $Listas[4]["cargos"]="D,S";
 $Listas[5]["cargos"]="D,S";
 $Listas[6]["cargos"]="D,S";
 $Listas[7]["cargos"]="D,S";
 $Listas[8]["cargos"]="D,S";
 $Listas[9]["cargos"]="D,S";
 $Listas[10]["cargos"]="D,S";
  
 $Listas[1]["imagen"]= "1.png"; // 
 $Listas[2]["imagen"]= "2.png"; // 
 $Listas[3]["imagen"]= "3.png"; // 
 $Listas[4]["imagen"]= "4.png"; // 
 $Listas[5]["imagen"]= "5.png"; // 
 $Listas[6]["imagen"]= "6.png"; // 
 $Listas[7]["imagen"]= "7.png"; // 
 $Listas[8]["imagen"]= "8.png"; // 
 $Listas[9]["imagen"]= "9.png"; // 
 $Listas[10]["imagen"]= "10.png"; // 
  
 $Listas[1]["color"]= "#CC0099"; // 
 $Listas[2]["color"]= "#FF0000";  // Violeta
 $Listas[3]["color"]= "#003366"; // 
 $Listas[4]["color"]= "#FF6600";   // 
 $Listas[5]["color"]= "#FF6600"; // 
 $Listas[6]["color"]= "#FF6600"; // 
 $Listas[7]["color"]= "#FF6600";
 $Listas[8]["color"]= "#FF6600";
 $Listas[9]["color"]= "#333333";
 $Listas[10]["color"]= "#333333"; // 
 
 $Totales[1]["color"]= "#CC0099"; // 
 $Totales[2]["color"]= "#FF0000";  // 
 $Totales[3]["color"]= "#003366"; // 
 $Totales[4]["color"]= "#FF6600";   // 
 $Totales[5]["color"]= "#333333"; // 
 
 $Totales[1]['partido']="P.S. VAMOS CON VOS";
 $Totales[2]['partido']="MOVIMIENTO AL SOCIALISMO";
 $Totales[3]['partido']="FRENTE DE TODOS";
 $Totales[4]['partido']="JUNTOS POR EL CAMBIO";
 $Totales[5]['partido']="F. IZQ. Y TRABAJADORES";
 
 $Totales[1]['nombre']="Solana - Zomoza / Vanini - Toselli";
 $Totales[2]['nombre']="P&eacute;rez Garc&iacute;a - Ber&oacute;n / Pilcic - Bustamante";
 $Totales[3]['nombre']="Bensus&aacute;n - Alonso / Mar&iacute;n - Rauschenberger";
 $Totales[4]['nombre']="";
 $Totales[5]['nombre']="";
 
 
 $Totales[1]['lista']="50 - V";
 $Totales[2]['lista']="200 - 1A";
 $Totales[3]['lista']="501 - A";
 $Totales[4]['lista']="502";
 $Totales[5]['lista']="503";
 
 
 $Totales[1]['interna']=0;
 $Totales[2]['interna']=0;
 $Totales[3]['interna']=0;
 $Totales[4]['interna']=1;
 $Totales[5]['interna']=2;
 
 
//"#E95B0F" = Naranja; "#FF0000" = Rojo; "#FFCC00"=  Amarillo; "#005693" = Celeste; "#11285C"=azul

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}


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

	$sel_consulta="select count(*) as cantidadmesas, sum(L1I) as SumaL1I, sum(L1G) as SumaL1G,sum(L2I) as SumaL2I, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10G) as SumaL10G from mesas WHERE Escrutada='S'";

 $sql=mysqli_query($con,$sel_consulta);
 $rw=mysqli_fetch_array($sql);

 $CantidadMesasEscrutadas=0;
 $CantidadMesasEscrutadas = $rw['cantidadmesas'];
 
 for ($i=1;$i<=10;$i++){
 $Listas[$i]["suma_intendente"]=0;
 $Listas[$i]["suma_gobernador"]=0;
 $Listas[$i]["porcentaje_intendente"]=0;
 $Listas[$i]["porcentaje_gobernador"]=0;
 
 }
 
 for ($i=1;$i<=5;$i++){
 $Totales[$i]["suma_intendente"]=0;
 $Totales[$i]["suma_gobernador"]=0;
 $Totales[$i]["porcentaje_intendente"]=0;
 $Totales[$i]["porcentaje_gobernador"]=0;
 }

 if ($CantidadMesasEscrutadas > 0)
 {
 $PorcentajeMesasEscrutadas = (($CantidadMesasEscrutadas * 100) / 883);

 $SumaL1I=$rw['SumaL1I'];
 $SumaL1G=$rw['SumaL1G'];
 $Listas[1]["suma_intendente"]= $SumaL1I;
 $Listas[1]["suma_gobernador"]= $SumaL1G;

 $SumaL2I=$rw['SumaL2I'];
 $SumaL2G=$rw['SumaL2G'];
 $Listas[2]["suma_intendente"]= $SumaL2I;
 $Listas[2]["suma_gobernador"]= $SumaL2G;

 $SumaL3I=$rw['SumaL3I'];
 $SumaL3G=$rw['SumaL3G'];
 $Listas[3]["suma_intendente"]= $SumaL3I;
 $Listas[3]["suma_gobernador"]= $SumaL3G;
 
 $SumaL4I=$rw['SumaL4I'];
 $SumaL4G=$rw['SumaL4G'];
 $Listas[4]["suma_intendente"]= $SumaL4I;
 $Listas[4]["suma_gobernador"]= $SumaL4G;
 
 $SumaL5I=$rw['SumaL5I'];
 $SumaL5G=$rw['SumaL5G'];
 $Listas[5]["suma_intendente"]= $SumaL5I;
 $Listas[5]["suma_gobernador"]= $SumaL5G;
 
 $SumaL6I=$rw['SumaL6I'];
 $SumaL6G=$rw['SumaL6G'];
 $Listas[6]["suma_intendente"]= $SumaL6I;
 $Listas[6]["suma_gobernador"]= $SumaL6G;
 
 $SumaL7I=$rw['SumaL7I'];
 $SumaL7G=$rw['SumaL7G'];
 $Listas[7]["suma_intendente"]= $SumaL7I;
 $Listas[7]["suma_gobernador"]= $SumaL7G;
 
 $SumaL8I=$rw['SumaL8I'];
 $SumaL8G=$rw['SumaL8G'];
 $Listas[8]["suma_intendente"]= $SumaL8I;
 $Listas[8]["suma_gobernador"]= $SumaL8G;
 
 $SumaL9I=$rw['SumaL9I'];
 $SumaL9G=$rw['SumaL9G'];
 $Listas[9]["suma_intendente"]= $SumaL9I;
 $Listas[9]["suma_gobernador"]= $SumaL9G;
 
 $SumaL10I=$rw['SumaL10I'];
 $SumaL10G=$rw['SumaL10G'];
 $Listas[10]["suma_intendente"]= $SumaL10I;
 $Listas[10]["suma_gobernador"]= $SumaL10G;
 
 
 $TotalesI= $SumaL1I + $SumaL2I + $SumaL3I + $SumaL4I + $SumaL5I + $SumaL6I+ $SumaL7I+ $SumaL8I+ $SumaL9I+ $SumaL10I;  
 $TotalesG= $SumaL1G + $SumaL2G + $SumaL3G + $SumaL4G + $SumaL5G+ $SumaL6G+ $SumaL7G+ $SumaL8G+ $SumaL9G+ $SumaL10G;
 
 
 $Totales[1]['suma_intendente']=$Listas[1]["suma_intendente"];
 $Totales[1]['suma_gobernador']= $Listas[1]["suma_gobernador"];
 
 $Totales[2]['suma_intendente']=$Listas[2]["suma_intendente"];
 $Totales[2]['suma_gobernador']= $Listas[2]["suma_gobernador"];
  
 $Totales[3]['suma_intendente']=$Listas[3]["suma_intendente"];
 $Totales[3]['suma_gobernador']= $Listas[3]["suma_gobernador"];
  
 $Totales[4]['suma_intendente']=$Listas[4]["suma_intendente"] + $Listas[5]["suma_intendente"] + $Listas[6]["suma_intendente"]+ $Listas[7]["suma_intendente"]+ $Listas[8]["suma_intendente"];
 
 $Totales[4]['suma_gobernador']=$Listas[4]["suma_gobernador"] + $Listas[5]["suma_gobernador"]+ $Listas[6]["suma_gobernador"]+ $Listas[7]["suma_gobernador"]+ $Listas[8]["suma_gobernador"];
 
 $Totales[5]['suma_intendente']= $Listas[9]["suma_intendente"]+  $Listas[10]["suma_intendente"];
 $Totales[5]['suma_gobernador']=$Listas[9]["suma_gobernador"]+ $Listas[10]["suma_gobernador"];
 
 $Totales1=$TotalesI + $TotalesG;

$mensaje= "";

if ($TotalesI > 0){
 if ($SumaL1I > 0){
  $PorcentajeL1I = ($SumaL1I * 100) / $TotalesI;
  $Listas[1]["porcentaje_intendente"]= $PorcentajeL1I;
  }
 if ($SumaL1G > 0){
  $PorcentajeL1G = ($SumaL1G * 100) / $TotalesG;
  $Listas[1]["porcentaje_gobernador"]= $PorcentajeL1G;
  }
 
 if ($SumaL2I > 0){
  $PorcentajeL2I = ($SumaL2I * 100) / $TotalesI;
  $Listas[2]["porcentaje_intendente"]= $PorcentajeL2I;
  }
 if ($SumaL2G > 0){
  $PorcentajeL2G = ($SumaL2G * 100) / $TotalesG;
  $Listas[2]["porcentaje_gobernador"]= $PorcentajeL2G;
  }
  
 if ($SumaL3I > 0){
  $PorcentajeL3I = ($SumaL3I * 100) / $TotalesI;
  $Listas[3]["porcentaje_intendente"]= $PorcentajeL3I;
  }
 if ($SumaL3G > 0){
  $PorcentajeL3G = ($SumaL3G * 100) / $TotalesG;
  $Listas[3]["porcentaje_gobernador"]= $PorcentajeL3G;
  }
 
 if ($SumaL4I > 0){
  $PorcentajeL4I = ($SumaL4I * 100) / $TotalesI;
  $Listas[4]["porcentaje_intendente"]= $PorcentajeL4I;
  }
 if ($SumaL4G > 0){
  $PorcentajeL4G = ($SumaL4G * 100) / $TotalesG;
  $Listas[4]["porcentaje_gobernador"]= $PorcentajeL4G;
  }
 
 if ($SumaL5I > 0){
  $PorcentajeL5I = ($SumaL5I * 100) / $TotalesI;
  $Listas[5]["porcentaje_intendente"]= $PorcentajeL5I;
  }
 if ($SumaL5G > 0){
  $PorcentajeL5G = ($SumaL5G * 100) / $TotalesG;
  $Listas[5]["porcentaje_gobernador"]= $PorcentajeL5G;
  }
 
 if ($SumaL6I > 0){
  $PorcentajeL6I = ($SumaL6I * 100) / $TotalesI;
  $Listas[6]["porcentaje_intendente"]= $PorcentajeL6I;
  }
 if ($SumaL6G > 0){
  $PorcentajeL6G = ($SumaL6G * 100) / $TotalesG;
  $Listas[6]["porcentaje_gobernador"]= $PorcentajeL6G;
  }
  
 if ($SumaL7I > 0){
  $PorcentajeL7I = ($SumaL7I * 100) / $TotalesI;
  $Listas[7]["porcentaje_intendente"]= $PorcentajeL7I;
  }
 if ($SumaL7G > 0){
  $PorcentajeL7G = ($SumaL7G * 100) / $TotalesG;
  $Listas[7]["porcentaje_gobernador"]= $PorcentajeL7G;
  }
  
  if ($SumaL8I > 0){
  $PorcentajeL8I = ($SumaL8I * 100) / $TotalesI;
  $Listas[8]["porcentaje_intendente"]= $PorcentajeL8I;
  }
 if ($SumaL8G > 0){
  $PorcentajeL8G = ($SumaL8G * 100) / $TotalesG;
  $Listas[8]["porcentaje_gobernador"]= $PorcentajeL8G;
  }
  
  if ($SumaL9I > 0){
  $PorcentajeL9I = ($SumaL9I * 100) / $TotalesI;
  $Listas[9]["porcentaje_intendente"]= $PorcentajeL9I;
  }
 if ($SumaL9G > 0){
  $PorcentajeL9G = ($SumaL9G * 100) / $TotalesG;
  $Listas[9]["porcentaje_gobernador"]= $PorcentajeL9G;
  }
  
 if ($SumaL10I > 0){
   $PorcentajeL10I = ($SumaL10I * 100) / $TotalesI;
   $Listas[10]["porcentaje_intendente"]= $PorcentajeL10I;
  }
 if ($SumaL10G > 0){
   $PorcentajeL10G = ($SumaL10G * 100) / $TotalesG;
   $Listas[10]["porcentaje_gobernador"]= $PorcentajeL10G;
  } 
 
  $Totales[1]["porcentaje_intendente"]= $Listas[1]["porcentaje_intendente"];
  $Totales[1]["porcentaje_gobernador"]= $Listas[1]["porcentaje_gobernador"];
  
  $Totales[2]["porcentaje_intendente"]= $Listas[2]["porcentaje_intendente"];
  $Totales[2]["porcentaje_gobernador"]= $Listas[2]["porcentaje_gobernador"];
  
  $Totales[3]["porcentaje_intendente"]= $Listas[3]["porcentaje_intendente"];
  $Totales[3]["porcentaje_gobernador"]= $Listas[3]["porcentaje_gobernador"];
  
  $Totales[4]["porcentaje_intendente"]= $Listas[4]["porcentaje_intendente"]+$Listas[5]["porcentaje_intendente"]+$Listas[6]["porcentaje_intendente"]+$Listas[7]["porcentaje_intendente"]+$Listas[8]["porcentaje_intendente"];
  $Totales[4]["porcentaje_gobernador"]= $Listas[4]["porcentaje_gobernador"]+$Listas[5]["porcentaje_gobernador"]+$Listas[6]["porcentaje_gobernador"]+$Listas[7]["porcentaje_gobernador"]+$Listas[8]["porcentaje_gobernador"];
  
  $Totales[5]["porcentaje_intendente"]= $Listas[9]["porcentaje_intendente"]+$Listas[10]["porcentaje_intendente"];
  $Totales[5]["porcentaje_gobernador"]= $Listas[9]["porcentaje_gobernador"]+$Listas[10]["porcentaje_gobernador"];
  
  for ($i=1;$i<=5;$i++)
   {
    $Interna1[$i]["suma_intendente"]=$Listas[$i+3]["suma_intendente"];
    $Interna1[$i]["suma_gobernador"]=$Listas[$i+3]["suma_gobernador"];
    $Interna1[$i]["porcentaje_gobernador"]=$Listas[$i+3]["porcentaje_gobernador"];
    $Interna1[$i]["porcentaje_intendente"]=$Listas[$i+3]["porcentaje_intendente"];
    $Interna1[$i]["partido"]=$Listas[$i+3]["partido"];
    $Interna1[$i]["color"]=$Listas[$i+3]["color"];
    $Interna1[$i]["lista"]=$Listas[$i+3]["lista"];
	$Interna1[$i]["nombre"]=$Listas[$i+3]["nombre"];
   }
   
  for ($i=1;$i<=2;$i++)
   {
    $Interna2[$i]["suma_intendente"]=$Listas[$i+8]["suma_intendente"];
    $Interna2[$i]["suma_gobernador"]=$Listas[$i+8]["suma_gobernador"];
    $Interna2[$i]["porcentaje_gobernador"]=$Listas[$i+8]["porcentaje_gobernador"];
    $Interna2[$i]["porcentaje_intendente"]=$Listas[$i+8]["porcentaje_intendente"];
    $Interna2[$i]["partido"]=$Listas[$i+8]["partido"];
    $Interna2[$i]["color"]=$Listas[$i+8]["color"];
	$Interna2[$i]["lista"]=$Listas[$i+8]["lista"];
	$Interna2[$i]["nombre"]=$Listas[$i+8]["nombre"];
	
   }  
     
 foreach ($Totales as $key => $row) {
    $aux[$key] = $row['suma_gobernador'];
 }
 
 array_multisort($aux, SORT_DESC, $Totales); 
 
 foreach ($Interna1 as $key => $row) {
    $aux1[$key] = $row['suma_gobernador'];
 }
 
 array_multisort($aux1, SORT_DESC, $Interna1); 
 
  foreach ($Interna2 as $key => $row) {
    $aux2[$key] = $row['suma_gobernador'];
 }
 
 array_multisort($aux2, SORT_DESC, $Interna2); 
  
 $Porcentaje = 100;
  
  }
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta charset=ISO-8859-1>
<meta http-equiv="refresh" content="60;URL=http://www.lapampaperonista.com.ar/elecciones/verestadistica.php">

 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<title>Elecciones <?php echo " ". date("Y"); ?></title>
<style type="text/css">
<!-- //color Verde= #003300 //color Azul=  #003366 //Naranja = #D8232A

.EstiloListas {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000;font-weight: bold;} /*24*/

.EstiloNombres {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif;} /*24*/

.EstiloVotos1 {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotos {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.Estilo152 {font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000;font-size: 23px}
.Estilo_Selector {font-size: 16px}

.cabeceras {font-family: Verdana, Arial, Helvetica, sans-serif; color:#666666;font-size: 20px}

.cabecera_logo {font-size: 14px; font-weight: bold; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif; color:#ffffff;
}

.EstiloVotosInterna {font-size: 20px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} 


-->
</style>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-104483907-1', 'auto');
  ga('send', 'pageview');

</script>

<script>

$(function() { 
   $("#one").addClass("progress-bar-purple");
   $("#two").addClass("progress-bar-orange");
});

</script>

<script type="text/javascript">const tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/"); document.write(unescape("<script src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript' %3E%3C/script%3E"));</script>
</head>

<body>

<div align="center" style="width:96%;margin-left:2%;border:inset;border-color:#E9ECEF">
<div align="center">
 
 <!-- <p align="center" class="Estilo8"><a target='_blank' href="https://eleccionesgenerales2019.lapampa.gob.ar/scripts/cgiip.exe/WService=Elecciones/EReLocCowr.htm?vPartido=1&SelectLocalidad=84&User=">Ver Resultados en Tribunal Electoral (clic aqu&iacute;)</a></p> -->
 <table width="100%" height="81" border="0">
 
   <tr>
     <td height="77" colspan="2" align="left" bgcolor="#016EA6"><p><img src="fotos/escudo_la_pampa.png" width="65" height="70" style="margin-top:4px; margin-left:5px; margin-right:15px; float:left" /></p>
       <p><span class="cabecera_logo">Elecciones PASO </span></p>
       <p><span class="cabecera_logo"> 12 de septiembre de <?php echo date("Y");?> - Escrutinio Provisorio.</span></p>        </td> 
       
       <td width="51%" colspan="2" align="right" bgcolor="#016EA6"><p><img src="fotos/Escudo_del_Partido_Justicialista.png" width="54" height="67" style="margin-top:2px; margin-right:5px; margin-bottom:2px; float:left" /></p>
       <p><span class="cabecera_logo"> Datos de Toda La Provincia de La Pampa &nbsp;&nbsp;</span></p>      <p><span class="cabecera_logo"><a style="color:#CCCCCC; margin-right:15px;" href="<?php echo $logoutAction ?>">Salir</a></span></p>       
       </td>  
       
   </tr>
  </table>
  <table width="100%" height="77" border="0"> 
  
   <tr>
     <td width="30%" height="20"><div align="center" style="margin-left:10px" class="Estilo152"> Mesas Escrutadas: <?php echo $CantidadMesasEscrutadas;?> &nbsp; &nbsp; <?php echo number_format($PorcentajeMesasEscrutadas,1, ',', '.') . "%"; ?> &nbsp; &nbsp; </div></td>
     <td width="20%"><div align="center" style="margin-left:10px" class="Estilo152"> <span class="cabeceras">Total Votos Sen:</span> <?php echo number_format($TotalesG,0, ',', '.') ;?>  </div></td><td width="20%"><div align="center" style="margin-left:10px" class="Estilo152"> <span class="cabeceras">Total Votos Dip:</span> <?php echo number_format($TotalesI,0, ',', '.') ;?>  </div></td>
     <td width="21%" align="center" style="vertical-align: middle;"><form action="verestadisticaspueblos1.php" method="post" name="form1" class="Estilo156" id="form1">
         <select name="menu" onchange="this.form.submit()" style="margin-top:10px">
           <option value="0">Ver Localidades</option>
           <option value="1" >25 de Mayo</option>
           <option value="2" >Abramo</option>
           <option value="3" >Adolfo Van Praet</option>
           <option value="4" >Agustoni</option>
           <option value="5" >Algarrobo del &Aacute;guila</option>
           <option value="7" >Alpachiri</option>
           <option value="8" >Alta Italia</option>
           <option value="9" >Anguil</option>
           <option value="10" >Arata</option>
           <option value="11" >Ataliva Roca</option>
           <option value="12" >Bernardo Larroude</option>
           <option value="13" >Bernasconi</option>
           <option value="14" >Caleuf&uacute </option>
           <option value="15" >Carro Quemado</option>
           <option value="16" >Catril&oacute </option>
           <option value="17" >Ceballos</option>
           <option value="18" >Chacharramendi</option>
           <option value="19" >Chamaic&oacute </option>
           <option value="34" >Col. Emilio Mitre</option>
           <option value="22" >Col. San Jos&eacute </option>
           <option value="23" >Col. Santa Mar&iacute;a</option>
           <option value="81" >Col. Santa Teresa</option>
           <option value="21" >Colonia Bar&oacute;n</option>
           <option value="24" >Conhello</option>
           <option value="20" >Coronel Hilario Lagos</option>
           <option value="27" >Cuchillo C&oacute </option>
           <option value="28" >Doblas</option>
           <option value="29" >Dorila</option>
           <option value="30" >Eduardo Castex</option>
           <option value="32" >Embajador Martini</option>
           <option value="35" >Falucho</option>
           <option value="38" >General Acha</option>
           <option value="39" >General Campos</option>
           <option value="36" >General Pico</option>
           <option value="41" >General San Mart&iacute;n </option>
           <option value="37" >Gobernador Duval</option>
           <option value="42" >Guatrach&eacute </option>
           <option value="43" >Ingeniero Luiggi</option>
           <option value="45" >Intendente Alvear</option>
           <option value="46" >Jacinto Arauz</option>
           <option value="47" >La Adela</option>
           <option value="48" >La Gloria</option>
           <option value="49" >La Humada</option>
           <option value="50" >La Maruja</option>
           <option value="51" >La Reforma</option>
           <option value="52" >Limay Mahuida</option>
           <option value="53" >Lonquimay</option>
           <option value="54" >Loventu&eacute </option>
           <option value="55" >Luan Toro</option>
           <option value="56" >Macach&iacute;n</option>
           <option value="57" >Maisonnave</option>
           <option value="58" >Mauricio Mayer</option>
           <option value="59" >Metileo</option>
           <option value="60" >Miguel Cane</option>
           <option value="61" >Miguel Riglos</option>
           <option value="62" >Monte Nievas</option>
           <option value="63" >Naic&oacute </option>
           <option value="64" >Oficial Enrique Segura</option>
           <option value="65" >Parera</option>
           <option value="66" >Per&uacute </option>
           <option value="67" >Pichi Huinca</option>
           <option value="68" >Puelches</option>
           <option value="69" >Puel&eacuten </option>
           <option value="70" >Quehu&eacute </option>
           <option value="71" >Quem&uacute Quem&uacute </option>
           <option value="72" >Quetrequ&eacute;n </option>
           <option value="73" >Rancul</option>
           <option value="74" >Realic&oacute </option>
           <option value="75" >Relmo</option>
           <option value="76" >Rol&oacute;n</option>
           <option value="77" >Rucanelo</option>
           <option value="79" >Santa Isabel</option>
           <option value="80" >Santa Rosa</option>
           <option value="82" >Sarah</option>
           <option value="83" >Speluzzi</option>
           <option value="84" >Tel&eacute;n </option>
           <option value="85" >Toay</option>
           <option value="86" >Tomas M. de Anchorena</option>
           <option value="87" >Trebolares</option>
           <option value="88" >Trenel</option>
           <option value="89" >Unanue</option>
           <option value="90" >Uriburu</option>
           <option value="91" >Vertiz</option>
           <option value="92" >Victorica</option>
           <option value="93" >Villa Mirasol</option>
           <option value="94" >Winifreda</option>
         </select>
       
     </form></td><td width="9%" style="vertical-align: middle;"><form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php 
		if($_SESSION['todos']==1)
		 $ver="ver menos";
		else
		 $ver="ver todos"; 
		?>
        <!-- <button class="btn btn-light btn-sm" style="margin-top:12px" name="submit" type="submit"><?php echo $ver; ?></button>  -->    
       </form> </td>
   </tr>
 </table>
 
</div>

<table class="table" >
 <tr bgcolor="#E9ECEF">
   <td><div align="center"><span class="cabeceras">Agrupaci&oacute;n</span> </div></td> 
 
   <td><div align="center"><span class="cabeceras">Lista</span> </div></td>
   <td> <div align="center"><span class="cabeceras">Votos Sen.</span></div></td> 
  <td><div align="center"><span class="cabeceras">% Sen.</span></div></td>
   <td> <div align="center"><span class="cabeceras">Votos Dip.</span></div></td> 
  <td><div align="center"><span class="cabeceras">% Dip.</span></div></td>
  </tr>
 
 <?php 
 
 if($_SESSION['todos']==1)
  $cantidad_a_mostrar=4;  // maximo es 5
 else
  $cantidad_a_mostrar=3; // 3
 
 $ancho=70;
 $alto=70;
 
 if ($TotalesG > 0)
  {
  $comienzo = 0;
  }
 else
  {$comienzo = 1; 
  $cantidad_a_mostrar=0;
  
    echo "<tr><td colspan='5' align='center'> Esperando Resultados... </td></tr>";
  }
 
 $posG=0;
 $posI=0; 
  
 for ($i=$comienzo; $i<=$cantidad_a_mostrar;$i++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = Intendente G= Gobernador
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
   <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila; ?>">
    <td  width="398"  bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Totales[$i]["color"]; ?>"><?php echo $Totales[$i]["partido"]; if ($Totales[$i]["partido"]<>"") echo "<br />". "<span class='EstiloNombres'>". $Totales[$i]["nombre"]."</span>"; ?></td>
   
    <td width="143" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Totales[$i]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo $Totales[$i]["lista"]; ?></div></td>
    <td width="125" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Totales[$i]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo number_format($Totales[$i]["suma_gobernador"],0, ',', '.') ?></div></td>
    <td width="101" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo $Totales[$i]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo number_format($Totales[$i]["porcentaje_gobernador"],1, ',', '.') . "%" ?> </div></td>
   
    <td width="125" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Totales[$i]["color"]; ?>" ><div align="center"><?php echo number_format($Totales[$i]["suma_intendente"],0, ',', '.') ?></div></td>
    <td width="87" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Totales[$i]["color"]; ?>" ><div align="center"><?php echo number_format($Totales[$i]["porcentaje_intendente"],1, ',', '.') . "%" ?> </div></td>
  </tr>
  
  <?php if ($Totales[$i]['interna']==1 and $Totales[$i]['suma_gobernador']>0){ 
   $comienzoj=0;
   $hastainterna=4;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = Intendente G= Gobernador
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td  width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna1[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna1[$j]["nombre"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo $Interna1[$j]["lista"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo number_format($Interna1[$j]["suma_gobernador"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna1[$j]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo number_format($Interna1[$j]["porcentaje_gobernador"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php echo number_format($Interna1[$j]["suma_intendente"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php echo number_format($Interna1[$j]["porcentaje_intendente"],1, ',', '.') . "%" ?> </div></td>
    </tr>
  
       
 <?php 
         } // $Totales[$i]['interna']==1
       } // for secundario, el de la interna 1
	   
  if ($Totales[$i]['interna']==2 and $Totales[$i]['suma_gobernador']>0){ 
   $comienzoj=0;
   $hastainterna=1;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = Intendente G= Gobernador
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td  width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna2[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna2[$j]["nombre"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo $Interna2[$j]["lista"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo number_format($Interna2[$j]["suma_gobernador"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna2[$j]["color"]; ?>" ><div align="center"><?php if($posI > -1) echo number_format($Interna2[$j]["porcentaje_gobernador"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php echo number_format($Interna2[$j]["suma_intendente"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php echo number_format($Interna2[$j]["porcentaje_intendente"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Totales[$i] ['interna']==2)
   } // for principal ?>
 </table>

<div style="background:#E9ECEF; height:10px"> </div>
 <div style="background:#016EA6; height:10px"> </div>
 <span>Copyright &copy; <?php echo date('Y')?> - Partido Justicialista La Pampa </span> 

</div>


</body>
</html>