<?php
//initialize the session
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
?>
<?php
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
?>
   
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

 <script src="js/3_4.js"></script>

<script> 

function BuscarMesa(mesa)
   { 
  		 $.ajax({
         type: "POST",
         url: "buscar_mesa.php",
         data: "mesa="+mesa,
		 success: function(recibe){
		 var datos =  $.parseJSON(recibe);
		 var codigo_localidad= parseInt(datos[0].codigo_localidad);
		 var cargo_elecciones= datos[0].cargo_elecciones;
		 
		 var myarr = cargo_elecciones.split(";");
		 
		 var nombre_junta="";
		 var lista="";
		 
		   switch (codigo_localidad) {
				case 1:
				  nombre_junta="M. P. Veinticinqueño";	 //25 de Mayo
				  lista="84";
				  break;
				case 3:
				  nombre_junta="P. P. Pampeano"; // A. V. Praet
				  lista="113";
				  break;
				case 17:
					nombre_junta="J. V. por y para Ceballos"; // Ceballos
					lista="96";
					break;
				case 20:
					nombre_junta="U. V. Por el Pueblo"; //Hilario Lagos 
					lista="111";
					break;
				case 35:
					nombre_junta="J. V. de Falucho"; // Falucho
					lista="46";
					break;
				case 38:
					nombre_junta="U. V. Achense"; // Gral. Acha
					lista="103";
					break;
				case 45:
					nombre_junta="F. V. Alvearense"; // Int. Alvear
					lista="602";
					break;
				case 62:
					nombre_junta="U. por Monte Nievas"; //Monte Nievas
					lista="117";
					break;
				case 72:
					nombre_junta="J. V. Quetrequén S.T."; //Quetrequ&eacute;n 
					lista="102";
					break;
				case 83:
					nombre_junta="P. Popular Pampeano "; // Speluzzi
					lista="113";
					break;
				case 92:
					nombre_junta="Alianza Victorica E. y P."; // Victorica
					lista="601";
					break;								
                }
		 
		 /*  
		  if (myarr[3].indexOf("I") > -1)
		    alert("Encontro");
		   else
		    alert("No");
		  */
		  document.getElementById('id_label_lista_'+'10').innerHTML =lista;
		  document.getElementById('id_label_nombre_'+'10').innerHTML =nombre_junta;
		  document.getElementById('id_label_lista_'+'7').innerHTML ="116";
		  document.getElementById('id_label_nombre_'+'7').innerHTML ="Compromiso Ciudadano";	
		   var i_num = 1;
		   var i_string = "1"; 
		   
		 for (var i = 0; i <= 9; i++) { //txtL9G ejemplo textos
		   i_num = i +1;
		   i_string = i_num.toString();
		   
		   if (myarr[i].indexOf("I") > -1 || myarr[i].indexOf("G") > -1)
		    {
			
			  if (myarr[i].indexOf("I") > -1)
			   {
			 //  document.getElementById('txtL'+i_string+'I').value="";
			   document.getElementById('id_txt_intendente_'+i_string).style.display='block';
			   
			   }
			  else
			   {
			   document.getElementById('id_txt_intendente_'+i_string).style.display='none';
			   document.getElementById('txtL'+i_string+'I').value="0";
			   }
			  if (myarr[i].indexOf("G") > -1)
			   {
			   document.getElementById('id_txt_gobernador_'+i_string).style.display='block';
			  //  document.getElementById('txtL'+i_string+'G').value="";
			   }
			  else
			   {
			   document.getElementById('id_txt_gobernador_'+i_string).style.display='none'; 
			    document.getElementById('txtL'+i_string+'G').value="0";
			   }
			} // if || 
		   else
		    {
			 document.getElementById('id_txt_gobernador_'+i_string).style.display='none';
			 document.getElementById('id_txt_intendente_'+i_string).style.display='none';
		     document.getElementById('id_label_lista_'+i_string).innerHTML ="";
		     document.getElementById('id_label_nombre_'+i_string).innerHTML ="";
			 if(document.getElementById('txtL'+i_string+'I').value=="")
			  document.getElementById('txtL'+i_string+'I').value="0";
			 if(document.getElementById('txtL'+i_string+'G').value=="") 
			  document.getElementById('txtL'+i_string+'G').value="0";	 
			} //else ||
		  
         } // for
		 
			 
		}
		});
  
	 }

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
  <table width="68%" border="1">
    <tr>
      <th width="58%" scope="row"><span class="Estilo14">La Pampa - Rep&uacute;blica Argentina</span></th>
      <td width="42%"><div align="center" class="Estilo2 Estilo16 Estilo16"><a href="<?php echo $logoutAction ?>"><strong>Desconectar</strong></a></div></td>
    </tr>
    <tr>
      <th height="40" scope="row"><div align="center" class="Estilo14"><strong>Su IP es:
        <?php $IP = $_SERVER["REMOTE_ADDR"]; echo $IP;?>
      </strong></div>        </th>
      <td><div align="center" class="Estilo2 Estilo16 Estilo16"><a href="vermesasfaltantes.php"><strong>Ver Mesas Faltantes</strong></a></div></td>
    </tr>
  </table>
