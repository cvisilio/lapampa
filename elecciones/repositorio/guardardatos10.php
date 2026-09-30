<?php
if (!isset($_SESSION)) {
  session_start();
}


  $Listas[1]["nombre"]="P.S. Vamos con Vos";
  $Listas[1]["valor"]=$L1;
  
  $Listas[2]["nombre"]="Movimiento al Socialismo";
  $Listas[2]["valor"]=$L2;
               
  $Listas[3]["nombre"]="Frente de Todos";
  $Listas[3]["valor"]=$L3;

  $Listas[4]["nombre"]="Juntos Somos el Cambio";
  $Listas[4]["valor"]=$L4;
		     
  $Listas[5]["nombre"]="Frente de Izquierda";
  $Listas[5]["valor"]=$L5;
             
  $Listas[6]["nombre"]="Juntos Somos el Cambio";
  $Listas[6]["valor"]=$L6;
  
  $Listas[7]["nombre"]="";
  $Listas[7]["valor"]=$L7;
  
  $Listas[8]["nombre"]="";
  $Listas[8]["valor"]=$L8;
  
  $Listas[9]["nombre"]="";
  $Listas[9]["valor"]=$L9;
  
  $Listas[10]["nombre"]="";
  $Listas[10]["valor"]=$L10;
  
  $Listas[1]["lista"]="50";
  $Listas[2]["lista"]="200";
  $Listas[3]["lista"]="501";
  $Listas[4]["lista"]="502";
  $Listas[5]["lista"]="503";
  $Listas[6]["lista"]="";
  $Listas[7]["lista"]="";
  $Listas[8]["lista"]="";
  $Listas[9]["lista"]="";
  $Listas[10]["lista"]="";  	
 		 
			 
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
  
 if (isset($_POST["txtL1I"]))
  $L1I = $_POST["txtL1I"];
 else
  $L1I = 0;

 if (isset($_POST["txtL1G"]))
  $L1G = $_POST["txtL1G"];
 else
  $L1G = 0;

 if (isset($_POST["txtL2I"]))
  $L2I = $_POST["txtL2I"];
 else
  $L2I = 0;

 if (isset($_POST["txtL2G"]))
  $L2G = $_POST["txtL2G"];
 else
  $L2G = 0;

 if (isset($_POST["txtL3I"]))
  $L3I = $_POST["txtL3I"];
 else
  $L3I = 0;
 
 if (isset($_POST["txtL3G"]))
  $L3G = $_POST["txtL3G"];
 else
  $L3G = 0;

if (isset($_POST["txtL4I"]))
  $L4I = $_POST["txtL4I"];
 else
  $L4I = 0;   
 
 if (isset($_POST["txtL4G"]))
  $L4G = $_POST["txtL4G"];
 else
  $L4G = 0;   

 if (isset($_POST["txtL5I"]))
  $L5I = $_POST["txtL5I"];
 else
  $L5I = 0;   
  
 if (isset($_POST["txtL5G"]))
  $L5G = $_POST["txtL5G"];
 else
  $L5G = 0;   

 if (isset($_POST["txtL6I"]))
  $L6I = $_POST["txtL6I"];
 else
  $L6I = 0; 
  
 if (isset($_POST["txtL6G"]))
  $L6G = $_POST["txtL6G"];
 else
  $L6G = 0;   
   
 if (isset($_POST["txtL7I"]))
  $L7I = $_POST["txtL7I"];
 else
  $L7I = 0;
    
 if (isset($_POST["txtL7G"]))
  $L7G = $_POST["txtL7G"];
 else
  $L7G = 0;
 
 if (isset($_POST["txtL8I"]))
  $L8I = $_POST["txtL8I"];
 else
  $L8I = 0; 
 
 if (isset($_POST["txtL8G"]))
  $L8G = $_POST["txtL8G"];
 else
  $L8G = 0;   
 
 if (isset($_POST["txtL9I"]))
  $L9I = $_POST["txtL9I"];
 else
  $L9I = 0; 
 
 if (isset($_POST["txtL9G"]))
  $L9G = $_POST["txtL9G"];
 else
  $L9G = 0;   
 
 if (isset($_POST["txtL10I"]))
  $L10I = $_POST["txtL10I"];
 else
  $L10I = 0; 
 
 if (isset($_POST["txtL10G"]))
  $L10G = $_POST["txtL10G"];
 else
  $L10G = 0;          

$Usuario = "-";
$IP = $_SERVER["REMOTE_ADDR"]; 


$L1I =intval($L1I);
$L1G =intval($L1G);

$L2I =intval($L2I);
$L2G =intval($L2G);

$L3I =intval($L3I);
$L3G =intval($L3G);

$L4I =intval($L4I);
$L4G =intval($L4G);

