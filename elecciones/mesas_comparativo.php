<?php
if (!isset($_SESSION)) {
  session_start();
}

require_once('Connections/conexionUsuarios.php'); 

 $Partido[1]["color"]= "#0066CC"; // Azul PJ
 $Partido[2]["color"]=  "#CC0099"; // Violeta - Milei
 

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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Mesas Comparativo</title>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.0.0/dist/tf.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

</head>

<body>
<div style="margin-top:10px"></div>

<div align="center" style="width:96%;margin-left:2%;border:inset;border-color:#E9ECEF">
<div align="center">
 
 <!-- <p align="center" class="Estilo8"><a target='_blank' href="https://eleccionesgenerales2019.lapampa.gob.ar/scripts/cgiip.exe/WService=Elecciones/EReLocCowr.htm?vPartido=1&SelectLocalidad=84&User=">Ver Resultados en Tribunal Electoral (clic aqu&iacute;)</a></p> -->
 <table width="100%" height="81" border="0">
 
   <tr>
     <td height="77" colspan="2" align="left" bgcolor="#016EA6"><p><img src="fotos/escudo_la_pampa.png" width="65" height="70" style="margin-top:4px; margin-left:5px; margin-right:15px; float:left" /></p>
       <p><span class="cabecera_logo">Elecciones <?php echo "2023";?></span></p>
       <p><span class="cabecera_logo"> 19 de noviembre de <?php echo "2023";?> - Escrutinio Provisorio.</span></p>        </td> 
       
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
  
 <div align="left"><a href="verestadistica.php" class="btn btn-secondary btn-sm active" style="margin-right:5px; margin-left:3px; margin-top:16px" role="button" aria-pressed="true">Inicio</a> </div>
  
  <table class="table">
    <tr>
      <td width="20%"><div align="center"><strong>Localidad</strong></div></td>
      
      <th width="12%" style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><strong>PJ Gral.</strong></div></th>
      <th width="9%" style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><strong>PJ Balotaje.</strong></div></th>
     
      <td>Variaci&oacute;n</td>
      <th width="13%" style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><strong>L. Avanza Gral.</strong></div></th>
      <th width="13%" style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><strong>L. Avanza Balotaje.</strong></div></th>
      <td>Variaci&oacute;n</td>
    </tr>
    <?php 
	
	$cantidad_escrutadas=0;
	$cantidad_mesas=0;
	 	  
	$total_pj_anterior=0;
	$total_pj_general=0;
	 
	$total_jxc_anterior=0;
	$total_jxc_general=0;
	 
	$total_libertad_avanza_anterior=0;
	$total_libertad_avanza_general=0;
	
	$filas=0;
	$cadena_x1="";
	$cadena_y1="";
	
   $query_mesas = "SELECT mesas.*,localidades.Localidad,localidades.id_loc_padron,localidades.cantidad_mesas FROM mesas, localidades WHERE mesas.Escrutada='S' and mesas.CodigoLocalidad = localidades.id_loc_padron ORDER BY localidades.id_loc_padron,mesas.Id";

   $mesas_1=mysqli_query($con,$query_mesas);
  // $row_mesas=mysqli_fetch_array($mesas_1);
   
   
	$id_loc_padron_anterior=0;
	$localidad_anterior="";
	$cantidad_mesas=0; 
	$cantidad_escrutadas=0;
	 
	 		
	while ($row_mesas = mysqli_fetch_assoc($mesas_1)) 
	 {
	 $Mesa=$row_mesas['Mesa'];
	 
	 $Escrutada=$row_mesas['Escrutada'];
	 
	 $id_loc_padron=$row_mesas['id_loc_padron'];  
		 
	 $sql="select * from mesas_generales_presidente_2023 where Escrutada='S' and Mesa='$Mesa'";
	 
	 $query_resultados=mysqli_query($con,$sql);
	 $esta_escrutada_anterior=mysqli_num_rows($query_resultados);
	
	if($esta_escrutada_anterior>0)
	 {
	 
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
	//  echo "Id= " .$id_loc_padron . " id_anterior: ". $id_loc_padron_anterior. "<br />";
	 $total_pj_anterior+=$suma_pj_anterior;
	 $total_pj_general+=$suma_pj_general;
	 
	 $total_jxc_anterior+=$suma_jxc_anterior;
	 $total_jxc_general+=$suma_jxc_general;
	 
	 $total_libertad_avanza_anterior+=$suma_libertad_avanza_anterior;
	 $total_libertad_avanza_general+=$suma_libertad_avanza_general;
	 
	 $localidad_anterior=$row_mesas['Localidad'];
	
	 if($Escrutada=="S")  
	   $cantidad_escrutadas++;
	
	if($id_loc_padron_anterior==0)
	 $id_loc_padron_anterior=$id_loc_padron;
			 
	 $cantidad_mesas=$row_mesas['cantidad_mesas'];
	 
     } // id_loc_padron <> id_loc_padron_anterior
    else 
	 {
	
	 
	if ($total_pj_anterior>0 and $total_jxc_anterior>0 and $total_libertad_avanza_anterior >0)
	  {
	 	 	  
	  $porcentaje_escrutadas= round(($cantidad_escrutadas/$cantidad_mesas) *100,2);
	  
	  if($total_pj_general > $total_pj_anterior)
	   $icono_PJ="&nbsp;<i class='fa fa-arrow-up'></i>";
	  else
	    $icono_PJ="&nbsp;<i class='fa fa-arrow-down'></i>";
		
	  if($total_jxc_general > $total_jxc_anterior)
	   $icono_JxC="&nbsp;<i class='fa fa-arrow-up'></i>";
	  else
	    $icono_JxC="&nbsp;<i class='fa fa-arrow-down'></i>";
		
	  if($total_libertad_avanza_general > $total_libertad_avanza_anterior)
	   $icono_LA="&nbsp;<i class='fa fa-arrow-up'></i>";
	  else
	    $icono_LA="&nbsp;<i class='fa fa-arrow-down'></i>";	
		
	// para determinar quién gana hasta ahora con las mesas escrutadas
		  
	if($total_pj_general > $total_jxc_general) 
	 {
	  $t = $total_pj_general;
      $color_ganador=$Partido[1]["color"];
	 }
	else 
	 {
	 $t = $total_jxc_general;
	 $color_ganador=$Partido[1]["color"];
	 }
	
	if($t > $total_libertad_avanza_general)  
	  { 
	 //  $mayor = $t;
	   $color_ganador=  $color_ganador;
	  }
	else 
	  { 
	 //  $mayor = $total_libertad_avanza_general;
	   $color_ganador= $Partido[2]["color"];
	  }
	// hasta acá determina quién gana
	
	// desde acá para calcular porcentaje incremento o decremento con respeco a la elección anterior
	// if ($total_pj_general >$total_pj_anterior) 
	  $porcentaje_incremento_decremento_PJ=number_format(($total_pj_general* 100/$total_pj_anterior)-100,2,',','')."%";
	
	   
	  $porcentaje_incremento_decremento_JxC=number_format(($total_jxc_general*100/$total_jxc_anterior)-100,2,',','')."%";
	  
	  $porcentaje_incremento_decremento_LA=number_format(($total_libertad_avanza_general* 100/$total_libertad_avanza_anterior)-100,2,',','')."%";
	
	//  acá para calcular porcentaje incremento o decremento con respeco a la elección anterior
	  
	 ?>
    <tr>
    
      <td><?php //echo "<strong style=color:".$color_ganador .">". utf8_encode($localidad_anterior); if($_SESSION["usuario_id"]==1) echo "</strong> (escrutadas ".  $porcentaje_escrutadas ."%)" . $cantidad_escrutadas ." de ". $cantidad_mesas; ?> 
      
      <a href="#" data-toggle="modal" style="font-weight:bold; color:<?php echo $color;?>" data-target="#myModal" onClick="informacion1('<?php echo $id_loc_padron;?>','<?php echo $localidad_anterior;?>');"><?php echo "<strong style=color:".$color_ganador .">". utf8_encode($localidad_anterior); if($_SESSION["usuario_id"]==1) echo "</strong> (escrutadas ".  $porcentaje_escrutadas ."%)" . $cantidad_escrutadas ." de ". $cantidad_mesas; ?> | <i class="fa fa-eye" aria-hidden="true"></i> </a> 
      
      </td>
      
      
      <th style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><?php echo number_format($total_pj_anterior,0,'','.') ; ?></div></th>
      <th style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_pj_general,0,'','.') . " " . $icono_PJ;?></div></th>
      
      <td align="right" style="color:#999999"><div align="left"><?php echo  $porcentaje_incremento_decremento_PJ ;?></div> </td>
      
           
       <th style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_libertad_avanza_anterior,0,'','.'); ?></div></th>
       
      <th style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_libertad_avanza_general,0,'','.'). " ". $icono_LA; ?></div></th>
      <td align="right" style="color:#999999"><div align="left"><?php echo  $porcentaje_incremento_decremento_LA;?> </div></td>
    </tr>
	<?php 
	 
	 } //if ($total_pj_anterior>0 and $total_jxc_anterior>0 and $total_libertad_avanza_anterior >0)
	 	 
	 $cantidad_escrutadas=0;
	 $cantidad_mesas=0;
	 $id_loc_padron_anterior=$id_loc_padron;
	 	  
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
	 
	 } // else // id_loc_padron <> id_loc_padron_anterior
	 
   
    } // if anterior_escrutada. Esto es porque hay mesas de las PASO que no fueron escrutadas
  	 
  } // while
  ?>
  
   <tr>
      <td><?php
	  if($cantidad_mesas>0)
	   $porcentaje_escrutadas= round(($cantidad_escrutadas/$cantidad_mesas) *100,2);
	   
	   if($total_pj_general > $total_pj_anterior)
	   $icono_PJ="&nbsp;<i class='fa fa-arrow-up'></i>";
	  else
	    $icono_PJ="&nbsp;<i class='fa fa-arrow-down'></i>";
		
	  if($total_jxc_general > $total_jxc_anterior)
	   $icono_JxC="&nbsp;<i class='fa fa-arrow-up'></i>";
	  else
	    $icono_JxC="&nbsp;<i class='fa fa-arrow-down'></i>";
		
	  if($total_libertad_avanza_general > $total_libertad_avanza_anterior)
	   $icono_LA="&nbsp;<i class='fa fa-arrow-up'></i>";
	  else
	    $icono_LA="&nbsp;<i class='fa fa-arrow-down'></i>";	
		
	
	 if($total_pj_general > $total_jxc_general) 
	 {
	  $t = $total_pj_general;
      $color=$Partido[1]["color"];
	 }
	 else 
	  {
	  $t = $total_jxc_general;
	  $color=$Partido[1]["color"];
	  }
	
	if($t > $total_libertad_avanza_general)  
	  { 
	   $mayor = $t;
	   $color=  $color;
	  }
	else 
	  { 
	   $mayor = $total_libertad_avanza_general;
	   $color= $Partido[2]["color"];
	  } 
	  
	  // desde acá para calcular porcentaje incremento o decremento con respeco a la elección anterior
	// if ($total_pj_general >$total_pj_anterior) 
	  $porcentaje_incremento_decremento_PJ="<span style=color:'#CCCCCC'>".number_format(($total_pj_general* 100/$total_pj_anterior)-100,2,',','')."%"."</span>";
	
	 	  
	  $porcentaje_incremento_decremento_LA="<span style=color:'#CCCCCC'>".number_format(($total_libertad_avanza_general* 100/$total_libertad_avanza_anterior)-100,2,',','')."%" ."</span>";
	
	//  acá para calcular porcentaje incremento o decremento con respeco a la elección anterior		
	   
	   
	   
	   //echo "<strong style=color:".$color_ganador .">". utf8_encode($localidad_anterior); if($_SESSION["usuario_id"]==1) echo "</strong> (escrutadas ".  $porcentaje_escrutadas ."%)" . $cantidad_escrutadas ." de ". $cantidad_mesas; ?> 
      
      <a href="#" data-toggle="modal" style="font-weight:bold; color:<?php echo $color;?>" data-target="#myModal" onClick="informacion1('<?php echo $id_loc_padron;?>','<?php echo $localidad_anterior;?>');"><?php echo "<strong style=color:".$color_ganador .">". utf8_encode($localidad_anterior); if($_SESSION["usuario_id"]==1) echo "</strong> (escrutadas ".  $porcentaje_escrutadas ."%)" . $cantidad_escrutadas ." de ". $cantidad_mesas; ?> | <i class="fa fa-eye" aria-hidden="true"></i> </a>
       
       </td>
     
      <th style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><?php echo number_format($total_pj_anterior,0,'','.') ; ?></div></th>
      <th style="color:<?php echo $Partido[1]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_pj_general,0,'','.') . " " . $icono_PJ;?></div></th>
      
      <td align="right" style="color:#999999"><div align="left"><?php echo  $porcentaje_incremento_decremento_PJ ;?> </div></td>
      
      <th style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_libertad_avanza_anterior,0,'','.'); ?></div></th>
       
      <th style="color:<?php echo $Partido[2]['color']; ?>" scope="row"><div align="center"><?php echo  number_format($total_libertad_avanza_general,0,'','.'). " ". $icono_LA; ?></div></th>
      <td align="right" style="color:#999999"><div align="left"><?php echo  $porcentaje_incremento_decremento_LA;?> </div></td>
     
    </tr>
  </table>
  
  <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
       <h4 class="modal-title">Mesas Comparativo</h4> 
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
  

<div style="background:#E9ECEF; height:10px"> </div>
 <div style="background:#016EA6; height:10px"> </div>
 <br />
 <span>Copyright &copy; <?php echo date('Y')?> - Partido Justicialista La Pampa </span> 

</div>


<?php
 // valores a calcular
 ?>
</body>

</html>

<script>
function informacion1(id_localidad,nombre_localidad){
  
  $.ajax({
	  type: "GET",
      url: 'mesas_comparativo_localidad.php', //trae datos del intendente, concejales y del resultado de la última elección
      data: {"menu":id_localidad},
      success: function(data) {
	   $(".modal-title").html(nombre_localidad);
      $("#resultados_ajax").html(data); 
	  }
   }
 );
		
}
</script>