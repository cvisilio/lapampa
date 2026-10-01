<?php 

/*define('DB_HOST', 'localhost');
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
};



//leemos el fichero

$fname="importar_datos_localidades.csv";
$fp=fopen($fname,"r") or die("Erro al abrir el fichero");
$line = fgets( $fp, 2024 ); //leemos la cabecera del excel 2024 es una medida del block, recomendado

$i=0;
while(!feof($fp))
{
$l7="-";
$l10="-";
$line = fgets( $fp, 2024 ); 
if ($line <> "") {
 list($id_padron,$localidad,$intendente,$partido,$l1,$l2,$l3,$l4,$l5,$l6,$l7,$l8,$l9,$l10) =explode( ";", $line);
 
$l1="L1:"."G-D-" . $l1;
$l2=";L2:"."G-D-" . $l2;
$l3=";L3:"."G-D-" . $l3;
$l4=";L4:"."G-D-" . $l4;
$l5=";L5:"."G-D-" . $l5;
$l6=";L6:"."G-D-" . $l6;
$l7=";L7:". $l7;
$l8=";L8:"."G-D-" . $l8;
$l9=";L9:"."G-D-" . $l9;
$l10= ";L10:". $l10;

$intendente=mysqli_real_escape_string($con,(strip_tags($intendente,ENT_QUOTES)));

$cargo_elecciones=$l1.$l2.$l3.$l4.$l5.$l6.$l7.$l8.$l9.$l10;
 
 //id_padron	Localidad	Intendente	Partido	Frejupa 	Cambiemos 	Pueblo_Nuevo	Com_Organizada	F_POP_PAMP	Partido_Socialista	Comp_Ciudadano	MST	Desde_el_Pie	Juntas_Vecinales


$i=$i+1;
 

//cantidad_habitantes='".$habitantes."', , id_loc_padron='".$id_padron."'

  $sql = "UPDATE localidades SET  intendente='".$intendente."', partido='".$partido."',  cargo_elecciones='".$cargo_elecciones."' WHERE id_loc_padron='".$id_padron."'";
 
 //id,expediente,fecha_apertura,empresa_adjudicataria_1,presupuesto_oficial,mes_base,partida_contable,cuenta,subclase,finalidad_y_funcion			

 
 $query = mysqli_query($con,$sql);  
 
}
}
fclose($fp);
 echo $i;


?>
 

