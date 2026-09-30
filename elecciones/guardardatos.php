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

include('Connections/conexionUsuarios.php'); 


$Mesa = mysqli_real_escape_string($con,(strip_tags($_POST['txtMesa'], ENT_QUOTES)));
$Mesa=intval($Mesa); 

$Cadenasql= "select entidades.Establecimiento,mesas.Escrutada, localidades.id_loc_padron,localidades.localidad, localidades.cargo_elecciones from mesas, entidades, localidades WHERE  mesas.CodigoEscuela=entidades.Id and mesas.CodigoLocalidad = localidades.id_loc_padron AND Mesa='".$Mesa."'";

 $sql1=mysqli_query($con,$Cadenasql);
 $rs_consulta1=mysqli_fetch_array($sql1);
   
 $cantidadRegistros = mysqli_num_rows($sql1);
 $Escrutada =$rs_consulta1['Escrutada']; 
 

 if (isset($_POST["txtL1I"]))
  $L1I = $_POST["txtL1I"];
 else
  $L1I = 0;

 if (isset($_POST["txtL1G"]))
  $L1G = $_POST["txtL1G"];
 else
  $L1G = 0;
  
 if (isset($_POST["txtL1DP"]))
  $L1DP = $_POST["txtL1DP"];
 else
  $L1DP = 0;
  
 if (isset($_POST["txtL1S"]))
  $L1S = $_POST["txtL1S"];
 else
  $L1S = 0;
   

 if (isset($_POST["txtL2I"]))
  $L2I = $_POST["txtL2I"];
 else
  $L2I = 0;

 if (isset($_POST["txtL2G"]))
  $L2G = $_POST["txtL2G"];
 else
  $L2G = 0;
  
 if (isset($_POST["txtL2DP"]))
  $L2DP = $_POST["txtL2DP"];
 else
  $L2DP = 0;
  
 if (isset($_POST["txtL2S"]))
  $L2S = $_POST["txtL2S"];
 else
  $L2S = 0;  

 if (isset($_POST["txtL3I"]))
  $L3I = $_POST["txtL3I"];
 else
  $L3I = 0;
 
 if (isset($_POST["txtL3G"]))
  $L3G = $_POST["txtL3G"];
 else
  $L3G = 0;
 
 if (isset($_POST["txtL3DP"]))
  $L3DP = $_POST["txtL3DP"];
 else
  $L3DP = 0;
  
 if (isset($_POST["txtL3S"]))
  $L3S = $_POST["txtL3S"];
 else
  $L3S = 0;  

