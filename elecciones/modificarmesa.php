<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

require_once('Connections/conexionUsuarios.php'); 

 $Listas[1]["nombre"]="FRENTE DEFENDEMOS LA PAMPA";
 $Listas[1]["lista"]="503";
 $Listas[1]["representacion"]="DN"; //Pdte,PN,DN,PR

 $Listas[2]["nombre"]="ALIANZA LA LIBERTAD AVANZA";
 $Listas[2]["lista"]="501";
 $Listas[2]["representacion"]="DN";
 
 $Listas[3]["nombre"]="FRENTE DE IZQUIERDA Y DE TRABAJADORES-UNIDAD";
 $Listas[3]["lista"]="502";
 $Listas[3]["representacion"]="DN";
 
 $Listas[4]["nombre"]="CAMBIA LA PAMPA";
 $Listas[4]["lista"]="504";
 $Listas[4]["representacion"]="DN";
 
 $Listas[5]["nombre"]="MOVIMIENTO AL SOCIALISMO";
 $Listas[5]["lista"]="13";
 $Listas[5]["representacion"]="DN";
   
 $Listas[6]["nombre"]="Blancos";
 $Listas[6]["lista"]="";
 $Listas[6]["representacion"]="DN";
 
 $Listas[7]["nombre"]="Nulos";
 $Listas[7]["lista"]="";
 $Listas[7]["representacion"]="DN";
 
 $Listas[8]["nombre"]="Recurridos";
 $Listas[8]["lista"]="";
 $Listas[8]["representacion"]="DN";
 
 $Listas[9]["nombre"]="Impugnados";
 $Listas[9]["lista"]="";
 $Listas[9]["representacion"]="DN";
   		 
 
 
	
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

$MM_authorizedUsers = "2,3,4";
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



// Mesa: GET (id o mesa) > POST (codmesa) > 0
$Mesa = 0;
$desde_link = false;

