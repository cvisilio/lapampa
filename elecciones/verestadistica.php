<?php
if (!isset($_SESSION)) {
  session_start();
  $_SESSION['todos']=1;
}

if(isset($_POST['submit']))
{
 if ($_SESSION['todos']==1)
  $_SESSION['todos']=0;
 else
  $_SESSION['todos']=1;
} 

$alto_fila = 46;
$alto_fila_interna = 34;

include('Connections/conexionUsuarios.php');

$TotalesG=0;
$TotalesI=0;
$PorcentajeMesasEscrutadas=0; 

$total_electores=304965;

$Partido[1]["nombre"]="FRENTE DEFENDEMOS LA PAMPA"; 
$Partido[2]["nombre"]="ALIANZA LA LIBERTAD AVANZA";  
$Partido[3]["nombre"]="FRENTE DE IZQUIERDA Y DE TRABAJADORES-UNIDAD";  
$Partido[4]["nombre"]="CAMBIA LA PAMPA";  
$Partido[5]["nombre"]="MOVIMIENTO AL SOCIALISMO";  
$Partido[6]["nombre"]="Votos Blancos";
$Partido[7]["nombre"]="Votos en Nulos"; 
$Partido[8]["nombre"]="Votos Recurridos"; 
$Partido[9]["nombre"]="Votos Impugnados"; 

$Partido[1]["numero"]="503"; 
$Partido[2]["numero"]="501";
$Partido[3]["numero"]="502";
$Partido[4]["numero"]="504";
$Partido[5]["numero"]="13";
$Partido[6]["numero"]="";
$Partido[7]["numero"]="";
$Partido[8]["numero"]="";
$Partido[9]["numero"]="";

$Partido[1]["cargos"]="DP";
$Partido[2]["cargos"]="DP";
$Partido[3]["cargos"]="DP";
$Partido[4]["cargos"]="DP";
$Partido[5]["cargos"]="DP";
$Partido[6]["cargos"]="DP";
$Partido[7]["cargos"]="DP";
$Partido[8]["cargos"]="DP";
$Partido[9]["cargos"]="DP";

$Partido[1]["imagen"]= "1.png";
$Partido[2]["imagen"]= "2.png";
$Partido[3]["imagen"]= "3.png";
$Partido[4]["imagen"]= "4.png";
$Partido[5]["imagen"]= "5.png";
$Partido[6]["imagen"]= "6.png";
$Partido[7]["imagen"]= "7.png";
$Partido[8]["imagen"]= "8.png";
$Partido[9]["imagen"]= "9.png";

$Partido[1]["color"]= "#0066CC"; // Azul PJ
$Partido[2]["color"]=  "#CC0099"; // Violeta - LLA
$Partido[3]["color"]=  "#FF6666"; // Rosa Fuerte - Izquierda
$Partido[4]["color"]=  "#FFCC00"; // Amarillo - Cambia La Pampa 
$Partido[5]["color"]=  "#FF0000"; // Rojo - MAS
$Partido[6]["color"]=  "#CCCCCC";
$Partido[7]["color"]=  "#CCCCCC";
$Partido[8]["color"]=  "#CCCCCC";
$Partido[9]["color"]=  "#CCCCCC";


$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
  $logoutGoTo = "index.php";
  if ($logoutGoTo) { header("Location: $logoutGoTo"); exit; }
} 

$MM_authorizedUsers = "1,2,3,4";
$MM_donotCheckaccess = "false";