if (isset($_POST["txtL4I"]))
  $L4I = $_POST["txtL4I"];
 else
  $L4I = 0;   
 
 if (isset($_POST["txtL4G"]))
  $L4G = $_POST["txtL4G"];
 else
  $L4G = 0; 
 
 if (isset($_POST["txtL4DP"]))
  $L4DP = $_POST["txtL4DP"];
 else
  $L4DP = 0; 
  
 if (isset($_POST["txtL4S"]))
  $L4S = $_POST["txtL4S"];
 else
  $L4S = 0;    

 if (isset($_POST["txtL5I"]))
  $L5I = $_POST["txtL5I"];
 else
  $L5I = 0;   
  
 if (isset($_POST["txtL5G"]))
  $L5G = $_POST["txtL5G"];
 else
  $L5G = 0;  
 
 if (isset($_POST["txtL5DP"]))
  $L5DP = $_POST["txtL5DP"];
 else
  $L5DP = 0; 
 
 if (isset($_POST["txtL5S"]))
  $L5S = $_POST["txtL5S"];
 else
  $L5S = 0;    

 if (isset($_POST["txtL6I"]))
  $L6I = $_POST["txtL6I"];
 else
  $L6I = 0; 
  
 if (isset($_POST["txtL6G"]))
  $L6G = $_POST["txtL6G"];
 else
  $L6G = 0; 
 
 if (isset($_POST["txtL6DP"]))
  $L6DP = $_POST["txtL6DP"];
 else
  $L6DP = 0; 
  
 if (isset($_POST["txtL6S"]))
  $L6S = $_POST["txtL6S"];
 else
  $L6S = 0;   
   
 if (isset($_POST["txtL7I"]))
  $L7I = $_POST["txtL7I"];
 else
  $L7I = 0;
    
 if (isset($_POST["txtL7G"]))
  $L7G = $_POST["txtL7G"];
 else
  $L7G = 0;
 
 if (isset($_POST["txtL7DP"]))
  $L7DP = $_POST["txtL7DP"];
 else
  $L7DP = 0;
  
 if (isset($_POST["txtL7S"]))
  $L7S = $_POST["txtL7S"];
 else
  $L7S = 0;  
  
 
 if (isset($_POST["txtL8I"]))
  $L8I = $_POST["txtL8I"];
 else
  $L8I = 0; 
 
 if (isset($_POST["txtL8G"]))
  $L8G = $_POST["txtL8G"];
 else
  $L8G = 0;
  
 if (isset($_POST["txtL8DP"]))
  $L8DP = $_POST["txtL8DP"];
 else
  $L8DP = 0; 
 
 if (isset($_POST["txtL8S"]))
  $L8S = $_POST["txtL8S"];
 else
  $L8S = 0;    
 
 if (isset($_POST["txtL9I"]))
  $L9I = $_POST["txtL9I"];
 else
  $L9I = 0; 
 
 if (isset($_POST["txtL9G"]))
  $L9G = $_POST["txtL9G"];
 else
  $L9G = 0;  
 
 if (isset($_POST["txtL9DP"]))
  $L9DP = $_POST["txtL9DP"];
 else
  $L9DP = 0; 
 
 if (isset($_POST["txtL9S"]))
  $L9S = $_POST["txtL9S"];
 else
  $L9S = 0;   
 
 if (isset($_POST["txtL10I"]))
  $L10I = $_POST["txtL10I"];
 else
  $L10I = 0; 
 
 if (isset($_POST["txtL10G"]))
  $L10G = $_POST["txtL10G"];
 else
  $L10G = 0; 
  
 if (isset($_POST["txtL10DP"]))
  $L10DP = $_POST["txtL10DP"];
 else
 $L10DP = 0;
 
 if (isset($_POST["txtL10S"]))
  $L10S = $_POST["txtL10S"];
 else
  $L10S = 0;

 //
 
 if (isset($_POST["txtL11I"]))
  $L11I = $_POST["txtL11I"];
 else
  $L11I = 0;

 if (isset($_POST["txtL11G"]))
  $L11G = $_POST["txtL11G"];
 else
  $L11G = 0;
  
 if (isset($_POST["txtL11DP"]))
  $L11DP = $_POST["txtL11DP"];
 else
  $L11DP = 0;
  
 if (isset($_POST["txtL11S"]))
  $L11S = $_POST["txtL11S"];
 else
  $L11S = 0;
   

 if (isset($_POST["txtL12I"]))
  $L12I = $_POST["txtL12I"];
 else
  $L12I = 0;

 if (isset($_POST["txtL12G"]))
  $L12G = $_POST["txtL12G"];
 else
  $L12G = 0;
  
 if (isset($_POST["txtL12DP"]))
  $L12DP = $_POST["txtL12DP"];
 else
  $L12DP = 0;
  
 if (isset($_POST["txtL12S"]))
  $L12S = $_POST["txtL12S"];
 else
  $L12S = 0;  

 if (isset($_POST["txtL13I"]))
  $L13I = $_POST["txtL13I"];
 else
  $L13I = 0;
 
 if (isset($_POST["txtL13G"]))
  $L13G = $_POST["txtL13G"];
 else
  $L13G = 0;
 
 if (isset($_POST["txtL13DP"]))
  $L13DP = $_POST["txtL13DP"];
 else
  $L13DP = 0;
  
 if (isset($_POST["txtL13S"]))
  $L13S = $_POST["txtL13S"];
 else
  $L13S = 0;  

