<?php 

 $Idlocalidad=intval($_POST['id_localidad']);
 $cadena_elecciones="";
  $cadena="";
 require_once('Connections/conexionUsuarios.php');
 
 $query =  mysqli_query($con,"SELECT * FROM localidades where id='$Idlocalidad'");
 $num=mysqli_num_rows($query);
  if ($num==1){
    $rw=mysqli_fetch_array($query);
	$nombre_localidad=utf8_encode($rw['localidad']);
	$id_loc_padron=$rw['id_loc_padron'];
	
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
		 
		 $Listas[1]["cargos"]="I,DP,G";
		 $Listas[2]["cargos"]="I,DP,G";
		 $Listas[3]["cargos"]="I,DP,G";
		 $Listas[4]["cargos"]="I,DP,G";
		 $Listas[5]["cargos"]="I,DP,G";
		 $Listas[6]["cargos"]="I,DP,G";
		 $Listas[7]["cargos"]="I,DP,G";
		 $Listas[8]["cargos"]="I,DP,G";
		 $Listas[9]["cargos"]="I,DP,G";
		 $Listas[10]["cargos"]="I,DP,G";
		  
	  
	     $Listas[1]["color"]= "#11285C"; // Azul
		 $Listas[2]["color"]= "#FF6600";  // Amarillo
		 $Listas[3]["color"]= "#333333"; // GRIS OSCURO
		 $Listas[4]["color"]= "#CC3333";   // 
		 $Listas[5]["color"]= "#f00000"; // Rojo
		 $Listas[6]["color"]= "#CC3333"; // Celeste
		 $Listas[7]["color"]= "#CCC000";
		 $Listas[8]["color"]= "#000000";
		 $Listas[9]["color"]= "#CC3333";
		 $Listas[10]["color"]= "#CC3333"; // Gris
		 
		 $Listas[1]["idlista"]="108";
		 $Listas[2]["idlista"]="109";
		 $Listas[3]["idlista"]="110";
		 $Listas[4]["idlista"]="128";
		 $Listas[5]["idlista"]="116-117";
		 $Listas[6]["idlista"]="";
		 $Listas[7]["idlista"]="";
		 $Listas[8]["idlista"]="";
		 $Listas[9]["idlista"]="";
		 $Listas[10]["idlista"]="";
		 
		//"#E95B0F" = Naranja; "#FF0000" = Rojo; "#FFCC00"=  Amarillo; "#005693" = Celeste; "#11285C"=azul
		 
		 
		$registros_localidades="select count(*) as CantidadMesas, localidad, localidades.id, cargo_elecciones, candidatos from mesas, localidades WHERE mesas.CodigoLocalidad=localidades.id_loc_padron and localidades.id_loc_padron='$id_loc_padron'";
		
		$consulta_localidades=mysqli_query($con,$registros_localidades);
		$filas_localidades=mysqli_fetch_array($consulta_localidades);
		
		$localidad = utf8_encode($filas_localidades['localidad']);
		
		$CantidadMesas = $filas_localidades['CantidadMesas'];
		$Idlocalidad=$filas_localidades['id'];
		$cargo_elecciones = $filas_localidades['cargo_elecciones'];
		list($L1,$L2,$L3,$L4,$L5,$L6,$L7,$L8,$L9,$L10)=explode(";",$cargo_elecciones);
		
		 /*$Listas[1]["cargos"]= $L1;
		 $Listas[2]["cargos"]= $L2;
		 $Listas[3]["cargos"]= $L3;
		 $Listas[4]["cargos"]= $L4;
		 $Listas[5]["cargos"]= $L5;
		 */
		 
		 
		 if($Idlocalidad ==1 || $Idlocalidad ==73 || $Idlocalidad ==79 || $Idlocalidad ==21 || $Idlocalidad ==90)
		  $campo="mesas.CodigoLocalidad_a_la_que_Suma";
		 else
		  $campo="mesas.CodigoLocalidad";
		 
		 
			$sel_consulta="select count(*) as cantidadmesas, sum(L1I) as SumaL1I, sum(L1DP) as SumaL1DP, sum(L1G) as SumaL1G,sum(L2I) as SumaL2I, sum(L2DP) as SumaL2DP, sum(L2G) as SumaL2G,sum(L3I) as SumaL3I, sum(L3DP) as SumaL3DP, sum(L3G) as SumaL3G,sum(L4I) as SumaL4I, sum(L4DP) as SumaL4DP, sum(L4G) as SumaL4G,sum(L5I) as SumaL5I, sum(L5DP) as SumaL5DP, sum(L5G) as SumaL5G,sum(L6I) as SumaL6I, sum(L6DP) as SumaL6DP, sum(L6G) as SumaL6G,sum(L7I) as SumaL7I, sum(L7DP) as SumaL7DP, sum(L7G) as SumaL7G,sum(L8I) as SumaL8I, sum(L8DP) as SumaL8DP, sum(L8G) as SumaL8G,sum(L9I) as SumaL9I, sum(L9DP) as SumaL9DP, sum(L9G) as SumaL9G,sum(L10I) as SumaL10I, sum(L10DP) as SumaL10DP, sum(L10G) as SumaL10G from mesas WHERE $campo='$id_loc_padron' and Escrutada='S'";
			
			
		
		 $sql=mysqli_query($con,$sel_consulta);
		 $rw=mysqli_fetch_array($sql);
		
		 $CantidadMesasEscrutadas=0;
		 $CantidadMesasEscrutadas = $rw['cantidadmesas'];
				 
		 for ($i=1;$i<=10;$i++){
		 $Listas[$i]["suma_intendente"]=0;
		 $Listas[$i]["suma_gobernador"]=0;
		 $Listas[$i]["suma_diputado"]=0;
		 $Listas[$i]["porcentaje_intendente"]=0;
		 $Listas[$i]["porcentaje_gobernador"]=0;
		 $Listas[$i]["porcentaje_diputado"]=0;
		 }
		
		 if ($CantidadMesasEscrutadas > 0)
		 {
		 
		 $PorcentajeMesasEscrutadas =round(($CantidadMesasEscrutadas / $CantidadMesas)*100,2);
		 
		 $SumaL1I=$rw['SumaL1I'];
		 $SumaL1DP=$rw['SumaL1DP'];
		 $SumaL1G=$rw['SumaL1G'];
		 
		 $Listas[1]["suma_intendente"]= $SumaL1I;
		 $Listas[1]["suma_diputado"]= $SumaL1DP;
		 $Listas[1]["suma_gobernador"]= $SumaL1G;
		
		 $SumaL2I=$rw['SumaL2I'];
		 $SumaL2DP=$rw['SumaL2DP'];
		 $SumaL2G=$rw['SumaL2G'];
		 $Listas[2]["suma_intendente"]= $SumaL2I;
		 $Listas[2]["suma_diputado"]= $SumaL2DP;
		 $Listas[2]["suma_gobernador"]= $SumaL2G;
		
		 $SumaL3I=$rw['SumaL3I'];
		 $SumaL3DP=$rw['SumaL3DP'];
		 $SumaL3G=$rw['SumaL3G'];
		 $Listas[3]["suma_intendente"]= $SumaL3I;
		 $Listas[3]["suma_diputado"]= $SumaL3DP;
		 $Listas[3]["suma_gobernador"]= $SumaL3G;
		 
		 $SumaL4I=$rw['SumaL4I'];
		 $SumaL4DP=$rw['SumaL4DP'];
		 $SumaL4G=$rw['SumaL4G'];
		 $Listas[4]["suma_intendente"]= $SumaL4I;
		 $Listas[4]["suma_diputado"]= $SumaL4DP;
		 $Listas[4]["suma_gobernador"]= $SumaL4G;
		 
		 $SumaL5I=$rw['SumaL5I'];
		 $SumaL5DP=$rw['SumaL5DP'];
		 $SumaL5G=$rw['SumaL5G'];
		 $Listas[5]["suma_intendente"]= $SumaL5I;
		 $Listas[5]["suma_diputado"]= $SumaL5DP;
		 $Listas[5]["suma_gobernador"]= $SumaL5G;
		 
		 $SumaL6I=$rw['SumaL6I'];
		 $SumaL6DP=$rw['SumaL6DP'];
		 $SumaL6G=$rw['SumaL6G'];
		 $Listas[6]["suma_intendente"]= $SumaL6I;
		 $Listas[6]["suma_diputado"]= $SumaL6DP;
		 $Listas[6]["suma_gobernador"]= $SumaL6G;
		 
		 $SumaL7I=$rw['SumaL7I'];
		 $SumaL7DP=$rw['SumaL7DP'];
		 $SumaL7G=$rw['SumaL7G'];
		 $Listas[7]["suma_intendente"]= $SumaL7I;
		 $Listas[7]["suma_diputado"]= $SumaL7DP;
		 $Listas[7]["suma_gobernador"]= $SumaL7G;
		 
		 $SumaL8I=$rw['SumaL8I'];
		 $SumaL8DP=$rw['SumaL8DP'];
		 $SumaL8G=$rw['SumaL8G'];
		 $Listas[8]["suma_intendente"]= $SumaL8I;
		 $Listas[8]["suma_diputado"]= $SumaL8DP;
		 $Listas[8]["suma_gobernador"]= $SumaL8G;
		 
		 $SumaL9I=$rw['SumaL9I'];
		 $SumaL9DP=$rw['SumaL9DP'];
		 $SumaL9G=$rw['SumaL9G'];
		 $Listas[9]["suma_intendente"]= $SumaL9I;
		 $Listas[9]["suma_diputado"]= $SumaL9DP;
		 $Listas[9]["suma_gobernador"]= $SumaL9G;
		 
		 $SumaL10I=$rw['SumaL10I'];
		 $SumaL10DP=$rw['SumaL10DP'];
		 $SumaL10G=$rw['SumaL10G'];
		 $Listas[10]["suma_intendente"]= $SumaL10I;
		 $Listas[10]["suma_diputado"]= $SumaL10DP;
		 $Listas[10]["suma_gobernador"]= $SumaL10G;
		 
		 
		 $TotalesI= $SumaL1I + $SumaL2I + $SumaL3I + $SumaL4I + $SumaL5I + $SumaL6I+ $SumaL7I+ $SumaL8I+ $SumaL9I+ $SumaL10I;  
		 $TotalesG= $SumaL1G + $SumaL2G + $SumaL3G + $SumaL4G + $SumaL5G+ $SumaL6G+ $SumaL7G+ $SumaL8G+ $SumaL9G+ $SumaL10G;
		 $TotalesDP= $SumaL1DP + $SumaL2DP + $SumaL3DP + $SumaL4DP + $SumaL5DP+ $SumaL6DP+ $SumaL7DP+ $SumaL8DP+ $SumaL9DP+ $SumaL10DP;
		 
		
		 $Totales=$TotalesI + $TotalesG+ $TotalesDP;
		
		$mensaje= "";
		
		if ($TotalesI > 0){
		 if ($SumaL1I > 0){
		  $PorcentajeL1I = ($SumaL1I * 100) / $TotalesI;
		  $Listas[1]["porcentaje_intendente"]= $PorcentajeL1I;
		  }
		 if ($SumaL1DP > 0){
		  $PorcentajeL1DP = ($SumaL1DP * 100) / $TotalesDP;
		  $Listas[1]["porcentaje_diputado"]= $PorcentajeL1DP;
		  } 
		  
		 if ($SumaL1G > 0){
		  $PorcentajeL1G = ($SumaL1G * 100) / $TotalesG;
		  $Listas[1]["porcentaje_gobernador"]= $PorcentajeL1G;
		  }
		 
		 if ($SumaL2I > 0){
		  $PorcentajeL2I = ($SumaL2I * 100) / $TotalesI;
		  $Listas[2]["porcentaje_intendente"]= $PorcentajeL2I;
		  }
		 
		 if ($SumaL2DP > 0){
		  $PorcentajeL2DP = ($SumaL2DP * 100) / $TotalesDP;
		  $Listas[2]["porcentaje_diputado"]= $PorcentajeL2DP;
		  } 
		  
		 if ($SumaL2G > 0){
		  $PorcentajeL2G = ($SumaL2G * 100) / $TotalesG;
		  $Listas[2]["porcentaje_gobernador"]= $PorcentajeL2G;
		  }
		  
		 if ($SumaL3I > 0){
		  $PorcentajeL3I = ($SumaL3I * 100) / $TotalesI;
		  $Listas[3]["porcentaje_intendente"]= $PorcentajeL3I;
		  }
		 if ($SumaL3DP > 0){
		  $PorcentajeL3DP = ($SumaL3DP * 100) / $TotalesDP;
		  $Listas[3]["porcentaje_diputado"]= $PorcentajeL3DP;
		  }  
		 if ($SumaL3G > 0){
		  $PorcentajeL3G = ($SumaL3G * 100) / $TotalesG;
		  $Listas[3]["porcentaje_gobernador"]= $PorcentajeL3G;
		  }
		 
		 if ($SumaL4I > 0){
		  $PorcentajeL4I = ($SumaL4I * 100) / $TotalesI;
		  $Listas[4]["porcentaje_intendente"]= $PorcentajeL4I;
		  }
		 if ($SumaL4DP > 0){
		  $PorcentajeL4DP = ($SumaL4DP * 100) / $TotalesDP;
		  $Listas[4]["porcentaje_diputado"]= $PorcentajeL4DP;
		  } 
		 if ($SumaL4G > 0){
		  $PorcentajeL4G = ($SumaL4G * 100) / $TotalesG;
		  $Listas[4]["porcentaje_gobernador"]= $PorcentajeL4G;
		  }
		 
		 if ($SumaL5I > 0){
		  $PorcentajeL5I = ($SumaL5I * 100) / $TotalesI;
		  $Listas[5]["porcentaje_intendente"]= $PorcentajeL5I;
		  }
		 if ($SumaL5DP > 0){
		  $PorcentajeL5DP = ($SumaL5DP * 100) / $TotalesDP;
		  $Listas[5]["porcentaje_diputado"]= $PorcentajeL5DP;
		  }  
		 if ($SumaL5G > 0){
		  $PorcentajeL5G = ($SumaL5G * 100) / $TotalesG;
		  $Listas[5]["porcentaje_gobernador"]= $PorcentajeL5G;
		  }
		 
		 if ($SumaL6I > 0){
		  $PorcentajeL6I = ($SumaL6I * 100) / $TotalesI;
		  $Listas[6]["porcentaje_intendente"]= $PorcentajeL6I;
		  }
		 if ($SumaL6DP > 0){
		  $PorcentajeL6DP = ($SumaL6DP * 100) / $TotalesDP;
		  $Listas[6]["porcentaje_diputado"]= $PorcentajeL6DP;
		  }   
		 if ($SumaL6G > 0){
		  $PorcentajeL6G = ($SumaL6G * 100) / $TotalesG;
		  $Listas[6]["porcentaje_gobernador"]= $PorcentajeL6G;
		  }
		  
		 if ($SumaL7I > 0){
		  $PorcentajeL7I = ($SumaL7I * 100) / $TotalesI;
		  $Listas[7]["porcentaje_intendente"]= $PorcentajeL7I;
		  }
		 if ($SumaL7DP > 0){
		  $PorcentajeL7DP = ($SumaL7DP * 100) / $TotalesDP;
		  $Listas[7]["porcentaje_diputado"]= $PorcentajeL7DP;
		  }    
		 if ($SumaL7G > 0){
		  $PorcentajeL7G = ($SumaL7G * 100) / $TotalesG;
		  $Listas[7]["porcentaje_gobernador"]= $PorcentajeL7G;
		  }
		  
		  if ($SumaL8I > 0){
		  $PorcentajeL8I = ($SumaL8I * 100) / $TotalesI;
		  $Listas[8]["porcentaje_intendente"]= $PorcentajeL8I;
		  }
		 if ($SumaL8DP > 0){
		  $PorcentajeL8DP = ($SumaL8DP * 100) / $TotalesDP;
		  $Listas[8]["porcentaje_diputado"]= $PorcentajeL8DP;
		  }    
		 if ($SumaL8G > 0){
		  $PorcentajeL8G = ($SumaL8G * 100) / $TotalesG;
		  $Listas[8]["porcentaje_gobernador"]= $PorcentajeL8G;
		  }
		  
		  if ($SumaL9I > 0){
		  $PorcentajeL9I = ($SumaL9I * 100) / $TotalesI;
		  $Listas[9]["porcentaje_intendente"]= $PorcentajeL9I;
		  }
		 if ($SumaL9DP > 0){
		  $PorcentajeL9DP = ($SumaL9DP * 100) / $TotalesDP;
		  $Listas[9]["porcentaje_diputado"]= $PorcentajeL9DP;
		  }    
		 if ($SumaL9G > 0){
		  $PorcentajeL9G = ($SumaL9G * 100) / $TotalesG;
		  $Listas[9]["porcentaje_gobernador"]= $PorcentajeL9G;
		  }
		  
		 if ($SumaL10I > 0){
		   $PorcentajeL10I = ($SumaL10I * 100) / $TotalesI;
		   $Listas[10]["porcentaje_intendente"]= $PorcentajeL10I;
		  }
		 if ($SumaL10DP > 0){
		  $PorcentajeL10DP = ($SumaL10DP * 100) / $TotalesDP;
		  $Listas[10]["porcentaje_diputado"]= $PorcentajeL10DP;
		  }    
		 if ($SumaL10G > 0){
		   $PorcentajeL10G = ($SumaL10G * 100) / $TotalesG;
		   $Listas[10]["porcentaje_gobernador"]= $PorcentajeL10G;
		  } 
		 
		   
		 foreach ($Listas as $key => $row) {
			$aux[$key] = $row['suma_gobernador'];
		 }
		 
		 array_multisort($aux, SORT_DESC, $Listas); 
		 
		 
		 $Porcentaje = 100;
		  
		  }
		}
    
 
 $cantidad_a_mostrar=2;
 if ($TotalesG > 0)
  {
   $comienzo = 0;
   }
  else
   {$comienzo = 1; 
    $cantidad_a_mostrar=0;
    $cadena_elecciones= "<h3> Esperando Resultados... </h3>";
  }
  
   $color="#000000";
   $cadena_elecciones="<table><tr><td><h5><strong style='color:".$color."'>Escrutadas: ".$CantidadMesasEscrutadas. " de ". $CantidadMesas. "</strong></h5></td><td><h5><strong style='color:".$color."'>". " = ".$PorcentajeMesasEscrutadas ."%</strong></h5></td></tr><tr>";
   
   for ($i=$comienzo; $i<=$cantidad_a_mostrar;$i++){ //dejar $i=0 
    $color =$Listas[$i]["color"];
	$lbl_class='label label-'.$color;
	$lista1=$Listas[$i]["partido"];
	$resultado= "   ". round($Listas[$i]["porcentaje_gobernador"],2)."%"; //porcentaje_intendente
	$cadena_elecciones.= "<td><h4><strong style='color:".$color."'>".$lista1 . "</strng></h4></td><td><h4><strong style='color:".$color."'>".$resultado ."</strong></h4></td></tr><tr>";
   
   }	  
   
    $cadena_elecciones.= "</tr></table><br>";
	 
	
   $cadena="<h3>". $nombre_localidad . "</h3><br>".$cadena_elecciones;
  }
  else
  {
  $cadena="...";
  }
  echo $cadena; //$cadena;
?>