if (isset($_GET['id']) && $_GET['id'] !== '') {
  $Mesa = intval($_GET['id']);
  $desde_link = true;
} elseif (isset($_GET['mesa']) && $_GET['mesa'] !== '') {
  $Mesa = intval($_GET['mesa']);
  $desde_link = true;
} elseif (isset($_POST['codmesa']) && $_POST['codmesa'] !== '') {
  $Mesa = intval($_POST['codmesa']);
}
 
 $sel_actualizar="update set Escrutada='N' WHERE  Mesa='".$Mesa."'";
 
 $sql=mysqli_query($con,$sel_actualizar);
 
 $sel_consulta1="select * from mesas WHERE Mesa='".$Mesa."'";
 
 $sql1=mysqli_query($con,$sel_consulta1);
 $rs_consulta=mysqli_fetch_array($sql1); 

 $L1I=$rs_consulta['L1I'];
 $L1G=$rs_consulta['L1G'];
 $L1DP=$rs_consulta['L1DP'];
 $L1S=$rs_consulta['L1S'];
  
 $L2I=$rs_consulta['L2I'];
 $L2G=$rs_consulta['L2G'];
 $L2DP=$rs_consulta['L2DP'];
 $L2S=$rs_consulta['L2S'];
 
 $L3I=$rs_consulta['L3I'];
 $L3G=$rs_consulta['L3G'];
 $L3DP=$rs_consulta['L3DP'];
 $L3S=$rs_consulta['L3S'];
 

 $L4I=$rs_consulta['L4I'];
 $L4G=$rs_consulta['L4G'];
 $L4DP=$rs_consulta['L4DP'];
 $L4S=$rs_consulta['L4S'];

 $L5I=$rs_consulta['L5I'];
 $L5G=$rs_consulta['L5G'];
 $L5DP=$rs_consulta['L5DP'];
 $L5S=$rs_consulta['L5S'];
 
 $L6I=$rs_consulta['L6I'];
 $L6G=$rs_consulta['L6G'];
 $L6DP=$rs_consulta['L6DP'];
 $L6S=$rs_consulta['L6S'];
 
 $L7I=$rs_consulta['L7I'];
 $L7G=$rs_consulta['L7G'];
 $L7DP=$rs_consulta['L7DP'];
 $L7S=$rs_consulta['L7S'];


 $L8I=$rs_consulta['L8I'];
 $L8G=$rs_consulta['L8G'];
 $L8DP=$rs_consulta['L8DP'];
 $L8S=$rs_consulta['L8S'];

 $L9I=$rs_consulta['L9I'];
 $L9G=$rs_consulta['L9G'];
 $L9DP=$rs_consulta['L9DP'];
 $L9S=$rs_consulta['L9S'];

 $L10I=$rs_consulta['L10I'];
 $L10G=$rs_consulta['L10G'];
 $L10DP=$rs_consulta['L10DP'];
 $L10S=$rs_consulta['L10S'];
 
 //
 
 $L11I=$rs_consulta['L11I'];
 $L11G=$rs_consulta['L11G'];
 $L11DP=$rs_consulta['L11DP'];
 $L11S=$rs_consulta['L11S'];
  
 $L12I=$rs_consulta['L12I'];
 $L12G=$rs_consulta['L12G'];
 $L12DP=$rs_consulta['L12DP'];
 $L12S=$rs_consulta['L12S'];
 
 $L13I=$rs_consulta['L13I'];
 $L13G=$rs_consulta['L13G'];
 $L13DP=$rs_consulta['L13DP'];
 $L13S=$rs_consulta['L13S'];
 

 $L14I=$rs_consulta['L14I'];
 $L14G=$rs_consulta['L14G'];
 $L14DP=$rs_consulta['L14DP'];
 $L14S=$rs_consulta['L14S'];

 $L15I=$rs_consulta['L15I'];
 $L15G=$rs_consulta['L15G'];
 $L15DP=$rs_consulta['L15DP'];
 $L15S=$rs_consulta['L15S'];
 
 $L16I=$rs_consulta['L16I'];
 $L16G=$rs_consulta['L16G'];
 $L16DP=$rs_consulta['L16DP'];
 $L16S=$rs_consulta['L16S'];
 
 $L17I=$rs_consulta['L17I'];
 $L17G=$rs_consulta['L17G'];
 $L17DP=$rs_consulta['L17DP'];
 $L17S=$rs_consulta['L17S'];


 $L18I=$rs_consulta['L18I'];
 $L18G=$rs_consulta['L18G'];
 $L18DP=$rs_consulta['L18DP'];
 $L18S=$rs_consulta['L18S'];

 $L19I=$rs_consulta['L19I'];
 $L19G=$rs_consulta['L19G'];
 $L19DP=$rs_consulta['L19DP'];
 $L19S=$rs_consulta['L19S'];

 $L20I=$rs_consulta['L20I'];
 $L20G=$rs_consulta['L20G'];
 $L20DP=$rs_consulta['L20DP'];
 $L20S=$rs_consulta['L20S'];
 
 //
 
 $L21I=$rs_consulta['L21I'];
 $L21G=$rs_consulta['L21G'];
 $L21DP=$rs_consulta['L21DP'];
 $L21S=$rs_consulta['L21S'];
  
 $L22I=$rs_consulta['L22I'];
 $L22G=$rs_consulta['L22G'];
 $L22DP=$rs_consulta['L22DP'];
 $L22S=$rs_consulta['L22S'];
 
 $L23I=$rs_consulta['L23I'];
 $L23G=$rs_consulta['L23G'];
 $L23DP=$rs_consulta['L23DP'];
 $L23S=$rs_consulta['L23S'];
 

 $L24I=$rs_consulta['L24I'];
 $L24G=$rs_consulta['L24G'];
 $L24DP=$rs_consulta['L24DP'];
 $L24S=$rs_consulta['L24S'];

 $L25I=$rs_consulta['L25I'];
 $L25G=$rs_consulta['L25G'];
 $L25DP=$rs_consulta['L25DP'];
 $L25S=$rs_consulta['L25S'];
 
 $L26I=$rs_consulta['L26I'];
 $L26G=$rs_consulta['L26G'];
 $L26DP=$rs_consulta['L26DP'];
 $L26S=$rs_consulta['L26S'];
 
 $L27I=$rs_consulta['L27I'];
 $L27G=$rs_consulta['L27G'];
 $L27DP=$rs_consulta['L27DP'];
 $L27S=$rs_consulta['L27S'];


 $L28I=$rs_consulta['L28I'];
 $L28G=$rs_consulta['L28G'];
 $L28DP=$rs_consulta['L28DP'];
 $L28S=$rs_consulta['L28S'];

 $L29I=$rs_consulta['L29I'];
 $L29G=$rs_consulta['L29G'];
 $L29DP=$rs_consulta['L29DP'];
 $L29S=$rs_consulta['L29S'];
 
