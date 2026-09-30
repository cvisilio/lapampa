<?php
if (!isset($_SESSION)) {
  session_start();
}

 require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

 use Regression\Matrix;
 use Regression\Regression;

 require_once('../Connections/conexionUsuarios.php'); 
 
 $Partido[1]["color"]= "#0066CC"; // Azul PJ
 $Partido[2]["color"]=  "#CC0099"; // Violeta - Milei
 
 $cantidad_mesas_minimas=8;


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
	
  $logoutGoTo = "../index.php";
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

$MM_restrictGoTo = "../index.php";
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Proyeccion</title>

<style type="text/css">
<!-- //color Verde= #003300 //color Azul=  #003366 //Naranja = #D8232A

.EstiloPartido {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotos1 {font-size: 21px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/
.Estilo_Selector {font-size: 16px}

.cabeceras {font-family: Verdana, Arial, Helvetica, sans-serif; color:#666666;font-size: 22px}

.cabecera_logo {font-size: 14px; font-weight: bold; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif; color:#ffffff;
}

.EstiloVotos {font-size: 26px; font-family: Verdana, Arial, Helvetica, sans-serif; color:#000000} /*24*/

.EstiloVotosInterna{font-size: 18px; font-weight: normal ; line-height: normal ;  
font-family: Verdana, Arial, Helvetica, sans-serif;}



<!--

-->
</style>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.0.0/dist/tf.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<style type="text/css">
<!--
.Estilo7 {
	color: #333333;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<div style="margin-top:10px"></div>

<div align="center" style="width:96%;margin-left:2%;border:inset;border-color:#E9ECEF">
<div align="center">
 
 <!-- <p align="center" class="Estilo8"><a target='_blank' href="https://eleccionesgenerales2019.lapampa.gob.ar/scripts/cgiip.exe/WService=Elecciones/EReLocCowr.htm?vPartido=1&SelectLocalidad=84&User=">Ver Resultados en Tribunal Electoral (clic aqu&iacute;)</a></p> -->
 <table width="100%" height="81" border="0">
 
   <tr>
     <td height="77" colspan="2" align="left" bgcolor="#016EA6"><p><img src="../fotos/escudo_la_pampa.png" width="65" height="70" style="margin-top:4px; margin-left:5px; margin-right:15px; float:left" /></p>
       <p><span class="cabecera_logo">Elecciones <?php echo "2023";?></span></p>
       <p><span class="cabecera_logo"> 19 de noviembre de <?php echo "2023";?> - Escrutinio Provisorio.</span></p>        </td> 
       
       <td width="51%" colspan="2" align="right" bgcolor="#016EA6"><p><img src="../fotos/Escudo_del_Partido_Justicialista.png" width="54" height="67" style="margin-top:2px; margin-right:5px; margin-bottom:2px; float:left" /></p>
       
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
  
 <div align="left"><a href="../verestadistica.php" class="btn btn-secondary btn-sm active" style="margin-right:5px; margin-left:3px; margin-top:16px" role="button" aria-pressed="true">Inicio</a> </div>
 
 <div class="cabeceras Estilo7"> Proyecci&oacute;n  </div>
 
 <br />

  <table class="table">
    <tr>
      <td width="20%"><div align="center"><strong>Localidad</strong></div></td>
      
      <th width="12%" style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><strong>PJ General</strong></div></th>
      <th width="9%" style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><strong>PJ Balotaje.</strong></div></th>
           
      <th width="13%" style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><strong>L. Avanza General.</strong></div></th>
      <th width="13%" style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><strong>L. Avanza Balotaje.</strong></div></th>
    </tr>
    <tr>
         
    
    <?php 
	$total_pj_anterior=0;
	$total_pj_general=0;
	$total_jxc_anterior=0;
	$total_jxc_general=0;
	$filas=0;
	$cadena_x1="";
	$cadena_y1="";
	
	$indice_fila_array_x=0; // las entradas para la regresion lineal multiple
	$indice_fila_array_y=0; // las salidas para la regresion lineal multiple
	$indice_fila_array_calcular=0; // el indice para guardar las mesas a calcular
	
   $query_mesas = "SELECT mesas.*,localidades.Localidad,localidades.id_loc_padron,localidades.cantidad_mesas FROM mesas, localidades WHERE mesas.CodigoLocalidad = localidades.id_loc_padron ORDER BY mesas.Id";

   $mesas_1=mysqli_query($con,$query_mesas);
  // $row_mesas=mysqli_fetch_array($mesas_1);

	$id_loc_padron_anterior=0;
	$localidad_anterior="";
	$cantidad_mesas=0; 
	$cantidad_escrutadas=0;	
	$Escrutadas="";
	$suma_calculo_libertad_avanza=0;
	 	
	while ($row_mesas = mysqli_fetch_assoc($mesas_1)) 
	 {
	 $Mesa=$row_mesas['Mesa'];
	 
	 $Escrutada=$row_mesas['Escrutada'];
	 
	 $id_loc_padron=$row_mesas['id_loc_padron'];  
	 
	 $sql="select * from mesas_generales_presidente_2023 where Mesa='$Mesa'";
	 
	 $query_resultados=mysqli_query($con,$sql);
	 $row_resultados=mysqli_fetch_array($query_resultados);
	 //$Codigo_Localidad_Mesa_Anterior=$row_resultados['CodigoLocalidad'];
	 
	 $suma_pj_anterior=$row_resultados['L3G'];
	 $suma_pj_general=$row_mesas['L1G'];
	 
	 $suma_jxc_anterior=$row_resultados['L1G'];
	 $suma_jxc_general=0;
	 
	 $suma_libertad_avanza_anterior=$row_resultados['L4G'];
	 $suma_libertad_avanza_general=$row_mesas['L2G'];
	 
	 $suma_hacemos_anterior=$row_resultados['L2G'];
	 $suma_hacemos_general=0;
	 
     $suma_izquierda_anterior=$row_resultados['L5G'];
	 $suma_izquierda_general=0;
	 
	 $suma_otros_anterior=0;
	
	
	if(($id_loc_padron == $id_loc_padron_anterior) or ($id_loc_padron_anterior==0))
    {	 	 
	 
	 if($Escrutada=="S")
	 {
	  $array_y_libertad_avanza[$indice_fila_array_y][]=$suma_libertad_avanza_general; //aca se guardan las salidas que es el resultado actual de cada mesa escrutada de los de libertad avanza. Es de tener en cuenta que se hace una prediccion por partido y por localidad
	  $array_y_PJ[$indice_fila_array_y][]=$suma_pj_general;
	  $array_y_JxC[$indice_fila_array_y][]=$suma_jxc_general;
	  $indice_fila_array_y++;
	 
	  $array_x[$indice_fila_array_x][0]=$suma_pj_anterior;
	  $array_x[$indice_fila_array_x][1]=$suma_jxc_anterior;
	  $array_x[$indice_fila_array_x][2]=$suma_libertad_avanza_anterior;
	  $array_x[$indice_fila_array_x][3]=$suma_hacemos_anterior;
	  $array_x[$indice_fila_array_x][4]=$suma_izquierda_anterior;
	//  $array_x[$indice_fila_array_x][5]=$suma_otros_anterior;
	  $indice_fila_array_x++;
	  $cantidad_escrutadas++;
	 }
	 else
	  {
	  $array_calular[$indice_fila_array_calcular][0]=$suma_pj_anterior;
	  $array_calular[$indice_fila_array_calcular][1]=$suma_jxc_anterior;
	  $array_calular[$indice_fila_array_calcular][2]=$suma_libertad_avanza_anterior;
	  $array_calular[$indice_fila_array_calcular][3]=$suma_hacemos_anterior;
	  $array_calular[$indice_fila_array_calcular][4]=$suma_izquierda_anterior;
	 // $array_calular[$indice_fila_array_calcular][5]=$suma_otros_anterior;
	  $indice_fila_array_calcular++;
	  }
	  
	 
	 $Escrutadas=$Escrutadas . "-" . $Escrutada;
	  	
	 $total_pj_anterior+=$suma_pj_anterior;
	 $total_pj_general+=$suma_pj_general;
	 
	 $total_jxc_anterior+=$suma_jxc_anterior;
	 $total_jxc_general+=$suma_jxc_general;
	 
	 $total_libertad_avanza_anterior+=$suma_libertad_avanza_anterior;
	 $total_libertad_avanza_general+=$suma_libertad_avanza_general;
	 
	 $localidad_anterior=utf8_encode($row_mesas['Localidad']);
	
	
	if($id_loc_padron_anterior==0)
	 $id_loc_padron_anterior=$id_loc_padron;
	 
	 $cantidad_mesas=$row_mesas['cantidad_mesas'];
	 
     } // id_loc_padron <> id_loc_padron_anterior
    else 
	 {
	 
	 if ($total_pj_anterior>0 and $total_jxc_anterior>0 and $total_libertad_avanza_anterior >0)
	  {
	  $porcentaje_escrutadas= round(($cantidad_escrutadas/$cantidad_mesas) *100,2);
	  
	    $explicacion_del_modelo=0;
	   
	  if($cantidad_escrutadas>$cantidad_mesas_minimas)
	   {
	    
	  	$regression = new Regression();
		$regression->setX(new Matrix($array_x));
		$regression->setY(new Matrix($array_y_libertad_avanza));
				
		$a1=$regression->exec();
		
		$array=$regression->getCoefficients();
				
		$r2=$regression->getRSQUARE(); // coeficiente de correlación
		$explicacion_del_modelo_LA= round(($r2*100),0);
				
		$count=count($array_calular); for($i=0;$i<$count;$i++) // se recorre el array con las mesas de la eleccion anterior de la localidad que todavia no tienen resultado actual, para predecir el resultado en función de una regresión lineal multiple
		 {
		// $intercepcion=$array->MainMatrix[0][0];
		 
		 $calculo_libertad_avanza= $array->MainMatrix[0][0]+($array->MainMatrix[1][0] * $array_calular[$i][0])+($array->MainMatrix[2][0] * $array_calular[$i][1])+($array->MainMatrix[3][0] * $array_calular[$i][2])+($array->MainMatrix[4][0] * $array_calular[$i][3]); //+($array->MainMatrix[5][0] * $array_calular[$i][4])
		 
		 $suma_calculo_libertad_avanza=$suma_calculo_libertad_avanza+ $calculo_libertad_avanza;
		
		// echo round($calculo_libertad_avanza,0);//($array_calular[$i][2]*$array->MainMatrix[1][0]) . " | ". ($array_calular[$i][2]*$array->MainMatrix[2][0]) . " | ". ($array_calular[$i][2]*$array->MainMatrix[3][0]) . " | ". ($array_calular[$i][2]*$array->MainMatrix[4][0]) . " | " . ($array_calular[$i][2]*$array->MainMatrix[5][0]);
						 
	     }
		
		$regression = new Regression();
		$regression->setX(new Matrix($array_x));
		$regression->setY(new Matrix($array_y_PJ));
		
		$a1=$regression->exec();
		
		$array=$regression->getCoefficients();
				
		$r2=$regression->getRSQUARE(); // coeficiente de correlación
		$explicacion_del_modelo_PJ= round(($r2*100),0);
		
		$count=count($array_calular); for($i=0;$i<$count;$i++) // se recorre el array con las mesas de la eleccion anterior de la localidad que todavia no tienen resultado actual, para predecir el resultado en función de una regresión lineal multiple
		 {
		 $intercepcion=$array->MainMatrix[0][0];
		 $calculo_PJ=  $array->MainMatrix[0][0]+($array->MainMatrix[1][0] * $array_calular[$i][0])+($array->MainMatrix[2][0] * $array_calular[$i][1])+($array->MainMatrix[3][0] * $array_calular[$i][2])+($array->MainMatrix[4][0] * $array_calular[$i][3]); //+($array->MainMatrix[5][0] * $array_calular[$i][4])
		 
		 $suma_calculo_PJ=$suma_calculo_PJ+ $calculo_PJ; 	
		 
		 }
				
		$regression = new Regression();
		$regression->setX(new Matrix($array_x));
		$regression->setY(new Matrix($array_y_JxC));
		
		$a1=$regression->exec();
		
		$array=$regression->getCoefficients();
				
		$r2=$regression->getRSQUARE(); // coeficiente de correlación
		$explicacion_del_modelo_JxC= round(($r2*100),0);
		
		$count=count($array_calular); for($i=0;$i<$count;$i++) // se recorre el array con las mesas de la eleccion anterior de la localidad que todavia no tienen resultado actual, para predecir el resultado en función de una regresión lineal multiple
		 {
		 $intercepcion=$array->MainMatrix[0][0];
		 $calculo_JxC=  $array->MainMatrix[0][0]+($array->MainMatrix[1][0] * $array_calular[$i][0])+($array->MainMatrix[2][0] * $array_calular[$i][1])+($array->MainMatrix[3][0] * $array_calular[$i][2])+($array->MainMatrix[4][0] * $array_calular[$i][3]); //+($array->MainMatrix[5][0] * $array_calular[$i][4])
		 
		 $suma_calculo_JxC=$suma_calculo_JxC+ $calculo_JxC; 	
		 
		 }
	
		$suma_calculo_PJ=$suma_calculo_PJ+$total_pj_general;
		$suma_calculo_JxC=$suma_calculo_JxC+$total_jxc_general;
		$suma_calculo_libertad_avanza=$suma_calculo_libertad_avanza+$total_libertad_avanza_general;
	 	
		
		//." R&sup2;:". $explicacion_del_modelo_LA . "%"
		//." R&sup2;:". $explicacion_del_modelo_JxC . "%"
		//." R&sup2;:". $explicacion_del_modelo_PJ . "%"
		
		
		//number_format($total_libertad_avanza_general,0,'','.') . " : "
		
		// para determinar quién gana hasta ahora con las mesas escrutadas
		  
	if($suma_calculo_PJ > $suma_calculo_JxC) 
	 {
	  $t = $suma_calculo_PJ;
      $color_ganador=$Partido[1]["color"];
	 }
	else 
	 {
	 $t = $suma_calculo_JxC;
	 $color_ganador=$Partido[2]["color"];
	 }
	
	if($t > $suma_calculo_libertad_avanza)  
	  { 
	 //  $mayor = $t;
	   $color_ganador=  $color_ganador;
	  }
	else 
	  { 
	 //  $mayor = $suma_calculo_libertad_avanza;
	   $color_ganador= $Partido[2]["color"];
	  }
	// hasta acá determina quién gana  
		
	 ?>
    <tr>
      <td><?php echo "<strong style=color:".$color_ganador .">". $localidad_anterior ."</strong>" . "  -mesas ".$cantidad_escrutadas. " de " .$cantidad_mesas . " (".  $porcentaje_escrutadas ."%)";?></td>
      <th style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><?php echo number_format($total_pj_anterior,0,'','.'); ?></div></th>
      <th style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><?php echo number_format($suma_calculo_PJ,0,'','.');?> </div></th>
           
       <th style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_libertad_avanza_anterior,0,'','.'); ?></div></th>
      <th scope="row"><div align="center"><?php  echo  "<strong style='color:". $Partido[2]['color'] ."'>" . number_format($suma_calculo_libertad_avanza,0,'','.') ."</strong>"; // if($_SESSION['usuario_id']==1) echo "<span style='color:#cccccc'> R&sup2;:". $explicacion_del_modelo_LA . "%</span>"; ?></div></th>
    </tr>
	<?php  
	   }	// if es > 10 
	
	 }
	 // if (anteriores > 0 
	 
	 $indice_fila_array_x=0;
	 $indice_fila_array_y=0;
	 $indice_fila_array_calcular=0;
	 unset($array_x);
	 unset($array_y_libertad_avanza);
	 unset($array_y_PJ);
	 unset($array_y_JxC);
	 unset($array_calular);
	 
	 $calculo_libertad_avanza=0;
	 $suma_calculo_libertad_avanza=0;
	 
     $calculo_PJ=0;
	 $suma_calculo_PJ=0;

     $calculo_JxC=0;
	 $suma_calculo_JxC=0;

	 $Escrutadas="";
	 $Escrutadas=$Escrutadas . "-" . $Escrutada;
	 $cantidad_escrutadas=0;
	 $cantidad_mesas=0;
	 $id_loc_padron_anterior= $id_loc_padron;
	 $total_pj_anterior=0;
	 $total_pj_general=0;
	 
	 $total_jxc_anterior=0;
	 $total_jxc_general=0;
	 
	 $total_libertad_avanza_anterior=0;
	 $total_libertad_avanza_general=0;
	 
	if($id_loc_padron_anterior >0)
	 {
	  $total_pj_anterior+=$suma_pj_anterior;
	  $total_pj_general+=$suma_pj_general;
	 
	  $total_jxc_anterior+=$suma_jxc_anterior;
	  $total_jxc_general+=$suma_jxc_general;
	 
	  $total_libertad_avanza_anterior+=$suma_libertad_avanza_anterior;
	  $total_libertad_avanza_general+=$suma_libertad_avanza_general;
	  
	  $localidad_anterior=$row_mesas['Localidad'];
	  $cantidad_mesas=$row_mesas['cantidad_mesas'];
	  
	   if($Escrutada=="S")  
	   $cantidad_escrutadas++;
	   
	} // if($id_loc_padron_anterior >0)
	 
	 
	 
	 }
	 
  } // while
  ?>
    
  </table>
<div style="background:#E9ECEF; height:10px"> </div>
 <div style="background:#016EA6; height:10px"> </div>
 <br />
 <span>Copyright &copy; <?php echo date('Y')?> - Partido Justicialista La Pampa </span> 

</div>

</body>
</html>