</div>
<div align="center"><a href="vermesasfaltantes.php"></a> </div>

 <form action="guardardatos.php" method="post" name="form1" target="_self" id="form1">
  <table width="68%" height="38" border="1" align="center">
    <tr>
      <td width="94" height="32"><div align="center" class="Estilo19"><strong>Mesa</strong></div></td>
      <td width="115"><span id="sprytextfield">
        <input name="txtMesa" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtMesa" size="10"  maxlength="4" />
      </span></td>
      <td width="530">&nbsp;</td>
    </tr>
  </table>
   
  <table width="68%" height="50" border="1" align="center">
    <tr>
      <td width="110" bgcolor="#CCCCCC"><div align="center"><strong>Nº</strong></div></td>
      <td width="245" bgcolor="#CCCCCC"><div align="center" class="Estilo19">Agrupación Pol&iacute;tica</div></td>
     
      <td width="173" bgcolor="#CCCCCC"><div align="center" class="Estilo6 Estilo17">Senador</div></td>
      <td width="170" bgcolor="#CCCCCC"><div align="center" class="Estilo6 Estilo17">Diputado</div></td>
    </tr>
    
    <tr>
      <td height="32" class="Estilo4"><div align="center" id="id_label_lista_3" class="Estilo19"><?php echo $Listas[1]['lista']; ?></div></td>
      <td class="Estilo4"><div align="left" id="id_label_nombre_1" class="Estilo19"><?php echo $Listas[1]['nombre']; ?></div></td>
     
      <td class="Estilo4"><div id="id_txt_gobernador_1" align="center"><span id="sprytextfield1">
        <input name="txtL1G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL1G" size="10" maxlength="4" />
      </span></div></td>
      
      <td class="Estilo4"><div id="id_txt_intendente_1" align="center"><span id="sprytextfield2">
        <input name="txtL1I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL1I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
    <tr>
      <td height="30" bgcolor="#CCCCCC" class="Estilo4"><div align="center" id="id_label_lista_2" class="Estilo19"><?php echo $Listas[2]['lista']; ?></div></td>
     <td bgcolor="#CCCCCC" class="Estilo4"><div align="left" id="id_label_nombre_6" class="Estilo19"><?php echo $Listas[2]['nombre']; ?></div></td>
     
      <td bgcolor="#CCCCCC" class="Estilo4"><div id="id_txt_gobernador_2" align="center"><span id="sprytextfield3">
        <input name="txtL2G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL2G" size="10" maxlength="4" />
      </span> </div></td>
         
      <td bgcolor="#CCCCCC" class="Estilo4"><div id="id_txt_intendente_2" align="center"><span id="sprytextfield4">
        <input name="txtL2I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL2I" size="10" maxlength="4" />
      </span> </div></td>
      <td bgcolor="#CCCCCC" class="Estilo4">&nbsp;</td>
    </tr>
    <tr>
      <td height="32" class="Estilo4"><div align="center" id="id_label_lista_3" class="Estilo19"><?php echo $Listas[3]['lista']; ?></div></td>
      <td class="Estilo4"><div align="left" id="id_label_nombre_3" class="Estilo19"><?php echo  $Listas[3]['nombre']; ?></div></td>
      
      <td class="Estilo3"><div id="id_txt_gobernador_3" align="center"><span id="sprytextfield5">
        <input name="txtL3G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL3G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4"><div id="id_txt_intendente_4" align="center"><span id="sprytextfield6">
        <input name="txtL3I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL3I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
    <tr>
      <td height="33" bgcolor="#CCCCCC" class="Estilo4"><div align="center" id="id_label_lista_4" class="Estilo19"><?php echo $Listas[4]['lista']; ?></div></td>
      <td bgcolor="#CCCCCC" class="Estilo4"><div align="left" id="id_label_nombre_4" class="Estilo19"><?php echo $Listas[4]['nombre']; ?></div></td>
    
      <td bgcolor="#CCCCCC" class="Estilo4"><div id="id_txt_gobernador_4" align="center"><span id="sprytextfield7">
        <input name="txtL4G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL4G" size="10" maxlength="4" />
      </span></div></td>
      <td bgcolor="#CCCCCC" class="Estilo4"><div id="id_txt_intendente_4" align="center"><span id="sprytextfield8">
        <input name="txtL4I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL4I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
    <tr>
      <td height="31" bgcolor="#FFFFFF" class="Estilo4"><div align="center" id="id_label_lista_5" class="Estilo19"><?php echo $Listas[5]['lista']; ?></div></td>
      <td bgcolor="#FFFFFF" class="Estilo4"><div align="left" id="id_label_nombre_5" class="Estilo19"><?php echo $Listas[5]['nombre']; ?></div></td>
    
      <td class="Estilo4"><div id="id_txt_gobernador_5" align="center"><span id="sprytextfield9">
        <input name="txtL5G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL5G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4"><div id="id_txt_intendente_5" align="center"><span id="sprytextfield10">
        <input name="txtL5I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL5I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
    
    <tr>
      <td height="31" bgcolor="#CCCCCC" class="Estilo4"><div align="center" id="id_label_lista_6" class="Estilo19"><?php echo $Listas[6]['lista']; ?></div></td>
      <td bgcolor="#CCCCCC" class="Estilo4"><div align="left" id="id_label_nombre_6" class="Estilo19"><?php echo $Listas[6]['nombre']; ?></div></td>
    
      <td class="Estilo4" bgcolor="#CCCCCC"><div id="id_txt_gobernador_6" align="center"><span id="sprytextfield11">
        <input name="txtL6G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL6G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4" bgcolor="#CCCCCC"><div id="id_txt_intendente_6" align="center"><span id="sprytextfield12">
        <input name="txtL6I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL6I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
   
   <tr>
      <td height="31" bgcolor="#FFFFFF" class="Estilo4"><div align="center" id="id_label_lista_7" class="Estilo19"><?php echo $Listas[7]['lista']; ?></div></td>
      <td bgcolor="#FFFFFF" class="Estilo4"><div align="left" id="id_label_nombre_7" class="Estilo19"><?php echo $Listas[7]['nombre']; ?></div></td>
    
      <td class="Estilo4"><div id="id_txt_gobernador_7" align="center"><span id="sprytextfield13">
        <input name="txtL7G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL7G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4"><div id="id_txt_intendente_7" align="center"><span id="sprytextfield14">
        <input name="txtL7I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL7I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
  
     <tr>
      <td height="31" bgcolor="#CCCCCC" class="Estilo4"><div align="center" id="id_label_lista_8" class="Estilo19"><?php echo $Listas[8]['lista']; ?></div></td>
      <td bgcolor="#CCCCCC" class="Estilo4"><div align="left" id="id_label_nombre_8" class="Estilo19"><?php echo $Listas[8]['nombre']; ?></div></td>
    
      <td class="Estilo4" bgcolor="#CCCCCC"><div id="id_txt_gobernador_8" align="center"><span id="sprytextfield15">
        <input name="txtL8G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL8G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4" bgcolor="#CCCCCC"><div id="id_txt_intendente_8" align="center"><span id="sprytextfield16">
        <input name="txtL8I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL8I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
      
    <tr>
      <td height="31" bgcolor="#FFFFFF" class="Estilo4"><div align="center" id="id_label_lista_9" class="Estilo19"><?php echo $Listas[9]['lista']; ?></div></td>
      <td bgcolor="#FFFFFF" class="Estilo4"><div align="left" id="id_label_nombre_9" class="Estilo19"><?php echo $Listas[9]['nombre']; ?></div></td>
    
      <td class="Estilo4"><div id="id_txt_gobernador_9" align="center"><span id="sprytextfield17">
        <input name="txtL9G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL9G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4"><div id="id_txt_intendente_9" align="center"><span id="sprytextfield18">
        <input name="txtL9I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL9I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
    
     <tr>
      <td height="31" bgcolor="#CCCCCC" class="Estilo4"><div align="center" id="id_label_lista_10" class="Estilo19"><?php echo $Listas[10]['lista']; ?></div></td>
      <td bgcolor="#CCCCCC" class="Estilo4"><div align="left" id="id_label_nombre_10" class="Estilo19"><?php echo $Listas[10]['nombre']; ?></div></td>
    
      <td class="Estilo4" bgcolor="#CCCCCC"><div id="id_txt_gobernador_10" align="center"><span id="sprytextfield19">
        <input name="txtL10G" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL10G" size="10" maxlength="4" />
      </span></div></td>
      <td class="Estilo4" bgcolor="#CCCCCC"><div id="id_txt_intendente_10" align="center"><span id="sprytextfield20">
        <input name="txtL10I" type="text" onkeypress="return tabular(event,this)" class="Estilo19" id="txtL10I" size="10" maxlength="4" />
      </span></div></td>
    </tr>
  </table>
  <br />
  <div align="center" id="pasardatos">
    <input name="enviardatos" type="submit" class="Estilo19" id="enviardatos" value="Enviar Datos" />
  </div>
   
