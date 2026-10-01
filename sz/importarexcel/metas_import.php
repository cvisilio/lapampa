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

$fname="importar_metas4.csv"; //importar_indicadores.csv
$fp=fopen($fname,"r") or die("Erro al abrir el fichero");
$line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

$i=0;
while(!feof($fp))
{

$line = fgets( $fp, 2024 ); 
if ($line <> "") {
 list($id_objetivo,$id,$codigo_meta,$meta) =explode( ";", $line);
 			

$meta=mysqli_real_escape_string($con,(strip_tags($meta,ENT_QUOTES)));


 
 if ($id_objetivo >0)
  {
  $sql="INSERT INTO metas_gestion (id,descripcion,id_objetivo,codigo_meta) VALUES ('$id','$meta','$id_objetivo','$codigo_meta')";

 $query = mysqli_query($con,$sql); 
 $i=$i+1; 
 }
 
}
}
fclose($fp);
 echo $i;


?>
 

