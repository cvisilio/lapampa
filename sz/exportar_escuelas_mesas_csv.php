<?php
session_start();
	if (!isset($_SESSION['user_id'])){
			header("location: index.php");//Redirecciona 
			exit;
	
	}
	
	include("config/db.php");
	include("config/conexion.php");
	//Ontengo variables pasadas por GET
	
	$tables="entidades, localidades";
	$campos="localidades.localidad,entidades.Id,entidades.Establecimiento";
		
	$sWhere.=" entidades.CodigoLocalidad=localidades.id_loc_padron";
	$query = mysqli_query($con,"SELECT $campos FROM  $tables where $sWhere ");
	$count=mysqli_num_rows($query);
	
if($count > 0){
    $delimiter = ";";
    $filename = "mesas_" . date('Y-m-d') . ".csv";
    
    //create a file pointer
    $f = fopen('php://memory', 'w');
	    
    //set column headers
    $fields = array('Localidad','Establecimiento','Mesas', 'Desde','Hasta');
    fputcsv($f, $fields, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
   while($row = mysqli_fetch_array($query)){
            $id=$row['Id'];	
            $localidad=$row['localidad'];
		   	$establecimiento=$row['Establecimiento'];
			$count=mysqli_query($con,"select count(*) AS num_mesas, MAX(Mesa) as Maximo, MIN(Mesa) as Minimo from mesas where CodigoEscuela='".$id."'" );
			$rw_count=mysqli_fetch_array($count);
			$Desde=$rw_count['Minimo'];
			$Hasta=$rw_count['Maximo'];
			$num_mesas=$rw_count['num_mesas'];
			       
            $lineData = array($localidad, $establecimiento,$num_mesas,$Desde,$Hasta);
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
exit;

?>