</form>


<script type="text/javascript">

var sprytextfield = new Spry.Widget.ValidationTextField("sprytextfield", "integer", {useCharacterMasking:true, validateOn:["blur"], minValue:1, maxValue:883});
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield2 = new Spry.Widget.ValidationTextField("sprytextfield2", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield3 = new Spry.Widget.ValidationTextField("sprytextfield3", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield4 = new Spry.Widget.ValidationTextField("sprytextfield4", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield5 = new Spry.Widget.ValidationTextField("sprytextfield5", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield6 = new Spry.Widget.ValidationTextField("sprytextfield6", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield7 = new Spry.Widget.ValidationTextField("sprytextfield7", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield8 = new Spry.Widget.ValidationTextField("sprytextfield8", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield9 = new Spry.Widget.ValidationTextField("sprytextfield9", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield10 = new Spry.Widget.ValidationTextField("sprytextfield10", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield11 = new Spry.Widget.ValidationTextField("sprytextfield11", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield12 = new Spry.Widget.ValidationTextField("sprytextfield12", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield13 = new Spry.Widget.ValidationTextField("sprytextfield13", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield14 = new Spry.Widget.ValidationTextField("sprytextfield14", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield15 = new Spry.Widget.ValidationTextField("sprytextfield15", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield16 = new Spry.Widget.ValidationTextField("sprytextfield16", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield17 = new Spry.Widget.ValidationTextField("sprytextfield17", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield18 = new Spry.Widget.ValidationTextField("sprytextfield18", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield19 = new Spry.Widget.ValidationTextField("sprytextfield19", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});
var sprytextfield20 = new Spry.Widget.ValidationTextField("sprytextfield20", "integer", {isRequired:false, useCharacterMasking:true, validateOn:["blur"], maxValue:3500, minValue:0});

//-->
</script>
</body>
</html>