$L1I =intval($L1I);
$L1G =intval($L1G);
$L1DP =intval($L1DP);
$L1S =intval($L1S);

$L2I =intval($L2I);
$L2G =intval($L2G);
$L2DP =intval($L2DP);
$L2S =intval($L2S);

$L3I =intval($L3I);
$L3G =intval($L3G);
$L3DP =intval($L3DP);
$L3S =intval($L3S);

$L4I =intval($L4I);
$L4G =intval($L4G);
$L4DP =intval($L4DP);
$L4S =intval($L4S);

$L5I =intval($L5I);
$L5G =intval($L5G);
$L5DP =intval($L5DP);
$L5S =intval($L5S);

$L6I =intval($L6I);
$L6G =intval($L6G);
$L6DP =intval($L6DP);
$L6S =intval($L6S);

$L7I =intval($L7I);
$L7G =intval($L7G);
$L7DP =intval($L7DP);
$L7S =intval($L7S);

$L8I =intval($L8I);
$L8G =intval($L8G);
$L8DP =intval($L8DP);
$L8S =intval($L8S);

$L9I =intval($L9I);
$L9G =intval($L9G);
$L9DP =intval($L9DP);
$L9S =intval($L9S);

$L10I =intval($L10I);
$L10G =intval($L10G);
$L10DP =intval($L10DP);
$L10S =intval($L10S);

////

$L11I =intval($L11I);
$L11G =intval($L11G);
$L11DP =intval($L11DP);
$L11S =intval($L11S);

$L12I =intval($L2I);
$L12G =intval($L12G);
$L12DP =intval($L12DP);
$L12S =intval($L12S);

$L13I =intval($L13I);
$L13G =intval($L13G);
$L13DP =intval($L13DP);
$L13S =intval($L13S);

$L14I =intval($L14I);
$L14G =intval($L14G);
$L14DP =intval($L14DP);
$L14S =intval($L14S);

$L15I =intval($L15I);
$L15G =intval($L15G);
$L15DP =intval($L15DP);
$L15S =intval($L15S);

$L16I =intval($L16I);
$L16G =intval($L16G);
$L16DP =intval($L16DP);
$L16S =intval($L16S);

$L17I =intval($L17I);
$L17G =intval($L17G);
$L17DP =intval($L17DP);
$L17S =intval($L17S);

$L18I =intval($L18I);
$L18G =intval($L18G);
$L18DP =intval($L18DP);
$L18S =intval($L18S);

$L19I =intval($L19I);
$L19G =intval($L19G);
$L19DP =intval($L19DP);
$L19S =intval($L19S);

$L20I =intval($L20I);
$L20G =intval($L20G);
$L20DP =intval($L20DP);
$L20S =intval($L20S);

//

$L21I =intval($L21I);
$L21G =intval($L21G);
$L21DP =intval($L21DP);
$L21S =intval($L21S);

$L22I =intval($L2I);
$L22G =intval($L22G);
$L22DP =intval($L22DP);
$L22S =intval($L22S);

$L23I =intval($L23I);
$L23G =intval($L23G);
$L23DP =intval($L23DP);
$L23S =intval($L23S);

$L24I =intval($L24I);
$L24G =intval($L24G);
$L24DP =intval($L24DP);
$L24S =intval($L24S);

$L25I =intval($L25I);
$L25G =intval($L25G);
$L25DP =intval($L25DP);
$L25S =intval($L25S);

$L26I =intval($L26I);
$L26G =intval($L26G);
$L26DP =intval($L26DP);
$L26S =intval($L26S);