if (isset($_POST["txtL14I"]))
  $L14I = $_POST["txtL14I"];
 else
  $L14I = 0;   
 
 if (isset($_POST["txtL14G"]))
  $L14G = $_POST["txtL14G"];
 else
  $L14G = 0; 
 
 if (isset($_POST["txtL14DP"]))
  $L14DP = $_POST["txtL14DP"];
 else
  $L14DP = 0; 
  
 if (isset($_POST["txtL14S"]))
  $L14S = $_POST["txtL14S"];
 else
  $L14S = 0;    

 if (isset($_POST["txtL15I"]))
  $L15I = $_POST["txtL15I"];
 else
  $L15I = 0;   
  
 if (isset($_POST["txtL15G"]))
  $L15G = $_POST["txtL15G"];
 else
  $L15G = 0;  
 
 if (isset($_POST["txtL15DP"]))
  $L15DP = $_POST["txtL15DP"];
 else
  $L15DP = 0; 
 
 if (isset($_POST["txtL15S"]))
  $L15S = $_POST["txtL15S"];
 else
  $L15S = 0;    

 if (isset($_POST["txtL16I"]))
  $L16I = $_POST["txtL16I"];
 else
  $L16I = 0; 
  
 if (isset($_POST["txtL16G"]))
  $L16G = $_POST["txtL16G"];
 else
  $L16G = 0; 
 
 if (isset($_POST["txtL16DP"]))
  $L16DP = $_POST["txtL16DP"];
 else
  $L16DP = 0; 
  
 if (isset($_POST["txtL16S"]))
  $L16S = $_POST["txtL16S"];
 else
  $L16S = 0;   
   
 if (isset($_POST["txtL17I"]))
  $L17I = $_POST["txtL17I"];
 else
  $L17I = 0;
    
 if (isset($_POST["txtL17G"]))
  $L17G = $_POST["txtL17G"];
 else
  $L17G = 0;
 
 if (isset($_POST["txtL17DP"]))
  $L17DP = $_POST["txtL17DP"];
 else
  $L17DP = 0;
  
 if (isset($_POST["txtL17S"]))
  $L17S = $_POST["txtL17S"];
 else
  $L17S = 0;  
  
 
 if (isset($_POST["txtL18I"]))
  $L18I = $_POST["txtL18I"];
 else
  $L18I = 0; 
 
 if (isset($_POST["txtL18G"]))
  $L18G = $_POST["txtL18G"];
 else
  $L18G = 0;
  
 if (isset($_POST["txtL18DP"]))
  $L18DP = $_POST["txtL18DP"];
 else
  $L18DP = 0; 
 
 if (isset($_POST["txtL18S"]))
  $L18S = $_POST["txtL18S"];
 else
  $L18S = 0;    
 
 if (isset($_POST["txtL19I"]))
  $L19I = $_POST["txtL19I"];
 else
  $L19I = 0; 
 
 if (isset($_POST["txtL19G"]))
  $L19G = $_POST["txtL19G"];
 else
  $L19G = 0;  
 
 if (isset($_POST["txtL19DP"]))
  $L19DP = $_POST["txtL19DP"];
 else
  $L19DP = 0; 
 
 if (isset($_POST["txtL19S"]))
  $L19S = $_POST["txtL19S"];
 else
  $L19S = 0;   
 
 if (isset($_POST["txtL20I"]))
  $L20I = $_POST["txtL20I"];
 else
  $L20I = 0; 
 
 if (isset($_POST["txtL20G"]))
  $L20G = $_POST["txtL20G"];
 else
  $L20G = 0; 
  
 if (isset($_POST["txtL20DP"]))
  $L20DP = $_POST["txtL20DP"];
 else
 $L20DP = 0;
 
 if (isset($_POST["txtL20S"]))
  $L20S = $_POST["txtL20S"];
 else
  $L20S = 0;
 
 //   