function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  $isValid = False; 
  if (!empty($UserName)) { 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { $isValid = true; } 
    if (in_array($UserGroup, $arrGroups)) { $isValid = true; } 
    if (($strUsers == "") && false) { $isValid = true; } 
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

$sel_consulta="select count(*) as cantidadmesas, sum(L1I) as SumaL1I, sum(L1DP) as SumaL1DP, sum(L1G) as SumaL1G,sum(L2I) as SumaL2I, sum(L2DP) as SumaL2DP, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3DP) as SumaL3DP, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4DP) as SumaL4DP, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5DP) as SumaL5DP, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6DP) as SumaL6DP, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7DP) as SumaL7DP, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8DP) as SumaL8DP, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9DP) as SumaL9DP, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10DP) as SumaL10DP, sum(L10G) as SumaL10G, sum(L11DP) as SumaL11DP, sum(L11G) as SumaL11G, sum(L12DP) as SumaL12DP, sum(L12G) as SumaL12G, sum(L13DP) as SumaL13DP, sum(L13G) as SumaL13G, sum(L14DP) as SumaL14DP, sum(L14G) as SumaL14G, sum(L15DP) as SumaL15DP, sum(L15G) as SumaL15G, sum(L16DP) as SumaL16DP, sum(L16G) as SumaL16G, sum(L17DP) as SumaL17DP, sum(L17G) as SumaL17G, sum(L18DP) as SumaL18DP, sum(L18G) as SumaL18G, sum(L19DP) as SumaL19DP, sum(L19G) as SumaL19G, sum(L20DP) as SumaL20DP, sum(L20G) as SumaL20G, sum(L21DP) as SumaL21DP, sum(L21G) as SumaL21G, sum(L22DP) as SumaL22DP, sum(L22G) as SumaL22G, sum(L23DP) as SumaL23DP, sum(L23G) as SumaL23G, sum(L24DP) as SumaL24DP, sum(L24G) as SumaL24G, sum(L25DP) as SumaL25DP, sum(L25G) as SumaL25G, sum(L26DP) as SumaL26DP, sum(L26G) as SumaL26G, sum(L27DP) as SumaL27DP, sum(L27G) as SumaL27G, sum(L28DP) as SumaL28DP, sum(L28G) as SumaL28G, sum(L29DP) as SumaL29DP, sum(L29G) as SumaL29G from mesas WHERE Escrutada='S'";

$sql=mysqli_query($con,$sel_consulta);
$rw=mysqli_fetch_array($sql);

$CantidadMesasEscrutadas=0;
$CantidadMesasEscrutadas = $rw['cantidadmesas'];

if ($CantidadMesasEscrutadas > 0)
{
 $PorcentajeMesasEscrutadas = (($CantidadMesasEscrutadas * 100) / 915);

 $SumaL1DP=$rw['SumaL1DP'];
 $SumaL1G=$rw['SumaL1G'];
 $SumaL2DP=$rw['SumaL2DP'];
 $SumaL2G=$rw['SumaL2G'];
 $SumaL3DP=$rw['SumaL3DP'];
 $SumaL3G=$rw['SumaL3G'];
 $SumaL4DP=$rw['SumaL4DP'];
 $SumaL4G=$rw['SumaL4G'];
 $SumaL5DP=$rw['SumaL5DP'];
 $SumaL5G=$rw['SumaL5G'];
 $SumaL6DP=$rw['SumaL6DP'];
 $SumaL6G=$rw['SumaL6G'];
 $SumaL7DP=$rw['SumaL7DP'];
 $SumaL7G=$rw['SumaL7G'];
 $SumaL8DP=$rw['SumaL8DP'];
 $SumaL8G=$rw['SumaL8G'];
 $SumaL9DP=$rw['SumaL9DP'];
 $SumaL9G=$rw['SumaL9G'];
 $SumaL10DP=$rw['SumaL10DP'];
 $SumaL10G=$rw['SumaL10G'];
 $SumaL11DP=$rw['SumaL11DP'];
 $SumaL11G=$rw['SumaL11G'];
 $SumaL12DP=$rw['SumaL12DP'];
 $SumaL12G=$rw['SumaL12G'];
 $SumaL13DP=$rw['SumaL13DP'];
 $SumaL13G=$rw['SumaL13G'];
 $SumaL14DP=$rw['SumaL14DP'];
 $SumaL14G=$rw['SumaL14G'];
 $SumaL15DP=$rw['SumaL15DP'];
 $SumaL15G=$rw['SumaL15G'];
 $SumaL16DP=$rw['SumaL16DP'];
 $SumaL16G=$rw['SumaL16G'];
 $SumaL17DP=$rw['SumaL17DP'];
 $SumaL17G=$rw['SumaL17G'];
 $SumaL18DP=$rw['SumaL18DP'];
 $SumaL18G=$rw['SumaL18G'];
 $SumaL19DP=$rw['SumaL19DP'];
 $SumaL19G=$rw['SumaL19G'];
 $SumaL20DP=$rw['SumaL20DP'];
 $SumaL20G=$rw['SumaL20G'];
 $SumaL21DP=$rw['SumaL21DP'];
 $SumaL21G=$rw['SumaL21G'];
 $SumaL22DP=$rw['SumaL22DP'];
 $SumaL22G=$rw['SumaL22G'];
 $SumaL23DP=$rw['SumaL23DP'];
 $SumaL23G=$rw['SumaL23G'];
 $SumaL24DP=$rw['SumaL24DP'];
 $SumaL24G=$rw['SumaL24G'];
 $SumaL25DP=$rw['SumaL25DP'];
 $SumaL25G=$rw['SumaL25G'];
 $SumaL26DP=$rw['SumaL26DP'];
 $SumaL26G=$rw['SumaL26G'];
 $SumaL27DP=$rw['SumaL27DP'];
 $SumaL27G=$rw['SumaL27G'];
 $SumaL28DP=$rw['SumaL28DP'];
 $SumaL28G=$rw['SumaL28G'];
 $SumaL29DP=$rw['SumaL29DP'];
 $SumaL29G=$rw['SumaL29DP'];

 $TotalesDP= $SumaL1DP + $SumaL2DP + $SumaL3DP + $SumaL4DP + $SumaL5DP + $SumaL6DP+ $SumaL7DP+ $SumaL8DP+ $SumaL9DP;
 
 $totalPositivos = ($SumaL1DP ?? 0) + ($SumaL2DP ?? 0) + ($SumaL3DP ?? 0) + ($SumaL4DP ?? 0) + ($SumaL5DP ?? 0);
 $totalEmitidos  = $totalPositivos + ($SumaL6DP ?? 0) + ($SumaL7DP ?? 0) + ($SumaL8DP ?? 0) + ($SumaL9DP ?? 0);
 
 $TotalesG2= $SumaL1G + $SumaL2G + $SumaL3G + $SumaL4G + $SumaL5G+ $SumaL6G+ $SumaL7G+ $SumaL8G+ $SumaL9G+ $SumaL10G+$SumaL11G+$SumaL12G+ $SumaL13G+$SumaL14G+$SumaL15G+$SumaL16G+$SumaL17G+$SumaL18G+$SumaL19G+$SumaL20G+$SumaL21G+$SumaL22G+$SumaL23G+$SumaL24G+$SumaL25G +$SumaL26G+$SumaL27G+$SumaL28G+$SumaL29G;

 $mensaje= "";

 $Partido[1]["suma_diputado"]=$SumaL1DP;
 $Partido[1]["suma_presidente"]= $SumaL1G;
 $Partido[2]["suma_diputado"]= $SumaL2DP;
 $Partido[2]["suma_presidente"]= $SumaL2G;
 $Partido[3]["suma_diputado"]= $SumaL3DP;
 $Partido[3]["suma_presidente"]= $SumaL3G;
 $Partido[4]["suma_diputado"]= $SumaL4DP;
 $Partido[4]["suma_presidente"]= $SumaL4G;   
 $Partido[5]["suma_diputado"]= $SumaL5DP; 
 $Partido[5]["suma_presidente"]=  $SumaL5G;
 $Partido[6]["suma_diputado"]= $SumaL6DP; 
 $Partido[6]["suma_presidente"]=  $SumaL6G;
 $Partido[7]["suma_diputado"]= $SumaL7DP; 
 $Partido[7]["suma_presidente"]=  $SumaL7G;
 $Partido[8]["suma_diputado"]= $SumaL8DP; 
 $Partido[8]["suma_presidente"]=  $SumaL8G;
 $Partido[9]["suma_diputado"]= $SumaL9DP; 
 $Partido[9]["suma_presidente"]=  $SumaL9G;

 if ($totalPositivos > 0){
   if ($SumaL1DP > 0){ $PorcentajeL1DP = ($SumaL1DP * 100) / $totalPositivos; } 
   if ($SumaL1G  > 0 && $TotalesG>0){  $PorcentajeL1G  = ($SumaL1G  * 100) / $TotalesG; }

   if ($SumaL2DP > 0){ $PorcentajeL2DP = ($SumaL2DP * 100) / $totalPositivos; } 
   if ($SumaL2G  > 0 && $TotalesG>0){  $PorcentajeL2G  = ($SumaL2G  * 100) / $TotalesG; }
   
   if ($SumaL3DP > 0){ $PorcentajeL3DP = ($SumaL3DP * 100) / $totalPositivos; }  
   if ($SumaL3G  > 0 && $TotalesG>0){  $PorcentajeL3G  = ($SumaL3G  * 100) / $TotalesG; }

   if ($SumaL4DP > 0){ $PorcentajeL4DP = ($SumaL4DP * 100) / $totalPositivos; } 
   if ($SumaL4G  > 0 && $TotalesG>0){  $PorcentajeL4G  = ($SumaL4G  * 100) / $TotalesG; }

   if ($SumaL5DP > 0){ $PorcentajeL5DP = ($SumaL5DP * 100) / $totalPositivos; }  
   if ($SumaL5G  > 0 && $TotalesG>0){  $PorcentajeL5G  = ($SumaL5G  * 100) / $TotalesG; }

   if ($SumaL6DP > 0){ $PorcentajeL6DP = ($SumaL6DP * 100) / $totalEmitidos; }   
   if ($SumaL6G  > 0 && $TotalesG>0){  $PorcentajeL6G  = ($SumaL6G  * 100) / $TotalesG; }
   
   if ($SumaL7DP > 0){ $PorcentajeL7DP = ($SumaL7DP * 100) / $totalEmitidos; }    
   if ($SumaL7G  > 0 && $TotalesG>0){  $PorcentajeL7G  = ($SumaL7G  * 100) / $TotalesG; }

   if ($SumaL8DP > 0){ $PorcentajeL8DP = ($SumaL8DP * 100) / $totalEmitidos; }    
   if ($SumaL8G  > 0 && $TotalesG>0){  $PorcentajeL8G  = ($SumaL8G  * 100) / $TotalesG; }

   if ($SumaL9DP > 0){ $PorcentajeL9DP = ($SumaL9DP * 100) / $totalEmitidos; }    
   if ($SumaL9G  > 0 && $TotalesG>0){  $PorcentajeL9G  = ($SumaL9G  * 100) / $TotalesG; }

   // (resto idem si los necesitás)
                      
   $Partido[1]["porcentaje_diputado"]= $PorcentajeL1DP ?? 0;
   $Partido[1]["porcentaje_presidente"]= $PorcentajeL1G ?? 0;
   $Partido[2]["porcentaje_diputado"]= $PorcentajeL2DP ?? 0;
   $Partido[2]["porcentaje_presidente"]= $PorcentajeL2G ?? 0;
   $Partido[3]["porcentaje_diputado"]= $PorcentajeL3DP ?? 0;
   $Partido[3]["porcentaje_presidente"]= $PorcentajeL3G ?? 0;
   $Partido[4]["porcentaje_diputado"]= $PorcentajeL4DP ?? 0;
   $Partido[4]["porcentaje_presidente"]= $PorcentajeL4G ?? 0;  
   $Partido[5]["porcentaje_diputado"]= $PorcentajeL5DP ?? 0;
   $Partido[5]["porcentaje_presidente"]= $PorcentajeL5G ?? 0;
   $Partido[6]["porcentaje_diputado"]= $PorcentajeL6DP ?? 0;
   $Partido[6]["porcentaje_presidente"]= $PorcentajeL6G ?? 0;
   $Partido[7]["porcentaje_diputado"]= $PorcentajeL7DP ?? 0;
   $Partido[7]["porcentaje_presidente"]= $PorcentajeL7G ?? 0;
   $Partido[8]["porcentaje_diputado"]= $PorcentajeL8DP ?? 0;
   $Partido[8]["porcentaje_presidente"]= $PorcentajeL8G ?? 0;
   $Partido[9]["porcentaje_diputado"]= $PorcentajeL9DP ?? 0;
   $Partido[9]["porcentaje_presidente"]= $PorcentajeL9G ?? 0;   
   
   foreach ($Partido as $key => $row) { $aux[$key] = $row['suma_diputado']; }
   array_multisort($aux, SORT_DESC, $Partido); 

   // === Densidad global (etiqueta derecha de la barrita) y color del líder provincial ===
   $densidadMax = 0.0;
   foreach ($idxFuerzas as $k) { if ($densMax[$k] > $densidadMax) $densidadMax = $densMax[$k]; }
   $colorRef = $Partido[0]['color'] ?? '#74c0fc';

   $Porcentaje = 100;
 }

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta charset=ISO-8859-1>
<meta http-equiv="refresh" content="600;URL=https://www.lapampaperonista.com.ar/elecciones/verestadistica.php">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
<title>Elecciones <?php echo " ". date("Y"); ?></title>
<style type="text/css">
<!--
#resultados_ajax h3 {
  font-size: 20px;      /* antes seguro era 24px+ */
  font-weight: bold;
}

#resultados_ajax h4 {
  font-size: 18px;
  margin: 5px 0;
}

