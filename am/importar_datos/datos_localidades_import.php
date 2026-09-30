<?php 

 define('DB_HOST','localhost');
 define('DB_USER','c2271089_amlp');
 define('DB_PASS','36maBAsera');
 define('DB_NAME','c2271089_amlp');
   


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
};



//leemos el fichero

$fname="importar_orden.csv"; //importar_indicadores.csv
$fp=fopen($fname,"r") or die("Erro al abrir el fichero");
$line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

$i=0;
while(!feof($fp))
{

$line = fgets( $fp, 2024 ); 
if ($line <> "") {
 list($id_localidad,$nombre_localidad,$orden) =explode( ";", $line);
 
 			
 $sql = "UPDATE localidades SET orden='".$orden."' WHERE id='".$id_localidad."'";


 $query = mysqli_query($con,$sql); 
 $i=$i+1; 
 
}
}
fclose($fp);
 echo $i;


?>
 