if (isset($_POST["txtL21I"]))
  $L21I = $_POST["txtL21I"];
 else
  $L21I = 0;

 if (isset($_POST["txtL21G"]))
  $L21G = $_POST["txtL21G"];
 else
  $L21G = 0;
  
 if (isset($_POST["txtL21DP"]))
  $L21DP = $_POST["txtL21DP"];
 else
  $L21DP = 0;
  
 if (isset($_POST["txtL21S"]))
  $L21S = $_POST["txtL21S"];
 else
  $L21S = 0;
   

 if (isset($_POST["txtL22I"]))
  $L22I = $_POST["txtL22I"];
 else
  $L22I = 0;

 if (isset($_POST["txtL22G"]))
  $L22G = $_POST["txtL22G"];
 else
  $L22G = 0;
  
 if (isset($_POST["txtL22DP"]))
  $L22DP = $_POST["txtL22DP"];
 else
  $L22DP = 0;
  
 if (isset($_POST["txtL22S"]))
  $L22S = $_POST["txtL22S"];
 else
  $L22S = 0;  

 if (isset($_POST["txtL23I"]))
  $L23I = $_POST["txtL23I"];
 else
  $L23I = 0;
 
 if (isset($_POST["txtL23G"]))
  $L23G = $_POST["txtL23G"];
 else
  $L23G = 0;
 
 if (isset($_POST["txtL23DP"]))
  $L23DP = $_POST["txtL23DP"];
 else
  $L23DP = 0;
  
 if (isset($_POST["txtL23S"]))
  $L23S = $_POST["txtL23S"];
 else
  $L23S = 0;  

if (isset($_POST["txtL24I"]))
  $L24I = $_POST["txtL24I"];
 else
  $L24I = 0;   
 
 if (isset($_POST["txtL24G"]))
  $L24G = $_POST["txtL24G"];
 else
  $L24G = 0; 
 
 if (isset($_POST["txtL24DP"]))
  $L24DP = $_POST["txtL24DP"];
 else
  $L24DP = 0; 
  
 if (isset($_POST["txtL24S"]))
  $L24S = $_POST["txtL24S"];
 else
  $L24S = 0;    

 if (isset($_POST["txtL25I"]))
  $L25I = $_POST["txtL25I"];
 else
  $L25I = 0;   
  
 if (isset($_POST["txtL25G"]))
  $L25G = $_POST["txtL25G"];
 else
  $L25G = 0;  
 
 if (isset($_POST["txtL25DP"]))
  $L25DP = $_POST["txtL25DP"];
 else
  $L25DP = 0; 
 
 if (isset($_POST["txtL25S"]))
  $L25S = $_POST["txtL25S"];
 else
  $L25S = 0;    

 if (isset($_POST["txtL26I"]))
  $L26I = $_POST["txtL26I"];
 else
  $L26I = 0; 
  
 if (isset($_POST["txtL26G"]))
  $L26G = $_POST["txtL26G"];
 else
  $L26G = 0; 
 
 if (isset($_POST["txtL26DP"]))
  $L26DP = $_POST["txtL26DP"];
 else
  $L26DP = 0; 
  
 if (isset($_POST["txtL26S"]))
  $L26S = $_POST["txtL26S"];
 else
  $L26S = 0;   
   
 if (isset($_POST["txtL27I"]))
  $L27I = $_POST["txtL27I"];
 else
  $L27I = 0;
    
 if (isset($_POST["txtL27G"]))
  $L27G = $_POST["txtL27G"];
 else
  $L27G = 0;
 
 if (isset($_POST["txtL27DP"]))
  $L27DP = $_POST["txtL27DP"];
 else
  $L27DP = 0;
  
 if (isset($_POST["txtL27S"]))
  $L27S = $_POST["txtL27S"];
 else
  $L27S = 0;  
  
 
 if (isset($_POST["txtL28I"]))
  $L28I = $_POST["txtL28I"];
 else
  $L28I = 0; 
 
 if (isset($_POST["txtL28G"]))
  $L28G = $_POST["txtL28G"];
 else
  $L28G = 0;
  
 if (isset($_POST["txtL28DP"]))
  $L28DP = $_POST["txtL28DP"];
 else
  $L28DP = 0; 
 
 if (isset($_POST["txtL28S"]))
  $L28S = $_POST["txtL28S"];
 else
  $L28S = 0;    
 
 if (isset($_POST["txtL29I"]))
  $L29I = $_POST["txtL29I"];
 else
  $L29I = 0; 
 
 if (isset($_POST["txtL29G"]))
  $L29G = $_POST["txtL29G"];
 else
  $L29G = 0;  
 
 if (isset($_POST["txtL29DP"]))
  $L29DP = $_POST["txtL29DP"];
 else
  $L29DP = 0; 
 
 if (isset($_POST["txtL29S"]))
  $L29S = $_POST["txtL29S"];
 else
  $L29S = 0;   
	
