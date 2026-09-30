<?php
include('Connections/conexionUsuarios.php');

  $sel_consulta="select mesas.*, localidades.seccion_padron,entidades.circuito from mesas, localidades, entidades WHERE mesas.CodigoEscuela=entidades.Id and entidades.CodigoLocalidad=localidades.id_loc_padron order by mesas.Mesa"; //Escrutada='S'

  $query=mysqli_query($con,$sel_consulta);
 		
  $delimiter = ",";
  $filename = "Mesas_Escrutadas_La_Pampa" . date('Y-m-d') . ".csv";
    
   //create a file pointer
  $f = fopen('php://memory', 'w');
	    
    //set column headers

 
 $Listas[1]["lista"]="135"; // Lista 135
 $Listas[2]["lista"]="131"; // Lista 131
 $Listas[3]["lista"]="136"; // Lista 136
 $Listas[4]["lista"]="133"; // Lista 133
 $Listas[5]["lista"]="137"; // Lista 137
 $Listas[6]["lista"]="87"; // Lista 87
 $Listas[7]["lista"]="10000"; // Blancos
 $Listas[8]["lista"]="10004"; // Blancos
 $Listas[9]["lista"]="10003"; // Blancos
 $Listas[10]["lista"]="10001"; // Blancos
 $Listas[11]["lista"]="10002"; // Nulos
 $Listas[12]["lista"]="10010"; // Total
 
 $Listas[1]["campo_intendente"]="L8I"; //  Lista 135
 $Listas[2]["campo_intendente"]="L5I"; //  Lista 131
 $Listas[3]["campo_intendente"]="L9I"; //  Lista 136
 $Listas[4]["campo_intendente"]="L7I"; //  Lista 133
 $Listas[5]["campo_intendente"]="L10I"; // Lista 137
 $Listas[6]["campo_intendente"]="L4I"; //  Lista 87

 
 $Listas[1]["campo_gobernador"]="L8G"; //  Lista 135
 $Listas[2]["campo_gobernador"]="L5G"; //  Lista 131
 $Listas[3]["campo_gobernador"]="L9G"; //  Lista 136
 $Listas[4]["campo_gobernador"]="L7G"; //  Lista 133
 $Listas[5]["campo_gobernador"]="L10G"; // Lista 137
 $Listas[6]["campo_gobernador"]="L4G"; //  Lista 87

 $Listas[1]["partido"]="Frente NOS"; // Lista 131
 $Listas[2]["partido"]="Juntos Somos el Cambio"; // Lista 135
 $Listas[3]["partido"]="Frente de Todos"; // Lista 136
 $Listas[4]["partido"]="F. Izquierda y Trabajadores"; // Lista 133
 $Listas[5]["partido"]="Consenso Federal"; // Lista 137
 $Listas[6]["partido"]="Unite por la Libertad"; // Lista 87
 	
 $fields = array('Distrito','Seccion', 'circuito', 'Nro de mesa','Nro de lista','Presidente y vice', 'Gobernador y vice', 'Senadores Nacionales', 'Diputados Nacionales', 'Senadores Provinciales', 'Diputados Provinciales','Legisladores provinciales','Intendente Concejales y Consejeros Escolares','Concejales y Consejeros Escolares','cantidad de electores del padron','Cantidad de sobres en la urna');
    fputcsv($f, $fields, $delimiter);

//Distrito,Seccion,Circuito,Nro de mesa,Nro de lista,Presidente y vice,Gobernador y vice,Senadores Nacionales,Diputados Nacionales,Senadores Provinciales,Diputados Provinciales,Legisladores provinciales,"""Intendente, Concejales y Consejeros Escolares""",Concejales y Consejeros Escolares,Cantidad de electores del padron,Cantidad de sobres en la urna	
	
    
    //output each row of the data, format line as csv and write to file pointer
	   
	
   while($row = mysqli_fetch_array($query)){
    
	   $dsitrito="";
		$seccion="";
		$circuito="";
		$mesa="";
		$nro_lista_partido="0";
		$votos_presidente_vice="";
		$votos_gobernador_vice="";
		$votos_senadores_nacionales="";
		$votos_diputados_nacionales="";
		$votos_senadores_provinciales="";
		$votos_diputados_provinciales="";
		$votos_legisladores_provinciales="";
		$votos_intendente=""; 
		$votos_concejales_y_consejeros_escolares="";
		$cantidad_electores_del_padron="";
		$cantidad_de_sobres="";
		
	        			       
        $lineData = array($dsitrito, $seccion, $circuito, $mesa,$nro_lista_partido,$votos_presidente_vice, $votos_gobernador_vice, $votos_senadores_nacionales, $votos_diputados_nacionales, $votos_senadores_provinciales, $votos_diputados_provinciales,$votos_legisladores_provinciales,$votos_intendente,$votos_concejales_y_consejeros_escolares,$cantidad_electores_del_padron,$cantidad_de_sobres);
		
        fputcsv($f, $lineData, $delimiter);
     	
        $dsitrito=11;
		$seccion=$row['seccion_padron'];
		$circuito=$row['circuito'];
		$mesa=$row['Mesa'];
		if ($mesa==698)
		 $circuito=70;
		
		if ($mesa==766)
		 $circuito=83;
        
		if ($mesa==767)
		 $circuito=84;
       
	   if ($mesa==768)
		 $circuito=85;
		 
		  
	
	 for ($i=1;$i<=12;$i++){
	   
		$nro_lista_partido=$Listas[$i]["lista"];
		$lista= $Listas[$i]["campo_gobernador"];
		
		if ($i <=6)
		 $votos_presidente_vice=$row[$lista];
		else
		 $votos_presidente_vice="0";
		 
		$votos_gobernador_vice="";
		$votos_senadores_nacionales="";
		$lista= $lista= $Listas[$i]["campo_intendente"];
		if ($i <=6)
		 $votos_diputados_nacionales=$row[$lista];
		else
		 $votos_diputados_nacionales="0";
		  
		$votos_senadores_provinciales="";
		$votos_diputados_provinciales="";
		$votos_legisladores_provinciales="";
		$votos_intendente="";
		$votos_concejales_y_consejeros_escolares="";
		$cantidad_electores_del_padron="";
		$cantidad_de_sobres="";
		
	        			       
        $lineData = array($dsitrito, $seccion, $circuito, $mesa,$nro_lista_partido,$votos_presidente_vice, $votos_gobernador_vice, $votos_senadores_nacionales, $votos_diputados_nacionales, $votos_senadores_provinciales, $votos_diputados_provinciales,$votos_legisladores_provinciales,$votos_intendente,$votos_concejales_y_consejeros_escolares,$cantidad_electores_del_padron,$cantidad_de_sobres);
        fputcsv($f, $lineData, $delimiter);
	
	}	
	  	  	
		
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