$L5I =intval($L5I);
$L5G =intval($L5G);

$L6I =intval($L6I);
$L6G =intval($L6G);

$L7I =intval($L7I);
$L7G =intval($L7G);

$L8I =intval($L8I);
$L8G =intval($L8G);

$L9I =intval($L9I);
$L9G =intval($L9G);

$L10I =intval($L10I);
$L10G =intval($L10G);

$TotalesI= $L1I + $L2I + $L3I + $L4I + $L5I + $L6I + $L7I + $L8I + $L9I + $L10I;  
$TotalesG= $L1G + $L2G + $L3G + $L4G + $L5G + $L6G + $L7G + $L8G + $L9G + $L10G;

$Totales=$TotalesI + $TotalesG;

$eror= 0;
$cadenaerror= "";
$modifica = "Si";

$Cadenasql= "select entidades.Establecimiento,mesas.Escrutada, localidades.localidad, localidades.cargo_elecciones from mesas, entidades, localidades WHERE  mesas.CodigoEscuela=entidades.Id and mesas.CodigoLocalidad = localidades.id_loc_padron AND Mesa='".$Mesa."'";

 $sql1=mysqli_query($con,$Cadenasql);
 $rs_consulta1=mysqli_fetch_array($sql1);
   
 $cantidadRegistros = mysqli_num_rows($sql1);
 