$Usuario = "-";
$IP = $_SERVER["REMOTE_ADDR"]; 


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

$L22I =intval($L22I);
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
 

$TotalesI= $L1I + $L2I + $L3I + $L4I + $L5I + $L6I + $L7I + $L8I + $L9I + $L10I;  
$TotalesG= $L1G + $L2G + $L3G + $L4G + $L5G + $L6G + $L7G + $L8G + $L9G + $L10G;
$TotalesDP= $L1DP + $L2DP + $L3DP + $L4DP + $L5DP + $L6DP + $L7DP + $L8DP + $L9DP + $L10DP;

$Totales=$TotalesI + $TotalesG+ $TotalesDP;

$eror= 0;
$cadenaerror= "";
$modifica = "Si";


if ($cantidadRegistros > 0){
 $Establecimiento = $rs_consulta1['Establecimiento'];
 $Localidad = $rs_consulta1['localidad'];
 
 $hacer=1;
 
 if(isset($_POST['si_viene_modifica']))
  $hacer=0;
  
 if ($Escrutada =="S" and $hacer==1)
 {
 
 $sql2=mysqli_query($con,"select * from mesas WHERE Mesa='".$Mesa."'");
 $rs_consulta=mysqli_fetch_array($sql2);
 
 for($i=1;$i<=9;$i++) { 
   $campo="L". $i ."G";
   $resultado[$i]["gobernador"]=$rs_consulta[$campo];
   $campo="L". $i ."I";
   $resultado[$i]["intendente"]=$rs_consulta[$campo];
   $campo="L". $i ."DP";
   $resultado[$i]["diputado"]=$rs_consulta[$campo];
   $campo="L". $i ."S";
   $resultado[$i]["senador"]=$rs_consulta[$campo];
 }
 
    
 $IPGrabo = $rs_consulta['IP'];


 //$cadenaerror = "Ya ha sido Grabada desde IP: " . $IPGrabo;
 $eror= 1;
 
  
 if($_SESSION['MM_UserGroup'] == 1 or $_SESSION['MM_UserGroup'] == 2 or $_SESSION['MM_UserGroup'] == 4){ 
     $modifica = "Si"; }
  else  {
     $modifica = "No";
    }
  }
 
 }
else
{
 $Establecimiento = "Mesa";
 $Localidad = "No Encontrada";
 $eror= 1;
} 

if ($eror <> 1){
 if ($Totales ==0) {
  $cadenaerror = "No puede haber totales en cero!"; 
  $eror= 1;
 } 
}

$Espacio = "--";
$Cadena = $Establecimiento . $Espacio  . $Localidad;


