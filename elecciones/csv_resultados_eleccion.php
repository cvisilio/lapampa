<?php 
 
 if (isset($_GET['id']))
  $elecciones_1 = intval($_GET['id']);
  
 
 require_once('Connections/conexionUsuarios.php');
  
 $f = fopen('php://memory', 'w');
 $fields = array('Localidad','Lista','Senador','Diputado','Intendente');
 $delimiter = ";";
 fputcsv($f, $fields, $delimiter);

 $query_localidades =  mysqli_query($con,"SELECT * FROM localidades where es_localidad_o_comision_fomento=1 order by localidad");	
 
 while($rw = mysqli_fetch_array($query_localidades)){
  
  $cadena_elecciones="";
  $cadena="";
 
    $nombre_localidad=utf8_encode($rw['localidad']);
	$id_loc_padron=$rw['id_loc_padron'];
	
    $query_elecciones =  mysqli_query($con,"SELECT * FROM elecciones where id_eleccion in ($elecciones_1) and cargada=1 order by fecha desc");	
	while($row_elecciones = mysqli_fetch_array($query_elecciones)){
	   $anio=$row_elecciones['fecha']; 
	   $cadena_elecciones.="<b>.".$row_elecciones['tipo'] ." </b><br>";
	   $cargos=$row_elecciones['cargos']; // los cargos que se eligieron
	   $eleccion=$row_elecciones['id_eleccion'];
	   if($row_elecciones['listas_comprar']<> NULL)
	   {
	    $listas_comprar=$row_elecciones['listas_comprar'];
	    list($comparar_uno,$comparar_dos) = explode( ";", $listas_comprar);
	    if (strpos($comparar_dos,",") >0)
	     $a=1;
	    }
		
		switch ($cargos) {
		   case 1 == preg_match('/DN/', $cargos):
			$orden="diputado_nacional";
			break;
		   case 1 == preg_match('/SN/', $cargos):
			$orden="senador_nacional";
			break;
		   case 1 == preg_match('/I/', $cargos):
			$orden="intendente";
			break;
          }
		 
		$sql="select resultados_elecciones.*,listas_elecciones.numero_lista_provincial,listas_elecciones.agrupacion_politica_provincial,listas_elecciones.color,listas_elecciones.abreviatura_provincial from resultados_elecciones,listas_elecciones where resultados_elecciones.lista <>'100' and resultados_elecciones.lista <>'101' and  resultados_elecciones.lista=listas_elecciones.id_lista and resultados_elecciones.localidad='$id_loc_padron' and resultados_elecciones.eleccion='$eleccion' order by resultados_elecciones.". $orden ." desc limit 5";
	 $query_resultados=mysqli_query($con,$sql);
	  while ($rw_resultados=mysqli_fetch_array($query_resultados)){
	
	  $lista1=$rw_resultados['abreviatura_provincial'];
	   
	   switch ($cargos) {
		  case 1 == preg_match('/DN/', $cargos):
			$resultadoD=number_format($rw_resultados['diputado_nacional'],"0",",",".");
			break;
		  case 1 == preg_match('/SN/', $cargos):
		    $resultadoD=number_format($rw_resultados['diputado_nacional'],"0",",",".");
			$resultadoS=number_format($rw_resultados['senador_nacional'],"0",",",".");
			break;
		  case 1 == preg_match('/I/', $cargos):
			$resultado=number_format($rw_resultados['intendente'],"0",",",".");
			break;
          }
	   
	  $color =$rw_resultados['color'];
	  $lbl_class='label label-'.$color;
	  $lista=$rw_resultados['lista'];
	  $lineData = array($nombre_localidad, $lista1 ,$resultadoS,$resultadoD,$resultado);
	  fputcsv($f, $lineData, $delimiter);
	  
	  }  
	   
	 
	 }
	
 
 } 
   fseek($f, 0);
    
    //set headers to download file rather than displayed
    header('Content-Type: text/csv');
	 $filename = "resultados_" . date('Y-m-d') . ".csv";
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
    fpassthru($f);
?>

