<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "4";
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
?><?php require_once('Connections/conexionUsuarios.php'); 

$N= "N";

$sel_actualizar_3="UPDATE mesas SET L1I='0',L1DP='0',L1S='0',L1G='0',L2I='0',L2DP='0',L2S='0',L2G='0',L3I='0',L3DP='0',L3S='0',L3G='0',L4I='0',L4DP='0',L4S='0',L4G='0',L5I='0',L5DP='0',L5S='0',L5G='0',L6I='0',L6DP='0',L6S='0',L6DP='0',L6G='0',L7I='0',L7DP='0',L7S='0',L7G='0',L8I='0',L8DP='0',L8S='0',L8G='0',L9I='0',L9DP='0',L9S='0',L9G='0',L10I='0',L10DP='0',L10S='0',L10G='0',   L11I='0',L11DP='0',L11S='0',L11G='0',L12I='0',L12DP='0',L12S='0',L12G='0',L13I='0',L13DP='0',L13S='0',L13G='0',L14I='0',L14DP='0',L14S='0',L14G='0',L15I='0',L15DP='0',L15S='0',L15G='0',L16I='0',L16DP='0',L16S='0',L16DP='0',L16G='0',L17I='0',L17DP='0',L17S='0',L17G='0',L18I='0',L18DP='0',L18S='0',L18G='0',L19I='0',L19DP='0',L19S='0',L19G='0',L20I='0',L20DP='0',L20S='0',L20G='0', L21I='0',L21DP='0',L21S='0',L21G='0',L22I='0',L22DP='0',L22S='0',L22G='0',L23I='0',L23DP='0',L23S='0',L23G='0',L24I='0',L24DP='0',L24S='0',L24G='0',L25I='0',L25DP='0',L25S='0',L25G='0',L26I='0',L26DP='0',L26S='0',L26DP='0',L26G='0',L27I='0',L27DP='0',L27S='0',L27G='0',L28I='0',L28DP='0',L28S='0',L28G='0',L29I='0',L29DP='0',L29S='0',L29G='0',Escrutada='$N' WHERE 1=1";


 $query = mysqli_query($con,$sel_actualizar_3);


 ?>