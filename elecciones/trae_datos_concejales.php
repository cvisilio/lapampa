 <?php 
 session_start();
  $id_localidad=intval($_POST['id_localidad']);
  $Idlocalidad=$id_localidad;
  $cadena_elecciones="";
  $cadena="";
  $anio_consulta_empleados=2025;
  $mes_consulta_empleados=10;
 
 
  $foto_intendente="https://lapampaperonista.com.ar/elecciones/fotos_intendentes/".$id_localidad.".png";
  
  $ver_mas=1;//$_SESSION['ver_mas'];
 
  $Listas[1]["partido"]="FREJUPA";
  $Listas[2]["partido"]="JXC";
  $Listas[3]["partido"]="Com. Org.";
  $Listas[4]["partido"]="Org. Civica";
  $Listas[5]["partido"]="F. de Izq.";
  $Listas[6]["partido"]="Desde el Pie";
  $Listas[7]["partido"]="MOFEPA";
  $Listas[8]["partido"]="Part. Libertario";
  $Listas[9]["partido"]="U. Vecinal";
  $Listas[10]["partido"]="Junta. Vecinal";
  
  $Listas[1]["color"]= "#11285C"; // Azul
  $Listas[2]["color"]= "#FF6600";  // Amarillo
  $Listas[3]["color"]= "#00CCFF"; // Celeste
  $Listas[4]["color"]= "#CC3333";   // 
  $Listas[5]["color"]= "#f00000"; // Rojo
  $Listas[6]["color"]= "#CC3333"; // Celeste
  $Listas[7]["color"]= "#CCC000";
  $Listas[8]["color"]= "#000000";
  $Listas[9]["color"]= "#2D572C";
  $Listas[10]["color"]= "#2D572C"; // Verde 
  
  
  require_once('Connections/conexionUsuarios.php');
 
 
 $query =  mysqli_query($con,"SELECT localidades.*,listas_elecciones.color2,listas_elecciones.abreviatura_provincial from localidades, listas_elecciones where localidades.lista_ganadora_proxima = listas_elecciones.id_lista and localidades.id='$id_localidad'");
 $num=mysqli_num_rows($query);
  if ($num==1){
    $rw=mysqli_fetch_array($query);
	$nombre_localidad=utf8_encode($rw['localidad']);
	$intendente_proximo=utf8_encode($rw['intendente_proximo']);
	$abreviatura_provincial=$rw['abreviatura_provincial'];
	
	$cantidad_habitantes=$rw['cantidad_habitantes'];
	
	$leyenda_intendente="Intendente/a";
	$leyenda_concejales="Concejales";
		
	if(intval($rw['es_localidad'])==2) // es_localidad = 2 es comisión de fomento
	 {
	 $leyenda_intendente="Presidente/a C.F.";
	 $leyenda_concejales="Vocales";
	
	 }
	 
	$id_loc_padron=intval($rw['id_loc_padron']);
	
	$id_loc_gob_c=intval($rw['id_loc_gob_c']);
	
	
	$eleccion=10;
	
	$color=$rw['color2'];
		  
	/* desde acá resultados de escrutinio */
	if($Idlocalidad ==1 || $Idlocalidad ==73 || $Idlocalidad ==79 || $Idlocalidad ==21 || $Idlocalidad ==90)
		  $campo="mesas_gobernador_2023.CodigoLocalidad_a_la_que_Suma";
		 else
		  $campo="mesas_gobernador_2023.CodigoLocalidad";
		 
		 
    $sel_consulta="select count(*) as cantidadmesas, sum(L1I) as SumaL1I, sum(L1DP) as SumaL1DP, sum(L1G) as SumaL1G,sum(L2I) as SumaL2I, sum(L2DP) as SumaL2DP, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3DP) as SumaL3DP, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4DP) as SumaL4DP, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5DP) as SumaL5DP, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6DP) as SumaL6DP, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7DP) as SumaL7DP, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8DP) as SumaL8DP, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9DP) as SumaL9DP, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10DP) as SumaL10DP, sum(L10G) as SumaL10G from mesas_gobernador_2023 WHERE $campo='$id_loc_padron' and Escrutada='S'";
	
	$sql=mysqli_query($con,$sel_consulta);
    $rw=mysqli_fetch_array($sql);
	
	/*cantidad empleados y funcionarios */
	
	$query_empleados = mysqli_query($con, "SELECT * FROM agentes WHERE id_dependencia=$id_loc_gob_c AND anio=$anio_consulta_empleados AND mes=$mes_consulta_empleados");

     if ($rw_empleados = mysqli_fetch_assoc($query_empleados)) {
      $cantidad_funcionarios = $rw_empleados['funcionarios'];
      $cantidad_empleados   = $rw_empleados['empleados'];
    } else {
      $cantidad_funcionarios = 0;
      $cantidad_empleados   = 0;
    }
    
	/*hasta acá cantidad empleados y funcionarios */
	
	/* Intendentes Anteriores */
	
	 $anio_actual = date("Y");

	 $query_historial_intendencias = mysqli_query(
			$con,
			"SELECT * FROM historial_intendencias 
			 WHERE id_loc_gob_c = $id_loc_gob_c 
			 ORDER BY anio DESC"
		);
		
		$cadena_historial = ""; // acumulador
		
	if (mysqli_num_rows($query_historial_intendencias) > 0) {
        $cadena_historial .= "<h4>Intendentes anteriores</h4>";
        $cadena_historial .= '<ul class="list-group">';

     while ($rw_historial_intendencias = mysqli_fetch_array($query_historial_intendencias)) {

        $partido        = mysqli_real_escape_string($con, (strip_tags($rw_historial_intendencias['partido'], ENT_QUOTES)));
        $intendente     = mysqli_real_escape_string($con, (strip_tags($rw_historial_intendencias['intendente'], ENT_QUOTES)));
		$intendente     = ucwords($intendente);
        $anio           = intval($rw_historial_intendencias['anio']);
		$anio_fin = $anio + 4;
	
        if ($anio_actual >= $anio && $anio_actual <= $anio_fin) {
            $anio_texto = $anio ."-".$anio_fin . " actual";
        } else {
            $anio_texto = $anio ."-".$anio_fin;
        }
		                    
        $color_partido  = mysqli_real_escape_string($con, (strip_tags($rw_historial_intendencias['color_partido'], ENT_QUOTES)));

        // Badge del partido con color personalizado
        $badge_color = "<span style='background:$color_partido; color:#fff; padding:2px 6px; border-radius:4px; font-size:12px;'>$partido</span>";

        $cadena_historial .= '<li class="list-group-item">';
        $cadena_historial .= "<strong>$anio_texto</strong> - $intendente $badge_color";
        $cadena_historial .= "</li>";
      }
       $cadena_historial .= "</ul>";
    }


       /*hasta acá Intendentes Anteriores */
	
		

	 for ($i=1;$i<=10;$i++){
		 $Listas[$i]["suma_intendente"]=0;
	 }
	
	 $SumaL1I=$rw['SumaL1I'];
	 $SumaL2I=$rw['SumaL2I'];
	 $SumaL3I=$rw['SumaL3I'];
	 $SumaL4I=$rw['SumaL4I'];
	 $SumaL5I=$rw['SumaL5I'];
	 $SumaL6I=$rw['SumaL6I'];
	 $SumaL7I=$rw['SumaL7I'];
	 $SumaL8I=$rw['SumaL8I'];
	 $SumaL9I=$rw['SumaL9I'];
	 $SumaL10I=$rw['SumaL10I'];
	   
	 $Listas[1]["suma_intendente"]= $SumaL1I;
	 $Listas[2]["suma_intendente"]= $SumaL2I;
	 $Listas[3]["suma_intendente"]= $SumaL3I;
	 $Listas[4]["suma_intendente"]= $SumaL4I;
	 $Listas[5]["suma_intendente"]= $SumaL5I;
	 $Listas[6]["suma_intendente"]= $SumaL6I;
	 $Listas[7]["suma_intendente"]= $SumaL7I;
	 $Listas[8]["suma_intendente"]= $SumaL8I;
	 $Listas[9]["suma_intendente"]= $SumaL9I;
	 $Listas[10]["suma_intendente"]= $SumaL10I;
	 
	 $TotalesI= $SumaL1I + $SumaL2I + $SumaL3I + $SumaL4I + $SumaL5I + $SumaL6I+ $SumaL7I+ $SumaL8I+ $SumaL9I+ $SumaL10I;
	 
	 if ($SumaL1I > 0){
	  $PorcentajeL1I = ($SumaL1I * 100) / $TotalesI;
	  $Listas[1]["porcentaje_intendente"]= $PorcentajeL1I;
	 }
	
	 if ($SumaL2I > 0){
	  $PorcentajeL2I = ($SumaL2I * 100) / $TotalesI;
	  $Listas[2]["porcentaje_intendente"]= $PorcentajeL2I;
	 }
		 
	 if ($SumaL3I > 0){
	  $PorcentajeL3I = ($SumaL3I * 100) / $TotalesI;
	  $Listas[3]["porcentaje_intendente"]= $PorcentajeL3I;
	  }
	
	 if ($SumaL4I > 0){
	  $PorcentajeL4I = ($SumaL4I * 100) / $TotalesI;
	  $Listas[4]["porcentaje_intendente"]= $PorcentajeL4I;
	  }
	
	 if ($SumaL5I > 0){
	  $PorcentajeL5I = ($SumaL5I * 100) / $TotalesI;
	  $Listas[5]["porcentaje_intendente"]= $PorcentajeL5I;
	  }
	
	 if ($SumaL6I > 0){
	  $PorcentajeL6I = ($SumaL6I * 100) / $TotalesI;
	  $Listas[6]["porcentaje_intendente"]= $PorcentajeL6I;
	  }
	
	 if ($SumaL7I > 0){
	  $PorcentajeL7I = ($SumaL7I * 100) / $TotalesI;
	  $Listas[7]["porcentaje_intendente"]= $PorcentajeL7I;
	  }
	
	 if ($SumaL8I > 0){
	  $PorcentajeL8I = ($SumaL8I * 100) / $TotalesI;
	  $Listas[8]["porcentaje_intendente"]= $PorcentajeL8I;
	 }
	
	 if ($SumaL9I > 0){
	  $PorcentajeL9I = ($SumaL9I * 100) / $TotalesI;
	  $Listas[9]["porcentaje_intendente"]= $PorcentajeL9I;
	  }
	
	 if ($SumaL10I > 0){
	   $PorcentajeL10I = ($SumaL10I * 100) / $TotalesI;
	   $Listas[10]["porcentaje_intendente"]= $PorcentajeL10I;
	  }
	 
	
  foreach ($Listas as $key => $row) {
			$aux[$key] = $row['suma_intendente'];
		 }
		 
  array_multisort($aux, SORT_DESC, $Listas); 		
  
  $color_secundario="#CDCDCD";
  $leyenda="Cantidad Votos ".$leyenda_intendente;
  $cadena_elecciones="<table class='table'><tr><td colspan='2'><h4><strong style='color:".$color_secundario."'>".$leyenda . "</strong></h4></td><tr><tr>";
  $cantidad_a_mostrar=2;
  $comienzo=0;
   
   for ($i=$comienzo; $i<=$cantidad_a_mostrar;$i++){ //dejar $i=0 
    
	$color1 =$Listas[$i]["color"];
	$lista1=$Listas[$i]["partido"];
	
   if($Listas[$i]["suma_intendente"]>0)
    {	
	$porcentaje=round($Listas[$i]["porcentaje_intendente"],1); //porcentaje_intendente
    $resultado= number_format($Listas[$i]["suma_intendente"],0,'','.'); // votos
	$cadena_elecciones.= "<td><h4><strong style='color:".$color1."'>".$lista1 . "</strng></h4></td><td><h4><strong style='color:".$color1."'>&nbsp;&nbsp;".$porcentaje ."%&nbsp;(".$resultado ." votos)</strong></h4></td></tr><tr>";
    }
	
   }	  
   
    $cadena_elecciones.= "</tr></table><br>";
 	
		/* Hasta acá resultados de escrutinio */
	if($ver_mas==1)
	 $visualizar_foto='<img height="70px" width="70px" src="'.$foto_intendente .'" class="img-responsive">';
	else
	 $visualizar_foto="";
	  	  
    $sql="select concejales.*,listas_elecciones.numero_lista_provincial,listas_elecciones.agrupacion_politica_provincial,listas_elecciones.color2,listas_elecciones.abreviatura_provincial from concejales,listas_elecciones where concejales.id_lista =listas_elecciones.id_lista and concejales.id_localidad='$id_loc_padron' and concejales.eleccion='$eleccion' order by concejales.posicion";
	 $query_resultados=mysqli_query($con,$sql);
		   
	 $cadena_elecciones.="<table class='table'><tr><td colspan='2'><h4><strong style='color:".$color_secundario."'>" . $leyenda_intendente . "</strong></h4></td> </tr><tr><td><h4><strong style='color:".$color."'>". $intendente_proximo."</strong></h4><h5><strong style='color:".$color."'>". " (".$abreviatura_provincial .")</strong></h5> </td><td>".$visualizar_foto."</td></tr><tr><tr><td colspan='2'><h4><strong style='color:".$color_secundario."'>". $leyenda_concejales .  "</strong></h4></td></tr><tr>";
		
	while ($rw_resultados=mysqli_fetch_array($query_resultados))
	  {
	   
	   $posicion=$rw_resultados['posicion'];
	   $color=$rw_resultados['color2'];
	   $apellido_nombre=utf8_encode($rw_resultados['apellido_nombre']);	   
	   $lista=$rw_resultados['abreviatura_provincial'];
	   
	   $cadena_elecciones.= "<td><h5><strong style='color:".$color."'>".$posicion .") ".$lista . " : " . "</strng></h5></td><td><h5><strong style='color:".$color."'>".$apellido_nombre ."</strong></h5></td></tr><tr>";
	  
	  }  
	  
	  $cadena_elecciones.= "</tr></table><hr />";
	  
	  $cadena_datos_1 .= "<h4>Datos Demograficos</h4>";
      $cadena_datos_1 .= '<ul class="list-group">';
      $cadena_datos_1 .= '<li class="list-group-item">'; 
	   
	  $cadena_datos_1.= "Poblaci&oacute;n: ".number_format($cantidad_habitantes,0,"","."). " habitantes";
	  $cadena_datos_1.="</li>";
	  $cadena_datos_1 .= '<li class="list-group-item">'; 
	  $cadena_datos_1.= "Empleados: ".$cantidad_empleados. " Funcionarios: ".$cantidad_funcionarios; 	   
	  $cadena_datos_1.="</li>";	
      $cadena="<h3>". $nombre_localidad . "</h3><br>".$cadena_elecciones.  $cadena_datos_1 .$cadena_historial;
   
  }
  else
  {
  $cadena="...";
  }
  echo $cadena;
?>