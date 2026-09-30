<?php
include('Connections/conexionUsuarios.php');

//$registros_localidades="select localidad, id_loc_padron from localidades";

//$consulta_localidades=mysqli_query($con,$registros_localidades);

$sel_consulta="select CodigoLocalidad, localidad, sum(L1I) as SumaL1I, sum(L1G) as  SumaL1G,sum(L2I) as SumaL2I, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10G) as SumaL10G, sum(L1I +L2I + L3I+L4I+L5I+L6I+L7I+L8I+L9I+L10I) as suma_intendente, sum(L1G +L2G + L3G+L4G+L5G+L6G+L7G+L8G+L9G+L10G) as suma_presidente from mesas, localidades where  mesas.CodigoLocalidad =localidades.id_loc_padron group by CodigoLocalidad";

  
  $query=mysqli_query($con,$sel_consulta);
 		
  $delimiter = ";";
  $filename = "Resultados_La_Pampa" . date('Y-m-d') . ".csv";
    
   //create a file pointer
  $f = fopen('php://memory', 'w');
	    
    //set column headers

 $Listas[1]["lista"]="13"; // Lista 13
 $Listas[2]["lista"]="36"; // Lista 36
 $Listas[3]["lista"]="57"; // Lista 57
 $Listas[4]["lista"]="87"; // Lista 87
 $Listas[5]["lista"]="131"; // Lista 131
 $Listas[6]["lista"]="132"; // Lista 132
 $Listas[7]["lista"]="133"; // Lista 133
 $Listas[8]["lista"]="135"; // Lista 135
 $Listas[9]["lista"]="136"; // Lista 136
 $Listas[10]["lista"]="137"; // Lista 137
 $Listas[11]["lista"]="10000"; // Blancos
 $Listas[12]["lista"]="10001"; // Nulos
 $Listas[13]["lista"]="10010"; // Total
 
 $Listas[1]["campo_intendente"]="L1I"; // Lista 13
 $Listas[2]["campo_intendente"]="L2I"; // Lista 36
 $Listas[3]["campo_intendente"]="L3I"; // Lista 57
 $Listas[4]["campo_intendente"]="L4I"; // Lista 87
 $Listas[5]["campo_intendente"]="L5I"; // Lista 131
 $Listas[6]["campo_intendente"]="L6I"; // Lista 132
 $Listas[7]["campo_intendente"]="L7I"; // Lista 133
 $Listas[8]["campo_intendente"]="L8I"; // Lista 135
 $Listas[9]["campo_intendente"]="L9I"; // Lista 136
 $Listas[10]["campo_intendente"]="L10I"; // Lista 137
 
 $Listas[1]["campo_gobernador"]="L1G"; // Lista 13
 $Listas[2]["campo_gobernador"]="L2G"; // Lista 36
 $Listas[3]["campo_gobernador"]="L3G"; // Lista 57
 $Listas[4]["campo_gobernador"]="L4G"; // Lista 87
 $Listas[5]["campo_gobernador"]="L5G"; // Lista 131
 $Listas[6]["campo_gobernador"]="L6G"; // Lista 132
 $Listas[7]["campo_gobernador"]="L7G"; // Lista 133
 $Listas[8]["campo_gobernador"]="L8G"; // Lista 135
 $Listas[9]["campo_gobernador"]="L9G"; // Lista 136
 $Listas[10]["campo_gobernador"]="L10G"; // Lista 137

 $Listas[1]["partido"]="Unidad de la Izquierda"; // Lista 13
 $Listas[2]["partido"]="Partido Autonomista"; // Lista 36
 $Listas[3]["partido"]="Mov. Acción Vecinal"; // Lista 57
 $Listas[4]["partido"]="Unite por la Libertad"; // Lista 87
 $Listas[5]["partido"]="Frente NOS"; // Lista 131
 $Listas[6]["partido"]="Frente Patriota"; // Lista 132
 $Listas[7]["partido"]="F. Izquierda y Trabajadores"; // Lista 133
 $Listas[8]["partido"]="Juntos Somos el Cambio"; // Lista 135
 $Listas[9]["partido"]="Frente de Todos"; // Lista 136
 $Listas[10]["partido"]="Consenso Federal"; // Lista 137
 											 		
	
    $fields = array('Localidad','Lista','Presidente','% Presidente', 'Diputado','% Diputado');
    fputcsv($f, $fields, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
	   
	
   while($row = mysqli_fetch_array($query)){
    
	$Localidad= $row['localidad'];
    
	 for ($i=1;$i<=10;$i++){
	    
		$suma_intendente= $row['suma_intendente'];
		$suma_presidente=$row['suma_presidente'];
		$nro_lista_partido=$Listas[$i]["lista"];
		$nombre_partido=$Listas[$i]["partido"];
		$lista= 'SumaL'.$i.'G';
		 
		$votos_presidente_vice=$row[$lista];
		
		$porcentaje_presidente_vice=round($votos_presidente_vice / $suma_presidente * 100,2);
		
		$votos_gobernador_vice="";
		$votos_senadores_nacionales="";
		$lista= 'SumaL'.$i.'I';
		if ($row[$lista]  > 0)
		 {
		 $votos_diputados_nacionales=$row[$lista];
		 $votos_diputados_nacionales= number_format($votos_diputados_nacionales,0,',','.');
		
		 $porcentaje_diputados_nacionales=round($votos_diputados_nacionales / $suma_intendente * 100,2);
		 $porcentaje_diputados_nacionales= number_format($porcentaje_diputados_nacionales,2,',','.')."%";
		
		 }
		else
		 {
		 $votos_diputados_nacionales="";
		 $porcentaje_diputados_nacionales="";
		 }
		 
		$votos_senadores_provinciales="";
		$votos_diputados_provinciales="";
		$votos_legisladores_provinciales="";
		$votos_intendente="";
		$votos_concejales_y_consejeros_escolares="";
		$cantidad_electores_del_padron="";
		$cantidad_de_sobres="";
		
		
		$todo_nombre= $nombre_partido . " - ". $nro_lista_partido;
		    			       
        $lineData = array($Localidad, $todo_nombre,number_format($votos_presidente_vice,0,',','.'), number_format($porcentaje_presidente_vice,2,',','.')."%", $votos_diputados_nacionales,$porcentaje_diputados_nacionales);
		
	   fputcsv($f, $lineData, $delimiter);
	   
	}	
	  
	  $lineData = array("","","","","","");
		
	   fputcsv($f, $lineData, $delimiter);	  	
		
    }
    
    //move back to beginning of file
    fseek($f, 0);
    
    //set headers to download file rather than displayed
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
    fpassthru($f);

exit;

?>