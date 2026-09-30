<?php
require_once('../pdf/_tcpdf_5.0.002/tcpdf.php');

class PDF_EAN12 extends tcpdf
{
function nada($x, $y)
{
	$a=1;
}
}

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
	$campos="transferencias.*, localidades.localidad,objetivos_motivos.motivo";
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
	
	$sWhere.=" order by localidades.localidad";
	
	$query = mysqli_query($con,"SELECT $campos FROM  $tables where $sWhere");
	
	$count_query   = mysqli_query($con,"SELECT count(*) AS numrows FROM $tables where $sWhere ");
	if ($row= mysqli_fetch_array($count_query)){$numrows = $row['numrows'];}
	else {echo mysqli_error($con);}
	
	//$numrows=1;
    if($numrows >0)
	 {
	 
	 $j=0; 
	
	    //realiza pdf
		$cantidad_registros=28;	
		$pdf=new PDF_EAN12();
		$pdf->AddPage();
		//$pdf->SetMargins(0, 0, 0, true); 
			  
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY(5);
		$pdf->SetX(60);
		$fecha="Santa Rosa La Pampa, " . date("d-m-Y");
		$desde_fechas= $dia_inicio. "-". $mes_inicio. "-" .$anio_inicio. "/" .$dia_fin. "-". $mes_fin. "-" .$anio_fin;
		$pdf->Cell(0, 0, $fecha, 0, 0, 'R');
		
				  
		$pdf->SetFont('helvetica', '', 14);
		$pdf->SetY(18);
		$pdf->SetX(10);
		$cadena="Transferencias a Municipios desde  " .  $desde_fechas;
		$pdf->Cell(0, 0, $cadena, 0, 0, 'C');		  
		
		$y=40;
		$pdf->SetFont('helvetica', '', 12);
		$pdf->SetY($y);
		$pdf->SetX(5);
		$pdf->Cell(0, 0, "Localidad", 0, 0, 'L');
		$pdf->SetY($y);
		$pdf->SetX(45);
		$pdf->Cell(0, 0, "Motivo", 0, 0, 'L');
		$pdf->SetY($y);
		$pdf->SetX(80);
		$pdf->Cell(0, 0, "Monto", 0, 0, 'R');
		
		$pdf->Line(2,$y+5,200,$y+5);
		
		$y=$y+8;
	    $i=0;
		$z=0;
	    $suma=0;
		
	 while($row = mysqli_fetch_array($query)){	
	    
		$localidad=$row['localidad'];
		$motivo=$row['motivo'];
		$monto=$row['monto'];
	    $pdf->SetFont('helvetica', '', 10);
		$pdf->SetY($y);
		$pdf->SetX(5);
		$cadena=substr($localidad,0,25);
		$pdf->Cell(0, 0, $cadena, 0, 0, 'L');
		
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY($y);
		$pdf->SetX(45);
		$cadena=substr($motivo,0,60);
		$pdf->Cell(0, 0, $cadena, 0, 0, 'L');
		
		$suma=$suma+$monto;
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY($y);
		$pdf->SetX(80);
		$cadena="$". number_format($monto,0,",",".");
		$pdf->Cell(0, 0, $cadena, 0, 0, 'R');
	    
		$pdf->Line(2,$y+5,200,$y+5);
		
	     $y=$y+8;
	  	 $z=$z+1;
	     if ($z > $cantidad_registros)
		  {
		  $z=0;
		  $y=15;
		  $pdf->AddPage(); // Nueva Pagina
		  }
	
	   }  // While consulta
	
				
		$pdf->SetFont('helvetica', '', 14);
		$pdf->SetY($y);
		$pdf->SetX(30);
		$cadena="Total: $". number_format($suma,0,",",".");
		$pdf->Cell(0, 0, $cadena, 0, 0, 'R');
		
		$pdf->Output();
		/*$path="transferencias.pdf";
		$this->Output($path, 'F');
	 	header("Content-type:application/pdf");
        echo file_get_contents($path,TRUE);
	   */
	 }   
}
}				
?>			