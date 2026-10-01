<?php 
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'c0780240_sz');
define('DB_PASS', '48kobuniFA');
define('DB_NAME', 'c0780240_sz');
*/

/*define('DB_HOST', 'localhost');
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

$fname="importar_indicadores2.csv"; //importar_indicadores.csv
$fp=fopen($fname,"r") or die("Erro al abrir el fichero");
$line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

$i=0;
while(!feof($fp))
{

$line = fgets( $fp, 2024 ); 
if ($line <> "") {
 list($id_meta,$codigo_indicador,$indicador) =explode( ";", $line);
 
 //$indicador=mysqli_real_escape_string($con,(strip_tags($indicador,ENT_QUOTES)));

$i=$i+1;
 
 if ($codigo_indicador <>"")
  {
  $sql="INSERT INTO indicadores_gestion (id_meta,nombre_indicador,codigo_indicador) VALUES ('$id_meta','$indicador','$codigo_indicador')";

 $query = mysqli_query($con,$sql);  
 }
 
}
}
fclose($fp);
 echo $i;


?>
 

