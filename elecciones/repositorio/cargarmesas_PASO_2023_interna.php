<?php
//initialize the session
//alter table `mesas` add (`L11S` smallint (6) NULL,,`L12S` smallint (6) NULL,,`L13S` smallint (6) NULL,`L14S` smallint (6) NULL,`L15S` smallint (6) NULL,`L16S` smallint (6) NULL,`L17S` smallint (6) NULL,`L18S` smallint (6) NULL,`L19S` smallint (6) NULL,`L20S` smallint (6) NULL,`L21S` smallint (6) NULL,`L22S` smallint (6) NULL,`L23S` smallint (6) NULL,`L24S` smallint (6) NULL,`L25S` smallint (6) NULL,`L26S` smallint (6) NULL,`L27S` smallint (6) NULL,`L28S` smallint (6) NULL,`L29S` smallint (6) NULL)

if (!isset($_SESSION)) {
  session_start();
}
   
 $Listas[1]["nombre"]="IZQUIERDA ANTICAPITALISTA";
 $Listas[1]["lista"]="13";
 $Listas[1]["representacion"]="Pdte,PN,DN,PR";
			  
 $Listas[2]["nombre"]="APERTURA LIBERAL ARGENTINA";
 $Listas[2]["lista"]="20";
 $Listas[2]["representacion"]="Pdte";
			  
 $Listas[3]["nombre"]="AZUL Y ROJO";
 $Listas[3]["lista"]="40";
 $Listas[3]["representacion"]="Pdte,PN";
			  
 $Listas[4]["nombre"]="COMPROMISO VECINAL";
 $Listas[4]["lista"]="57";
 $Listas[4]["representacion"]="Pdte,PN";
			  
 $Listas[5]["nombre"]="DIGNIDAD";
 $Listas[5]["lista"]="90";
 $Listas[5]["representacion"]="Pdte,PN";
			 
 $Listas[6]["nombre"]="CONFEDERAL";
 $Listas[6]["lista"]="90";
 $Listas[6]["representacion"]="Pdte,PN";
			   			  
 $Listas[7]["nombre"]="UNIDAD OBRERA";
 $Listas[7]["lista"]="92";
 $Listas[7]["representacion"]="Pdte,PN";
			 
 $Listas[8]["nombre"]="COALICION PAZ DEMOCRACIA SOBERANIA";
 $Listas[8]["lista"]="94";
 $Listas[8]["representacion"]="Pdte,PN";
			  
 $Listas[9]["nombre"]="PATRIA UNIDA";
 $Listas[9]["lista"]="94";
 $Listas[9]["representacion"]="Pdte";
			  
 $Listas[10]["nombre"]="C-TODEX";
 $Listas[10]["lista"]="94";
 $Listas[10]["representacion"]="Pdte,PN";
 
 $Listas[11]["nombre"]="D-NUEVA DEMOCRACIA";
 $Listas[11]["lista"]="94";
 $Listas[11]["representacion"]="PN";
 
 $Listas[12]["nombre"]="PRIMERO LA PATRIA";
 $Listas[12]["lista"]="95";
 $Listas[12]["representacion"]="Pdte,PN";
 
 $Listas[13]["nombre"]="DEMOS";
 $Listas[13]["lista"]="131";
 $Listas[13]["representacion"]="Pdte,PN";

 $Listas[14]["nombre"]="RECONQUISTA";
 $Listas[14]["lista"]="131";
 $Listas[14]["valor"]=$L14;
 $Listas[14]["representacion"]="Pdte"; 
 
 $Listas[15]["nombre"]="C-ANTICORRUPCION";
 $Listas[15]["lista"]="131";
 $Listas[15]["representacion"]="Pdte,PN";
 
 $Listas[16]["nombre"]="EL CAMBIO DE NUESTRAS VIDAS/JUNTOS POR LA PAMPA";
 $Listas[16]["lista"]="134/501";
 $Listas[16]["representacion"]="Pdte,PN,DN,PR";
 
 $Listas[17]["nombre"]="LA FUERZA DEL CAMBIO";
 $Listas[17]["lista"]="134/501";
 $Listas[17]["representacion"]="Pdte,PN,DN,PR";
 
 $Listas[18]["nombre"]="HACEMOS";
 $Listas[18]["lista"]="133";
 $Listas[18]["representacion"]="Pdte,PN";
 
 $Listas[19]["nombre"]="CELESTE Y BLANCA";
 $Listas[19]["lista"]="134/502";
 $Listas[19]["representacion"]="Pdte,PN";
 
 $Listas[20]["nombre"]="JUSTA Y SOBERANA";
 $Listas[20]["lista"]="134/502";
 $Listas[20]["representacion"]="Pdte";
 
 $Listas[21]["nombre"]="CONSENSO PERONISTA";
 $Listas[21]["lista"]="134/502";
 $Listas[21]["representacion"]="DN,PR";
 
 $Listas[22]["nombre"]="LIBERTAD POR SIEMPRE";
 $Listas[22]["lista"]="135";
 $Listas[22]["representacion"]="Pdte,PN";
 
 $Listas[23]["nombre"]="UNIR Y FORTALECER LA IZQUIERDA";
 $Listas[23]["lista"]="136/503";
 $Listas[23]["representacion"]="Pdte,PN,DN,PR";
 
 $Listas[24]["nombre"]="UNIDAD DE LUCHADORES Y LA IZQUIERDA";
 $Listas[24]["lista"]="136/503";
 $Listas[24]["representacion"]="Pdte,PN,DN,PR";
 
 $Listas[25]["nombre"]="1A-TIERRA, TECHO Y TRABAJO";
 $Listas[25]["lista"]="137";
 $Listas[25]["representacion"]="Pdte,PN";
 
 $Listas[26]["nombre"]="2B-TRANSFORMAR";
 $Listas[26]["lista"]="137";
 $Listas[26]["representacion"]="Pdte,PN";
 
 $Listas[27]["nombre"]="3C-TRES BANDERAS";
 $Listas[27]["lista"]="137";
 $Listas[27]["representacion"]="Pdte";
 
 $Listas[28]["nombre"]="4D-GENTE DE TRABAJO";
 $Listas[28]["lista"]="137";
 $Listas[28]["representacion"]="Pdte";
 
 $Listas[29]["nombre"]="5E-LABORISTA";
 $Listas[29]["lista"]="137";
 $Listas[29]["representacion"]="Pdte";
  
 			  
  
   

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