$eror=0;
if ($TotalesI>0 || $TotalesG>0 || $TotalesDP >0){
$Escrutada="S";


$sel_actualizar1="UPDATE mesas SET L1I='".$L1I."', L1G='".$L1G."', L1DP='".$L1DP."',L1S='".$L1S."', L2I='".$L2I."', L2DP='".$L2DP."', L2G='".$L2G."',L2S='".$L2S."', L3I='".$L3I."', L3G='".$L3G."', L3DP='".$L3DP."',L3S='".$L3S."', L4I='".$L4I."', L4DP='".$L4DP."', L4S='".$L4S."',L4G='".$L4G."', L5I='".$L5I."', L5DP='".$L5DP."',L5S='".$L5S."', L5G='".$L5G."', L6I='".$L6I."', L6DP='".$L6DP."',L6S='".$L6S."', L6G='".$L6G."', L7I='".$L7I."', L7DP='".$L7DP."', L7S='".$L7S."', L7G='".$L7G."', L8I='".$L8I."', L8DP='".$L8DP."',L8S='".$L8S."', L8G='".$L8G."', L9I='".$L9I."', L9DP='".$L9DP."',L9S='".$L9S."', L9G='".$L9G."', L10I='".$L10I."', L10DP='".$L10DP."',L10S='".$L10S."', L10G='".$L10G."', L11I='".$L11I."', L11G='".$L11G."', L11DP='".$L11DP."',L11S='".$L11S."', L12I='".$L12I."', L12DP='".$L12DP."', L12G='".$L12G."',L12S='".$L12S."', L13I='".$L13I."', L13G='".$L13G."', L13DP='".$L13DP."',L13S='".$L13S."', L14I='".$L14I."', L14DP='".$L14DP."', L14S='".$L14S."',L14G='".$L14G."', L15I='".$L15I."', L15DP='".$L15DP."',L15S='".$L15S."', L15G='".$L15G."', L16I='".$L16I."', L16DP='".$L16DP."',L16S='".$L16S."', L16G='".$L16G."', L17I='".$L17I."', L17DP='".$L17DP."', L17S='".$L17S."', L17G='".$L17G."', L18I='".$L18I."', L18DP='".$L18DP."',L18S='".$L18S."', L18G='".$L18G."', L19I='".$L19I."', L19DP='".$L19DP."',L19S='".$L19S."', L19G='".$L19G."', L20I='".$L20I."', L20DP='".$L20DP."',L20S='".$L20S."', L20G='".$L20G."',  L21I='".$L21I."', L21G='".$L21G."', L21DP='".$L21DP."',L21S='".$L21S."', L22I='".$L22I."', L22DP='".$L22DP."', L22G='".$L22G."',L22S='".$L22S."', L23I='".$L23I."', L23G='".$L23G."', L23DP='".$L23DP."',L23S='".$L23S."', L24I='".$L24I."', L24DP='".$L24DP."', L24S='".$L24S."',L24G='".$L24G."', L25I='".$L25I."', L25DP='".$L25DP."',L25S='".$L25S."', L25G='".$L25G."', L26I='".$L26I."', L26DP='".$L26DP."',L26S='".$L26S."', L26G='".$L26G."', L27I='".$L27I."', L27DP='".$L27DP."', L27S='".$L27S."', L27G='".$L27G."', L28I='".$L28I."', L28DP='".$L28DP."',L28S='".$L28S."', L28G='".$L28G."', L29I='".$L29I."', L29DP='".$L29DP."',L29S='".$L29S."', L29G='".$L29G."',Escrutada='S',fecha_modificacion = NOW() WHERE Mesa='".$Mesa."'";

 //$query = mysqli_query($con,$sel_actualizar1);
 
// echo mysqli_error($con);

}

?>

<html>
<title>Datos Grabados </title>
<head>


