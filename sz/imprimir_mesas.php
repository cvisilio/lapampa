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

 
 $query="15619037";

 $consulta22 = mysqli_query($con,"SELECT mesas.Mesa, mesas.CodigoLocalidad,localidades.*, entidades.Telefono_Referencia,entidades.Establecimiento FROM mesas, localidades,entidades where  mesas.CodigoEscuela=entidades.Id and mesas.CodigoLocalidad=localidades.id_loc_padron order by Mesa");

	  
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
	$y=8;
	$ancho_recuadro=205;
    $x_d=$x_i+$ancho_recuadro+$x_i;
	$cantidad_vertical_por_hoja=1;
 	$j=1;
	$i=0;
	$alto_rec_celdas=18;
	
	$alto_recuadro=280; //con 70 entran 4 verticales
	
	 while($fila = mysqli_fetch_array($consulta22)){			
       
		   $i=$i+1; 		
			
			//$this->SetTextColor($data['color']['red'], $data['color']['green'], $data['color']['blue']);
			
			 $cargo_elecciones= $fila['cargo_elecciones'];
			 $CodigoLocalidad=$fila['CodigoLocalidad'];
			 $Telefono_Referencia=$fila['Telefono_Referencia'];
			 $leyenda="Pasar resultados al Tel: ".  $Telefono_Referencia;
			 $leyenda2="No pasar datos por WhatsApp";
			   
			 $Mesa= $fila['Mesa'];
			 //if($cargo_elecciones <>"")

			 list($L1,$L2,$L3,$L4,$L5,$L6,$L7,$L8,$L9,$L10)=explode(";",$cargo_elecciones);
			 $L1="G"; // esto esta fijo, sacarlo en otra ocasión
			 $L2="G";
			 $L3="D,G";
			 $L4="D,G";
			 $L5="D,G";
			 $L6="D,G";
			
			 
			  
			  $Listas[1]["nombre"]="Unite por la Libertad";
			  $Listas[1]["lista"]="87";
			  $Listas[1]["valor"]=$L1;
			  
			  $Listas[2]["nombre"]="Frente NOS";
			  $Listas[2]["lista"]="131";
			  $Listas[2]["valor"]=$L2;
						  
			  $Listas[3]["nombre"]="F. Izquierda y Trabajadores";
			  $Listas[3]["lista"]="133/503";
			  $Listas[3]["valor"]=$L3;
			
			  $Listas[4]["nombre"]="Juntos Somos el Cambio";
			  $Listas[4]["lista"]="135/502";
			  $Listas[4]["valor"]=$L4;
						 
			  $Listas[5]["nombre"]="Frente de Todos";
			  $Listas[5]["lista"]="136/501";
			  $Listas[5]["valor"]=$L5;
						 
			  $Listas[6]["nombre"]="Consenso Federal";
			  $Listas[6]["lista"]="137/50";
			  $Listas[6]["valor"]=$L6;

     			
		  // echo "Mesa.: " . $Mesa . ": ".  $cargo_elecciones . "<br>";
					
			$etiqueta1= utf8_encode($fila['localidad']);
			$localidad= substr($etiqueta1, 0, 50);
			
			$etiqueta2= utf8_encode($fila['Establecimiento']);
			$escuela= substr($etiqueta2, 0, 50);
										
			$mesa="Mesa: ".$fila['Mesa'];
			
			$cabecera= $localidad . " - " .  $escuela;
			//$codigo=$fila['product_code'];
	
			 $pdf->Rect($x_i, $y, $ancho_recuadro, $alto_recuadro, 'D');
			 $pdf->SetFont('helvetica', '', 16);
			 $pdf->SetY($y+6);
			 $pdf->SetX(7);
			 $pdf->Cell(0, 0, $cabecera, 0, 0, 'L');
			  
             $pdf->SetFont('helvetica', 'B', 26);
			 $pdf->SetY($y+22);
			 $pdf->SetX(80);
			 $pdf->Cell(0, 0, $mesa, 0, 0, 'L');
			 
			 $pdf->SetFont('helvetica', '', 15);
			 $pdf->SetY($y+40);
			 $pdf->SetX(4);
			 $pdf->Cell(22,$alto_rec_celdas, "Nº", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 15);
			 $pdf->SetY($y+40);
			 $pdf->SetX(26);
			 $pdf->Cell(78,$alto_rec_celdas, "Agrupación Política", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 15);
			 $pdf->SetY($y+40);
			 $pdf->SetX(104);
			 $pdf->Cell(50,$alto_rec_celdas, "Presidente", 1, 0, 'C');
			 
			 $pdf->SetFont('helvetica', '', 15);
			 $pdf->SetY($y+40);
			 $pdf->SetX(154);
			 $pdf->Cell(50,$alto_rec_celdas, "Diputado", 1, 0, 'C');
			 
			 // desde aca las listas
			 
			 $distancia_abajo=58;
			
			for ($k = 1; $k <= 6; $k++) { // se recorre el array qu econtiene los valores y nombres de las listas para determinar cuáles aparecen en cada Mesa en función si en esa localidad (Mesa) hay cargos a intendente o Gobernador 
			 $posI = strpos($Listas[$k]["valor"], "D"); //I = Intendente G= Gobernador
			 $posG = strpos($Listas[$k]["valor"], "G");
			
			if ($posI > -1 || $posG > -1) { // Imprime la Lista si tiene cargos a Int. o Gob.
			  $pdf->SetFont('helvetica', '', 15);
			  $pdf->SetY($y+$distancia_abajo);
			  $pdf->SetX(4);
			  $pdf->Cell(22,$alto_rec_celdas, $Listas[$k]["lista"], 1, 0, 'C');  
			  
			  $pdf->SetFont('helvetica', '', 15);
			  $pdf->SetY($y+$distancia_abajo);
			  $pdf->SetX(26);
			  $pdf->Cell(78,$alto_rec_celdas, $Listas[$k]["nombre"], 1, 0, 'L');  
			  
			  if ($posG > -1){ // Imprime el cuadrado si tiene cargos a Gobernador 
			   $pdf->SetFont('helvetica', '', 15);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(104);
			   $pdf->Cell(50,$alto_rec_celdas, '', 1, 0, 'L');
			   }
			   else{ // Imprime el cuadrado Gris si no tiene cargos a Gobernador
			   $pdf->SetFont('helvetica', '', 15);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(104);
			   $pdf->Cell(50,$alto_rec_celdas, '', 1, 0, 'L',true);
			   }
			   
			   if ($posI > -1){ // Imprime el cuadrado si tiene cargos a Intendente
			   $pdf->SetFont('helvetica', '', 15);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(154);
			   $pdf->Cell(50,$alto_rec_celdas, '', 1, 0, 'L');
			   }
			   else{ // Imprime el cuadrado Gris si no tiene cargos a Intendente
			   $pdf->SetFont('helvetica', '', 15);
			   $pdf->SetY($y+$distancia_abajo);
			   $pdf->SetX(154);
			   $pdf->Cell(50,$alto_rec_celdas, '', 1, 0, 'L',true);
			   }
			   
			  $distancia_abajo=$distancia_abajo+$alto_rec_celdas;
			} // if  $posI > -1 || $posG > -1 
			
			 if($k==6){
			  // Leyenda pie Izquierdo
			   $pdf->SetFont('helvetica', 'B', 28);
			   $pdf->SetY($y+$distancia_abajo+12); // $y+$distancia_abajo
			   $pdf->SetX(5);
			   $pdf->Cell(0,0, $leyenda,0 , 0, 'L');
			   $pdf->SetY($y+$distancia_abajo+45); // $y+$distancia_abajo
			   $pdf->SetX(32);
			   $pdf->Cell(0,0, $leyenda2,0 , 0, 'L');
			 
			 }
			
			}  // for
			 // hasta aca listas
		if ($i < $cantidad_registros)
		 $pdf->AddPage(); // Nueva Pagina
								
		  } // While
		  
		  
	  	 $pdf->Output();

?>
