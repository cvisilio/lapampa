
<?php
if (!isset($_SESSION)) {
  session_start();
}


$MM_authorizedUsers = "1,2,3";
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

include('Connections/conexionUsuarios.php');


if (!isset($_POST['menu']))
 {
 $theValue = $_SESSION['condicion']; 
 }
else
 {
 $theValue = $_POST['menu'];
 }

$_SESSION['condicion'] = $theValue;

$condicion = $theValue;

if ($condicion==1)
 $cantidadmesas1=49;
else
  $cantidadmesas1=187;

$sel_consulta="select count(*) as cantidadmesas, sum(LI) as SumaLI, sum(LC) as SumaLC,sum(LJ) as SumaLJ, sum(KI) as SumaKI, sum(KC) as SumaKC, sum(KJ) as SumaKJ, sum(NI) as SumaNI, sum(NC) as SumaNC, sum(NJ) as SumaNJ, sum(CI) as SumaCI, sum(CC) as SumaCC, sum(CJ) as SumaCJ, sum(BI) as SumaBI, sum(BC) as SumaBC, sum(BJ) as SumaBJ, sum(OI) as SumaOI, sum(OC) as SumaOC, sum(OJ) as SumaOJ, sum(BlancosI) as SumaBlancosI, sum(BlancosC) as SumaBlancosC, sum(BlancosJ) as SumaBlancosJ from mesas WHERE mesas.Escrutada='S' and mesas.Categoria='".$condicion."'";

 $sql=mysqli_query($con,$sel_consulta);
 $rw=mysqli_fetch_array($sql);

$CantidadMesasEscrutadas=0;
$CantidadMesasEscrutadas = $rw['cantidadmesas'];

