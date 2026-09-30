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
  
 $Partido[1]["nombre"]="MOV. AL SOCIALISMO"; //
 $Partido[2]["nombre"]="CENTRO DEMOCR&Aacute;TICO"; 
 $Partido[3]["nombre"]="MOV. LIBRE DEL SUR"; // Tierno - Winschel
 $Partido[4]["nombre"]="MOV. DE ACCI&Oacute;N VECINAL"; 
 $Partido[5]["nombre"]="MOV. IZQ. JUVENTUD DIGNIDAD"; //Gpnzalez Cabiati - Fernandez
 $Partido[6]["nombre"]="POL&Iacute;TICA OBRERA"; // Acosta - Gonzalez
 $Partido[7]["nombre"]="PROYECETO JOVEN"; // Fazzini - Wisner
 $Partido[8]["nombre"]="PATRIOTA FEDERAL";
 $Partido[9]["nombre"]="FRENTE LIBERAR";
 $Partido[10]["nombre"]="JUNTOS POR EL CAMBIO";
 $Partido[11]["nombre"]="HACEMOS POR NUESTRO PA&Iacute;S";
 $Partido[12]["nombre"]="UNI&Oacute;N POR LA PATRIA";
 $Partido[13]["nombre"]="LA LIBERTAD AVANZA";
 $Partido[14]["nombre"]="FRENTE DE IZQ. Y DE TRABAJADORES";
 $Partido[15]["nombre"]="PRINCIPIOS Y VALORES";
   
 $Partido[1]["numero"]="13"; 
 $Partido[2]["numero"]="20"; 
 $Partido[3]["numero"]="40"; 
 $Partido[4]["numero"]="57"; 
 $Partido[5]["numero"]="90"; 
 $Partido[6]["numero"]="92"; 
 $Partido[7]["numero"]="94"; 
 $Partido[8]["numero"]="95";
 $Partido[9]["numero"]="131r";
 $Partido[10]["numero"]="132/501";
 $Partido[11]["numero"]="133";
 $Partido[12]["numero"]="134/502";
 $Partido[13]["numero"]="135";
 $Partido[14]["numero"]="136/503";
 $Partido[15]["numero"]="137";
  
 $Partido[1]["cargos"]="P,DN";
 $Partido[2]["cargos"]="P,DN";
 $Partido[3]["cargos"]="P,DN";
 $Partido[4]["cargos"]="P,DN";
 $Partido[5]["cargos"]="P,DN";
 $Partido[6]["cargos"]="P,DN";
 $Partido[7]["cargos"]="P,DN";
 $Partido[8]["cargos"]="P,DN";
 $Partido[9]["cargos"]="P,DN";
 $Partido[10]["cargos"]="P,DN";
 $Partido[11]["cargos"]="P,DN";
 $Partido[12]["cargos"]="P,DN";
 $Partido[13]["cargos"]="P,DN";
 $Partido[14]["cargos"]="P,DN";
 $Partido[15]["cargos"]="P,DN";
	  
 $Partido[1]["imagen"]= "1.png"; // 
 $Partido[2]["imagen"]= "2.png"; // 
 $Partido[3]["imagen"]= "3.png"; // 
 $Partido[4]["imagen"]= "4.png"; // 
 $Partido[5]["imagen"]= "5.png"; // 
 $Partido[6]["imagen"]= "6.png"; // 
 $Partido[7]["imagen"]= "7.png"; // 
 $Partido[8]["imagen"]= "8.png"; // 
 $Partido[9]["imagen"]= "9.png"; // 
 $Partido[10]["imagen"]= "10.png"; // 
 $Partido[11]["imagen"]= "10.png"; // 
 $Partido[12]["imagen"]= "10.png"; // 
 $Partido[13]["imagen"]= "10.png"; // 
 $Partido[14]["imagen"]= "10.png"; // 
 $Partido[15]["imagen"]= "10.png"; // 
 
  
 $Partido[1]["color"]= "#F92231";   // Rojo
 $Partido[2]["color"]= "#0172D0";  // Celeste
 $Partido[3]["color"]= "#AF3530"; // Bordo
 $Partido[4]["color"]= "#70AB37"; // verde
 $Partido[5]["color"]= "#FF00CC"; // Fluor
 $Partido[6]["color"]= "#FF6699";  // rosa
 $Partido[7]["color"]= "#6666CC"; // violeta apagado
 $Partido[8]["color"]= "#6633FF"; // violeta claro
 $Partido[9]["color"]= "#006600"; // verde oscuro
 $Partido[10]["color"]= "#FF6600"; // Naranja JXC
 $Partido[11]["color"]= "#001A6E"; // Azul Oscuro
 $Partido[12]["color"]= "#0066CC"; // Azul - PJ
 $Partido[13]["color"]= "#CC0099"; // Violeta - Milei
 $Partido[14]["color"]= "#CD5056";  // Rosa fuerte
 $Partido[15]["color"]= "#FF0000"; // Rojp
 
 $Lista_Interna[1]["nombre"]="IZQUIERDA ANTICAPITALISTA";
 $Lista_Interna[1]["color"]=  $Partido[1]["color"]; 
 $Lista_Interna[2]["nombre"]="APERTURA LIBERAL ARGENTINA";
 $Lista_Interna[2]["color"]=  $Partido[2]["color"];  
 $Lista_Interna[3]["nombre"]="AZUL Y ROJO"; 
 $Lista_Interna[3]["color"]=  $Partido[3]["color"]; 
 $Lista_Interna[4]["nombre"]="COMPROMISO FEDERAL";
 $Lista_Interna[4]["color"]=  $Partido[4]["color"];
   
 $Lista_Interna[5]["nombre"]="DIGNIDAD";
 $Lista_Interna[5]["color"]=  $Partido[5]["color"];   
 $Lista_Interna[6]["nombre"]="CONFEDERAL"; 
 $Lista_Interna[6]["color"]=  $Partido[5]["color"]; 
 
 $Lista_Interna[7]["nombre"]="UNIDAD OBRERA";
 $Lista_Interna[7]["color"]=  $Partido[6]["color"]; 
 $Lista_Interna[8]["nombre"]="COALICION PAZ DEMOCRACIA SOBERANIA";
 $Lista_Interna[8]["color"]=  $Partido[7]["color"]; 
 $Lista_Interna[9]["nombre"]="PATRIA UNIDA";
 $Lista_Interna[9]["color"]=  $Partido[7]["color"]; 
 $Lista_Interna[10]["nombre"]="TODEX";
 $Lista_Interna[10]["color"]=  $Partido[7]["color"]; 
 $Lista_Interna[11]["nombre"]="NUEVA DEMOCRACIA";
 $Lista_Interna[11]["color"]=  $Partido[7]["color"]; 
 $Lista_Interna[12]["nombre"]="PRIMERO LA PATRIA";
 $Lista_Interna[12]["color"]=  $Partido[8]["color"]; 
 $Lista_Interna[13]["nombre"]="DEMOS";
 $Lista_Interna[13]["color"]=  $Partido[9]["color"]; 
 $Lista_Interna[14]["nombre"]="RECONQUISTA";
 $Lista_Interna[14]["color"]=  $Partido[9]["color"]; 
 $Lista_Interna[15]["nombre"]="ANTICORRUPCI&Oacute;N";
 $Lista_Interna[15]["color"]=  $Partido[9]["color"]; 
 $Lista_Interna[16]["nombre"]="LARRETA / TORROBA";
 $Lista_Interna[16]["color"]=  $Partido[10]["color"]; 
 $Lista_Interna[17]["nombre"]="BULLRICH / ARDOHAIN";
 $Lista_Interna[17]["color"]=  $Partido[10]["color"]; 
 $Lista_Interna[18]["nombre"]="HACEMOS";
 $Lista_Interna[18]["color"]=  $Partido[11]["color"]; 
 $Lista_Interna[19]["nombre"]="MASSA"; 
 $Lista_Interna[19]["color"]=  $Partido[12]["color"]; 
 $Lista_Interna[20]["nombre"]="GRABOIS";
 $Lista_Interna[20]["color"]=  $Partido[12]["color"];   
 $Lista_Interna[21]["nombre"]="RAUSCHENBERGER";   
 $Lista_Interna[21]["color"]=  $Partido[12]["color"];
 $Lista_Interna[22]["nombre"]="LIBERTAD POR SIEMPRE"; 
 $Lista_Interna[22]["color"]=  $Partido[13]["color"];  
 $Lista_Interna[23]["nombre"]="UNIR Y FORTALECER LA IZQUIERDA"; 
 $Lista_Interna[23]["color"]=  $Partido[14]["color"];   
 $Lista_Interna[24]["nombre"]="UNIDAD DE LUCHADORES Y LA IZQUIERDA";   
 $Lista_Interna[24]["color"]=  $Partido[14]["color"];   
 $Lista_Interna[25]["nombre"]="TIERRA, TECHO Y TRABAJO"; 
 $Lista_Interna[25]["color"]=  $Partido[15]["color"];   
 $Lista_Interna[26]["nombre"]="TRANSFORMAR";   
 $Lista_Interna[26]["color"]=  $Partido[15]["color"];   
 $Lista_Interna[27]["nombre"]="TRES BANDERAS";   
 $Lista_Interna[27]["color"]=  $Partido[15]["color"];   
 $Lista_Interna[28]["nombre"]="GENTE DE TRABAJO";   
 $Lista_Interna[28]["color"]=  $Partido[15]["color"];   
 $Lista_Interna[29]["nombre"]="LABORISTA";
 $Lista_Interna[29]["color"]=  $Partido[15]["color"];   

 
 $Lista_Interna[1]["lista"]="A"; 
 $Lista_Interna[2]["lista"]="A"; 
 $Lista_Interna[3]["lista"]="A"; 
 $Lista_Interna[4]["lista"]="A"; 
 $Lista_Interna[5]["lista"]="A"; 
 $Lista_Interna[6]["lista"]="B"; 
 $Lista_Interna[7]["lista"]="A"; 
 $Lista_Interna[8]["lista"]="A";
 $Lista_Interna[9]["lista"]="B";
 $Lista_Interna[10]["lista"]="C";
 $Lista_Interna[11]["lista"]="D";
 $Lista_Interna[12]["lista"]="A";
 $Lista_Interna[13]["lista"]="A";
 $Lista_Interna[14]["lista"]="B";
 $Lista_Interna[15]["lista"]="C";
 $Lista_Interna[16]["lista"]="A";
 $Lista_Interna[17]["lista"]="B";
 $Lista_Interna[18]["lista"]="A";
 $Lista_Interna[19]["lista"]="A";   
 $Lista_Interna[20]["lista"]="B";   
 $Lista_Interna[21]["lista"]="C";   
 $Lista_Interna[22]["lista"]="A";   
 $Lista_Interna[23]["lista"]="A";   
 $Lista_Interna[24]["lista"]="B";   
 $Lista_Interna[25]["lista"]="1A";   
 $Lista_Interna[26]["lista"]="2B";   
 $Lista_Interna[27]["lista"]="3C";   
 $Lista_Interna[28]["lista"]="4D";   
 $Lista_Interna[29]["lista"]="5E";      
 
 $Partido[1]['interna']=0;
 $Partido[2]['interna']=0;
 $Partido[3]['interna']=0;
 $Partido[4]['interna']=0;
 $Partido[5]['interna']=1;
 $Partido[6]['interna']=0;
 $Partido[7]['interna']=2;
 $Partido[8]['interna']=0;
 $Partido[9]['interna']=3;
 $Partido[10]['interna']=4;
 $Partido[11]['interna']=0;
 $Partido[12]['interna']=5;
 $Partido[13]['interna']=0;
 $Partido[14]['interna']=6;
 $Partido[15]['interna']=7;
 
 
    
 