#resultados_ajax h5 {
  font-size: 16px;
  margin: 2px 0;
}

#resultados_ajax td {
  font-size: 15px;
  padding: 4px 6px;
}

#resultados_ajax table {
  margin-bottom: 10px;
}

#resultados_ajax strong {
  font-weight: 600; /* un poco más liviano si querés */
}

.EstiloPartido {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000}
.EstiloVotos1 {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000}
.Estilo152 {font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000;font-size: 24px}
.Estilo_Selector {font-size: 16px}
.cabeceras {font-family: Verdana, Arial, Helvetica, sans-serif; color:#666666;font-size: 22px}
.cabecera_logo {font-size: 14px; font-weight: bold; line-height: normal ;  font-family: Verdana, Arial, Helvetica, sans-serif; color:#ffffff;}
.EstiloVotos {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000}
.EstiloVotosInterna{font-size: 18px; font-weight: normal ; line-height: normal ;  font-family: Verdana, Arial, Helvetica, sans-serif;}

/* === Leyenda Densidad === */
.escala-global {
  position: relative;
  width: 640px; height: 10px; border-radius: 999px;
  background: linear-gradient(to right, #e9eef6 0%, #e9eef6 100%);
  overflow: hidden; box-shadow: inset 0 1px 2px rgba(0,0,0,.12);
}
.escala-global .grad {
  position:absolute; inset:0;
  background: linear-gradient(to right, #A8E6FF 0%, #0077C8 100%);
  opacity:.35;
}
.escala-global .marcas {
  margin-top:6px; display:flex; justify-content:space-between; font-size:12px; color:#555;
}
.fila-den {
  display:grid; grid-template-columns: 220px 1fr; gap:12px; align-items:center;
  margin: 10px 0;
}
.etiqueta-den { font-weight:600; color:#222; font-size:14px; }
.barra-den {
  position: relative; width: var(--w); height: var(--h);
  border-radius: 999px; background:#eef2f7; overflow:hidden;
  box-shadow: inset 0 1px 2px rgba(0,0,0,.14);
}
.tramo-den {
  position:absolute; top:0; bottom:0; border-radius:999px;
  background: linear-gradient(to right, var(--c1) 0%, var(--c2) 100%);
  box-shadow: inset 0 1px 1px rgba(255,255,255,.6), 0 4px 10px rgba(0,0,0,.12);
}
.ticks-den {
  position:absolute; inset:calc(100% + 4px) 0 auto 0;
  display:flex; justify-content:space-between; font-size:11px; color:#444; opacity:.9;
}
-->
</style>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-MQMBPYV27H"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-MQMBPYV27H');
</script>

<script>
$(function() { 
   $("#one").addClass("progress-bar-purple");
   $("#two").addClass("progress-bar-orange");
   initialPanelHTML = $('#panelTotalProv').html();
});
</script>
</head>

<body style="background:#f7f8fa">
<div class="container-fluid py-3">

  <!-- HEADER -->
  <div class="d-flex align-items-center justify-content-between bg-primary px-3 py-2 rounded text-white">
    <div class="d-flex align-items-center">
      <img src="fotos/escudo_la_pampa.png" width="56" height="60" class="mr-3"/>
      <div>
        <div class="font-weight-bold" style="font-size:20px;">Elecciones <?php echo date('Y'); ?></div>
        <div class="">26 de octubre de <?php echo date('Y'); ?> &nbsp; Escrutinio provisorio</div>
      </div>
    </div>
    
    <div align="center"> <img src="fotos/Escudo_del_Partido_Justicialista.png" width="50" height="60" class="mr-3"/></div>
    
    <div class="text-right">
     
      <div class="small">
        <?php echo htmlspecialchars($_SESSION['MM_Username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
        &nbsp;&nbsp;<a href="<?php echo $logoutAction ?>" class="text-light">Salir</a>
      </div>
    </div>
  </div>

  <!-- KPIs -->
  <div class="row mt-3">
    <div class="col-md-4 mb-2">
      <div class="card shadow-sm">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="h3 mb-0">
              <span class="text-muted h4">  Mesas escrutadas: <?php echo number_format($CantidadMesasEscrutadas,0, ',', '.'); ?>
                 (<?php echo number_format($PorcentajeMesasEscrutadas,1, ',', '.'); ?>%)</span>
              </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-2">
      <div class="card shadow-sm">
        <div class="card-body py-3">
           <div class="h3 mb-0"> 
           <span class="text-muted h4"> Votos emitidos (Dip.): <?php echo number_format($totalEmitidos ?? 0,0, ',', '.'); ?></span></div>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 mb-2">
      <div class="card shadow-sm">
        <div class="card-body py-3">
          <div class="form-inline">
            <label class="mr-2 small text-muted">Localidad</label>
            <select id="selectLocalidad" name="selectLocalidad" class="form-control">
              <option value="0">Toda la Provincia</option>
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
              <option value="1019">Casa de Piedra</option>
              <option value="16" >Catril&oacute </option>
              <option value="17" >Ceballos</option>
              <option value="18" >Chacharramendi</option>
              <option value="23" >Col. Santa Mar&iacute;a</option>
              <option value="81" >Col. Santa Teresa</option>
              <option value="21" >Colonia Bar&oacute;n</option>
              <option value="24" >Conhello</option>
              <option value="20" >Cnel Hilario Lagos</option>
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
              <option value="86" >Tomas M Anchorena</option>
              <option value="88" >Trenel</option>
              <option value="89" >Unanue</option>
              <option value="90" >Uriburu</option>
              <option value="91" >Vertiz</option>
              <option value="92" >Victorica</option>
              <option value="93" >Villa Mirasol</option>
              <option value="94" >Winifreda</option>
            </select>
            </div> 
        </div>
      </div>
    </div>
    
    
  </div>

  <div class="row mt-2">
    <!-- IZQUIERDA: MAPA + LEYENDA -->
    <div class="col-lg-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="h5 mb-0">Provincia de La Pampa</div>
            
          </div>
            
      <div class="d-flex justify-content-center">
       <div style="position:relative; width:640px; max-width:100%;">
        <canvas id="mapa" width="640" height="640" class="d-block mx-auto"
                style="width:100%; height:auto; border:1px solid #e9ecef; border-radius:.5rem;"></canvas>
          <div id="tooltip" class="badge badge-light"
             style="position:absolute; display:none; pointer-events:none;"></div>
          </div>
     </div>


        </div>
      </div>
      <div class="text-muted small mt-2">Fuente: C&oacute;mputos propios</div>
    </div>
    
    <!-- modal -->
     
     <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title">Gobiernos Locales 2023-2027</h4> 
      </div>
      <div class="modal-body">
        <div id="resultados_ajax"> </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

     </div>
   </div>
    <!-- modal --> 

    <!-- DERECHA: PANEL "TOTAL PROVINCIAL" -->
    <div class="col-lg-6 mb-3">
      <div class="card shadow-sm">
        <div class="card-body" id="panelTotalProv">
          <div class="h4 mb-3">Total Provincial (sobre votos positivos)</div>

          <?php
            $mostrar = array_slice($Partido, 0, 5);
            foreach ($mostrar as $p):
              $logo = 'fotos/'.($p['imagen'] ?? '1.png');
              $nombre = $p['nombre'] ?? '';
              $votos  = (int)($p['suma_diputado'] ?? 0);
              $pct    = number_format($p['porcentaje_diputado'] ?? 0, 1, ',', '.');
              $pctNum = floatval(str_replace(',', '.', $pct));
              $color  = $p['color'] ?? '#999999';
          ?>
          <div class="media align-items-center mb-3">
           
            <div class="media-body">
              <div class="d-flex justify-content-between">
                <div class="font-weight-bold"><?php echo htmlspecialchars($nombre,ENT_QUOTES,'UTF-8'); ?></div>
                <div class="font-weight-bold"><?php echo $pct; ?>%</div>
              </div>
              <div class="progress" style="height:27px;">
                <div class="progress-bar" role="progressbar"
                     style="width: <?php echo $pctNum; ?>%; background: <?php echo $color; ?>;"
                     aria-valuenow="<?php echo $pctNum; ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <div class="text-muted small mt-1">
                <?php echo number_format($votos,0, ',', '.'); ?> votos
              </div>
            </div>
          </div>
          <?php endforeach; ?>
         
         <?php
  // Totales provinciales extra
  $blancos    = (int)($SumaL6DP ?? 0);
  $nulos      = (int)($SumaL7DP ?? 0);
  $recurridos = (int)($SumaL8DP ?? 0);
  $impugnados = (int)($SumaL9DP ?? 0);
  $totExtra   = $blancos + $nulos + $recurridos + $impugnados;

  $fmtPct = function($n,$den){
    $v = ($den > 0) ? ($n * 100.0 / $den) : 0.0;
    return number_format($v, 1, ',', '.');
  };
?>

  <hr class="my-3">

  <div class="small text-muted">
  <div>Votos en blanco: <?php echo number_format($blancos,0,',','.'); ?> (<?php echo $fmtPct($blancos,$totalEmitidos); ?>%)</div>
  <div>Votos nulos: <?php echo number_format($nulos,0,',','.'); ?> (<?php echo $fmtPct($nulos,$totalEmitidos); ?>%)</div>
  <div>Votos recurridos: <?php echo number_format($recurridos,0,',','.'); ?> (<?php echo $fmtPct($recurridos,$totalEmitidos); ?>%)</div>
  <div>Votos impugnados: <?php echo number_format($impugnados,0,',','.'); ?> (<?php echo $fmtPct($impugnados,$totalEmitidos); ?>%)</div>
  <div class="mt-2">Total de votos emitidos: <?php echo number_format($totalEmitidos,0,',','.'); ?></div> 
  <div class="mt-2">Total de votos positivos: <?php echo number_format($totalPositivos,0,',','.'); ?></div>  
  </div>
           
          <hr>
          <div class="row text-muted small">
            <div class="col-6">Mesas escrutadas: <?php echo number_format($CantidadMesasEscrutadas,0, ',', '.'); ?> (<?php echo number_format($PorcentajeMesasEscrutadas,1, ',', '.'); ?>%)
            <?php $concurrencia =($totalEmitidos / $total_electores )*100 ?> 
            | Concurrencia: (<?php echo number_format($concurrencia,1, ',', '.'); ?>%)
            
            </div>
            
            <div class="col-6 text-right">Actualizaci&oacute;n: <?php echo date('d/m/Y'); ?> | <?php echo date('H:i'); ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center text-muted mt-3 small"><?php echo date('Y')?> Partido Justicialista La Pampa</div>
</div>

<!-- ======== SCRIPTS MAPA ======== -->
<script>
  const BASE_FILL      = '#cfd4da';
  const BASE_STROKE    = '#ffffff';
  const SELECTED_FILL  = '#ff0000';
  const SELECTED_STROKE= '#7a0000';

  const PIN_FILL       = '#ffffff';
  const PIN_STROKE     = '#202020';
  const PIN_HEAD       = '#e03131';
  const PIN_RADIUS     = 7;
  const PIN_HEIGHT     = 20;

  // --- Utiles ---
  const canvas   = document.getElementById('mapa');
  const ctx      = canvas.getContext('2d');
  const tooltip  = document.getElementById('tooltip');
  const $selTxt  = document.getElementById('seleccionActual');
  let initialPanelHTML; 
  

   
  function normalizeStr(s){
    return (s || '').toString()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/\s+/g,' ')
      .trim().toLowerCase();
  }

  const selectLocalidad = document.getElementById('selectLocalidad');
  const nameToId = {};
  const idToName  = {};

  if (selectLocalidad){
    [...selectLocalidad.options].forEach(opt=>{
      const id = parseInt(opt.value,10);
      const label = (opt.text || '').trim();
      if (!isNaN(id) && id>0){
        nameToId[ normalizeStr(label) ] = id;
        idToName[id] = label;
      }
    });
  }

  const partidoNombre = {
    1: "FRENTE DEFENDEMOS LA PAMPA",
    2: "ALIANZA LA LIBERTAD AVANZA",
    3: "FRENTE DE IZQUIERDA Y DE TRABAJADORES-UNIDAD",
    4: "CAMBIA LA PAMPA",
    5: "MOVIMIENTO AL SOCIALISMO",
    6: "Votos Blancos",
    7: "Votos Nulos",
    8: "Votos Recurridos",
    9: "Votos Impugnados"
  };

  let selectedLocalidad = null;
  let shapes = [];
  let winnersById = {};

  function getCanvasCoords(evt){
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width  / rect.width;
    const scaleY = canvas.height / rect.height;
    return {
      x: (evt.clientX - rect.left) * scaleX,
      y: (evt.clientY - rect.top)  * scaleY,
      pageX: evt.pageX, pageY: evt.pageY
    };
  }

  
 function drawMap(){
  ctx.clearRect(0,0,canvas.width,canvas.height);

  for (const obj of shapes){
    const idLoc = nameToId[ normalizeStr(obj.name) ];
    const info  = idLoc ? winnersById[idLoc] : null;

    // Color de relleno: base gris o color del ganador con densidad por %.
    let fill = BASE_FILL;
	if (info && info.total > 0){
	  const partyHex = info.color || BASE_FILL;
	  fill = partyHex; // color puro, sin densidad
	}

    // Trazo del polígono
    ctx.beginPath();
    const pts = obj.coords;
    for (let i = 0; i < pts.length; i++){
      const [x, y] = pts[i];
      if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    }
    ctx.closePath();

    ctx.fillStyle   = fill;
    ctx.strokeStyle = BASE_STROKE;
    ctx.lineWidth   = 1;
    ctx.fill();
    ctx.stroke();
  }

  // Pin si hay localidad seleccionada
  if (selectedLocalidad){
    const sel = shapes.find(s => s.name === selectedLocalidad);
    if (sel && sel.centroid){
      const [cx, cy] = sel.centroid;
      drawPin(ctx, cx, cy);
    }
  }
}
  
  

  canvas.addEventListener('mousemove', (e)=>{
    const {x,y,pageX,pageY} = getCanvasCoords(e);
    let hit = null;
    for (const obj of shapes){
      if (ctx.isPointInPath(obj.path,x,y)){ hit = obj; break; }
    }
    if(hit){
      drawMap();
      ctx.save();
      ctx.lineWidth = 2;
      ctx.strokeStyle = '#888';
      ctx.stroke(hit.path);
      ctx.restore();

      const idLoc = nameToId[ normalizeStr(hit.name) ];
      let extra = '';
      if (idLoc && winnersById[idLoc] && winnersById[idLoc].total>0){
        const w = winnersById[idLoc];
        const nombre = partidoNombre[w.winner] || `Partido ${w.winner}`;
        extra = ` - ${nombre} ${w.pct}%`;
      }

      tooltip.innerText = `${hit.name || '(s/n)'}${extra}`;
	  tooltip.innerText = `${hit.name || '(s/n)'}${extra}`;
	  tooltip.style.display = 'block';
		
		// Posicionar relativo al canvas (su wrapper es position:relative)
	  const rect = canvas.getBoundingClientRect();
	  const localX = e.clientX - rect.left;
	  const localY = e.clientY - rect.top;
		
	  const offsetX = 12, offsetY = 12;
	  tooltip.style.left = `${localX + offsetX}px`;
	  tooltip.style.top  = `${localY - offsetY - tooltip.offsetHeight}px`;
     
	  canvas.style.cursor = 'pointer';
    }else{
      tooltip.style.display = 'none';
      canvas.style.cursor = 'default';
    }
  });

  canvas.addEventListener('mouseleave', ()=>{ tooltip.style.display='none'; });

  canvas.addEventListener('click', (e)=>{
    const {x,y} = getCanvasCoords(e);
    let hit = null;
    for (const obj of shapes){
      if (ctx.isPointInPath(obj.path,x,y)){ hit = obj; break; }
    }
    if(hit){
      selectedLocalidad = hit.name || null;
      drawMap();

      const id = nameToId[ normalizeStr(hit.name || '') ];
      if (id){
        if (selectLocalidad){ selectLocalidad.value = String(id); }
        irALocalidad(id);
      }else{
        console.warn('No pude mapear nombre a id_localidad:', hit.name);
      }
    }
  });

  if (selectLocalidad){
    selectLocalidad.addEventListener('change', ()=>{
      const val = parseInt(selectLocalidad.value,10);
      if (isNaN(val) || val<=0){
        selectedLocalidad = null;
        drawMap();
       
	    if (typeof initialPanelHTML !== 'undefined') {
         $('#panelTotalProv').html(initialPanelHTML); // ? restaura Total Provincial
        }
       return;
  
      }
      const label = idToName[val] || '';
      const targetName = normalizeStr(label);
      const shape = shapes.find(s => normalizeStr(s.name) === targetName);

      if (shape){ selectedLocalidad = shape.name; } else { selectedLocalidad = null; }
      drawMap();
      irALocalidad(val);
    });
  }

  function drawPin(ctx, x, y) {
    ctx.save();
    ctx.beginPath();
    ctx.moveTo(x, y);
    ctx.quadraticCurveTo(x - PIN_RADIUS, y - (PIN_HEIGHT * 0.35), x, y - PIN_HEIGHT);
    ctx.quadraticCurveTo(x + PIN_RADIUS, y - (PIN_HEIGHT * 0.35), x, y);
    ctx.closePath();
    ctx.fillStyle = PIN_HEAD;
    ctx.strokeStyle = PIN_STROKE;
    ctx.lineWidth = 1.5;
    ctx.fill();
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(x, y - (PIN_HEIGHT * 0.55), PIN_RADIUS, 0, Math.PI * 2);
    ctx.fillStyle = PIN_FILL;
    ctx.strokeStyle = PIN_STROKE;
    ctx.lineWidth = 1.2;
    ctx.fill();
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(x, y - (PIN_HEIGHT * 0.55), PIN_RADIUS * 0.35, 0, Math.PI * 2);
    ctx.fillStyle = PIN_HEAD;
    ctx.fill();
    ctx.restore();
  }

  function polygonCentroid(points) {
    let area = 0, cx = 0, cy = 0;
    const n = points.length;
    for (let i = 0; i < n; i++) {
      const [x1, y1] = points[i];
      const [x2, y2] = points[(i + 1) % n];
      const cross = x1 * y2 - x2 * y1;
      area += cross;
      cx += (x1 + x2) * cross;
      cy += (y1 + y2) * cross;
    }
    area *= 0.5;
    if (Math.abs(area) < 1e-7) {
      let sx = 0, sy = 0;
      points.forEach(([x, y]) => { sx += x; sy += y; });
      return [sx / n, sy / n];
    }
    cx /= (6 * area);
    cy /= (6 * area);
    return [cx, cy];
  }

  function informacion1(localidad){
    $.ajax({
      type: "POST",
      url: 'ver_localidad.php',
      data: {"id_localidad": localidad},
      success: function(html) { $('#panelTotalProv').html(html); },
      error: function() { alert('No se pudo obtener datos'); }
    });
  }

  function irALocalidad(idLocalidad){
    if (typeof informacion1 === 'function'){
      informacion1(String(idLocalidad));
    }
  }

  Promise.all([
    fetch('poligonos_la_pampa_departamentos_final.json').then(r=>r.json()),
    fetch('ganadores_localidades.php').then(r=>r.json())
  ])
  .then(([dataPolys, dataWinners])=>{
    winnersById = dataWinners || {};

    let minX=Infinity,maxX=-Infinity,minY=Infinity,maxY=-Infinity;
    dataPolys.forEach(p=>{
      p.coords.forEach(([x,y])=>{
        if(x<minX) minX=x; if(x>maxX) maxX=x;
        if(y<minY) minY=y; if(y>maxY) maxY=y;
      });
    });
    const W=640,H=640;
    const scale = Math.min( W/(maxX-minX), H/(maxY-minY) )*0.97;
    const offX = (W - (maxX-minX)*scale)/2;
    const offY = (H - (maxY-minY)*scale)/2;

    shapes = dataPolys.map(p => {
      const pts = p.coords.map(([x,y]) => [(x - minX) * scale + offX, (y - minY) * scale + offY]);
      const path = new Path2D();
      pts.forEach(([x, y], i) => { if (i === 0) path.moveTo(x, y); else path.lineTo(x, y); });
      path.closePath();

      const [cx, cy] = polygonCentroid(pts);
      return { ...p, coords: pts, path, centroid: [cx, cy] };
    });

    drawMap();
  })
  .catch(err=>{
    console.error('Error inicializando mapa:', err);
    ctx.fillStyle='#fafafa';
    ctx.fillRect(0,0,canvas.width,canvas.height);
    ctx.fillStyle='#999';
    ctx.fillText('No se pudo cargar el mapa', 10, 20);
  });
 </script>
 
 <script>

 function informacion11(id_localidad, nombre_localidad) {
  console.log('informacion11()', id_localidad, nombre_localidad);

  $.ajax({
    type: "POST",
    url: 'trae_datos_concejales.php',
    data: { id_localidad: id_localidad },
    beforeSend: function () {
      $("#resultados_ajax").html("Cargando datos...");
    },
    success: function (data) {
      console.log('respuesta OK:', data);
      $(".modal-title").html("Cuerpos Colegiados 2023-2027 - " + nombre_localidad);
      $("#resultados_ajax").html(data);
    },
    error: function (xhr, status, error) {
      console.error('Error AJAX:', status, error);
      console.log('Respuesta del servidor:', xhr.responseText);
      $("#resultados_ajax").html(
        "<div class='alert alert-danger'>Error cargando datos</div>"
      );
    }
  });
}

function informacion22(id_localidad,nombre_localidad){
  
  $.ajax({
	  type: "POST",
      url: 'trae_datos_localidad.php', //trae_datos_localidad.php, trae el historial de elecciones pasadas. Y el trae_datos_localidad_actual trae datos eleccion actual
      data: {"id_localidad":id_localidad},
      success: function(data) {
	  $(".modal-title").html("Historial Elecciones");
      $("#resultados_ajax").html(data); 
	  }
   }
 );
		
}
 </script>

 </body>