if ($CantidadMesasEscrutadas > 0)
{
$PorcentajeMesasEscrutadas = (($CantidadMesasEscrutadas * 100) / $cantidadmesas1); //afiliados 49 mesas y 211 Independientes

$SumaLI=$rw['SumaLI'];
$SumaLC=$rw['SumaLC'];
$SumaLJ=$rw['SumaLJ'];

$SumaKI=$rw['SumaKI'];
$SumaKC=$rw['SumaKC'];
$SumaKJ=$rw['SumaKJ'];

$SumaNI=$rw['SumaNI'];
$SumaNC=$rw['SumaNC'];
$SumaNJ=$rw['SumaNJ'];

$SumaCI=$rw['SumaCI'];
$SumaCC=$rw['SumaCC'];
$SumaCJ=$rw['SumaCJ'];

$SumaBI=$rw['SumaBI'];
$SumaBC=$rw['SumaBC'];
$SumaBJ=$rw['SumaBJ'];

$SumaOI=$rw['SumaOI'];
$SumaOC=$rw['SumaOC'];
$SumaOJ=$rw['SumaOJ'];

$SumaBlancosI=$rw['SumaBlancosI'];
$SumaBlancosC=$rw['SumaBlancosC'];
$SumaBlancosJ=$rw['SumaBlancosJ'];

$TotalPositivosIntendente = $SumaLI + $SumaKI + $SumaNI + $SumaCI + $SumaBI + $SumaOI+ $SumaBlancosI;
$TotalPositivosConcejal = $SumaLC + $SumaKC + $SumaNC + $SumaCC + $SumaBC + $SumaOC+ $SumaBlancosC;
$TotalPositivosJuezPaz = $SumaLJ + $SumaKJ + $SumaNJ + $SumaCJ + $SumaBJ + $SumaOJ+ $SumaBlancosJ;

$mensaje= "";

if ($TotalPositivosIntendente > 0){
 if ($SumaLI > 0){
  $PorcentajeLI = ($SumaLI * 100) / $TotalPositivosIntendente;}
 if ($SumaLC > 0){
  $PorcentajeLC = ($SumaLC * 100) / $TotalPositivosConcejal;}
 if ($SumaLJ > 0){
  $PorcentajeLJ = ($SumaLJ * 100) / $TotalPositivosJuezPaz;}
 
 
 if ($SumaKI > 0){
  $PorcentajeKI = ($SumaKI * 100) / $TotalPositivosIntendente;}
 if ($SumaKC > 0){
  $PorcentajeKC = ($SumaKC * 100) / $TotalPositivosConcejal;}
 if ($SumaKJ > 0){
  $PorcentajeKJ = ($SumaKJ * 100) / $TotalPositivosJuezPaz;}
  
 if ($SumaNI > 0){
  $PorcentajeNI = ($SumaNI * 100) / $TotalPositivosIntendente;}
 if ($SumaNC > 0){
  $PorcentajeNC = ($SumaNC * 100) / $TotalPositivosConcejal;}
 if ($SumaNJ > 0){
  $PorcentajeNJ = ($SumaNJ * 100) / $TotalPositivosJuezPaz;}
  
 if ($SumaCI > 0){
  $PorcentajeCI = ($SumaCI * 100) / $TotalPositivosIntendente;}
 if ($SumaCC > 0){
  $PorcentajeCC = ($SumaCC * 100) / $TotalPositivosConcejal;}
 if ($SumaCJ > 0){
  $PorcentajeCJ = ($SumaCJ * 100) / $TotalPositivosJuezPaz;}
  
 if ($SumaBI > 0){
  $PorcentajeBI = ($SumaBI * 100) / $TotalPositivosIntendente;}
 if ($SumaBC > 0){
  $PorcentajeBC = ($SumaBC * 100) / $TotalPositivosConcejal;}
 if ($SumaBJ > 0){
  $PorcentajeBJ = ($SumaBJ * 100) / $TotalPositivosJuezPaz;}
  
 if ($SumaOI > 0){
  $PorcentajeOI = ($SumaOI * 100) / $TotalPositivosIntendente;}
 if ($SumaOC > 0){
  $PorcentajeOC = ($SumaOC * 100) / $TotalPositivosConcejal;}
 if ($SumaOJ > 0){
  $PorcentajeOJ = ($SumaOJ * 100) / $TotalPositivosJuezPaz;}
  
 if ($SumaBlancosI > 0){
  $PorcentajeBlancosI = ($SumaBlancosI * 100) / $TotalPositivosIntendente;}
 if ($SumaBlancosC > 0){
  $PorcentajeBlancosC = ($SumaBlancosC * 100) / $TotalPositivosConcejal;}
 if ($SumaBlancosJ > 0){
  $PorcentajeBlancosJ = ($SumaBlancosJ * 100) / $TotalPositivosJuezPaz;}     
  
 /*
 $users[] = array('lista' => '2', 'suma' => $SumaL2);
$users[] = array('lista' => '22', 'suma' => $SumaL22);
$users[] = array('lista' => '190', 'suma' => $SumaL190);
$users[] = array('lista' => '501', 'suma' => $SumaL501);
$users[] = array('lista' => '502', 'suma' => $SumaL502);
$users[] = array('lista' => '503', 'suma' => $SumaL503);


foreach ($users as $key => $row) {
    $aux[$key] = $row['suma'];
}

array_multisort($aux, SORT_DESC, $users); 

  $valor1= $users[0]['suma'];
  $valor2= $users[1]['suma'];
  $valor3= $users[2]['suma'];
 
 $mitad1= $valor1 / 2;
 
 if ($valor3 > $mitad1)
  $mensaje = "Entra: un Diputado Lista " . $users[0]['lista'] . ", un Diputado Lista " . $users[1]['lista']. " y un Diputado Lista " . $users[2]['lista'];
 else
  $mensaje = "Entran: dos Diputados Lista " .$users[0]['lista'] . " y un Diputado Lista " . $users[1]['lista']; 
   	 

 
 
 
 */ 
  
  
// $Porcentaje= $PorcentajeL2A +  $PorcentajeL2C +  $PorcentajeL2K +  $PorcentajeL2P +  $PorcentajeL2V + PorcentajeL22K + PorcentajeL190A + PorcentajeL501S + PorcentajeL501G + PorcentajeL502A + PorcentajeL503B + PorcentajeL503M +PorcentajeL503T; 

$Porcentaje = 100;
//$PorcentajeDiutado = 100;




//+  $PorcentajeL5Dip + $PorcentajeL6Dip + $PorcentajeL7Dip
 //$PorcentajeIntendente=  $PorcentajeL1Int +  $PorcentajeL2Int +  $PorcentajeL3Int +  $PorcentajeL4Int+  $PorcentajeL5Int + $PorcentajeL6Int + $PorcentajeL7Int; 

  
  }
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta charset=ISO-8859-1>
<meta http-equiv="refresh" content="60;URL=http://www.lapampaperonista.com.ar/i2019/verestadistica.php">
<title>Estad&iacute;sticas Elecciones Santa Rosa</title>
<style type="text/css">
<!-- //color Verde= #003300 //color Azul=  #003366 //Naranja = #FF0000

.Estilo1 {
	font-size: 24px;
	font-weight: bold;
}
.Estilo8 {color: #FF0033; font-weight: bold; }
.Estilo9 {color: #000066}
.EstiloTotales {font-size: 26px; color:#000033; font-weight: bold; font-family:Verdana, Arial, Helvetica, sans-serif }

.EstiloPorcentajeTotales{font-size: 26px; color:#999999; font-weight: bold; font-family:Verdana, Arial, Helvetica, sans-serif }
.EstiloListas {font-size: 24px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000 }

.EstiloListaBlanca {font-size: 18px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF }

.EstiloListaNegra {font-size: 18px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000 }

.EstiloVotos {font-size: 24px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000 }

.EstiloPorcentajeRojo {font-size: 23px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#FF3333 }

.EstiloPorcentajeAzul {font-size: 23px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#003399}


.EstiloVotosListaBlanca {font-size: 23px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#FFFFFF }

.EstiloVotosListaNegra {font-size: 24px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333}
.Estilo151 {
	color: #FF0033;
	font-weight: bold;
	font-size: 20px;
}
.Estilo152 {font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; color: #003366;}
.Estilo156 {font-size: 16px}
.Estilo157 {font-weight: bold; color: #003366;font-family: Verdana, Arial, Helvetica, sans-serif;}
.Estilo162 {font-size: 22px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; }

.Estilo170 {
	font-size: 21px;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color:#999999; /* #003399*/
}
.Estilo176 {font-size: 16px; font-weight: bold; 
font-family: Verdana, Arial, Helvetica, sans-serif;
}

.EstiloTotalesNegro {font-size: 26px; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif; }
.Estilo178 {
	color: #003300
}
.Estilo179 {
	font-size: 22px;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #FF3300;
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

</head>

<body>

<div align="center" style="width:96%;margin-left:2%;border:inset;border-color:#CCCCCC">
<div align="center">
<table width="100%" border="0">
<tr>
<td width="10%"> <a href="verestadistica.php" class="Estilo176">&lt;&lt;Volver</a> </td>
 <td width="81%"> <p align="center" class="Estilo8"><img src="fotos/escudo.jpg" width="37" height="41" />
    <?php 
	
$MinimoValor =1000;

?></p> </td> <td width="9%"> </td>

</tr></table>

  <table width="100%" height="77" border="0">
  <tr>
    <td width="50%" height="20">
      <div align="left" class="Estilo151">
        <div align="center" class="Estilo9">
          <div align="center" class="Estilo152"> Mesas Escrutadas:        <span class="Estilo157"><?php echo $CantidadMesasEscrutadas;?></span> &nbsp; &nbsp; <?php echo number_format($PorcentajeMesasEscrutadas,1, ',', '.') . "%"; ?>  </div>
        </div>
     </div></td>
     <td width="50%"><form action="ver_estadisticas_condicion.php" method="post" name="form1" class="Estilo156" id="form1">
  <div align="center"><span class="Estilo157"> Mesas:</span>
      <select name="menu">
            <option value="1" <?php if($condicion==1) echo "selected"; ?>>Afiliados  </option>
	      <option value="2" <?php if($condicion==2) echo "selected"; ?>>Independientes  </option>
	          
    </select>
    <input type="submit" name="button" id="button" value="Ver" />
  </div>

</form></td>
     
    </tr> 
    
    <?php if ($condicion ==1)
	  $texto_condicion=" Afiliados";
	 else
	  $texto_condicion=" Independientes";
	?>
    
   <tr>
      <th height="20" colspan="2" scope="row"><br />
        <div align="center" class="Estilo170">Datos de Mesas<?php echo $texto_condicion; ?></div> </th>
    </tr> 
  </table>
  
  <div style="height:10px"> </div>
</div>

<table width="100%" height="516" border="0" align="center">
  <tr bgcolor="#339966">
    <td height="30"  bgcolor="#CCCCCC" class="Estilo176"><div align="center"><strong>Frente</strong></div></td>
    <td height="30"  bgcolor="#CCCCCC" class="Estilo176"><div align="center"><strong>Lista</strong></div></td>
    <td bgcolor="#CCCCCC" class="Estilo176"><div align="center"><strong>Intendente</strong></div></td>
    
    <td bgcolor="#CCCCCC" class="Estilo176"><div align="center">%</div></td>
    <td bgcolor="#CCCCCC" class="Estilo176"><div align="center"><strong>Concejales</strong></div></td>
    <td bgcolor="#CCCCCC" class="Estilo176"><div align="center">%</div></td>
    <td bgcolor="#CCCCCC" class="Estilo176"><div align="center">Juez de Paz</div></td>
    <td bgcolor="#CCCCCC" class="Estilo176"><div align="center">%</div></td>
  </tr>
  <tr bgcolor="#339966">
    <td width="326" height="58" bgcolor="#3366FF" class="EstiloListas">&nbsp;Todos Somos Santa Rosa</td>
    <td width="45" height="58" bgcolor="#3366FF" class="EstiloListas"><div align="center"> L</div></td>
    <td width="119" bgcolor="#3366FF" class="EstiloVotos"><div align="center"><?php echo number_format($SumaLI,0, ',', '.') ?></div></td>
    <td width="89" bgcolor="#3366FF" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeLI,1, ',', '.') . "%" ?> </div></td>
    <td width="118" bgcolor="#3366FF" class="EstiloVotos"><div align="center"><?php echo number_format($SumaLC,0, ',', '.') ?></div></td>
    <td width="89" bgcolor="#3366FF" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeLC,1, ',', '.') . "%" ?> </div></td>
    <td width="100" bgcolor="#3366FF" class="EstiloVotos"><div align="center"><?php echo number_format($SumaLJ,0, ',', '.') ?></div></td>
    <td width="94" bgcolor="#3366FF" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeLJ,1, ',', '.') . "%" ?> </div></td>
  </tr>
  
  
  <tr bgcolor="#339966">
    <td height="57" bgcolor="#CC99FF" class="EstiloListas">&nbsp;Unidad Ciudadana</td>
    <td height="57" bgcolor="#CC99FF" class="EstiloListas"><div align="center">K</div></td>
    <td bgcolor="#CC99FF" class="EstiloVotos"><div align="center"><?php echo number_format($SumaKI,0, ',', '.') ?></div></td>
    <td bgcolor="#CC99FF" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeKI,1, ',', '.') . "%" ?> </div></td>
    <td bgcolor="#CC99FF" class="EstiloVotos"><div align="center"><?php echo number_format($SumaKC,0, ',', '.') ?></div></td>
    <td bgcolor="#CC99FF" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeKC,1, ',', '.') . "%" ?> </div></td>
    <td bgcolor="#CC99FF" class="EstiloVotos"><div align="center"><?php echo number_format($SumaKJ,0, ',', '.') ?></div></td>
    <td bgcolor="#CC99FF" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeKJ,1, ',', '.') . "%" ?> </div></td>
  </tr>
  <tr bgcolor="#339966">
    <td height="55" bgcolor="#FF3366" class="EstiloListas">&nbsp;F. Peronista Barrial</td>
    <td height="55" bgcolor="#FF3366" class="EstiloListas"><div align="center">&nbsp;B</div></td>
    <td bgcolor="#FF3366" class="EstiloVotos"><div align="center"><?php echo number_format($SumaBI,0, ',', '.') ?></div></td>
    <td bgcolor="#FF3366" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeBI,1, ',', '.') . "%" ?> </div></td>
    <td bgcolor="#FF3366" class="EstiloVotos"><div align="center"><?php echo number_format($SumaBC,0, ',', '.') ?></div></td>
    <td bgcolor="#FF3366" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeBC,1, ',', '.') . "%" ?> </div></td>
    <td bgcolor="#FF3366" class="EstiloVotos"><div align="center"><?php echo number_format($SumaBJ,0, ',', '.') ?></div></td>
    <td bgcolor="#FF3366" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeBJ,1, ',', '.') . "%" ?> </div></td>
  </tr>
 
 <tr bgcolor="#DFFFFF">
    <td height="53" bgcolor="#999999" class="EstiloListas"><div align="left">&nbsp;F. Justicialista Renovador<br />
    </div></td>
    <td height="53" bgcolor="#999999" class="EstiloListas"><div align="center">&nbsp;&Ntilde;</div></td>
    <td bgcolor="#999999" class="EstiloVotos"><div align="center"><?php echo number_format($SumaNI,0, ',', '.') ?></div></td>
    <td bgcolor="#999999" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeNI,1, ',', '.') . "%"?> </div></td>
    <td bgcolor="#999999" class="EstiloVotos"><div align="center"><?php echo number_format($SumaNC,0, ',', '.') ?></div></td>
    <td bgcolor="#999999" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeNC,1, ',', '.') . "%"?> </div></td>
    <td bgcolor="#999999" class="EstiloVotos"><div align="center"><?php echo number_format($SumaNJ,0, ',', '.') ?></div></td>
    <td bgcolor="#999999" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeNJ,1, ',', '.') . "%"?> </div></td>
 </tr>
  <tr bgcolor="#FF3366">
    <td height="46" bgcolor="#FFCC66" class="EstiloListas">&nbsp;Militancia 44</td>
    <td height="46" bgcolor="#FFCC66" class="EstiloListas"><div align="center">&nbsp;O</div></td>
    <td bgcolor="#FFCC66" class="EstiloVotos"><div align="center"><?php echo number_format($SumaOI,0, ',', '.') ?></div></td>
    <td bgcolor="#FFCC66" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeOI,1, ',', '.') . "%" ?> </div></td>
    <td bgcolor="#FFCC66" class="EstiloVotos"><div align="center"><?php echo number_format($SumaOC,0, ',', '.') ?></div></td>
    <td bgcolor="#FFCC66" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeOC,1, ',', '.') . "%" ?> </div></td>
    <td bgcolor="#FFCC66" class="EstiloVotos"><div align="center"><?php echo number_format($SumaOJ,0, ',', '.') ?></div></td>
    <td bgcolor="#FFCC66" class="EstiloVotos"><div align="center"><?php echo number_format($PorcentajeOJ,1, ',', '.') . "%" ?> </div></td>
  </tr>
  <tr bgcolor="#DFFFFF">
    <td height="50" bgcolor="#009999" class="EstiloListas"><div align="left">&nbsp;Pampeanos y Peronistas</div></td>
    <td height="50" bgcolor="#009999" class="EstiloListas"><div align="center">&nbsp;C </div></td>
    <td bgcolor="#009999" class="EstiloVotos"><div align="center"><?php echo number_format($SumaCI,0, ',', '.') ?></div></td>
    <td bgcolor="#009999" class="EstiloVotos"><div align="center"> <?php echo number_format($PorcentajeCI,1, ',', '.'). "%"?> </div></td>
    <td bgcolor="#009999" class="EstiloVotos"><div align="center"><?php echo number_format($SumaCC,0, ',', '.') ?></div></td>
    <td bgcolor="#009999" class="EstiloVotos"><div align="center"> <?php echo number_format($PorcentajeCC,1, ',', '.'). "%"?> </div></td>
    <td bgcolor="#009999" class="EstiloVotos"><div align="center"><?php echo number_format($SumaCJ,0, ',', '.') ?></div></td>
    <td bgcolor="#009999" class="EstiloVotos"><div align="center"> <?php echo number_format($PorcentajeCJ,1, ',', '.'). "%"?> </div></td>
  </tr>
  
  <tr bgcolor="#DFFFFF">
    <td bgcolor="#DDDDDD" class="EstiloListas"><div align="left">&nbsp;Votos en Blanco</div></td>
    <td bgcolor="#DDDDDD" class="EstiloListas">&nbsp;</td>
    <td bgcolor="#DDDDDD" class="EstiloVotos"><div align="center"><?php echo number_format($SumaBlancosI,0, ',', '.') ?></div></td>
    <td bgcolor="#DDDDDD" class="EstiloVotos"><div align="center"> <?php echo number_format($PorcentajeBlancosI,1, ',', '.'). "%"?> </div></td>
    <td bgcolor="#DDDDDD" class="EstiloVotos"><div align="center"><?php echo number_format($SumaBlancosC,0, ',', '.') ?></div></td>
    <td bgcolor="#DDDDDD" class="EstiloVotos"><div align="center"> <?php echo number_format($PorcentajeBlancosC,1, ',', '.'). "%"?> </div></td>
    <td bgcolor="#DDDDDD" class="EstiloVotos"><div align="center"><?php echo number_format($SumaBlancosJ,0, ',', '.') ?></div></td>
    <td bgcolor="#DDDDDD" class="EstiloVotos"><div align="center"> <?php echo number_format($PorcentajeBlancosJ,1, ',', '.'). "%"?> </div></td>
  </tr>
  
  <tr>
    <td height="71" colspan="2" bgcolor="#FFFFFF" class="EstiloTotalesNegro">&nbsp;Totales</td>
    <td width="119" bgcolor="#FFFFFF" class="EstiloTotalesNegro"><div align="center"> <?php echo number_format($TotalPositivosIntendente,0, ',', '.')  ?> </div></td>
    <td width="89" bgcolor="#FFFFFF" class="EstiloPorcentajeTotales"><div align="center"><?php echo number_format($Porcentaje,0, ',', '.')  . "%"  ?></div></td>
    <td width="118" bgcolor="#FFFFFF" class="EstiloPorcentajeTotales"><div align="center"> <?php echo number_format($TotalPositivosConcejal,0, ',', '.')  ?> </div></td>
    <td width="89" bgcolor="#FFFFFF" class="EstiloPorcentajeTotales"><div align="center"><?php echo number_format($Porcentaje,0, ',', '.')  . "%"  ?></div></td>
    <td width="100" bgcolor="#FFFFFF" class="EstiloPorcentajeTotales"><div align="center"> <?php echo number_format($TotalPositivosJuezPaz,0, ',', '.')  ?> </div></td>
    <td width="94" bgcolor="#FFFFFF" class="EstiloPorcentajeTotales"><div align="center"><?php echo number_format($Porcentaje,0, ',', '.')  . "%"  ?></div></td>
  </tr>
  <tr>
    <td colspan="8" bgcolor="#FFFFFF"><hr width="100%" />    </td>
  </tr>
  <tr>
    <td colspan="8"><span class="Estilo157"> <?php //echo $mensaje; ?>
    </span></td>
  </tr>
</table>
<div style="height:5px"> </div>

</div>
</body>
</html>
