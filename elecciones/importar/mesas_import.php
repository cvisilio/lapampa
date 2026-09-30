<?php 
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'c0780240_sz');
define('DB_PASS', '48kobuniFA');
define('DB_NAME', 'c0780240_sz');
*/
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sz');
*/

 $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }

function explota($fecha) // local2bd
{
	$vector_fecha = explode("/",$fecha);
	$aux = $vector_fecha[2];
	$vector_fecha[2] = $vector_fecha[0];
	$vector_fecha[0] = $aux;
	return implode("-",$vector_fecha);
}

//leemos el fichero

 $fname="resultados_totales_L_2025.csv"; //importar_indicadores.csv
 $fp=fopen($fname,"r") or die("Erro al abrir el fichero");
 $line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

 $j=0;
 while(!feof($fp))
 {

 $line = fgets($fp, 2024 ); 
 if ($line <> "") {
 list($localidad,$L1DP,$L2DP,$L3DP,$L4DP,$L5DP,$L6DP,$L7DP) = explode(";", $line);
 	//,$L6DP,$L6G,$L7DP,$L7G,$L8DP,$L8G,$L9G,$L9DP,$L10G,$L10DP,$L11DP,$L11G, $L12DP,$L12G,$L13DP,$L13G,$L14DP,$L14G,$L15DP,$L15G,$L16DP,$L16G,$L17DP,$L17G,$L18DP,$L18G,$L19DP,$L19G,$L20DP,$L20G,$L21DP,$L21G,$L22DP,$L22G,$L23DP,$L23G,$L24DP,$L24G,$L25DP,$L25G,$L26DP,$L26G,$L27DP,$L27G,$L28DP,$L28G,$L29DP,$L29G						
 
 $eleccion=14;
 $j=$j+1;
 
 //184 a 188
 
 $constante=190;
  
 for ($i=1;$i<=7;$i++)
  {
  
  $lista=$constante + $i;
 
 switch ($i)
 {  
  case 1:
   $variable1=intval($L1DP);
  // $variable1=intval($L1G);
   break;
  case 2:
   $variable1=intval($L2DP);
 //  $variable1=intval($L2G);
   break;
   
  case 3:
   $variable1=intval($L3DP);
  // $variable2=intval($L3G);
   break;
  case 4:
   $variable1=intval($L4DP);
  // $variable2=intval($L4G);
   break;
  case 5:
   $variable1=intval($L5DP);
  // $variable2=intval($L5G);
   break;
  
  case 6:
   $variable1=intval($L6DP);
  // $variable2=intval($L6G);
   break;
  case 7:
   $variable1=intval($L7DP);
  // $variable2=intval($L7G);
   break;
 /*
  case 8:
   $variable1=intval($L8DP);
 //  $variable2=intval($L8G);
   break;
  case 9:
   $variable1=intval($L9DP);
 //  $variable2=intval($L9G);
   break;
  case 10:
   $variable1=intval($L10DP);
   $variable2=intval($L10G);
   break;
  case 11:
   $variable1=intval($L11DP);
   $variable2=intval($L11G);
   break;
  case 12:
   $variable1=intval($L12DP);
   $variable2=intval($L12G);
   break;
  case 13:
   $variable1=intval($L13DP);
   $variable2=intval($L13G);
   break;
  case 14:
   $variable1=intval($L14DP);
   $variable2=intval($L14G);
   break;
  case 15:
   $variable1=intval($L15DP);
   $variable2=intval($L15G);
   break;
  case 16:
   $variable1=intval($L16DP);
   $variable2=intval($L16G);
   break;
  case 17:
   $variable1=intval($L17DP);
   $variable2=intval($L17G);
   break;
  case 18:
   $variable1=intval($L18DP);
   $variable2=intval($L18G);
   break;
  case 19:
   $variable1=intval($L19DP);
   $variable2=intval($L19G);
   break;
  case 20:
   $variable1=intval($L20DP);
   $variable2=intval($L20G);
   break;
  case 21:
   $variable1=intval($L21DP);
   $variable2=intval($L21G);
   break;
  case 22:
   $variable1=intval($L22DP);
   $variable2=intval($L22G);
   break;
  case 23:
   $variable1=intval($L23DP);
   $variable2=intval($L23G);
   break;
  case 24:
   $variable1=intval($L24DP);
   $variable2=intval($L24G);
   break;
  case 25:
   $variable1=intval($L25DP);
   $variable2=intval($L25G);
   break;
  case 26:
   $variable1=intval($L26DP);
   $variable2=intval($L26G);
   break;
  case 27:
   $variable1=intval($L27DP);
   $variable2=intval($L27G);
   break;
  case 28:
   $variable1=intval($L28DP);
   $variable2=intval($L28G);
   break;
  case 29:
   $variable1=intval($L29DP);
   $variable2=intval($L29G);
   break;
  */ 
  } 
          
  $sql="INSERT INTO resultados_elecciones (eleccion,localidad,lista,diputado_nacional) VALUES ('$eleccion','$localidad','$lista','".$variable1."')";

  $query = mysqli_query($con,$sql);
   
  }
  
  
}
}
fclose($fp);
 echo $j;

exit();
?>
 