$L27I =intval($L27I);
$L27G =intval($L27G);
$L27DP =intval($L27DP);
$L27S =intval($L27S);

$L28I =intval($L28I);
$L28G =intval($L28G);
$L28DP =intval($L28DP);
$L28S =intval($L28S);

$L29I =intval($L29I);
$L29G =intval($L29G);
$L29DP =intval($L29DP);
$L29S =intval($L29S);

 $resultado[1]["gobernador"]=$L1G;
 $resultado[1]["intendente"]=$L1I;
 $resultado[1]["diputado"]=$L1DP;
 $resultado[1]["senador"]=$L1S;
 
 $resultado[2]["gobernador"]=$L2G;
 $resultado[2]["intendente"]=$L2I;
 $resultado[2]["diputado"]=$L2DP;
 $resultado[2]["senador"]=$L2S;
 
 $resultado[3]["gobernador"]=$L3G;
 $resultado[3]["intendente"]=$L3I;
 $resultado[3]["diputado"]=$L3DP;
 $resultado[3]["senador"]=$L3S;
 
 $resultado[4]["gobernador"]=$L4G;
 $resultado[4]["intendente"]=$L4I;
 $resultado[4]["diputado"]=$L4DP;
 $resultado[4]["senador"]=$L4S;
 
 $resultado[5]["gobernador"]=$L5G;
 $resultado[5]["intendente"]=$L5I;
 $resultado[5]["diputado"]=$L5DP;
 $resultado[5]["senador"]=$L5S;

 $resultado[6]["gobernador"]=$L6G;
 $resultado[6]["intendente"]=$L6I;
 $resultado[6]["diputado"]=$L6DP;
 $resultado[6]["senador"]=$L6S;
 
 $resultado[7]["gobernador"]=$L7G;
 $resultado[7]["intendente"]=$L7I;
 $resultado[7]["diputado"]=$L7DP;
 $resultado[7]["senador"]=$L7S;
 
 $resultado[8]["gobernador"]=$L8G;
 $resultado[8]["intendente"]=$L8I;
 $resultado[8]["diputado"]=$L8DP;
 $resultado[8]["senador"]=$L8S;
 
 $resultado[9]["gobernador"]=$L9G;
 $resultado[9]["intendente"]=$L9I;
 $resultado[9]["diputado"]=$L9DP;
 $resultado[9]["senador"]=$L9S;
 
 $resultado[10]["gobernador"]=$L10G;
 $resultado[10]["intendente"]=$L10I;
 $resultado[10]["diputado"]=$L10DP;
 $resultado[10]["senador"]=$L10S;
 
 //
 
 $resultado[11]["gobernador"]=$L11G;
 $resultado[11]["intendente"]=$L11I;
 $resultado[11]["diputado"]=$L11DP;
 $resultado[11]["senador"]=$L11S;
 
 $resultado[12]["gobernador"]=$L12G;
 $resultado[12]["intendente"]=$L12I;
 $resultado[12]["diputado"]=$L12DP;
 $resultado[12]["senador"]=$L12S;
 
 $resultado[13]["gobernador"]=$L13G;
 $resultado[13]["intendente"]=$L13I;
 $resultado[13]["diputado"]=$L13DP;
 $resultado[13]["senador"]=$L13S;
 
 $resultado[14]["gobernador"]=$L14G;
 $resultado[14]["intendente"]=$L14I;
 $resultado[14]["diputado"]=$L14DP;
 $resultado[14]["senador"]=$L14S;
 
 $resultado[15]["gobernador"]=$L15G;
 $resultado[15]["intendente"]=$L15I;
 $resultado[15]["diputado"]=$L15DP;
 $resultado[15]["senador"]=$L15S;

 $resultado[16]["gobernador"]=$L16G;
 $resultado[16]["intendente"]=$L16I;
 $resultado[16]["diputado"]=$L16DP;
 $resultado[16]["senador"]=$L16S;
 
 $resultado[17]["gobernador"]=$L17G;
 $resultado[17]["intendente"]=$L17I;
 $resultado[17]["diputado"]=$L17DP;
 $resultado[17]["senador"]=$L17S;
 
 $resultado[18]["gobernador"]=$L18G;
 $resultado[18]["intendente"]=$L18I;
 $resultado[18]["diputado"]=$L18DP;
 $resultado[18]["senador"]=$L18S;
 
 $resultado[19]["gobernador"]=$L19G;
 $resultado[19]["intendente"]=$L19I;
 $resultado[19]["diputado"]=$L19DP;
 $resultado[19]["senador"]=$L19S;
 
 $resultado[20]["gobernador"]=$L20G;
 $resultado[20]["intendente"]=$L20I;
 $resultado[20]["diputado"]=$L20DP;
 $resultado[20]["senador"]=$L20S;
 
 //
 
 $resultado[21]["gobernador"]=$L21G;
 $resultado[21]["intendente"]=$L21I;
 $resultado[21]["diputado"]=$L21DP;
 $resultado[21]["senador"]=$L21S;
 
 $resultado[22]["gobernador"]=$L22G;
 $resultado[22]["intendente"]=$L22I;
 $resultado[22]["diputado"]=$L22DP;
 $resultado[22]["senador"]=$L22S;
 
 $resultado[23]["gobernador"]=$L23G;
 $resultado[23]["intendente"]=$L23I;
 $resultado[23]["diputado"]=$L23DP;
 $resultado[23]["senador"]=$L23S;
 
 $resultado[24]["gobernador"]=$L24G;
 $resultado[24]["intendente"]=$L24I;
 $resultado[24]["diputado"]=$L24DP;
 $resultado[24]["senador"]=$L24S;
 
 $resultado[25]["gobernador"]=$L25G;
 $resultado[25]["intendente"]=$L25I;
 $resultado[25]["diputado"]=$L25DP;
 $resultado[25]["senador"]=$L25S;

 $resultado[26]["gobernador"]=$L26G;
 $resultado[26]["intendente"]=$L26I;
 $resultado[26]["diputado"]=$L26DP;
 $resultado[26]["senador"]=$L26S;
 
 $resultado[27]["gobernador"]=$L27G;
 $resultado[27]["intendente"]=$L27I;
 $resultado[27]["diputado"]=$L27DP;
 $resultado[27]["senador"]=$L27S;
 
 $resultado[28]["gobernador"]=$L28G;
 $resultado[28]["intendente"]=$L28I;
 $resultado[28]["diputado"]=$L28DP;
 $resultado[28]["senador"]=$L28S;
 
 $resultado[29]["gobernador"]=$L29G;
 $resultado[29]["intendente"]=$L29I;
 $resultado[29]["diputado"]=$L29DP;
 $resultado[29]["senador"]=$L29S;
 
 
