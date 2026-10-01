<style type="text/css">
<!--
.Estilo2 {
	color:#FF0000;
	font-weight: bold;
}
-->
</style>

<?php 

define('DB_HOST', 'localhost');
define('DB_USER', 'c0780240_sz');
define('DB_PASS', '48kobuniFA');
define('DB_NAME', 'c0780240_sz');


 $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }

	
$query="j&oacute;venes";
//$query2="~los +jovenes ~no ~tienen +futuro";

$cadbusca="SELECT *, MATCH (dato) AGAINST ('".$query."') as relevancia from datos WHERE MATCH (dato) AGAINST ('".$query."') HAVING relevancia > 0.2 ORDER BY relevancia desc";


 
 $result=mysqli_query($con,$cadbusca);
 
  $i=1;

 while ($row = mysqli_fetch_array($result))
   {
   
 
 echo "------------------------------- " . $i . "------------------------------- ";
 $texto= $row['dato'];
 $texto=str_replace($query,'<span class="Estilo2">'.$query.'</span>',$texto);
 echo $texto . "<br />";
$i++;
    
  }
  
 ?>