if (!isset($_SESSION)) {
  session_start();
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

require_once('Connections/conexionUsuarios.php'); 
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

<body onload = "document.forms[0].elements[0].focus()">

<div align="center">
  <table width="68%" clas="table">
    <tr>
     <th width="30%" scope="row" align="right"><span class="Estilo14">La Pampa </span></th>
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
        <input name="txtMesa" type="text" onKeyPress="return tabular(event,this)" class="Estilo19" id="txtMesa" size="10"  maxlength="4" />
      </span></td>
      <td width="530"><div id="nombre_localidad1" align="right">&nbsp;</div><?php $IP = $_SERVER["REMOTE_ADDR"]; echo "IP: ". $IP;?></td>
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
                       <strong><span style="color:#3366CC">Presidente</span></strong>       
                    </div> <!-- col-md--> 
                   
                   <div class="col-md-2" align="center">
                       <strong><span style="color:#3366CC">Parlamentario DN</span></strong>       
                    </div> <!-- col-md--> 
                    
                    <div class="col-md-2" align="center">
                       <strong><span style="color:#3366CC">Diputado</span></strong>       
                    </div> <!-- col-md--> 
                    
                    <div class="col-md-2" align="center">
                       <strong><span style="color:#3366CC">Parlamentario DR</span></strong>       
                    </div> <!-- col-md--> 
                                     
                </div> <!-- row -->
               
             
              <div> <br /> </div>
          
    <?php 
	  
	  $j=1;
	  
	 for($i=1;$i<=29;$i++){  
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
                    
                    <?php if($posPdte > -1){ ?> 
                     <div id="id_txt_gobernador_<?php echo $i; ?>" align="center"><span id="sprytextfield<?php echo $j; $j++ ?>">
        <input name="txtL<?php echo $i; ?>G" type="text" onKeyPress="return tabular(event,this)" class="Estilo19" id="txtL<?php echo $i; ?>G" size="10" maxlength="4" />
                     </span></div>     
                    <?php } ?> 
                      
                    </div> <!-- col-md--> 
                    
                   <div class="col-md-2">
                     <?php if($posPN > -1){ ?>  
                     <div id="id_txt_senador_<?php echo $i; ?>" align="center"><span id="sprytextfield<?php echo $j; $j++ ?>">
        <input name="txtL<?php echo $i; ?>S" type="text" onKeyPress="return tabular(event,this)" class="Estilo19" id="txtL<?php echo $i; ?>S" size="10" maxlength="4" />
                     </span></div>     
                      <?php } ?> 
                      
                    </div> <!-- col-md--> 
                     
                   
                   <div class="col-md-2">
                     <?php if($posDN > -1){ ?>  
                         <div id="id_txt_diputado_<?php echo $i; ?>" align="center"><span id="sprytextfield<?php echo $j; $j++ ?>">
        <input name="txtL<?php echo $i; ?>DP" type="text" onKeyPress="return tabular(event,this)" class="Estilo19" id="txtL<?php echo $i; ?>DP" size="10" maxlength="4" />
                        </span></div>  
                     <?php } ?> 
                         
                  </div> <!-- col-md--> 
                   
                   <div class="col-md-2">
                   
                      <?php if($posPR > -1){ ?> 
                      <div id="id_txt_intendente_<?php echo $i; ?>" align="center"><span id="sprytextfield<?php echo $j; $j++ ?>">
        <input name="txtL<?php echo $i; ?>I" type="text" onKeyPress="return tabular(event,this)" class="Estilo19" id="txtL<?php echo $i; ?>I" size="10" maxlength="4" />
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

var sprytextfield = new Spry.Widget.ValidationTextField("sprytextfield", "integer", {useCharacterMasking:true, validateOn:["blur"], minValue:1, maxValue:902});
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