<?php 
/*define('DB_HOST', 'localhost');
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

$fname="importar_obras.csv";
$fp=fopen($fname,"r") or die("Erro al abrir el fichero");
$line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

$i=0;
while(!feof($fp))
{

$line = fgets( $fp, 2024 ); 
if ($line <> "") {
 list($id_donde_extrae_dato,$ministerio,$datos_tecnicos,$nombre_obra,$observaciones,$fecha_finalizada,$Fecha_a_licitar,$cantidad,$porcentaje_avance,$localidad2,$localidad,$datos_catastrales,$monto_adjudicado,$estado,$empresa,$monto_actual,$presupuesto_estimado,$cantidad_nuevos,$numero_prioridad) =explode( ";", $line);
 

$i=$i+1;
$monto_adjudicado=0; //str_replace(",",".",$monto_adjudicado);
$monto_actual= 0;//str_replace(",",".",$monto_actual);

$id_donde_extrae_dato= mysqli_real_escape_string($con,(strip_tags($id_donde_extrae_dato,ENT_QUOTES)));
$ministerio= mysqli_real_escape_string($con,(strip_tags($ministerio,ENT_QUOTES)));

$nombre_obra=mysqli_real_escape_string($con,(strip_tags($nombre_obra,ENT_QUOTES)));
$observaciones=mysqli_real_escape_string($con,(strip_tags($observaciones,ENT_QUOTES)));

$datos_tecnicos=mysqli_real_escape_string($con,(strip_tags($datos_tecnicos,ENT_QUOTES))); //
$datos_catastrales=mysqli_real_escape_string($con,(strip_tags($datos_catastrales,ENT_QUOTES))); //

//$fecha_finalizada=NULL;//explota($fecha_finalizada);
//$Fecha_a_licitar=NULL;//explota($Fecha_a_licitar);
$cantidad=intval($cantidad);
$cantidad_nuevos=intval($cantidad_nuevos); //
$numero_prioridad=intval($numero_prioridad); //
$porcentaje_avance=mysqli_real_escape_string($con,(strip_tags($porcentaje_avance,ENT_QUOTES)));
$localidad2=intval($localidad2);
$localidad=intval($localidad);
$estado=intval($estado);




 $sql="INSERT INTO obras (ministerio,nombre_obra,estado,porcentaje_avance,cantidad,localidad,localidad2,monto_adjudicado,monto_actual,observaciones,id_donde_extrae_dato,datos_tecnicos_caracteristicas,datos_catastrales, cantidad_nuevos,presupuesto_estimado,numero_prioridad) VALUES ('$ministerio','$nombre_obra','$estado','$porcentaje_avance','$cantidad','$localidad','$localidad2','$monto_adjudicado','$monto_actual','$observaciones','$id_donde_extrae_dato','$datos_tecnicos','$datos_catastrales','$cantidad_nuevos','$presupuesto_estimado','$numero_prioridad')";
 
 //id,expediente,fecha_apertura,empresa_adjudicataria_1,presupuesto_oficial,mes_base,partida_contable,cuenta,subclase,finalidad_y_funcion			


 $query = mysqli_query($con,$sql) or trigger_error("Query Failed! SQL: $sql - Error: ".mysqli_error($con), E_USER_ERROR);
 
}
}
fclose($fp);
 echo $i;


?>
 