<style type="text/css">
<!--
.Estilo5 {font-weight: bold; font-size: 18px; }
.Estilo17 {font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
.Estilo18 {font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold;}
.Estilo23 {font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; color: #0033CC; }
.Estilo24 {font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; color: #006666; }
.Estilo28 {font-weight: bold; font-size: 18px; color: #333333; }
.Estilo29 {
	color: #FF3333
}
.Estilo31 {color: #FF3300}
.Estilo32 {color: #003333}
-->
</style>



</head>

<style type="text/css">
<!--
.Estilo4 {font-size: 24}
-->
</style>


<div class="row">
 <div class="col-md-12">
  <div class="box">
  
  <table width="803" border="1" class="table" align="center" bgcolor="#E9E9E9">
    <tr>
      <td width="123" height="27px"><div align="center" class="Estilo18"><strong>Mesa N&ordm;</strong></div></td>
      <td width="93"><div align="center" class="Estilo18"><strong><?php echo $Mesa;?></strong></div></td>
      <td width="565"><div align="center" class="Estilo18"><strong><?php echo $Cadena;?></strong> </div></td>
    </tr>
  </table>

<table width="805" height="437" border="1" align="center"  bgcolor="#E9E9E9">
  <tr>
    <td width="26px" height="37px"><div align="center" class="Estilo17">N&ordm;</div></td>
     <td width="180px" height="37px"><div align="center" class="Estilo17">Lista Interna</div></td>
    <td width="115px"><div align="center" class="Estilo31">
      <div align="center" class="Estilo24 Estilo32">Dip.</div>
    </div></td>
   
  </tr>
  
  
  <?php for($i=1;$i<=9;$i++) { 
        $posPdte =strpos($Listas[$i]["representacion"], "Pdte");
		$posPN =strpos($Listas[$i]["representacion"], "PN");
		$posDN =strpos($Listas[$i]["representacion"], "DN");
		$posPR =strpos($Listas[$i]["representacion"], "PR");
						
  
  ?>
   
  <tr>
     <td height="25"><div align="left" class="Estilo18"><span><?php echo $Listas[$i]['lista']; ?></span></div></td>
     <td height="37"><div align="left" class="Estilo18"><span><?php echo $Listas[$i]['nombre']; ?></span></div></td>
     
     <td><div align="center" class="Estilo18"> <?php if($posDN > -1) echo $resultado[$i]["diputado"]; ?></div></td>
        
  </tr>
 
<?php
    
	 }
	 
 	 
 
 ?>
</table>
<table width="804" height="114" class="table" align="center">
  <tr>
  
    <td width="302" height="108"> <div align="center">
    <?php
    if($modifica <> "No"){ ?>
     <form id="formulario" name="formulario" method="post" action="modificarmesa.php">
     <input id="codmesa" name="codmesa" value="<?php echo $Mesa?>" type="hidden">
  
     <input name="button" type="submit" class="Estilo28" id="button" value="Modificar">
     </form> </div><?php } ?></td> 
  <?php //if ($TotalesInt == $TotalesGob) { // La cantidad de Votos tiene que ser igual en una columna y en otra, en este caso la suma de votos a Intendente debe ser igual a la suma de votos a Gobernador, pero esto no quierre decir qeu las filas sean iguales, porque por ejemplo eun votante emite votos para Gobernador e Intendente ?> 
    <td width="267"><div align="center" class="Estilo5"><a href="cargarmesas.php">
    <button type="button" class="btn btn-success btn-lg">Confirmar</button>
    </a></div></td>
   <?php  // }  else {
   // $sel_actualizar="UPDATE mesas SET Escrutada='N' WHERE Mesa='$Mesa'";
   // $descriptor= mysql_select_db($_SESSION['database_conexionUsuarios'], $_SESSION['conexionUsuarios']);
  //  $rs_actualizar=mysql_query($sel_actualizar); 
    ?>
    <?php // }?>
   <td width="219"> <?php //echo $cadenaerror;?></td>
</tr>
</table>
</div>
<div align="center"></div>
</div></div>
</body>
</html>

<script> 
function actualizar_cantidad_en_linea(id_div,Que_Modifica,Mesa)
 {
  	    var cantidad_actualizar=0;
		cantidad_actualizar=prompt('Ingrese cantidad de votos:','');
		if (isNaN(cantidad_actualizar))
			{
			alert('Esto no es un numero');
			return false;
		 }			
		
				
		$.ajax({
        type: "GET",
        url: "actualizar_en_linea.php",
        data:"Que_Modifica="+Que_Modifica+"&Mesa="+Mesa+"&cantidad_actualizar="+cantidad_actualizar,
		 beforeSend: function(objeto){
			//$("#loader").html("Mensaje: Cargando...");
		  },
        success: function(datos){
		$("#id_"+id_div).html(datos);
		//load(1);
		}
			});

		}
</script>