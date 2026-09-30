<?php 
require_once("../classes/Login.php");
 $login = new Login();
 if ($login->isUserLoggedIn() == true) 
  {	  
   		 
	require_once ("../conexion.php");
   	
    $action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
    if($action == 'ajax'){
	$query = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));
    $daterange = mysqli_real_escape_string($con,(strip_tags($_REQUEST['daterange'], ENT_QUOTES)));
	$motivo =intval($_REQUEST['motivo']);
	$tables="transferencias, localidades, objetivos_motivos";
	$campos="sum(transferencias.monto) as monto,localidades.localidad,localidades.orden,objetivos_motivos.motivo";
	$sWhere=" transferencias.id_localidad=localidades.id and transferencias.afectacion=objetivos_motivos.id and (transferencias.observaciones LIKE '%".$query."%'";
	
	$sWhere.=" OR objetivos_motivos.motivo LIKE '%".$query."%'";
	$sWhere.=" OR localidades.localidad LIKE '%".$query."%'";
	$sWhere.=" OR transferencias.monto LIKE '".$query."%') ";
	
	if($motivo >0)
	 $sWhere.=" and objetivos_motivos.id ='$motivo'";
	
	if (!empty($daterange)){
		list ($f_inicio,$f_final)=explode(" - ",$daterange);//Extrae la fecha inicial y la fecha final en formato espa?ol
		list ($dia_inicio,$mes_inicio,$anio_inicio)=explode("/",$f_inicio);//Extrae fecha inicial 
		$fecha_inicial="$anio_inicio-$mes_inicio-$dia_inicio 00:00:00";//Fecha inicial formato ingles
		list($dia_fin,$mes_fin,$anio_fin)=explode("/",$f_final);//Extrae la fecha final
		$fecha_final="$anio_fin-$mes_fin-$dia_fin 23:59:59";
		
		$sWhere .= " and transferencias.fecha_registro between '$fecha_inicial' and '$fecha_final' ";
	    }
	
	$sWhere.="  group by localidades.id order by localidades.orden";
	
	$query = mysqli_query($con,"SELECT $campos FROM  $tables where $sWhere");
	
	$count_query   = mysqli_query($con,"SELECT count(*) AS numrows FROM $tables where $sWhere ");
	if ($row= mysqli_fetch_array($count_query)){$numrows = $row['numrows'];}
	else {echo mysqli_error($con);}
	
	//$numrows=1;
    if($numrows >0)
	 { 
	  $desde_fechas= $dia_inicio. "-". $mes_inicio. "-" .$anio_inicio. "/" .$dia_fin. "-". $mes_fin. "-" .$anio_fin;
	  
	  $delimiter = ";";
	  $filename = "InformeTransferencias_" . date('Y-m-d') . ".csv";
    
    //create a file pointer
    $f = fopen('php://memory', 'w');
	    
    //set column headers
    $fields = array('Localidad','Concepto', 'Total Transferido');
    fputcsv($f, $fields, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
   while($row = mysqli_fetch_array($query)){
           $localidad=$row['localidad'];
		   $motivo=utf8_decode($row['motivo']);
		   $monto=$row['monto'];
		    $lineData = array($localidad, $motivo ,$monto);
        fputcsv($f, $lineData, $delimiter);
    }
    
    //move back to beginning of file
    fseek($f, 0);
    
    //set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
    fpassthru($f);
}
}
}
exit;