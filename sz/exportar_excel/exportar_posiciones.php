<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'c0780240_sz');
define('DB_PASS', '48kobuniFA');
define('DB_NAME', 'c0780240_sz');


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

//$registros_localidades="select localidad, id_loc_padron from localidades";

//$consulta_localidades=mysqli_query($con,$registros_localidades);

$sel_consulta="select localidades.id_loc_padron,localidades.es_localidad, localidades.cantidad_habitantes, localidades.localidad, resultados_elecciones.lista,resultados_elecciones.concejal,listas_elecciones.numero_lista_provincial from resultados_elecciones,listas_elecciones, localidades where resultados_elecciones.localidad =localidades.id_loc_padron and resultados_elecciones.lista =listas_elecciones.id_lista order by localidades.id_loc_padron, resultados_elecciones.concejal desc"; //
// listas_elecciones.nombre_lista_provincial
// and resultados_elecciones.lista=listas_elecciones.id_lista

  
  $query=mysqli_query($con,$sel_consulta);
 		
  $delimiter = ";";
  $filename = "Resultados_Concejales" . date('Y-m-d') . ".csv";
    
   //create a file pointer
  $f = fopen('php://memory', 'w');
	    
    //set column headers


    $fields = array('Codigo Localidad','Localidad','Codigo Lista','nombre lista','Resultado Concejal','cantidad concejales');
    fputcsv($f, $fields, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
	   
		
   while($row = mysqli_fetch_array($query)){
    
	   $Localidad= $row['localidad'];
	   $tipo_localidad= $row['es_localidad'];
	   $cantidad_habitantes=$row['cantidad_habitantes'];
	 
	   $cantidad="";
	 if ($tipo_localidad<>1){  
	   
	  switch ($cantidad_habitantes) {
       case $cantidad_habitantes>=0 && $cantidad_habitantes<=1150:
	    $cantidad=3;
		break;
	   case $cantidad_habitantes>=1151 && $cantidad_habitantes <=2300:
	    $cantidad=5;
		break;
	   case $cantidad_habitantes>=2301 && $cantidad_habitantes<=4000:
	    $cantidad=6;
		break;
	   case $cantidad_habitantes>=4001 && $cantidad_habitantes<=15000:
	   	 $cantidad=8;
		break;
       case	$cantidad_habitantes>15001:
	     $cantidad=12;
		 break;
	   }
    
	 //for ($i=1;$i<=10;$i++){
	    
		$CodigoLocalidad= $row['id_loc_padron'];
		$codigo_lista=$row['lista'];
		$nombre_lista_provincial="Lista ".$row["numero_lista_provincial"];
		$concejal=$row["concejal"];
		
		if($CodigoLocalidad==39)
		 $cantidad=3;
		elseif($CodigoLocalidad==47)
		 $cantidad=5;
		elseif($CodigoLocalidad==71)
		 $cantidad=6;
		elseif($CodigoLocalidad==73)
		 $cantidad=6;
		elseif($CodigoLocalidad==85)
		 $cantidad=8;
		elseif($CodigoLocalidad==88)
		 $cantidad=6;     
		elseif($CodigoLocalidad==90)
		 $cantidad=5;    
		    			       
        $lineData = array($CodigoLocalidad,$Localidad, $codigo_lista,$nombre_lista_provincial, $concejal,$cantidad);
		
	   fputcsv($f, $lineData, $delimiter);
	  
	  } 
	
    }
    
	  fputcsv($f, $lineData, $delimiter);
	
    //move back to beginning of file
    fseek($f, 0);
    
    //set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
    fpassthru($f);
	

//exit;

?>