?>
   
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

 
<script> 

function tabular(e,obj) { 
   
  tecla=(document.all) ? e.keyCode : e.which; 
  if(tecla!=13) return; 
  frm=obj.form; 
  for(i=0;i<frm.elements.length;i++){ 
    if(frm.elements[i]==obj) { 
      if (i==frm.elements.length-1) i=-1; // vuelve al principio 
      break 
	 } 
   }
  
  
  for(j=i;j<frm.elements.length;j++){
   if (j==frm.elements.length-1)
    break
	
   var valor_objeto= frm.elements[j+1].value;
   if (valor_objeto=="")
     break
	
  }	 	
     
  frm.elements[j+1].focus();
  
  return false; 
} 




</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Estad&iacute;sticas Elecciones La Pampa</title>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>

<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo5 {color: #000000}
.Estilo4 {color: #000000}
.Estilo6 {
	color: #3366CC;
	font-weight: bold;
}
.Estilo14 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; }
.Estilo16 {font-size: 16px}
.Estilo17 {
	color: #333333
}
.Estilo19 {
	color: #333333;
	font-weight: bold;
}



-->
</style>
</head>

<body onload = "document.forms[0].elements[0].focus();">

<div align="center">
  <table width="68%" clas="table">
    <tr>
      <th width="30%" scope="row"><span class="Estilo14">La Pampa  </span></th>
       <td width="30%"><div align="right" class="Estilo2 Estilo16 Estilo16"><a href="vermesasfaltantes.php"><strong>Ver Mesas Faltantes</strong></a></div></td>
      <td width="30%"><div align="center" class="Estilo2 Estilo16 Estilo16"><a href="<?php echo $logoutAction ?>"><strong>Desconectar</strong></a></div></td>
    </tr>
    
  </table>
</div>
<div align="center"><a href="vermesasfaltantes.php"></a> </div>

 <form action="guardardatos.php" method="post" name="form1" target="_self" id="form1">
  <table width="68%" height="38" border="1" align="center">
    <tr>
      <td width="94" height="32"><div align="center" class="Estilo19"><strong>Mesa</strong></div></td>
      <td width="115"><span id="sprytextfield">
        <input name="txtMesa" type="text" value="<?php echo $Mesa; ?>" class="Estilo19" id="txtMesa" size="10"  maxlength="4" />
        
         <input type="hidden" class="form-control" id="si_viene_modifica"  name="si_viene_modifica" value="1">
      </span></td>
      <td width="530"><div id="nombre_localidad1">&nbsp;</div><?php $IP = $_SERVER["REMOTE_ADDR"]; echo $IP;?></td>
    </tr>
  </table>
  
  
 <div class="container">
        <div> <br /> </div>
                <div class="row">
          
                <div class="col-md-2" align="center">
                       <strong><span style="color:#3366CC">Nº</span></strong>       
                    </div> <!-- col-md-->
                   
                   <div class="col-md-2" align="center">
                       <strong><span style="color:#3366CC">Lista Interna</span></strong>       
                    </div> <!-- col-md-->
                   
                    <div class="col-md-2" align="center">
                       <strong><span style="color:#3366CC">Diputado</span></strong>       
                    </div> <!-- col-md--> 
                    
                </div> <!-- row -->
               
             
              <div> <br /> </div>
               
     <?php
      $j=1;
	  
	  for($i=1;$i<=9;$i++){  
		  if(($i % 2)==0)
		    $color1="#EBEBEB"; 
		  else
		    $color1="#FDE6D5";
		
			$posPdte =strpos($Listas[$i]["representacion"], "Pdte");
			$posPN =strpos($Listas[$i]["representacion"], "PN");
			$posDN =strpos($Listas[$i]["representacion"], "DN");
			$posPR =strpos($Listas[$i]["representacion"], "PR");	
			
		   ?>      
            <div class="row" style="background-color:<?php echo $color1; ?>; height:50">
             
                   <div class="col-md-2">
                   
                     <div align="center" id="id_label_lista_><?php echo $i; ?>" class="Estilo19"><?php echo $Listas[$i]['lista']; ?></div>       
                    </div> <!-- col-md-->
                   
                   <div class="col-md-2">
                   
                       <div align="left" id="id_label_nombre_<?php echo $i; ?>" class="Estilo19"><?php echo  $Listas[$i]['nombre']; ?></div>     
                    </div> <!-- col-md-->
                   
                    <div class="col-md-2">
                    
                    <?php if($posDN > -1){ ?> 
                     <div id="id_txt_diputado_<?php echo $i; ?>" align="center"><span id="sprytextfield<?php echo $j; $j++; ?>">
                       <input name="txtL<?php echo $i; ?>DP" type="text" onkeypress="return tabular(event,this)" class="Estilo19" value="<?php echo $resultado[$i]["diputado"]; ?>" id="txtL<?php echo $i; ?>DP" size="10" maxlength="4" />
                     </span></div>     
                    <?php } ?> 
                      
                    </div> <!-- col-md--> 
                 
              
                </div> <!-- row -->    
           
             <div> <br /> </div>
          
          <?php } ?>
                
               <div class="row">
                  <div align="center" id="pasardatos">
    <input name="enviardatos" type="submit" class="Estilo19" id="enviardatos" value="Enviar Datos" />
  </div>   
                </div> <!-- row -->                                                    
                
           
 </div>   <!-- container -->             
   
</form>


<script type="text/javascript">


var sprytextfield = new Spry.Widget.ValidationTextField("sprytextfield", "integer", {useCharacterMasking:true, validateOn:["blur"], minValue:1, maxValue:915});
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield11 = new Spry.Widget.ValidationTextField("sprytextfield11", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield12 = new Spry.Widget.ValidationTextField("sprytextfield12", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield13 = new Spry.Widget.ValidationTextField("sprytextfield13", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield14 = new Spry.Widget.ValidationTextField("sprytextfield14", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield15 = new Spry.Widget.ValidationTextField("sprytextfield15", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield16 = new Spry.Widget.ValidationTextField("sprytextfield16", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield17 = new Spry.Widget.ValidationTextField("sprytextfield17", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield18 = new Spry.Widget.ValidationTextField("sprytextfield18", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield19 = new Spry.Widget.ValidationTextField("sprytextfield19", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield20 = new Spry.Widget.ValidationTextField("sprytextfield20", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield21 = new Spry.Widget.ValidationTextField("sprytextfield21", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield22 = new Spry.Widget.ValidationTextField("sprytextfield22", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield23 = new Spry.Widget.ValidationTextField("sprytextfield23", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield24 = new Spry.Widget.ValidationTextField("sprytextfield24", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield25 = new Spry.Widget.ValidationTextField("sprytextfield25", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield26 = new Spry.Widget.ValidationTextField("sprytextfield26", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield27 = new Spry.Widget.ValidationTextField("sprytextfield27", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield28 = new Spry.Widget.ValidationTextField("sprytextfield28", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield29 = new Spry.Widget.ValidationTextField("sprytextfield29", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield30 = new Spry.Widget.ValidationTextField("sprytextfield30", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield31 = new Spry.Widget.ValidationTextField("sprytextfield31", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield32 = new Spry.Widget.ValidationTextField("sprytextfield32", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield33 = new Spry.Widget.ValidationTextField("sprytextfield33", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield34 = new Spry.Widget.ValidationTextField("sprytextfield34", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield35 = new Spry.Widget.ValidationTextField("sprytextfield35", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield36 = new Spry.Widget.ValidationTextField("sprytextfield36", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});

var sprytextfield37 = new Spry.Widget.ValidationTextField("sprytextfield37", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield38 = new Spry.Widget.ValidationTextField("sprytextfield38", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield39 = new Spry.Widget.ValidationTextField("sprytextfield39", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});

var sprytextfield40 = new Spry.Widget.ValidationTextField("sprytextfield40", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield41 = new Spry.Widget.ValidationTextField("sprytextfield41", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield42 = new Spry.Widget.ValidationTextField("sprytextfield42", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield43 = new Spry.Widget.ValidationTextField("sprytextfield43", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield44 = new Spry.Widget.ValidationTextField("sprytextfield44", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield45 = new Spry.Widget.ValidationTextField("sprytextfield45", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield46 = new Spry.Widget.ValidationTextField("sprytextfield46", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield47 = new Spry.Widget.ValidationTextField("sprytextfield47", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield48 = new Spry.Widget.ValidationTextField("sprytextfield48", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield49 = new Spry.Widget.ValidationTextField("sprytextfield49", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield50 = new Spry.Widget.ValidationTextField("sprytextfield50", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield51 = new Spry.Widget.ValidationTextField("sprytextfield51", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield52 = new Spry.Widget.ValidationTextField("sprytextfield52", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield53 = new Spry.Widget.ValidationTextField("sprytextfield53", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield54 = new Spry.Widget.ValidationTextField("sprytextfield54", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield55 = new Spry.Widget.ValidationTextField("sprytextfield55", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield56 = new Spry.Widget.ValidationTextField("sprytextfield56", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield57 = new Spry.Widget.ValidationTextField("sprytextfield57", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield58 = new Spry.Widget.ValidationTextField("sprytextfield58", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield59 = new Spry.Widget.ValidationTextField("sprytextfield59", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield60 = new Spry.Widget.ValidationTextField("sprytextfield60", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield61 = new Spry.Widget.ValidationTextField("sprytextfield61", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield62 = new Spry.Widget.ValidationTextField("sprytextfield62", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield63 = new Spry.Widget.ValidationTextField("sprytextfield63", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield64 = new Spry.Widget.ValidationTextField("sprytextfield64", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield65 = new Spry.Widget.ValidationTextField("sprytextfield65", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield66 = new Spry.Widget.ValidationTextField("sprytextfield66", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield67 = new Spry.Widget.ValidationTextField("sprytextfield67", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield68 = new Spry.Widget.ValidationTextField("sprytextfield68", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield69 = new Spry.Widget.ValidationTextField("sprytextfield69", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield70 = new Spry.Widget.ValidationTextField("sprytextfield70", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield71 = new Spry.Widget.ValidationTextField("sprytextfield71", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield72 = new Spry.Widget.ValidationTextField("sprytextfield72", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield73 = new Spry.Widget.ValidationTextField("sprytextfield73", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield74 = new Spry.Widget.ValidationTextField("sprytextfield74", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield75 = new Spry.Widget.ValidationTextField("sprytextfield75", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield76 = new Spry.Widget.ValidationTextField("sprytextfield76", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield77 = new Spry.Widget.ValidationTextField("sprytextfield77", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield78 = new Spry.Widget.ValidationTextField("sprytextfield78", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield79 = new Spry.Widget.ValidationTextField("sprytextfield79", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield80 = new Spry.Widget.ValidationTextField("sprytextfield80", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield81 = new Spry.Widget.ValidationTextField("sprytextfield81", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield82 = new Spry.Widget.ValidationTextField("sprytextfield82", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield83 = new Spry.Widget.ValidationTextField("sprytextfield83", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield84 = new Spry.Widget.ValidationTextField("sprytextfield84", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield85 = new Spry.Widget.ValidationTextField("sprytextfield85", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield86 = new Spry.Widget.ValidationTextField("sprytextfield86", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield87 = new Spry.Widget.ValidationTextField("sprytextfield87", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield88 = new Spry.Widget.ValidationTextField("sprytextfield88", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield89 = new Spry.Widget.ValidationTextField("sprytextfield89", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield90 = new Spry.Widget.ValidationTextField("sprytextfield90", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield91 = new Spry.Widget.ValidationTextField("sprytextfield91", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield92 = new Spry.Widget.ValidationTextField("sprytextfield92", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield93 = new Spry.Widget.ValidationTextField("sprytextfield93", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield94 = new Spry.Widget.ValidationTextField("sprytextfield94", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield95 = new Spry.Widget.ValidationTextField("sprytextfield95", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield96 = new Spry.Widget.ValidationTextField("sprytextfield96", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield97 = new Spry.Widget.ValidationTextField("sprytextfield97", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield98 = new Spry.Widget.ValidationTextField("sprytextfield98", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield99 = new Spry.Widget.ValidationTextField("sprytextfield99", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield100 = new Spry.Widget.ValidationTextField("sprytextfield100", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield101 = new Spry.Widget.ValidationTextField("sprytextfield101", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield102 = new Spry.Widget.ValidationTextField("sprytextfield102", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield103 = new Spry.Widget.ValidationTextField("sprytextfield103", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield104 = new Spry.Widget.ValidationTextField("sprytextfield104", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield105 = new Spry.Widget.ValidationTextField("sprytextfield105", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield106 = new Spry.Widget.ValidationTextField("sprytextfield106", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield107 = new Spry.Widget.ValidationTextField("sprytextfield107", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield108 = new Spry.Widget.ValidationTextField("sprytextfield108", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield109 = new Spry.Widget.ValidationTextField("sprytextfield109", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield110 = new Spry.Widget.ValidationTextField("sprytextfield110", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield111 = new Spry.Widget.ValidationTextField("sprytextfield111", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield112 = new Spry.Widget.ValidationTextField("sprytextfield112", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield113 = new Spry.Widget.ValidationTextField("sprytextfield113", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield114 = new Spry.Widget.ValidationTextField("sprytextfield114", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield115 = new Spry.Widget.ValidationTextField("sprytextfield115", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});
var sprytextfield116 = new Spry.Widget.ValidationTextField("sprytextfield116", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:350, minValue:0});


//-->
</script>
</body>
</html>
