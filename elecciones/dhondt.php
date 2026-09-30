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

include('Connections/conexionUsuarios.php');

 $TotalesG=0;
 $TotalesI=0;
 $PorcentajeMesasEscrutadas=0; 

 $Listas[1]["nombre"]=""; 
 $Listas[2]["nombre"]="";  
 $Listas[3]["nombre"]=""; 
 $Listas[4]["nombre"]="";
 $Listas[5]["nombre"]=""; 
 $Listas[6]["nombre"]=""; 
 $Listas[7]["nombre"]="";  
 $Listas[8]["nombre"]=""; 
 $Listas[9]["nombre"]="";
 $Listas[10]["nombre"]=""; 
 
 $Listas[1]["partido"]="FREJUPA";
 $Listas[2]["partido"]="Juntos por el Cambio";
 $Listas[3]["partido"]="Comunidad Organizada";
 $Listas[4]["partido"]="Org. Civica";
 $Listas[5]["partido"]="F. de Izq. y Trabajadores";
 $Listas[6]["partido"]="Desde el Pie";
 $Listas[7]["partido"]="MOFEPA";
 $Listas[8]["partido"]="Part. Libertario";
 $Listas[9]["partido"]="U. Vecinal";
 $Listas[10]["partido"]="Junta. Vecinal";
    
 $Listas[1]["color"]= "#11285C"; // Azul
 $Listas[2]["color"]= "#FF6600";  // Amarillo
 $Listas[3]["color"]= "#333333"; // GRIS OSCURO
 $Listas[4]["color"]= "#CC3333";   // 
 $Listas[5]["color"]= "#f00000"; // Rojo
 $Listas[6]["color"]= "#CC3333"; // Celeste
 $Listas[7]["color"]= "#CCC000";
 $Listas[8]["color"]= "#000000";
 $Listas[9]["color"]= "#CC3333";
 $Listas[10]["color"]= "#CC3333"; // Gris
 
 
 
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

	$sel_consulta="select count(*) as cantidadmesas, sum(L1I) as SumaL1I, sum(L1DP) as SumaL1DP, sum(L1G) as SumaL1G,sum(L2I) as SumaL2I, sum(L2DP) as SumaL2DP, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3DP) as SumaL3DP, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4DP) as SumaL4DP, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5DP) as SumaL5DP, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6DP) as SumaL6DP, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7DP) as SumaL7DP, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8DP) as SumaL8DP, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9DP) as SumaL9DP, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10DP) as SumaL10DP, sum(L10G) as SumaL10G from mesas_gobernador_2023 WHERE Escrutada='S'";

 $sql=mysqli_query($con,$sel_consulta);
 $rw=mysqli_fetch_array($sql);

 $CantidadMesasEscrutadas=0;
 $CantidadMesasEscrutadas = $rw['cantidadmesas'];
 
 for ($i=1;$i<=10;$i++){
  $Listas[$i]["suma_diputado"]=0;
 
 }

 if ($CantidadMesasEscrutadas > 0)
 {
 $PorcentajeMesasEscrutadas = (($CantidadMesasEscrutadas * 100) / 889);

 $SumaL1DP=$rw['SumaL1DP'];
  
 $SumaL2DP=$rw['SumaL2DP'];
 
 $SumaL3DP=$rw['SumaL3DP'];
 
 $SumaL4DP=$rw['SumaL4DP'];
  
 $SumaL5DP=$rw['SumaL5DP'];
  
 $SumaL6G=$rw['SumaL6G'];
 
 $SumaL7DP=$rw['SumaL7DP'];
 
 $SumaL8DP=$rw['SumaL8DP'];
  
 $SumaL9DP=$rw['SumaL9DP'];
 
 $SumaL10DP=$rw['SumaL10DP'];
  
 
 $mensaje= "";
  
 
  $Porcentaje = 100;
  
  $cantidad_frejupa=0;
  $cantidad_cambiemos=0;
  $cantidad_comunidad=0;
  $cantidad_libertarios=0;
  $cantidad_mofepa=0;
  $cantidad_izquierda=0;
  

$diputados[1][1]='PÉREZ ARAUJO, HERNÁN';
$diputados[1][2]='ROBLEDO, LILIANA VANESA';
$diputados[1][3]='MARIN, ESPARTACO';
$diputados[1][4]='ALONSO, MARÍA LUZ';
$diputados[1][5]='LOVERA, DANIEL ANÍBAL';
$diputados[1][6]='LARRETA, MARÍA SILVIA';
$diputados[1][7]='ORTIZ GARCÍA, PEDRO FEDERICO';
$diputados[1][8]='PÁEZ, MARCELA LILIA ANA';
$diputados[1][9]='BALSA, MARTÍN HORACIO';
$diputados[1][10]='SOSA, NOELIA JORGELINA';
$diputados[1][11]='BARRIONUEVO, JUAN RAMÓN';
$diputados[1][12]='GIUSSI, ANA CAROLINA';
$diputados[1][13]='MONTES DE OCA, CÉSAR OSCAR';
$diputados[1][14]='GEORGE, PATRICIA VIVIANA';
$diputados[1][15]='NICANOFF, LEÓN ALEXIS';
$diputados[1][16]='CAIMARI, LILIA ANA MARTA';
$diputados[1][17]='IGNASZEWSKI, FEDERICO ERNESTO';
$diputados[1][18]='SILVESTRO, ARACELI ANA';
$diputados[1][19]='MIGUEZ MARTÍN, CARLOS ALBERTO';
$diputados[1][20]='SALVINI, MARTA GRACIELA';
$diputados[1][21]='GIRAUDO, RODRIGO MAURO';
$diputados[1][22]='CAMINOS, SILVIA BEATRIZ';
$diputados[1][23]='ROBLEDO, FRANCO DANIEL';
$diputados[1][24]='CÁCERES, MARÍA EUGENIA';
$diputados[1][25]='DRAEGER, RODRIGO EMANUEL';
$diputados[1][26]='HEICK, XIOMARA ANABEL';
$diputados[1][27]='ROBLEDO, FEDERICO NICOLÁS';
$diputados[1][28]='CAMON, MARÍA SOFÍA';
$diputados[1][29]='PALAVECINO, LUCAS ALEJANDRO MARCELO';
$diputados[1][30]='JUNQUERA RAMOS, MARÍA LUJAN';
$diputados[2][1]='ALTOLAGUIRRE, HIPÓLITO GUSTAVO "POLI"';
$diputados[2][2]='TRAPAGLIA, MARÍA LAURA';
$diputados[2][3]='TORROBA, JAVIER';
$diputados[2][4]='VIARA, NOELIA SUSANA';
$diputados[2][5]='AGUILAR, JULIÁN OSCAR "PANCHO"';
$diputados[2][6]='RIVAS, CELESTE FANY';
$diputados[2][7]='PREGNO, SERGIO HEBER';
$diputados[2][8]='MOTA, ROMINA PAOLA';
$diputados[2][9]='JUAN, ENRIQUE MANUEL';
$diputados[2][10]='CUADRADO, GISELA VIVIANA';
$diputados[2][11]='LAZARIC, LUCAS ANDRÉS';
$diputados[2][12]='VALDERRAMA CALVO, MARÍA ANDREA';
$diputados[2][13]='TRABA, MATÍAS FRANCISCO';
$diputados[2][14]='ANCIN, NIDIA LILIAN';
$diputados[2][15]='PRIETO, RUBEN DARÍO "NEGRO"';
$diputados[2][16]='RUIZ, LORENA PAOLA';
$diputados[2][17]='COULY, JAVIER RAÚL HORACIO';
$diputados[2][18]='KAIL, DANIELA ADRIANA';
$diputados[2][19]='GALLOTTI MARTÍNEZ, BAUTISTA';
$diputados[2][20]='MÁRQUEZ, RENATA';
$diputados[2][21]='FOLMER, CELESTINO OSVALDO';
$diputados[2][22]='ECHEVESTE, MARÍA MAGDALENA';
$diputados[2][23]='CARDONI, FABRICIO MIGUEL';
$diputados[2][24]='VIVES, GABRIELA';
$diputados[2][25]='VERALLI, ARMANDO GUSTAVO ALEJANDRO';
$diputados[2][26]='CUEVAS, MARÍA EVA';
$diputados[2][27]='SOSTILLO, CARLOS PATRICIO';
$diputados[2][28]='ANDRADA, ALBINA ADELINA';
$diputados[2][29]='MACEDA, CARLOS LUCIANO';
$diputados[2][30]='PALERMO, LUCÍA FERNANDA';
$diputados[3][1]='FONSECA, SANDRA FABIANA';
$diputados[3][2]='ALIAGA SOUTO, CARLOS MAXIMILIANO';
$diputados[3][3]='ESCUDERO, LAURA ELENA';
$diputados[3][4]='MONTOYA, CARLOS MARTÍN "CAICO"';
$diputados[3][5]='MÉNDEZ, NATALIA SOLEDAD';
$diputados[3][6]='MUÑOZ, JOSÉ CARLOS';
$diputados[3][7]='SUÁREZ, MARÍA SOLEDAD';
$diputados[3][8]='LOGIOIO, OSCAR RUBENS';
$diputados[3][9]='ERAZUN, CRISTINA ANGÉLICA "PILI"';
$diputados[3][10]='BALLESTERO, JUAN CRUZ';
$diputados[3][11]='NOVACK, MARTA BEATRIZ';
$diputados[3][12]='CAROSIO, RICARDO OMAR';
$diputados[3][13]='LAGOS, MARTA ANDREA';
$diputados[3][14]='NOTAO, DOMINGO';
$diputados[3][15]='POLVARAN DÍAZ, SURAY';
$diputados[3][16]='CARPUZIS, ALEJANDRO DANIEL';
$diputados[3][17]='SUÁREZ, HAYDEE INÉS';
$diputados[3][18]='CASTRILLI, ALBERTO LUIS';
$diputados[3][19]='BOGADO, GLADYS ZULEMA';
$diputados[3][20]='MONTES DE OCA, JULIO CÉSAR';
$diputados[3][21]='MARTÍNEZ, ALICIA NOEMÍ';
$diputados[3][22]='ROMERO, LUIS ALBERTO';
$diputados[3][23]='SIMOUANG, SABINA BELÉN';
$diputados[3][24]='SOSA HIDALGO, CARLOS ALBERTO';
$diputados[3][25]='GUARDATTI, CAROLINA IVANA';
$diputados[3][26]='MEDERO, HILDO RODRIGO';
$diputados[3][27]='CAIROLA, MARÍA DEL CARMEN';
$diputados[3][28]='LUJÁN, FRANCO DAMIÁN';
$diputados[3][29]='BAUDIS, ALDANA YAEL';
$diputados[3][30]='PALOMEQUE, MAURO EZEQUIEL';
$diputados[4][1]='ARAGONES, RA�L OMAR OSVALDO';
$diputados[4][2]='PRIETO, ROSANA FABIANA';
$diputados[4][3]='RAMONDA, CRISTIAN MARCELO';
$diputados[4][4]='VEL�ZQUEZ, LILIANA ELISABET';
$diputados[4][5]='ARAGONES, CARLOS OSVALDO';
$diputados[4][6]='ROMERO, MARCELA M�NICA';
$diputados[4][7]='GIRABEL, FABIO ORLANDO';
$diputados[4][8]='G�MEZ, MAR�A BEL�N';
$diputados[4][9]='LEDESMA, DANIEL ADRI�N';
$diputados[4][10]='ALDERETE S�NCHEZ, LOURDES GABRIELA';
$diputados[4][11]='PRCHLIK, ROBERTO MIGUEL';
$diputados[4][12]='GONZ�LEZ, BARBARA MAIL�N';
$diputados[4][13]='BENSIMON, ROBERTO JOS� JUAN';
$diputados[4][14]='VIALE, ESTHER LEONOR';
$diputados[4][15]='QUIROZ, EMANUEL SERAF�N';
$diputados[4][16]='GONZ�LEZ CUBITO, M�NICA ROXANA';
$diputados[4][17]='DOM�NGUEZ, FERNANDO JOS�';
$diputados[4][18]='GONZ�LEZ, MARINA';
$diputados[4][19]='PE�IN, RODOLFO EZEQUIEL';
$diputados[4][20]='GONZ�LEZ, JOANA HAIDEE';
$diputados[4][21]='RODR�GUEZ, JAVIER ALEJANDRO';
$diputados[4][22]='OSES, SORAYA RAQUEL';
$diputados[4][23]='ARRIETA, CRISTIAN RUBEN';
$diputados[4][24]='LESCANO, ROC�O CAROLINA';
$diputados[4][25]='LEDO, RUBEN ANDR�S';
$diputados[4][26]='MACRETTI, ILDA';
$diputados[4][27]='PRCHLIK, JAROSLAV ROBERTO';
$diputados[4][28]='MARIN, VIVIANA BEATRIZ';
$diputados[4][29]='GIRABEL, ESPARTACO FRANCO IV�N';
$diputados[4][30]='OSES, D�BORA GRECIA';

   $cantidad_diputados=30;
   $j=1; 
	   for ($i=1;$i<=$cantidad_diputados;$i++){
	    $division =$SumaL1DP / $i;
	    $array_resultados[$j]['lista']="FREJUPA";
		$array_resultados[$j]['color']="#11285C";
	    $array_resultados[$j]['nombre']= $diputados[1][$i];
	    $array_resultados[$j]['diputado']=intval(floor($division)); // foor toma el entero
	    $j++;
	  }
	  
	   for ($i=1;$i<=$cantidad_diputados;$i++){
	    $division =$SumaL2DP / $i;
	    $array_resultados[$j]['lista']="JxC";
		$array_resultados[$j]['color']="#FF6600";
	    $array_resultados[$j]['nombre']= $diputados[2][$i];
	    $array_resultados[$j]['diputado']=intval(floor($division)); // foor toma el entero
	    $j++;
	  }
	  
	   for ($i=1;$i<=$cantidad_diputados;$i++){
	    $division =$SumaL3DP / $i;
	    $array_resultados[$j]['lista']="Com. Org.";
		$array_resultados[$j]['color']="#FF0000";
	    $array_resultados[$j]['nombre']= $diputados[3][$i];
	    $array_resultados[$j]['diputado']=intval(floor($division)); // foor toma el entero
	    $j++;		
	  }
	 
	  for ($i=1;$i<=$cantidad_diputados;$i++){
	    $division =$SumaL8DP / $i;
	    $array_resultados[$j]['lista']="Libertarios";
		$array_resultados[$j]['color']="#CCCCCC";
	    $array_resultados[$j]['nombre']= $diputados[4][$i];
	    $array_resultados[$j]['diputado']=intval(floor($division)); // foor toma el entero
	    $j++;		
	  }	
	  
	   for ($i=1;$i<=$cantidad_diputados;$i++){
	    $division =$SumaL7DP / $i;
	    $array_resultados[$j]['lista']="MOFEPA";
		$array_resultados[$j]['color']="#000000";
	    $array_resultados[$j]['nombre']= $diputados[7][$i];
	    $array_resultados[$j]['diputado']=intval(floor($division)); // foor toma el entero
	    $j++;		
	  }	
	  
	   for ($i=1;$i<=$cantidad_diputados;$i++){
	    $division =$SumaL5DP / $i;
	    $array_resultados[$j]['lista']="Izquierda";
		$array_resultados[$j]['color']="#000000";
	    $array_resultados[$j]['nombre']= $diputados[5][$i];
	    $array_resultados[$j]['diputado']=intval(floor($division)); // foor toma el entero
	    $j++;		
	  }	    
	  
	  
	  
	 foreach ($array_resultados as $key => $row) {
        $aux1[$key] = $row['diputado'];
      }
 
      array_multisort($aux1, SORT_DESC, $array_resultados);
 
 for($i=0;$i<=29;$i++){
  
  if ($array_resultados[$i]['lista']=="FREJUPA")
    $cantidad_frejupa++;
  elseif ($array_resultados[$i]['lista']=="JxC")
    $cantidad_cambiemos++;
  elseif ($array_resultados[$i]['lista']=="Com. Org.")
   $cantidad_comunidad++;
  elseif ($array_resultados[$i]['lista']=="Libertarios")
   $cantidad_libertarios++;
  elseif ($array_resultados[$i]['lista']=="MOFEPA")
   $cantidad_mofepa++;
  elseif ($array_resultados[$i]['lista']=="Izquierda")
   $cantidad_izquierda++;
    
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

.EstiloListas {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotos1 {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotos {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.Estilo152 {font-family: Verdana, Arial, Helvetica, sans-serif; color: #000000;font-size: 23px}
.Estilo_Selector {font-size: 16px}

.cabeceras {font-family: Verdana, Arial, Helvetica, sans-serif; color:#666666;font-size: 22px}

.cabecera_logo {font-size: 14px; font-weight: bold; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif; color:#ffffff;

}


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
       <p><span class="cabecera_logo"> 14 de mayo de <?php echo "2023";?> - Escrutinio Provisorio.</span></p>        </td> 
       
       <td width="51%" colspan="2" align="right" bgcolor="#016EA6"><p><img src="fotos/Escudo_del_Partido_Justicialista.png" width="54" height="67" style="margin-top:2px; margin-right:5px; margin-bottom:2px; float:left" /></p>
       <p><span class="cabecera_logo"> Datos de Toda La Provincia de La Pampa &nbsp;&nbsp;</span></p>      
       <p>
       
       <span class="cabecera_logo"><a style="color:#CCCCCC; margin-right:15px;" href="verestadistica.php">Inicio</a></span></p>       
       
       </td>  
   </tr>
   
    <tr bgcolor="#FFFFFF">
 <td colspan="4"> </td>
 </tr>
 
 <tr bgcolor="#000000">
 <td>
 <div align="left"><span style="color:#39ff14;" class="cabeceras"> <?php echo "Cantidad Dip. FREJUPA: " .
  $cantidad_frejupa; ?> </span></div>
  
  </td>
 
 <td>
 <div align="left"><span style="color:#39ff14;" class="cabeceras"> <?php echo "Cantidad Dip. JxC: " .
  $cantidad_cambiemos; ?> </span></div>
  
  </td>
 
 <td>
 <div align="left"><span style="color:#39ff14;" class="cabeceras"> <?php if($cantidad_comunidad>0) echo "Cantidad Dip. Com. Org: " .
  $cantidad_comunidad; ?> </span></div>
 </td>

 <td>
 <div align="left"><span style="color:#39ff14;" class="cabeceras"> <?php if($cantidad_libertarios>0) echo "Cantidad Dip. Libertarios: " .
  $cantidad_libertarios; if($cantidad_mofepa>0) echo " Cant. Dip. MOFEPA: " .
  $cantidad_mofepa; if($cantidad_izquierda>0) echo " Cant. Dip. Izquierda: " .
  $cantidad_izquierda; ?> </span></div>
 </td>
 

 </tr>
   
   
  </table>
 
 <table class="table" >
 <tr bgcolor="#E9ECEF">
   <td><div align="left"><span class="cabeceras">Posici&oacute;n</span> </div></td> 
 
  <td> <div align="left"><span class="cabeceras">Partido</span></div></td> 
  <td><div align="left"><span class="cabeceras">Nombre</span></div></td>
  <
   
 </tr>
 
 
 <?php for($i=0;$i<=29;$i++){ ?>
   
 <tr bgcolor="#FFFFFF">
   <td><div align="left"><span style="color:<?php echo $array_resultados[$i]['color'];?>" class="cabeceras"><?php echo $i+1; ?>º</span> </div></td> 
   <td> <div align="left"><span style="color:<?php echo $array_resultados[$i]['color'];?>" class="cabeceras"><?php echo $array_resultados[$i]['lista']; ?></span></div></td> 
   <td><div align="left"><span style="color:<?php echo $array_resultados[$i]['color'];?>" class="cabeceras"><?php echo $array_resultados[$i]['nombre']; ?></span></div></td>
  
 </tr>
 
 <?php } ?>
 

 
</table> 


 <div style="background:#E9ECEF; height:10px"> </div>
 <div style="background:#016EA6; height:10px"> </div>
 <span>Copyright &copy; <?php echo date('Y')?> - Partido Justicialista La Pampa </span> 

</div>

</body>
</html>