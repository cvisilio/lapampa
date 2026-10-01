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
};



//leemos el fichero

$fname="volumenes_microregiones_rubros.csv"; //importar_indicadores.csv
$fp=fopen($fname,"r") or die("Erro al abrir el fichero");
$line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

$i=0;
while(!feof($fp))
{

$line = fgets( $fp, 2024 ); 
if ($line <> "") {
 list($id_rubro,$nombre,$id_grupo,$Region_1,$Region_2,$Region_3,$Region_4,$Region_5,$Region_6,$Region_7,$Region_8,$Region_9,$Region_10) =explode( ";", $line);
 		

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','1','$Region_1')";
 $query = mysqli_query($con,$sql); 

			
$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','2','$Region_2')";
 $query = mysqli_query($con,$sql); 

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','3','$Region_3')";
 $query = mysqli_query($con,$sql); 


$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','4','$Region_4')";
 $query = mysqli_query($con,$sql); 

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','5','$Region_5')";
 $query = mysqli_query($con,$sql); 

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','6','$Region_6')";
 $query = mysqli_query($con,$sql); 

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','7','$Region_7')";
 $query = mysqli_query($con,$sql); 

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','8','$Region_8')";
 $query = mysqli_query($con,$sql); 

$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','9','$Region_9')";
 $query = mysqli_query($con,$sql); 
 
$sql="INSERT INTO volumenes_rubros_microregiones (rubro,microregion,volumen) VALUES ('$id_rubro','10','$Region_10')";
 $query = mysqli_query($con,$sql);  
 
 $i=$i+1; 
 
}
}
fclose($fp);
 echo $i;


?>
 