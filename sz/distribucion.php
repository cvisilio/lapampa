<?php 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sz');

  $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }
	# obtengo la zona horaria registrada en la db

$cantidad_distribuir=1000;
$maximo=50;
$minimo=5;

$query   = mysqli_query($con,"SELECT sum(votantes) as suma FROM votantesxlocalidad  where hacer=1");
$row1 = mysqli_fetch_array($query);
$total_votantes=$row1['suma'];
$cantidad=0;

$query   = mysqli_query($con,"SELECT * FROM votantesxlocalidad where hacer=1");
while($row = mysqli_fetch_array($query)){

$votantes_localidad= $row['votantes'];
$cantidad=round(($votantes_localidad * $cantidad_distribuir) / $total_votantes,0);
if ($cantidad > $maximo)
 {
  
  $cantidad_distribuir= $cantidad_distribuir-$maximo;
  $total_votantes= $total_votantes - $votantes_localidad;
  $cantidad=$maximo;
 }

echo "<br />";
echo $row['localidad'] . "..." . $cantidad; 

}		

echo "<br /><br />";
echo "Total....: ". $total_votantes; 

?>