if ($cantidadRegistros > 0){
 $Establecimiento = utf8_encode($rs_consulta1['Establecimiento']);
 $Localidad = utf8_encode($rs_consulta1['localidad']);
 
 $cargo_elecciones = $rs_consulta1['cargo_elecciones'];
 list($L1,$L2,$L3,$L4,$L5,$L6,$L7,$L8,$L9,$L10)=explode(";",$cargo_elecciones); 
 
 $L1="D,G"; // se hace de forma manual si sabemos que están todos los candidatos en todas las localidades
 $L2="D,G";
 $L3="D,G";
 $L4="D,G";
 $L5="D,G";
 $L6="D,G";
 $L7= "D,G";
 $L8="D,G";
 $L9="D,G";
 $L10 ="D,G"; 
   
 $Escrutada =$rs_consulta1['Escrutada']; 
 if ($Escrutada =="S")
 {
 
 $sql2=mysqli_query($con,"select * from mesas WHERE Mesa='".$Mesa."'");
 $rs_consulta=mysqli_fetch_array($sql2);

 $L1I=$rs_consulta['L1I'];
 $L1G=$rs_consulta['L1G'];
  
 $L2I=$rs_consulta['L2I'];
 $L2G=$rs_consulta['L2G'];
 
 $L3I=$rs_consulta['L3I'];
 $L3G=$rs_consulta['L3G'];

 $L4I=$rs_consulta['L4I'];
 $L4G=$rs_consulta['L4G'];

 $L5I=$rs_consulta['L5I'];
 $L5G=$rs_consulta['L5G'];
 
 $L6I=$rs_consulta['L6I'];
 $L6G=$rs_consulta['L6G'];
 
 $L7I=$rs_consulta['L7I'];
 $L7G=$rs_consulta['L7G'];

 $L8I=$rs_consulta['L8I'];
 $L8G=$rs_consulta['L8G'];

 $L9I=$rs_consulta['L9I'];
 $L9G=$rs_consulta['L9G'];

 $L10I=$rs_consulta['L10I'];
 $L10G=$rs_consulta['L10G'];
      
$IPGrabo = $rs_consulta['IP'];


 //$cadenaerror = "Ya ha sido Grabada desde IP: " . $IPGrabo;
 $eror= 1;
 
  
 if($_SESSION['MM_UserGroup'] == 1 or $_SESSION['MM_UserGroup'] == 2){ 
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


if ($eror <> 1){
$Escrutada="S";

$sel_actualizar1="UPDATE mesas SET L1I='".$L1I."', L1G='".$L1G."', L2I='".$L2I."', L2G='".$L2G."', L3I='".$L3I."', L3G='".$L3G."', L4I='".$L4I."', L4G='".$L4G."', L5I='".$L5I."', L5G='".$L5G."', L6I='".$L6I."', L6G='".$L6G."', L7I='".$L7I."', L7G='".$L7G."', L8I='".$L8I."', L8G='".$L8G."', L9I='".$L9I."', L9G='".$L9G."', L10I='".$L10I."', L10G='".$L10G."', Escrutada='".$Escrutada."', Usuario='".$Usuario."' WHERE Mesa='".$Mesa."'";

//$sel_actualizar1="UPDATE mesas SET Escrutada='S' WHERE Mesa='".$Mesa."'";

 $query = mysqli_query($con,$sel_actualizar1);
 
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



<div align="center">
  <table width="576" border="0" bgcolor="#E9E9E9">
    <tr>
      <td width="100" height="27"><div align="center" class="Estilo18"><strong>Mesa N&ordm;</strong></div></td>
      <td width="75"><div align="center" class="Estilo18"><strong><?php echo $Mesa;?></strong></div></td>
      <td width="356"><div align="center" class="Estilo18"><strong><?php echo $Cadena;?></strong> </div></td>
    </tr>
  </table>
</div>

<table width="419" height="286" border="1" align="center"  bgcolor="#E9E9E9">
  <tr>
    <td width="147" height="37"><div align="center" class="Estilo17">Lista</div></td>
    <td width="125"><div align="center" class="Estilo31">
      <div align="center" class="Estilo24 Estilo32">Senador</div>
    </div></td>
    <td width="125"><div align="center" class="Estilo31">
      <div align="center" class="Estilo24 Estilo32">Diputado</div>
    </div></td>
   
  </tr>
  
  <?php  
  $posI = strpos($L1, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L1, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
  
  <tr>
    <td height="37"><div align="left" class="Estilo18"><span><?php echo $Listas[1]['nombre']; ?></span></div></td>
        <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L1G; ?></div></td>
        <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L1I; ?></div></td>
   </tr>
 
 <?php } 
 
  $posI = strpos($L2, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L2, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><span><?php echo $Listas[2]['nombre']; ?></span></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L2G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L2I; ?></div></td>
    
  </tr>
 
 <?php } 
 
  $posI = strpos($L3, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L3, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
  
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[3]['nombre']; ?></div></td>
    <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L3G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L3I; ?></div></td>
  </tr>
  
  <?php } 
 
  $posI = strpos($L4, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L4, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
  
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[4]['nombre']; ?></div></td>
    <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L4G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L4I; ?></div></td>
  </tr>
 
 <?php } 
 
  $posI = strpos($L5, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L5, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[5]['nombre']; ?></div></td>
     <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L5G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L5I; ?></div></td>
  </tr>
 
 <?php } 
 
  $posI = strpos($L6, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L6, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[6]['nombre']; ?></div></td>
     <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L6G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L6I; ?></div></td>
  </tr>
 
 <?php } 
 
 $posI = strpos($L7, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L7, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[7]['nombre']; ?></div></td>
     <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L7G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L7I; ?></div></td>
  </tr>
 
 <?php } 
 
 $posI = strpos($L8, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L8, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[8]['nombre']; ?></div></td>
     <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L8G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L8I; ?></div></td>
  </tr>
 
 <?php } 
 
 $posI = strpos($L9, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L9, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[9]['nombre']; ?></div></td>
     <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L9G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L9I; ?></div></td>
  </tr>
 
 <?php } 
 
 $posI = strpos($L10, "D"); //I = Intendente G= Gobernador
  $posG = strpos($L10, "G"); 
 if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
  ?>
 
  <tr>
    <td height="15"><div align="left" class="Estilo18"><?php echo $Listas[10]['nombre']; ?></div></td>
     <td><div align="center" class="Estilo18"><?php if ($posG > -1) echo $L10G; ?></div></td>
   <td><div align="center" class="Estilo18"><?php if ($posI > -1) echo $L10I; ?></div></td>
  </tr>
 
 <?php } 
 
 ?>
  
</table>
<table width="288" height="114" border="1" align="center">
  <tr>
  
    <td width="122" height="108"> <div align="center">
    <?php
    if($modifica <> "No"){ ?>
     <form id="formulario" name="formulario" method="post" action="modificarmesa.php">
     <input id="codmesa" name="codmesa" value="<?php echo $Mesa?>" type="hidden">
  
     <input name="button" type="submit" class="Estilo28" id="button" value="Modificar">
     </form> </div><?php } ?></td> 
  <?php //if ($TotalesInt == $TotalesGob) { // La cantidad de Votos tiene que ser igual en una columna y en otra, en este caso la suma de votos a Intendente debe ser igual a la suma de votos a Gobernador, pero esto no quierre decir qeu las filas sean iguales, porque por ejemplo eun votante emite votos para Gobernador e Intendente ?> 
    <td width="108"><div align="center" class="Estilo5"><a href="cargarmesas.php">(Aceptar y Grabar)</a></div></td>
   <?php  // }  else {
   // $sel_actualizar="UPDATE mesas SET Escrutada='N' WHERE Mesa='$Mesa'";
   // $descriptor= mysql_select_db($_SESSION['database_conexionUsuarios'], $_SESSION['conexionUsuarios']);
  //  $rs_actualizar=mysql_query($sel_actualizar); 
    ?>
    <?php // }?>
   <td width="36"> <?php //echo $cadenaerror;?></td>
</tr>
</table>
</div>
<div align="center"></div>
</body>
</html>