//"#E95B0F" = Naranja; "#FF0000" = Rojo; "#FFCC00"=  Amarillo; "#005693" = Celeste; "#11285C"=azul

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

	$sel_consulta="select count(*) as cantidadmesas, sum(L1I) as SumaL1I, sum(L1DP) as SumaL1DP, sum(L1G) as SumaL1G,sum(L2I) as SumaL2I, sum(L2DP) as SumaL2DP, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3DP) as SumaL3DP, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4DP) as SumaL4DP, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5DP) as SumaL5DP, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6DP) as SumaL6DP, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7DP) as SumaL7DP, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8DP) as SumaL8DP, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9DP) as SumaL9DP, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10DP) as SumaL10DP, sum(L10G) as SumaL10G, sum(L11DP) as SumaL11DP, sum(L11G) as SumaL11G, sum(L12DP) as SumaL12DP, sum(L12G) as SumaL12G, sum(L13DP) as SumaL13DP, sum(L13G) as SumaL13G, sum(L14DP) as SumaL14DP, sum(L14G) as SumaL14G, sum(L15DP) as SumaL15DP, sum(L15G) as SumaL15G, sum(L16DP) as SumaL16DP, sum(L16G) as SumaL16G, sum(L17DP) as SumaL17DP, sum(L17G) as SumaL17G, sum(L18DP) as SumaL18DP, sum(L18G) as SumaL18G, sum(L19DP) as SumaL19DP, sum(L19G) as SumaL19G, sum(L20DP) as SumaL20DP, sum(L20G) as SumaL20G, sum(L21DP) as SumaL21DP, sum(L21G) as SumaL21G, sum(L22DP) as SumaL22DP, sum(L22G) as SumaL22G, sum(L23DP) as SumaL23DP, sum(L23G) as SumaL23G, sum(L24DP) as SumaL24DP, sum(L24G) as SumaL24G, sum(L25DP) as SumaL25DP, sum(L25G) as SumaL25G, sum(L26DP) as SumaL26DP, sum(L26G) as SumaL26G, sum(L27DP) as SumaL27DP, sum(L27G) as SumaL27G, sum(L28DP) as SumaL28DP, sum(L28G) as SumaL28G, sum(L29DP) as SumaL29DP, sum(L29G) as SumaL29G from mesas WHERE Escrutada='S'";

 $sql=mysqli_query($con,$sel_consulta);
 $rw=mysqli_fetch_array($sql);

 $CantidadMesasEscrutadas=0;
 $CantidadMesasEscrutadas = $rw['cantidadmesas'];
 
 for ($i=1;$i<=29;$i++){
  $Lista_Interna[$i]["suma_presidente"]=0;
  $Lista_Interna[$i]["suma_diputado"]=0;
  $Lista_Interna[$i]["porcentaje_presidente"]=0;
  $Lista_Interna[$i]["porcentaje_diputado"]=0;
 }

 if ($CantidadMesasEscrutadas > 0)
 {
 $PorcentajeMesasEscrutadas = (($CantidadMesasEscrutadas * 100) / 902);

 $SumaL1DP=$rw['SumaL1DP'];
 $SumaL1G=$rw['SumaL1G'];
 
 $Lista_Interna[1]["suma_diputado"]= $SumaL1DP;
 $Lista_Interna[1]["suma_presidente"]= $SumaL1G;

 $SumaL2DP=$rw['SumaL2DP'];
 $SumaL2G=$rw['SumaL2G'];
 $Lista_Interna[2]["suma_diputado"]= $SumaL2DP;
 $Lista_Interna[2]["suma_presidente"]= $SumaL2G;

 $SumaL3DP=$rw['SumaL3DP'];
 $SumaL3G=$rw['SumaL3G'];
 $Lista_Interna[3]["suma_diputado"]= $SumaL3DP;
 $Lista_Interna[3]["suma_presidente"]= $SumaL3G;
 
 $SumaL4DP=$rw['SumaL4DP'];
 $SumaL4G=$rw['SumaL4G'];
 $Lista_Interna[4]["suma_diputado"]= $SumaL4DP;
 $Lista_Interna[4]["suma_presidente"]= $SumaL4G;
 
 $SumaL5DP=$rw['SumaL5DP'];
 $SumaL5G=$rw['SumaL5G'];
 $Lista_Interna[5]["suma_diputado"]= $SumaL5DP;
 $Lista_Interna[5]["suma_presidente"]= $SumaL5G;
 
 $SumaL6DP=$rw['SumaL6DP'];
 $SumaL6G=$rw['SumaL6G'];
 $Lista_Interna[6]["suma_diputado"]= $SumaL6DP;
 $Lista_Interna[6]["suma_presidente"]= $SumaL6G;
 
 $SumaL7DP=$rw['SumaL7DP'];
 $SumaL7G=$rw['SumaL7G'];
 $Lista_Interna[7]["suma_diputado"]= $SumaL7DP;
 $Lista_Interna[7]["suma_presidente"]= $SumaL7G;
 
 $SumaL8DP=$rw['SumaL8DP'];
 $SumaL8G=$rw['SumaL8G'];
 $Lista_Interna[8]["suma_diputado"]= $SumaL8DP;
 $Lista_Interna[8]["suma_presidente"]= $SumaL8G;
 
 $SumaL9DP=$rw['SumaL9DP'];
 $SumaL9G=$rw['SumaL9G'];
 $Lista_Interna[9]["suma_diputado"]= $SumaL9DP;
 $Lista_Interna[9]["suma_presidente"]= $SumaL9G;
 
 $SumaL10DP=$rw['SumaL10DP'];
 $SumaL10G=$rw['SumaL10G'];
 $Lista_Interna[10]["suma_diputado"]= $SumaL10DP;
 $Lista_Interna[10]["suma_presidente"]= $SumaL10G;
 
 $SumaL11DP=$rw['SumaL11DP'];
 $SumaL11G=$rw['SumaL11G'];
 $Lista_Interna[11]["suma_diputado"]= $SumaL11DP;
 $Lista_Interna[11]["suma_presidente"]= $SumaL11G;
 
 $SumaL12DP=$rw['SumaL12DP'];
 $SumaL12G=$rw['SumaL12G'];
 $Lista_Interna[12]["suma_diputado"]= $SumaL12DP;
 $Lista_Interna[12]["suma_presidente"]= $SumaL12G;
 
 $SumaL13DP=$rw['SumaL13DP'];
 $SumaL13G=$rw['SumaL13G'];
 $Lista_Interna[13]["suma_diputado"]= $SumaL13DP;
 $Lista_Interna[13]["suma_presidente"]= $SumaL13G;
 
 $SumaL14DP=$rw['SumaL14DP'];
 $SumaL14G=$rw['SumaL14G'];
 $Lista_Interna[14]["suma_diputado"]= $SumaL14DP;
 $Lista_Interna[14]["suma_presidente"]= $SumaL14G;

 $SumaL15DP=$rw['SumaL15DP'];
 $SumaL15G=$rw['SumaL15G'];
 $Lista_Interna[15]["suma_diputado"]= $SumaL15DP;
 $Lista_Interna[15]["suma_presidente"]= $SumaL15G;

 $SumaL16DP=$rw['SumaL16DP'];
 $SumaL16G=$rw['SumaL16G'];
 $Lista_Interna[16]["suma_diputado"]= $SumaL16DP;
 $Lista_Interna[16]["suma_presidente"]= $SumaL16G;

 $SumaL17DP=$rw['SumaL17DP'];
 $SumaL17G=$rw['SumaL17G'];
 $Lista_Interna[17]["suma_diputado"]= $SumaL17DP;
 $Lista_Interna[17]["suma_presidente"]= $SumaL17G;

 $SumaL18DP=$rw['SumaL18DP'];
 $SumaL18G=$rw['SumaL18G'];
 $Lista_Interna[18]["suma_diputado"]= $SumaL18DP;
 $Lista_Interna[18]["suma_presidente"]= $SumaL18G;

 $SumaL19DP=$rw['SumaL19DP'];
 $SumaL19G=$rw['SumaL19G'];
 $Lista_Interna[19]["suma_diputado"]= $SumaL19DP;
 $Lista_Interna[19]["suma_presidente"]= $SumaL19G;

 $SumaL20DP=$rw['SumaL20DP'];
 $SumaL20G=$rw['SumaL20G'];
 $Lista_Interna[20]["suma_diputado"]= $SumaL20DP;
 $Lista_Interna[20]["suma_presidente"]= $SumaL20G;

 $SumaL21DP=$rw['SumaL21DP'];
 $SumaL21G=$rw['SumaL21G'];
 $Lista_Interna[21]["suma_diputado"]= $SumaL21DP;
 $Lista_Interna[21]["suma_presidente"]= $SumaL21G;

 $SumaL22DP=$rw['SumaL22DP'];
 $SumaL22G=$rw['SumaL22G'];
 $Lista_Interna[22]["suma_diputado"]= $SumaL22DP;
 $Lista_Interna[22]["suma_presidente"]= $SumaL22G;

 $SumaL23DP=$rw['SumaL23DP'];
 $SumaL23G=$rw['SumaL23G'];
 $Lista_Interna[23]["suma_diputado"]= $SumaL23DP;
 $Lista_Interna[23]["suma_presidente"]= $SumaL23G;

 $SumaL24DP=$rw['SumaL24DP'];
 $SumaL24G=$rw['SumaL24G'];
 $Lista_Interna[24]["suma_diputado"]= $SumaL24DP;
 $Lista_Interna[24]["suma_presidente"]= $SumaL24G;

 $SumaL25DP=$rw['SumaL25DP'];
 $SumaL25G=$rw['SumaL25G'];
 $Lista_Interna[25]["suma_diputado"]= $SumaL25DP;
 $Lista_Interna[25]["suma_presidente"]= $SumaL25G;

 $SumaL26DP=$rw['SumaL26DP'];
 $SumaL26G=$rw['SumaL26G'];
 $Lista_Interna[26]["suma_diputado"]= $SumaL26DP;
 $Lista_Interna[26]["suma_presidente"]= $SumaL26G;

 $SumaL27DP=$rw['SumaL27DP'];
 $SumaL27G=$rw['SumaL27G'];
 $Lista_Interna[27]["suma_diputado"]= $SumaL27DP;
 $Lista_Interna[27]["suma_presidente"]= $SumaL27G;

 $SumaL28DP=$rw['SumaL28DP'];
 $SumaL28G=$rw['SumaL28G'];
 $Lista_Interna[28]["suma_diputado"]= $SumaL28DP;
 $Lista_Interna[28]["suma_presidente"]= $SumaL28G;

 $SumaL29DP=$rw['SumaL29DP'];
 $SumaL29G=$rw['SumaL29G'];
 $Lista_Interna[29]["suma_diputado"]= $SumaL29DP;
 $Lista_Interna[29]["suma_presidente"]= $SumaL29G;

 
 $TotalesG= $SumaL1G + $SumaL2G + $SumaL3G + $SumaL4G + $SumaL5G+ $SumaL6G+ $SumaL7G+ $SumaL8G+ $SumaL9G+ $SumaL10G+$SumaL11G+$SumaL12G+$SumaL13G+$SumaL14G+$SumaL15G+$SumaL16G+$SumaL17G+$SumaL18G+$SumaL19G+$SumaL20G+$SumaL21G+$SumaL22G+$SumaL23G+$SumaL24G+$SumaL25G+$SumaL26G+$SumaL27G+$SumaL28G+$SumaL29G;
 $TotalesDP= $SumaL1DP + $SumaL2DP + $SumaL3DP + $SumaL4DP + $SumaL5DP+ $SumaL6DP+ $SumaL7DP+ $SumaL8DP+ $SumaL9DP+ $SumaL10DP+$SumaL11DP+$SumaL12DP+$SumaL13DP+$SumaL14DP+$SumaL15DP+$SumaL16DP+$SumaL17DP+$SumaL18DP+$SumaL19DP+$SumaL20DP+$SumaL21DP+$SumaL22DP+$SumaL23DP+$SumaL24DP+$SumaL25DP+$SumaL26DP+$SumaL27DP+$SumaL28DP+$SumaL29DP;;

$mensaje= "";


 $Partido[1]["suma_diputado"]= $Lista_Interna[1]["suma_diputado"];
 $Partido[1]["suma_presidente"]= $Lista_Interna[1]["suma_presidente"];
 
 $Partido[2]["suma_diputado"]= $Lista_Interna[2]["suma_diputado"];
 $Partido[2]["suma_presidente"]= $Lista_Interna[2]["suma_presidente"];
 
 $Partido[3]["suma_diputado"]= $Lista_Interna[3]["suma_diputado"];
 $Partido[3]["suma_presidente"]= $Lista_Interna[3]["suma_presidente"];

 $Partido[4]["suma_diputado"]= $Lista_Interna[4]["suma_diputado"];
 $Partido[4]["suma_presidente"]= $Lista_Interna[4]["suma_presidente"];   
 
 
 $Partido[5]["suma_diputado"]= $Lista_Interna[5]["suma_diputado"] + $Lista_Interna[6]["suma_diputado"];
 $Partido[5]["suma_presidente"]= $Lista_Interna[5]["suma_presidente"] +$Lista_Interna[6]["suma_presidente"];

 $Partido[6]["suma_diputado"]= $Lista_Interna[7]["suma_diputado"];
 $Partido[6]["suma_presidente"]= $Lista_Interna[7]["suma_presidente"];

 $Partido[7]["suma_diputado"]= $Lista_Interna[8]["suma_diputado"] +$Lista_Interna[9]["suma_diputado"]+$Lista_Interna[10]["suma_diputado"]+$Lista_Interna[11]["suma_diputado"];
 $Partido[7]["suma_presidente"]= $Lista_Interna[8]["suma_presidente"]+$Lista_Interna[9]["suma_presidente"]+$Lista_Interna[10]["suma_presidente"]+$Lista_Interna[11]["suma_presidente"];

 $Partido[8]["suma_diputado"]= $Lista_Interna[12]["suma_diputado"];
 $Partido[8]["suma_presidente"]= $Lista_Interna[12]["suma_presidente"];

 $Partido[9]["suma_diputado"]= $Lista_Interna[13]["suma_diputado"] +$Lista_Interna[14]["suma_diputado"]+$Lista_Interna[15]["suma_diputado"];
 $Partido[9]["suma_presidente"]= $Lista_Interna[13]["suma_presidente"]+ $Lista_Interna[14]["suma_presidente"]+ $Lista_Interna[15]["suma_presidente"];

 $Partido[10]["suma_diputado"]= $Lista_Interna[16]["suma_diputado"] +$Lista_Interna[17]["suma_diputado"];
 $Partido[10]["suma_presidente"]= $Lista_Interna[16]["suma_presidente"]+$Lista_Interna[17]["suma_presidente"];

 $Partido[11]["suma_diputado"]= $Lista_Interna[18]["suma_diputado"];
 $Partido[11]["suma_presidente"]= $Lista_Interna[18]["suma_presidente"];

 $Partido[12]["suma_diputado"]= $Lista_Interna[19]["suma_diputado"] +$Lista_Interna[20]["suma_diputado"]+$Lista_Interna[21]["suma_diputado"];
 $Partido[12]["suma_presidente"]= $Lista_Interna[19]["suma_presidente"]+$Lista_Interna[20]["suma_presidente"]+$Lista_Interna[21]["suma_presidente"];

 $Partido[13]["suma_diputado"]= $Lista_Interna[22]["suma_diputado"];
 $Partido[13]["suma_presidente"]= $Lista_Interna[22]["suma_presidente"];

 $Partido[14]["suma_diputado"]= $Lista_Interna[23]["suma_diputado"]+ $Lista_Interna[24]["suma_diputado"];
 $Partido[14]["suma_presidente"]= $Lista_Interna[23]["suma_presidente"] +$Lista_Interna[24]["suma_presidente"];

 $Partido[15]["suma_diputado"]= $Lista_Interna[25]["suma_diputado"]+$Lista_Interna[26]["suma_diputado"]+$Lista_Interna[27]["suma_diputado"]+$Lista_Interna[28]["suma_diputado"]+$Lista_Interna[29]["suma_diputado"];
 $Partido[15]["suma_presidente"]= $Lista_Interna[25]["suma_presidente"]+$Lista_Interna[26]["suma_presidente"]+$Lista_Interna[27]["suma_presidente"]+$Lista_Interna[28]["suma_presidente"]+$Lista_Interna[29]["suma_presidente"];


if ($TotalesG > 0){

 if ($SumaL1DP > 0){
  $PorcentajeL1DP = ($SumaL1DP * 100) / $TotalesDP;
  $Lista_Interna[1]["porcentaje_diputado"]= $PorcentajeL1DP;
  } 
  
 if ($SumaL1G > 0){
  $PorcentajeL1G = ($SumaL1G * 100) / $TotalesG;
  $Lista_Interna[1]["porcentaje_presidente"]= $PorcentajeL1G;
  }
 
  
 if ($SumaL2DP > 0){
  $PorcentajeL2DP = ($SumaL2DP * 100) / $TotalesDP;
  $Lista_Interna[2]["porcentaje_diputado"]= $PorcentajeL2DP;
  } 
  
 if ($SumaL2G > 0){
  $PorcentajeL2G = ($SumaL2G * 100) / $TotalesG;
  $Lista_Interna[2]["porcentaje_presidente"]= $PorcentajeL2G;
  }
  
 
 if ($SumaL3DP > 0){
  $PorcentajeL3DP = ($SumaL3DP * 100) / $TotalesDP;
  $Lista_Interna[3]["porcentaje_diputado"]= $PorcentajeL3DP;
  }  
 if ($SumaL3G > 0){
  $PorcentajeL3G = ($SumaL3G * 100) / $TotalesG;
  $Lista_Interna[3]["porcentaje_presidente"]= $PorcentajeL3G;
  }
 
 
 if ($SumaL4DP > 0){
  $PorcentajeL4DP = ($SumaL4DP * 100) / $TotalesDP;
  $Lista_Interna[4]["porcentaje_diputado"]= $PorcentajeL4DP;
  } 
 if ($SumaL4G > 0){
  $PorcentajeL4G = ($SumaL4G * 100) / $TotalesG;
  $Lista_Interna[4]["porcentaje_presidente"]= $PorcentajeL4G;
  }
  
 if ($SumaL5DP > 0){
  $PorcentajeL5DP = ($SumaL5DP * 100) / $TotalesDP;
  $Lista_Interna[5]["porcentaje_diputado"]= $PorcentajeL5DP;
  }  
 if ($SumaL5G > 0){
  $PorcentajeL5G = ($SumaL5G * 100) / $TotalesG;
  $Lista_Interna[5]["porcentaje_presidente"]= $PorcentajeL5G;
  }
 
 
 if ($SumaL6DP > 0){
  $PorcentajeL6DP = ($SumaL6DP * 100) / $TotalesDP;
  $Lista_Interna[6]["porcentaje_diputado"]= $PorcentajeL6DP;
  }   
 if ($SumaL6G > 0){
  $PorcentajeL6G = ($SumaL6G * 100) / $TotalesG;
  $Lista_Interna[6]["porcentaje_presidente"]= $PorcentajeL6G;
  }
  
 if ($SumaL7DP > 0){
  $PorcentajeL7DP = ($SumaL7DP * 100) / $TotalesDP;
  $Lista_Interna[7]["porcentaje_diputado"]= $PorcentajeL7DP;
  }    
 if ($SumaL7G > 0){
  $PorcentajeL7G = ($SumaL7G * 100) / $TotalesG;
  $Lista_Interna[7]["porcentaje_presidente"]= $PorcentajeL7G;
  }
  
 if ($SumaL8DP > 0){
  $PorcentajeL8DP = ($SumaL8DP * 100) / $TotalesDP;
  $Lista_Interna[8]["porcentaje_diputado"]= $PorcentajeL8DP;
  }    
 if ($SumaL8G > 0){
  $PorcentajeL8G = ($SumaL8G * 100) / $TotalesG;
  $Lista_Interna[8]["porcentaje_presidente"]= $PorcentajeL8G;
  }
 
 if ($SumaL9DP > 0){
  $PorcentajeL9DP = ($SumaL9DP * 100) / $TotalesDP;
  $Lista_Interna[9]["porcentaje_diputado"]= $PorcentajeL9DP;
  }    
 if ($SumaL9G > 0){
  $PorcentajeL9G = ($SumaL9G * 100) / $TotalesG;
  $Lista_Interna[9]["porcentaje_presidente"]= $PorcentajeL9G;
  }

 if ($SumaL10DP > 0){
  $PorcentajeL10DP = ($SumaL10DP * 100) / $TotalesDP;
  $Lista_Interna[10]["porcentaje_diputado"]= $PorcentajeL10DP;
  }    
 if ($SumaL10G > 0){
   $PorcentajeL10G = ($SumaL10G * 100) / $TotalesG;
   $Lista_Interna[10]["porcentaje_presidente"]= $PorcentajeL10G;
  } 
  
  if ($SumaL11DP > 0){
  $PorcentajeL11DP = ($SumaL11DP * 100) / $TotalesDP;
  $Lista_Interna[11]["porcentaje_diputado"]= $PorcentajeL11DP;
  }    
 if ($SumaL11G > 0){
   $PorcentajeL11G = ($SumaL11G * 100) / $TotalesG;
   $Lista_Interna[11]["porcentaje_presidente"]= $PorcentajeL11G;
  } 
  
 if ($SumaL12DP > 0){
  $PorcentajeL12DP = ($SumaL12DP * 100) / $TotalesDP;
  $Lista_Interna[12]["porcentaje_diputado"]= $PorcentajeL12DP;
  }    
 if ($SumaL12G > 0){
   $PorcentajeL12G = ($SumaL12G * 100) / $TotalesG;
   $Lista_Interna[12]["porcentaje_presidente"]= $PorcentajeL12G;
  } 
  
 if ($SumaL13DP > 0){
  $PorcentajeL13DP = ($SumaL13DP * 100) / $TotalesDP;
  $Lista_Interna[13]["porcentaje_diputado"]= $PorcentajeL13DP;
  }    

 if ($SumaL13G > 0){
   $PorcentajeL13G = ($SumaL13G * 100) / $TotalesG;
   $Lista_Interna[13]["porcentaje_presidente"]= $PorcentajeL13G;
  } 
  
   if ($SumaL14DP > 0){
  $PorcentajeL14DP = ($SumaL14DP * 100) / $TotalesDP;
  $Lista_Interna[14]["porcentaje_diputado"]= $PorcentajeL14DP;
  }    
 if ($SumaL14G > 0){
   $PorcentajeL14G = ($SumaL14G * 100) / $TotalesG;
   $Lista_Interna[14]["porcentaje_presidente"]= $PorcentajeL14G;
  } 
  
  if ($SumaL15DP > 0){
  $PorcentajeL15DP = ($SumaL15DP * 100) / $TotalesDP;
  $Lista_Interna[15]["porcentaje_diputado"]= $PorcentajeL15DP;
  }    
 if ($SumaL15G > 0){
   $PorcentajeL15G = ($SumaL15G * 100) / $TotalesG;
   $Lista_Interna[15]["porcentaje_presidente"]= $PorcentajeL15G;
  } 
  
   if ($SumaL16DP > 0){
  $PorcentajeL16DP = ($SumaL16DP * 100) / $TotalesDP;
  $Lista_Interna[16]["porcentaje_diputado"]= $PorcentajeL16DP;
  }    
 if ($SumaL16G > 0){
   $PorcentajeL16G = ($SumaL16G * 100) / $TotalesG;
   $Lista_Interna[16]["porcentaje_presidente"]= $PorcentajeL16G;
  } 
  
 if ($SumaL17DP > 0){
  $PorcentajeL17DP = ($SumaL17DP * 100) / $TotalesDP;
  $Lista_Interna[17]["porcentaje_diputado"]= $PorcentajeL17DP;
  }    
 if ($SumaL17G > 0){
   $PorcentajeL17G = ($SumaL17G * 100) / $TotalesG;
   $Lista_Interna[17]["porcentaje_presidente"]= $PorcentajeL17G;
  } 
  
 if ($SumaL18DP > 0){
  $Porcentaje18DP = ($SumaL18DP * 100) / $TotalesDP;
  $Lista_Interna[18]["porcentaje_diputado"]= $PorcentajeL18DP;
  }    
 if ($SumaL18G > 0){
   $PorcentajeL18G = ($SumaL18G * 100) / $TotalesG;
   $Lista_Interna[18]["porcentaje_presidente"]= $PorcentajeL18G;
  } 
 
 if ($SumaL19DP > 0){
  $PorcentajeL19DP = ($SumaL19DP * 100) / $TotalesDP;
  $Lista_Interna[19]["porcentaje_diputado"]= $PorcentajeL19DP;
  }    
 
 if ($SumaL19G > 0){
   $PorcentajeL19G = ($SumaL19G * 100) / $TotalesG;
   $Lista_Interna[19]["porcentaje_presidente"]= $PorcentajeL19G;
  } 
  
 if ($SumaL20DP > 0){
  $PorcentajeL20DP = ($SumaL20DP * 100) / $TotalesDP;
  $Lista_Interna[20]["porcentaje_diputado"]= $PorcentajeL20DP;
  }    
 
 if ($SumaL20G > 0){
   $PorcentajeL20G = ($SumaL20G * 100) / $TotalesG;
   $Lista_Interna[20]["porcentaje_presidente"]= $PorcentajeL20G;
  } 
  
  if ($SumaL21G > 0){
   $PorcentajeL21G = ($SumaL21G * 100) / $TotalesG;
   $Lista_Interna[21]["porcentaje_presidente"]= $PorcentajeL21G;
  } 
  
  if ($SumaL21DP > 0){
   $PorcentajeL21DP = ($SumaL21DP * 100) / $TotalesDP;
   $Lista_Interna[21]["porcentaje_diputado"]= $PorcentajeL21DP;
  }  
 
 if ($SumaL22G > 0){
   $PorcentajeL22G = ($SumaL22G * 100) / $TotalesG;
   $Lista_Interna[22]["porcentaje_presidente"]= $PorcentajeL22G;
  } 
  
  if ($SumaL22DP > 0){
   $PorcentajeL22DP = ($SumaL22DP * 100) / $TotalesDP;
   $Lista_Interna[22]["porcentaje_diputado"]= $PorcentajeL22DP;
  }     
 
 if ($SumaL23G > 0){
   $PorcentajeL23G = ($SumaL23G * 100) / $TotalesG;
   $Lista_Interna[23]["porcentaje_presidente"]= $PorcentajeL23G;
  } 
  
  if ($SumaL23DP > 0){
   $PorcentajeL23DP = ($SumaL23DP * 100) / $TotalesDP;
   $Lista_Interna[23]["porcentaje_diputado"]= $PorcentajeL23DP;
  } 
  
  if ($SumaL24G > 0){
   $PorcentajeL24G = ($SumaL24G * 100) / $TotalesG;
   $Lista_Interna[24]["porcentaje_presidente"]= $PorcentajeL24G;
  } 
  
  if ($SumaL24DP > 0){
   $PorcentajeL24DP = ($SumaL24DP * 100) / $TotalesDP;
   $Lista_Interna[24]["porcentaje_diputado"]= $PorcentajeL24DP;
  }  
  
  if ($SumaL25G > 0){
   $PorcentajeL25G = ($SumaL25G * 100) / $TotalesG;
   $Lista_Interna[25]["porcentaje_presidente"]= $PorcentajeL25G;
  } 
  
  if ($SumaL25DP > 0){
   $PorcentajeL25DP = ($SumaL25DP * 100) / $TotalesDP;
   $Lista_Interna[25]["porcentaje_diputado"]= $PorcentajeL25DP;
  } 
  
  if ($SumaL26G > 0){
   $PorcentajeL26G = ($SumaL26G * 100) / $TotalesG;
   $Lista_Interna[26]["porcentaje_presidente"]= $PorcentajeL26G;
  } 
  
  if ($SumaL26DP > 0){
   $PorcentajeL26DP = ($SumaL26DP * 100) / $TotalesDP;
   $Lista_Interna[26]["porcentaje_diputado"]= $PorcentajeL26DP;
  }              
  
  if ($SumaL27G > 0){
   $PorcentajeL27G = ($SumaL27G * 100) / $TotalesG;
   $Lista_Interna[27]["porcentaje_presidente"]= $PorcentajeL27G;
  } 
  
  if ($SumaL27DP > 0){
   $PorcentajeL27DP = ($SumaL27DP * 100) / $TotalesDP;
   $Lista_Interna[27]["porcentaje_diputado"]= $PorcentajeL27DP;
  }  
  
  if ($SumaL28G > 0){
   $PorcentajeL28G = ($SumaL28G * 100) / $TotalesG;
   $Lista_Interna[28]["porcentaje_presidente"]= $PorcentajeL28G;
  } 
  
  if ($SumaL28DP > 0){
   $PorcentajeL28DP = ($SumaL28DP * 100) / $TotalesDP;
   $Lista_Interna[28]["porcentaje_diputado"]= $PorcentajeL28DP;
  }  
  
  if ($SumaL29G > 0){
   $PorcentajeL29G = ($SumaL29G * 100) / $TotalesG;
   $Lista_Interna[29]["porcentaje_presidente"]= $PorcentajeL29G;
  } 
  
  if ($SumaL29DP > 0){
   $PorcentajeL29DP = ($SumaL29DP * 100) / $TotalesDP;
   $Lista_Interna[29]["porcentaje_diputado"]= $PorcentajeL29DP;
  }   
  
                     
 $Partido[1]["porcentaje_diputado"]= $Lista_Interna[1]["porcentaje_diputado"];
 $Partido[1]["porcentaje_presidente"]= $Lista_Interna[1]["porcentaje_presidente"];
 
 $Partido[2]["porcentaje_diputado"]= $Lista_Interna[2]["porcentaje_diputado"];
 $Partido[2]["porcentaje_presidente"]= $Lista_Interna[2]["porcentaje_presidente"];
 
 $Partido[3]["porcentaje_diputado"]= $Lista_Interna[3]["porcentaje_diputado"];
 $Partido[3]["porcentaje_presidente"]= $Lista_Interna[3]["porcentaje_presidente"];

 $Partido[4]["porcentaje_diputado"]= $Lista_Interna[4]["porcentaje_diputado"];
 $Partido[4]["porcentaje_presidente"]= $Lista_Interna[4]["porcentaje_presidente"];   
 
 
 $Partido[5]["porcentaje_diputado"]= $Lista_Interna[5]["porcentaje_diputado"] + $Lista_Interna[6]["porcentaje_diputado"];
 $Partido[5]["porcentaje_presidente"]= $Lista_Interna[5]["porcentaje_presidente"] +$Lista_Interna[6]["porcentaje_presidente"];

 $Partido[6]["porcentaje_diputado"]= $Lista_Interna[7]["porcentaje_diputado"];
 $Partido[6]["porcentaje_presidente"]= $Lista_Interna[7]["porcentaje_presidente"];

 $Partido[7]["porcentaje_diputado"]= $Lista_Interna[8]["porcentaje_diputado"] +$Lista_Interna[9]["porcentaje_diputado"]+$Lista_Interna[10]["porcentaje_diputado"]+$Lista_Interna[11]["porcentaje_diputado"];
 $Partido[7]["porcentaje_presidente"]= $Lista_Interna[8]["porcentaje_presidente"]+$Lista_Interna[9]["porcentaje_presidente"]+$Lista_Interna[10]["porcentaje_presidente"]+$Lista_Interna[11]["porcentaje_presidente"];

 $Partido[8]["porcentaje_diputado"]= $Lista_Interna[12]["porcentaje_diputado"];
 $Partido[8]["porcentaje_presidente"]= $Lista_Interna[12]["porcentaje_presidente"];

 $Partido[9]["porcentaje_diputado"]= $Lista_Interna[13]["porcentaje_diputado"] +$Lista_Interna[14]["porcentaje_diputado"]+$Lista_Interna[15]["porcentaje_diputado"];
 $Partido[9]["porcentaje_presidente"]= $Lista_Interna[13]["porcentaje_presidente"]+ $Lista_Interna[14]["porcentaje_presidente"]+ $Lista_Interna[15]["porcentaje_presidente"];

 $Partido[10]["porcentaje_diputado"]= $Lista_Interna[16]["porcentaje_diputado"] +$Lista_Interna[17]["porcentaje_diputado"];
 $Partido[10]["porcentaje_presidente"]= $Lista_Interna[16]["porcentaje_presidente"]+$Lista_Interna[17]["porcentaje_presidente"];

 $Partido[11]["porcentaje_diputado"]= $Lista_Interna[18]["porcentaje_diputado"];
 $Partido[11]["porcentaje_presidente"]= $Lista_Interna[18]["porcentaje_presidente"];

 $Partido[12]["porcentaje_diputado"]= $Lista_Interna[19]["porcentaje_diputado"] +$Lista_Interna[20]["porcentaje_diputado"]+$Lista_Interna[21]["porcentaje_diputado"];
 $Partido[12]["porcentaje_presidente"]= $Lista_Interna[19]["porcentaje_presidente"]+$Lista_Interna[20]["porcentaje_presidente"]+$Lista_Interna[21]["porcentaje_presidente"];

 $Partido[13]["porcentaje_diputado"]= $Lista_Interna[22]["porcentaje_diputado"];
 $Partido[13]["porcentaje_presidente"]= $Lista_Interna[22]["porcentaje_presidente"];

 $Partido[14]["porcentaje_diputado"]= $Lista_Interna[23]["porcentaje_diputado"]+ $Lista_Interna[24]["porcentaje_diputado"];
 $Partido[14]["porcentaje_presidente"]= $Lista_Interna[23]["porcentaje_presidente"] +$Lista_Interna[24]["porcentaje_presidente"];

 $Partido[15]["porcentaje_diputado"]= $Lista_Interna[25]["porcentaje_diputado"]+$Lista_Interna[26]["porcentaje_diputado"]+$Lista_Interna[27]["porcentaje_diputado"]+$Lista_Interna[28]["porcentaje_diputado"]+$Lista_Interna[29]["porcentaje_diputado"];
 $Partido[15]["porcentaje_presidente"]= $Lista_Interna[25]["porcentaje_presidente"]+$Lista_Interna[26]["porcentaje_presidente"]+$Lista_Interna[27]["porcentaje_presidente"]+$Lista_Interna[28]["porcentaje_presidente"]+$Lista_Interna[29]["porcentaje_presidente"];
  
 
 for ($i=1;$i<=2;$i++)
   {
    $Interna1[$i]["suma_diputado"]=$Lista_Interna[$i+4]["suma_diputado"];
    $Interna1[$i]["suma_presidente"]=$Lista_Interna[$i+4]["suma_presidente"];
    $Interna1[$i]["porcentaje_presidente"]=$Lista_Interna[$i+4]["porcentaje_presidente"];
    $Interna1[$i]["porcentaje_diputado"]=$Lista_Interna[$i+4]["porcentaje_diputado"];
    $Interna1[$i]["lista"]=$Lista_Interna[$i+4]["lista"];
   // $Interna1[$i]["partido"]=$Lista_Interna[$i+3]["partido"];
    $Interna1[$i]["nombre"]=$Lista_Interna[$i+4]["nombre"];
    $Interna1[$i]["color"]=$Lista_Interna[$i+4]["color"];
   }
   
  for ($i=1;$i<=4;$i++)
   {
    $Interna2[$i]["suma_diputado"]=$Lista_Interna[$i+7]["suma_diputado"];
    $Interna2[$i]["suma_presidente"]=$Lista_Interna[$i+7]["suma_presidente"];
    $Interna2[$i]["porcentaje_presidente"]=$Lista_Interna[$i+7]["porcentaje_presidente"];
    $Interna2[$i]["porcentaje_diputado"]=$Lista_Interna[$i+7]["porcentaje_diputado"];
    $Interna2[$i]["lista"]=$Lista_Interna[$i+7]["lista"];
    $Interna2[$i]["nombre"]=$Lista_Interna[$i+7]["nombre"];
    $Interna2[$i]["color"]=$Lista_Interna[$i+7]["color"];
   }  
   
   for ($i=1;$i<=3;$i++)
   {
    $Interna3[$i]["suma_diputado"]=$Lista_Interna[$i+12]["suma_diputado"];
    $Interna3[$i]["suma_presidente"]=$Lista_Interna[$i+12]["suma_presidente"];
    $Interna3[$i]["porcentaje_presidente"]=$Lista_Interna[$i+12]["porcentaje_presidente"];
    $Interna3[$i]["porcentaje_diputado"]=$Lista_Interna[$i+12]["porcentaje_diputado"];
    $Interna3[$i]["lista"]=$Lista_Interna[$i+12]["lista"];
    $Interna3[$i]["nombre"]=$Lista_Interna[$i+12]["nombre"];
    $Interna3[$i]["color"]=$Lista_Interna[$i+12]["color"];
   }  
  
   for ($i=1;$i<=2;$i++)
   {
    $Interna4[$i]["suma_diputado"]=$Lista_Interna[$i+15]["suma_diputado"];
    $Interna4[$i]["suma_presidente"]=$Lista_Interna[$i+15]["suma_presidente"];
    $Interna4[$i]["porcentaje_presidente"]=$Lista_Interna[$i+15]["porcentaje_presidente"];
    $Interna4[$i]["porcentaje_diputado"]=$Lista_Interna[$i+15]["porcentaje_diputado"];
    $Interna4[$i]["lista"]=$Lista_Interna[$i+15]["lista"];
    $Interna4[$i]["nombre"]=$Lista_Interna[$i+15]["nombre"];	
	$Interna4[$i]["color"]=$Lista_Interna[$i+15]["color"];
   }   
  
   for ($i=1;$i<=3;$i++)
   {
    $Interna5[$i]["suma_diputado"]=$Lista_Interna[$i+18]["suma_diputado"];
    $Interna5[$i]["suma_presidente"]=$Lista_Interna[$i+18]["suma_presidente"];
    $Interna5[$i]["porcentaje_presidente"]=$Lista_Interna[$i+18]["porcentaje_presidente"];
    $Interna5[$i]["porcentaje_diputado"]=$Lista_Interna[$i+18]["porcentaje_diputado"];
    $Interna5[$i]["lista"]=$Lista_Interna[$i+18]["lista"];
 	$Interna5[$i]["nombre"]=$Lista_Interna[$i+18]["nombre"];
	$Interna5[$i]["color"]=$Lista_Interna[$i+18]["color"];
   }    
  
   for ($i=1;$i<=2;$i++)
   {
    $Interna6[$i]["suma_diputado"]=$Lista_Interna[$i+22]["suma_diputado"];
    $Interna6[$i]["suma_presidente"]=$Lista_Interna[$i+22]["suma_presidente"];
    $Interna6[$i]["porcentaje_presidente"]=$Lista_Interna[$i+22]["porcentaje_presidente"];
    $Interna6[$i]["porcentaje_diputado"]=$Lista_Interna[$i+22]["porcentaje_diputado"];
    $Interna6[$i]["lista"]=$Lista_Interna[$i+22]["lista"];
 	$Interna6[$i]["nombre"]=$Lista_Interna[$i+22]["nombre"];
	$Interna6[$i]["color"]=$Lista_Interna[$i+22]["color"];
   }    
  
   for ($i=1;$i<=5;$i++)
   {
    $Interna7[$i]["suma_diputado"]=$Lista_Interna[$i+24]["suma_diputado"];
    $Interna7[$i]["suma_presidente"]=$Lista_Interna[$i+24]["suma_presidente"];
    $Interna7[$i]["porcentaje_presidente"]=$Lista_Interna[$i+24]["porcentaje_presidente"];
    $Interna7[$i]["porcentaje_diputado"]=$Lista_Interna[$i+24]["porcentaje_diputado"];
    $Interna7[$i]["lista"]=$Lista_Interna[$i+24]["lista"];
    $Interna7[$i]["nombre"]=$Lista_Interna[$i+24]["nombre"];
	$Interna7[$i]["color"]=$Lista_Interna[$i+24]["color"];
   }      
   
   
   
 foreach ($Partido as $key => $row) {
    $aux[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux, SORT_DESC, $Partido); 
 
 foreach ($Interna1 as $key => $row) {
    $aux1[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux1, SORT_DESC, $Interna1); 
 
  foreach ($Interna2 as $key => $row) {
    $aux2[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux2, SORT_DESC, $Interna2); 
 
 foreach ($Interna3 as $key => $row) {
    $aux3[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux3, SORT_DESC, $Interna3); 

foreach ($Interna4 as $key => $row) {
    $aux4[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux4, SORT_DESC, $Interna4);
 
 foreach ($Interna5 as $key => $row) {
    $aux5[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux5, SORT_DESC, $Interna5);
 
 foreach ($Interna6 as $key => $row) {
    $aux6[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux6, SORT_DESC, $Interna6);
 
 foreach ($Interna7 as $key => $row) {
    $aux7[$key] = $row['suma_presidente'];
 }
 
 array_multisort($aux7, SORT_DESC, $Interna7); 
 
 
 $Porcentaje = 100;
  
  }
  
 
  
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta charset=ISO-8859-1>
<meta http-equiv="refresh" content="60;URL=https://www.lapampaperonista.com.ar/elecciones/verestadistica.php">

 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  
  
  
  
<title>Elecciones <?php echo " ". date("Y"); ?></title>
<style type="text/css">
<!-- //color Verde= #003300 //color Azul=  #003366 //Naranja = #D8232A

.EstiloPartido {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotos1 {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/



.Estilo152 {font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000;font-size: 24px}
.Estilo_Selector {font-size: 16px}

.cabeceras {font-family: Verdana, Arial, Helvetica, sans-serif; color:#666666;font-size: 22px}

.cabecera_logo {font-size: 14px; font-weight: bold; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif; color:#ffffff;
}

.EstiloVotos {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotosInterna{font-size: 18px; font-weight: normal ; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif;}



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
</head>

<body>

<div align="center" style="width:96%;margin-left:2%;border:inset;border-color:#E9ECEF">
<div align="center">
 
 <!-- <p align="center" class="Estilo8"><a target='_blank' href="https://eleccionesgenerales2019.lapampa.gob.ar/scripts/cgiip.exe/WService=Elecciones/EReLocCowr.htm?vPartido=1&SelectLocalidad=84&User=">Ver Resultados en Tribunal Electoral (clic aqu&iacute;)</a></p> -->
 <table width="100%" height="81" border="0">
 
   <tr>
     <td height="77" colspan="2" align="left" bgcolor="#016EA6"><p><img src="fotos/escudo_la_pampa.png" width="65" height="70" style="margin-top:4px; margin-left:5px; margin-right:15px; float:left" /></p>
       <p><span class="cabecera_logo">Elecciones <?php echo "2023";?></span></p>
       <p><span class="cabecera_logo"> 13 de agosto de <?php echo "2023";?> - Escrutinio Provisorio.</span></p>        </td> 
       
       <td width="51%" colspan="2" align="right" bgcolor="#016EA6"><p><img src="fotos/Escudo_del_Partido_Justicialista.png" width="54" height="67" style="margin-top:2px; margin-right:5px; margin-bottom:2px; float:left" /></p>
       
       <p><span class="cabecera_logo"> Datos de Toda La Provincia de La Pampa &nbsp;&nbsp;</span></p>      
       <p>
       
       
       <span class="cabecera_logo"> <i class="fa fa-user"></i><?php echo $_SESSION['MM_Username']." |  "; ?></span> 
     
     <?php if($_SESSION['usuario_id']==1) { ?>
      <span class="cabecera_logo"><a target="_blank" style="color:#39FF14; margin-right:15px;" href="mapa_gobernador_2023.php">Gob. 2023 </a></span>
      <?php } ?>
     
       <span class="cabecera_logo"><a style="color:#CCCCCC; margin-right:15px;" href="<?php echo $logoutAction ?>">Salir</a></span></p>       
       
       </td>  
   </tr>
  </table>
  <table width="100%" height="77" border="0"> 
  
   <tr>
     
     <td width="25%" height="20"><div align="center" style="margin-left:5px" class="Estilo152">Mesas Escrutadas: <?php echo $CantidadMesasEscrutadas;?> &nbsp;<?php echo number_format($PorcentajeMesasEscrutadas,1, ',', '.') . "%"; ?> &nbsp; &nbsp; </div>
     </td>
      
      <td width="40%"> <span class="Estilo152"> Votos Pdte: <?php echo number_format($TotalesG,0, ',', '.') ;?></span><span class="Estilo152"> &nbsp; &nbsp;Votos Dip: <?php echo number_format($TotalesDP,0, ',', '.') ;?></span></td>
      
               
     <td width="20%" align="center" style="vertical-align: middle;"><form action="verestadisticaspueblos1.php" method="post" name="form1" class="Estilo156" id="form1">
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
           <option value="1019">Casa de Piedra</option>
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
           <option value="37" >presidente Duval</option>
           <option value="42" >Guatrach&eacute </option>
           <option value="43" >Ingeniero Luiggi</option>
           <option value="45" >diputado Alvear</option>
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
       
     </form>
     </td><td width="1%" style="vertical-align: middle;"><form method="post" action="<?php  echo $_SERVER['PHP_SELF']; ?>">
        <?php 
		if($_SESSION['todos']==1)
		 $ver="ver menos";
	 	else
		 $ver="ver todos"; 
		?>
       <button class="btn btn-light btn-sm" style="margin-top:12px" name="submit" type="submit"><?php echo $ver; ?></button>     
      </form> </td> 
   </tr>
     
 </table>
 
</div>

<table class="table" >
 <tr bgcolor="#E9ECEF">
   <td><div align="center"><span class="cabeceras">Agrupaci&oacute;n</span> </div></td> 
  <td><div align="center"><span class="cabeceras">Lista Interna</span> </div></td> 
  <td> <div align="center"><span class="cabeceras">Votos Pdte.</span></div></td> 
  <td><div align="center"><span class="cabeceras">% Pdte.</span></div></td>
   <td> <div align="center"><span class="cabeceras">Votos Dip.</span></div></td> 
  <td><div align="center"><span class="cabeceras">% Dip.</span></div></td>
  </tr>
 
 <?php 
 
 if($_SESSION['todos']==1)
  $cantidad_a_mostrar=14;  // maximo es 5
 else
  $cantidad_a_mostrar=4; // 3
 
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
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
   <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila; ?>">
      
    <td align="left" colspan="2" width="398"  bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo $Partido[$i]["color"]; ?>"><?php echo $Partido[$i]["nombre"]; ?></td>
      
    <td width="125" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Partido[$i]["color"]; ?>" ><div align="center"><?php if ($Partido[$i]["suma_presidente"]>0) echo number_format($Partido[$i]["suma_presidente"],0, ',', '.') ?></div></td>
    <td width="101" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo $Partido[$i]["color"]; ?>" ><div align="center"><?php if ($Partido[$i]["suma_presidente"]>0) echo number_format($Partido[$i]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
   
    <td width="125" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Partido[$i]["color"]; ?>" ><div align="center"><?php if ($Partido[$i]["suma_diputado"]>0) echo number_format($Partido[$i]["suma_diputado"],0, ',', '.') ?></div></td>
    <td width="87" bgcolor="#FFFFFF" class="EstiloVotos" style="vertical-align: middle;color:<?php echo  $Partido[$i]["color"]; ?>" ><div align="center"><?php if ($Partido[$i]["suma_diputado"]>0) echo number_format($Partido[$i]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
  </tr>
  
  <?php if ($Partido[$i]['interna']==1 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=1;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td  width="398" align="right" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna1[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna1[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna1[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php if($Interna1[$j]["suma_presidente"] > 0) echo number_format($Interna1[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna1[$j]["color"]; ?>" ><div align="center"><?php if($Interna1[$j]["suma_presidente"] > 0) echo number_format($Interna1[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php if ($Interna1[$j]["suma_diputado"]>0) echo number_format($Interna1[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna1[$j]["color"]; ?>" ><div align="center"><?php if ($Interna1[$j]["suma_diputado"]>0) echo number_format($Interna1[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
  
       
 <?php 
         } // $Partido[$i]['interna']==1
       } // for secundario, el de la interna 1
	   
  if ($Partido[$i]['interna']==2 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=3;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td align="right"  width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna2[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna2[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna2[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php if ($Interna2[$j]["suma_presidente"]>0) echo number_format($Interna2[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna2[$j]["color"]; ?>" ><div align="center"><?php if ($Interna2[$j]["suma_presidente"]>0) echo number_format($Interna2[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php if ($Interna2[$j]["suma_diputado"]>0) echo number_format($Interna2[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna2[$j]["color"]; ?>" ><div align="center"><?php if ($Interna2[$j]["suma_diputado"]>0) echo number_format($Interna2[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Partido[$i] ['interna']==2)
	 
  
  if ($Partido[$i]['interna']==3 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=2;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td align="right"  width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna3[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna3[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna3[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna3[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna3[$j]["color"]; ?>" ><div align="center"><?php if ($Interna3[$j]["suma_presidente"]>0) echo number_format($Interna3[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna3[$j]["color"]; ?>" ><div align="center"><?php if ($Interna3[$j]["porcentaje_presidente"]>0) echo number_format($Interna3[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna3[$j]["color"]; ?>" ><div align="center"><?php if ($Interna3[$j]["suma_diputado"]>0) echo number_format($Interna3[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna3[$j]["color"]; ?>" ><div align="center"><?php if ($Interna3[$j]["porcentaje_diputado"]>0) echo number_format($Interna3[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Partido[$i] ['interna']==3)	 
	 
  
  if ($Partido[$i]['interna']==4 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=1;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td align="right" width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna4[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna4[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna4[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna4[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna4[$j]["color"]; ?>" ><div align="center"><?php if ($Interna4[$j]["suma_presidente"]>0) echo number_format($Interna4[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna4[$j]["color"]; ?>" ><div align="center"><?php if ($Interna4[$j]["suma_presidente"]>0) echo number_format($Interna4[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna4[$j]["color"]; ?>" ><div align="center"><?php if ($Interna4[$j]["suma_diputado"]>0) echo number_format($Interna4[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna4[$j]["color"]; ?>" ><div align="center"><?php if ($Interna4[$j]["suma_diputado"]>0) echo number_format($Interna4[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Partido[$i] ['interna']==4)	 
  
  if ($Partido[$i]['interna']==5 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=2;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td align="right" width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna5[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna5[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna5[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna5[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna5[$j]["color"]; ?>" ><div align="center"><?php if ($Interna5[$j]["suma_presidente"]>0) echo number_format($Interna5[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna5[$j]["color"]; ?>" ><div align="center"><?php if ($Interna5[$j]["suma_presidente"]>0) echo number_format($Interna5[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna5[$j]["color"]; ?>" ><div align="center"><?php if ($Interna5[$j]["suma_diputado"]>0) echo number_format($Interna5[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna5[$j]["color"]; ?>" ><div align="center"><?php if ($Interna5[$j]["suma_diputado"]>0) echo number_format($Interna5[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Partido[$i] ['interna']==4)		 
	 
  if ($Partido[$i]['interna']==6 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=1;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td align="right" width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna6[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna6[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna6[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna6[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna6[$j]["color"]; ?>" ><div align="center"><?php if ($Interna6[$j]["suma_presidente"]>0) echo number_format($Interna6[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna6[$j]["color"]; ?>" ><div align="center"><?php if ($Interna6[$j]["suma_presidente"]>0) echo number_format($Interna6[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna6[$j]["color"]; ?>" ><div align="center"><?php if ($Interna6[$j]["suma_diputado"]>0) echo number_format($Interna6[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna6[$j]["color"]; ?>" ><div align="center"><?php if ($Interna6[$j]["suma_diputado"]>0) echo number_format($Interna6[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Partido[$i] ['interna']==6)		 
  
  if ($Partido[$i]['interna']==7 and $Partido[$i]['suma_presidente']>0){ 
   $comienzoj=0;
   $hastainterna=4;
  for ($j=$comienzoj;$j<=$hastainterna;$j++){ //dejar $i=0 
  
   $posI = 1;//strpos($Listas[$i]["cargos"], "D"); //I = diputado G= presidente
   $posG = 1;//strpos($Listas[$i]["cargos"], "S"); ?>
  
       <tr bgcolor="#FFFFFF" height="<?php echo $alto_fila_interna; ?>">
        <td align="right" width="398" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna7[$j]["color"]; ?>"><?php echo "<span class='EstiloNombres'>".$Interna7[$j]["lista"]. "<span>"; ?></td>
       
        <td width="143" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna7[$j]["color"]; ?>" ><div align="left"><?php  echo $Interna7[$j]["nombre"]; ?></div></td>
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna7[$j]["color"]; ?>" ><div align="center"><?php if ($Interna7[$j]["suma_presidente"]>0) echo number_format($Interna7[$j]["suma_presidente"],0, ',', '.') ?></div></td>
        <td width="101" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo $Interna7[$j]["color"]; ?>" ><div align="center"><?php if ($Interna7[$j]["suma_presidente"]>0) echo number_format($Interna7[$j]["porcentaje_presidente"],1, ',', '.') . "%" ?> </div></td>
       
        <td width="125" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna7[$j]["color"]; ?>" ><div align="center"><?php if ($Interna7[$j]["suma_diputado"]>0) echo number_format($Interna7[$j]["suma_diputado"],0, ',', '.') ?></div></td>
        <td width="87" bgcolor="#FFFFFF" class="EstiloVotosInterna" style="vertical-align: middle;color:<?php echo  $Interna7[$j]["color"]; ?>" ><div align="center"><?php if ($Interna7[$j]["suma_diputado"]>0) echo number_format($Interna7[$j]["porcentaje_diputado"],1, ',', '.') . "%" ?> </div></td>
    </tr>
      
	  <?php	
	  } // for interna 2 
     } // if ($Partido[$i] ['interna']==6)		 	 
	 
   } // for principal ?>
 </table>

 <div style="background:#E9ECEF; height:10px"> </div>
 <div style="background:#016EA6; height:10px"> </div>
 <br />
 <span>Copyright &copy; <?php echo date('Y')?> - Partido Justicialista La Pampa </span> 

</div>

</body>
</html>