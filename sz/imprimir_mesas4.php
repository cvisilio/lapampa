<?php
 session_start();

//ejemplo parametros cell librería tcpdf http://www.fpdf.org/en/doc/cell.htm 
 
/* 
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sz');
*/

define('DB_HOST', 'localhost');
define('DB_USER', 'c0780240_sz');
define('DB_PASS', '48kobuniFA');
define('DB_NAME', 'c0780240_sz');

 $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("imposible conectarse: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Conexión falló: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    } 
 
require_once('pdf/_tcpdf_5.0.002/tcpdf.php');

class PDF_EAN12 extends tcpdf
{
function EAN10($x, $y)
{
	$a=1;
}
}

 $consulta22 = mysqli_query($con,"SELECT mesas.Mesa, mesas.CodigoLocalidad,localidades.*, entidades.Telefono_Referencia,entidades.Establecimiento FROM mesas, localidades,entidades where  mesas.CodigoEscuela=entidades.Id and mesas.CodigoLocalidad=localidades.id_loc_padron order by localidades.localidad");
	  
	// $cantidad_registros=mysqli_num_rows($consulta) 
    
	
//	for ($i = 0; $i <= $cantidad_registros; $i++) {
      // if (!mysqli_data_seek($consulta, $i)) {
      //  exit;
	//	}
	
	$pdf=new PDF_EAN12();
	$pdf->AddPage();
	$pdf->SetMargins(0, 0, 0, true);
	$cantidad_registros=mysqli_num_rows($consulta22);
	$x_i=2;
	$y=4;
	$ancho_recuadro=102;
    $x_d=$x_i+$ancho_recuadro+$x_i;
	
 	$j=1;
	$i=0;
	
	$alto_recuadro=135; //con 70 entran 4 verticales
	
	 while($fila = mysqli_fetch_array($consulta22)){			
       
		   $i=$i+1; 		
			
			//$this->SetTextColor($data['color']['red'], $data['color']['green'], $data['color']['blue']);
			 
			 $id_loc_padron=$fila['id_loc_padron'];
			 $cargo_elecciones= $fila['cargo_elecciones'];
			 $CodigoLocalidad=$fila['CodigoLocalidad'];
			 $Telefono_Referencia=$fila['Telefono_Referencia'];
			 $leyenda="Pasar resultados al Tel: ".  $Telefono_Referencia;
			 $leyenda2="";
			 
			 $Mesa= $fila['Mesa'];
			 //if($cargo_elecciones <>"")				
			 list($L1,$L2,$L3,$L4,$L5,$L6,$L7,$L8,$L9,$L10)=explode(";",$cargo_elecciones);
			
			 
			  $Listas[1]["nombre"]="MOFEPA";
			  $Listas[1]["lista"]="7";
			  $Listas[1]["valor"]=$L7;
			  
			  $Listas[2]["nombre"]="Comunidad Organizada";
			  $Listas[2]["lista"]="94";
			  $Listas[2]["valor"]=$L3;
			  
			  $Listas[3]["nombre"]="Desde el Pie";
			  $Listas[3]["lista"]="115";
			  $Listas[3]["valor"]=$L6;
			  
			  $Listas[4]["nombre"]="Part. Libertario";
			  $Listas[4]["lista"]="124";
			  $Listas[4]["valor"]=$L8;
			  
			  $Listas[5]["nombre"]="Org. Cívica";
			  $Listas[5]["lista"]="125";
			  $Listas[5]["valor"]=$L4;
			 
			  $Listas[6]["nombre"]="Juntos por el Cambio";
			  $Listas[6]["lista"]="501";
			  $Listas[6]["valor"]=$L2;
			   			  
			  $Listas[7]["nombre"]="FREJUPA";
			  $Listas[7]["lista"]="502";
			  $Listas[7]["valor"]=$L1;
			 
			  $Listas[8]["nombre"]="F. de Izq. y Trabajadores";
			  $Listas[8]["lista"]="503";
			  $Listas[8]["valor"]=$L5;
			  
			  $Listas[9]["nombre"]="U. Vecinal";
			  $Listas[9]["lista"]="111";
			  $Listas[9]["valor"]=$L9;
			  
			  $Listas[10]["nombre"]="J. Vecinal";
			  $Listas[10]["lista"]="";
			  $Listas[10]["valor"]=$L10;
			  
			  switch ($id_loc_padron) {
              case 1:
               $Listas[10]["nombre"]="M. Popular Veinticinqueño";
			   $Listas[10]["lista"]="84";
               break;
			  case 35:
               $Listas[10]["nombre"]="Junta Vecinal de FALUCHO";
			   $Listas[10]["lista"]="46";
               break;
			  case 62:
               $Listas[10]["nombre"]="Union Por Montenievas";
			   $Listas[10]["lista"]="117";
               break;
			  case 20:
               $Listas[10]["nombre"]="Todos Por C.H. Lagos";
			   $Listas[10]["lista"]="126";
               break; 
			  case 28:
               $Listas[10]["nombre"]="Punto de Unión Doblense";
			   $Listas[10]["lista"]="104";
               break; 
			  case 72:
               $Listas[10]["nombre"]="Quetrequen Somos Todos";
			   $Listas[10]["lista"]="102";
               break;  
		      case 38:
               $Listas[10]["nombre"]="Unión Vecinalista Achense";
			   $Listas[10]["lista"]="103";
               break; 
			  case 92:
               $Listas[10]["nombre"]="Alianza Victorica";
			   $Listas[10]["lista"]="601";
               break;  
			   
			  
			  }	 
			
	
		  // echo "Mesa.: " . $Mesa . ": ".  $cargo_elecciones . "<br>";
			
			$etiqueta1= utf8_encode($fila['localidad']);
			$localidad= substr($etiqueta1, 0, 30);
			
			$etiqueta2= $fila['Establecimiento'];
			$escuela= substr($etiqueta2, 0, 35);
										
			$mesa="Mesa: ".$fila['Mesa'];
			
			$cabecera= $localidad . " - " .  $escuela;
			//$codigo=$fila['product_code'];
	
			if($i%2==0)
			 { // parte derecha
			 $pdf->Rect($x_d, $y, $ancho_recuadro, $alto_recuadro, 'D');
			 			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+2);
			 $pdf->SetX($x_d+5);
			 $pdf->Cell(0, 0, $cabecera, 0, 0, 'L');
				 			   
			 $pdf->SetFont('helvetica', '', 18);
			 $pdf->SetY($y+10);
			 $pdf->SetX($x_d+33);
			 $pdf->Cell(0,0, $mesa, 0, 0, 'L');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX($x_d+4);
			 $pdf->Cell(50,10, "Lista", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX($x_d+54);
			 $pdf->Cell(10,10, "Gob.", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX($x_d+64);
			 $pdf->Cell(10,10, "Dip.", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX($x_d+74);
			 $pdf->Cell(10,10, "Int.", 1, 0, 'C');
			
			 // desde aca las listas
			 
			 $distancia_abajo=30;
			
			for ($k = 1; $k <= 10; $k++) { // se recorre el array qu econtiene los valores y nombres de las listas para determinar cuáles aparecen en cada Mesa en función si en esa localidad (Mesa) hay cargos a intendente o Gobernador 
			 $posI = strpos($Listas[$k]["valor"], "I"); //I = Intendente G= Gobernador
			 $posG = strpos($Listas[$k]["valor"], "G");
			 $posD = strpos($Listas[$k]["valor"], "DP");
			 
			if ($posI > -1 || $posG > -1 || $posD > -1) {
			  $pdf->SetFont('helvetica', '', 12);
			  $pdf->SetY($y+$distancia_abajo);
			  $pdf->SetX($x_d+4);
			  $pdf->Cell(50,10, $Listas[$k]["nombre"], 1, 0, 'L');
			  			  
			  if ($posG > -1){ // Imprime la cuadrado si tiene cargos a Intendente
			    $pdf->SetFont('helvetica', '', 12);
			    $pdf->SetY($y+$distancia_abajo);
			    $pdf->SetX($x_d+54);
			    $pdf->Cell(10,10, '', 1, 0, 'L');
			   }
			  else // se pinta de gris si no tiene cargos a Intendente
			   {
			    $pdf->SetFont('helvetica', '', 12);
			    $pdf->SetY($y+$distancia_abajo);
			    $pdf->SetX($x_d+54);
			    $pdf->Cell(10,10, '', 1, 0, 'L',true);
			   } 
			   
			   if ($posD > -1){ // Imprime la cuadrado si tiene cargos a Gobernador
			    $pdf->SetFont('helvetica', '', 12);
			    $pdf->SetY($y+$distancia_abajo);
			    $pdf->SetX($x_d+64);
			    $pdf->Cell(10,10, '', 1, 0, 'L');
			   }
			   else // se pinta de gris si no tiene tiene cargos a Gobernador
			   {
			   $pdf->SetFont('helvetica', '', 12);
			    $pdf->SetY($y+$distancia_abajo);
			    $pdf->SetX($x_d+64);
			    $pdf->Cell(10,10, '', 1, 0, 'L',true);
			   } 
			   
			   if ($posI > -1){ // Imprime la cuadrado si tiene cargos a Gobernador
			    $pdf->SetFont('helvetica', '', 12);
			    $pdf->SetY($y+$distancia_abajo);
			    $pdf->SetX($x_d+74);
			    $pdf->Cell(10,10, '', 1, 0, 'L');
			   }
			   else // se pinta de gris si no tiene tiene cargos a Gobernador
			   {
			   $pdf->SetFont('helvetica', '', 12);
			    $pdf->SetY($y+$distancia_abajo);
			    $pdf->SetX($x_d+74);
			    $pdf->Cell(10,10, '', 1, 0, 'L',true);
			   } 
			  
			  $distancia_abajo=$distancia_abajo+10;
		
			}
			 
			 if($k==10){
			  // Leyenda pie derecho
			  /*
			   $pdf->SetFont('helvetica', 'B', 14);
			   $pdf->SetY($y+$distancia_abajo+3); // $y+$distancia_abajo
			   $pdf->SetX($x_d+1);
			   $pdf->Cell(0,0, $leyenda,0 , 0, 'L');
			 */
			 }
			
			
			} 
			 // hasta aca listas
			 
					  
			 
			// $pdf->EAN13($x_d+25,$y+43,$codigo);
			 $y=$y+$alto_recuadro+2;
			 $j=$j+1;
			}
			else
			 { // Parte Izquierda
			 $pdf->Rect($x_i, $y, $ancho_recuadro, $alto_recuadro, 'D');
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+2);
			 $pdf->SetX(7);
			 $pdf->Cell(0, 0, $cabecera, 0, 0, 'L');
			  
             $pdf->SetFont('helvetica', '', 18);
			 $pdf->SetY($y+10);
			 $pdf->SetX(37);
			 $pdf->Cell(0, 0, $mesa, 0, 0, 'L');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX(7);
			 $pdf->Cell(50,10, "Lista", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX(57);
			 $pdf->Cell(10,10, "Gob.", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX(67);
			 $pdf->Cell(10,10, "Dip.", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 12);
			 $pdf->SetY($y+20);
			 $pdf->SetX(77);
			 $pdf->Cell(10,10, "Int.", 1, 0, 'C');
			 
			 // desde aca las listas
			 
			 $distancia_abajo=30;
			
			for ($k = 1; $k <= 10; $k++) { // se recorre el array qu econtiene los valores y nombres de las listas para determinar cuáles aparecen en cada Mesa en función si en esa localidad (Mesa) hay cargos a intendente o Gobernador 
			 $posI = strpos($Listas[$k]["valor"], "I"); //I = Intendente G= Gobernador
			 $posG = strpos($Listas[$k]["valor"], "G");
			 $posD = strpos($Listas[$k]["valor"], "DP");
			
			if ($posI > -1 || $posG > -1 || $posD > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
			  $pdf->SetFont('helvetica', '', 12);
			  $pdf->SetY($y+$distancia_abajo);
			  $pdf->SetX(7);
			  $pdf->Cell(50,10, $Listas[$k]["nombre"], 1, 0, 'L');  
			  
			  if ($posG > -1){ // Imprime el cuadrado si tiene cargos a Intendente
			   $pdf->SetFont('helvetica', '', 12);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(57);
			   $pdf->Cell(10,10, '', 1, 0, 'L');
			   }
			   else{ // Imprime el cuadrado Gris si no tiene cargos a Intendente
			   $pdf->SetFont('helvetica', '', 12);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(57);
			   $pdf->Cell(10,10, '', 1, 0, 'L',true);
			   }
			  
			   if ($posD > -1){ // Imprime el cuadrado si tiene cargos a Gobernador
			   $pdf->SetFont('helvetica', '', 12);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(67);
			   $pdf->Cell(10,10, '', 1, 0, 'L');
			   }
			   else{ // Imprime el cuadrado Gris si no tiene cargos a Gobernador
			   $pdf->SetFont('helvetica', '', 12);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(67);
			   $pdf->Cell(10,10, '', 1, 0, 'L',true);
			   }
			   
			   if ($posI > -1){ // Imprime el cuadrado si tiene cargos a Gobernador
			   $pdf->SetFont('helvetica', '', 12);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(77);
			   $pdf->Cell(10,10, '', 1, 0, 'L');
			   }
			   else{ // Imprime el cuadrado Gris si no tiene cargos a Gobernador
			   $pdf->SetFont('helvetica', '', 12);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(77);
			   $pdf->Cell(10,10, '', 1, 0, 'L',true);
			   }
			   
			  $distancia_abajo=$distancia_abajo+10;
			} // if  $posI > -1 || $posG > -1 
			
			 if($k==10){
			  // Leyenda pie Izquierdo
			  /*
			   $pdf->SetFont('helvetica', 'B', 14);
			   $pdf->SetY($y+$distancia_abajo+3); // $y+$distancia_abajo
			   $pdf->SetX(4);
			   $pdf->Cell(0,0, $leyenda,0 , 0, 'L');
			 */
			 }
			
			}  // for
			 // hasta aca listas
			 
			
			 		 
			// $pdf->EAN13(35,$y+43,$codigo);
			}
					 
			if ($j>2 && $i < $cantidad_registros)
			 {$j=1;
			  $y=10;
			  $pdf->AddPage();
			}
			
								
		  } // While
		  
		  
	  	 $pdf->Output();

?>
