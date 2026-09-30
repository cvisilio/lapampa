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
    include '../classes/cart.php';
    $cart = new Cart;
	$cartItems = $cart->contents();
	
	include '../classes/cart_compromisos.php';
	
	$cart_compromisos = new Cart_compromisos;
	$cartItemsCompromisos = $cart_compromisos->contents();
		 
	require_once ("../conexion.php");
	
	$transferencias[][]=""; // array para luego armar pdf
	$j=0; 
	foreach ($cartItemsCompromisos as $item) {
     $id_en_array=$item["id_compromiso"]; //id_compromisos es localidad (jjjjj)
   	 if($id_en_array)
	    {
	    $localidad =$id_en_array; //id_compromisos es localidad (jjjjj)
		
		$monto =floatval($item["price"]);
		$afectacion= intval($item["id_motivo"]);
		$cuotas = intval($item["qty"]);
		$id_compromiso = intval($item["id"]);
		
		$transferencias[$j]['id_localidad']=$localidad;
		$transferencias[$j]['id_motivo']=$afectacion;
		$transferencias[$j]['monto']=$item["price"];
		$j++;
	   } //if  if($id_en_array)
	  } // for each compromisos 
	   
	foreach ($cartItems as $item) {
     $id_en_array=$item["id_localidad"];
     if($id_en_array)
	  {
	    $localidad =intval($item["id_localidad"]); 
		$monto =floatval($item["price"]);
		$afectacion= intval($item["id_motivo"]);
		$cuotas = intval($item["qty"]);
		$id_compromiso = intval($item["id_compromiso"]);
		
		$transferencias[$j]['id_localidad']=$localidad;
		$transferencias[$j]['id_motivo']=$afectacion;
		$transferencias[$j]['monto']=$item["price"];
	 	$j++;
			
	    $id_localidad_id_Motivo=$localidad."-".$afectacion;
		$indice2=md5($id_localidad_id_Motivo);		
		$eliminar = $cart->remove($indice2); 
	 
	  } //if  if($id_en_array)
	  } // for each Item 
	  
	   //realiza pdf
		$cantidad_registros=28;	
		$pdf=new PDF_EAN12();
		$pdf->AddPage();
		//$pdf->SetMargins(0, 0, 0, true); 
			  
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetY(5);
		$pdf->SetX(60);
		$fecha="Santa Rosa La Pampa, " . date("d-m-Y");
		$pdf->Cell(0, 0, $fecha, 0, 0, 'R');
				  
		$pdf->SetFont('helvetica', '', 14);
		$pdf->SetY(18);
		$pdf->SetX(10);
		$cadena="Transferencias a Municipios";
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
		
	  for ($i = 0; $i < count($transferencias); $i++) {
	   
	   if($transferencias[$i]["id_localidad"]>0)
	   {
	    $id_localidad= $transferencias[$i]["id_localidad"];
		$id_motivo= $transferencias[$i]["id_motivo"];
	    $monto= $transferencias[$i]["monto"];
		
	    $sql1 = "select * from localidades where id='$id_localidad'";
        $query1 = mysqli_query($con,$sql1);
		$row= mysqli_fetch_array($query1);
		$localidad=$row['localidad'];
		
		$sql2 = "select * from objetivos_motivos where id='$id_motivo'";
        $query2 = mysqli_query($con,$sql2);
		$row= mysqli_fetch_array($query2);
		$motivo=$row['motivo'];
								 
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
		$pdf->Cell(0, 0, $cadena, 0, 0, 'C');
	    
		$pdf->Line(2,$y+5,200,$y+5);
		
	     $y=$y+8;
	  	 $z=$z+1;
	     if ($z > $cantidad_registros)
		  {
		  $z=0;
		  $y=15;
		  $pdf->AddPage(); // Nueva Pagina
		  }
	  
	   } // if
		  
	   }  // for
	    
		session_start();
	    unset($_SESSION['cart_contents']);
	    unset($_SESSION['cart_compromisos']);
		
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
?>			