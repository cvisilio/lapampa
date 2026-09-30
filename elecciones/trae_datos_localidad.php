<?php 

  $id_localidad=intval($_POST['id_localidad']);
  $cadena_elecciones="";
  $cadena="";
  $color_leyenda="#000000";
  $suma_resultados=0;
  $resultado=0;
  
 require_once('Connections/conexionUsuarios.php');
 
 $query =  mysqli_query($con,"SELECT * FROM localidades where id='$id_localidad'");
 $num=mysqli_num_rows($query);
  if ($num==1){
    $rw=mysqli_fetch_array($query);
	$nombre_localidad=utf8_encode($rw['localidad']);
	$id_loc_padron=$rw['id_loc_padron'];
	
    $query_elecciones =  mysqli_query($con,"SELECT * FROM elecciones where cargada=1 order by fecha desc");	
	while($row_elecciones = mysqli_fetch_array($query_elecciones)){
	   $anio=$row_elecciones['fecha']; 
	   $cadena_elecciones.="<tr><td colspan='2' align='center'><h4><strong style='color:".$color_leyenda."'>".$row_elecciones['tipo']. "</strong></h4></td></tr>";
	   	   
	   $cargos=$row_elecciones['cargos']; // los cargos que se eligieron
	   $eleccion=$row_elecciones['id_eleccion'];
	   $listas_comprar=$row_elecciones['listas_comprar'];
	   list($comparar_uno,$comparar_dos) = explode(";", $listas_comprar);
	   if (strpos($comparar_dos,",") >0)
	    $a=1;
	
		switch ($cargos) {
		  case "Pdte":
			$orden="presidente";
			break;
		  case "DN":
			$orden="diputado_nacional";
			break;
		  case "SN":
			$orden="senador_nacional";
			break;
		  case "C,J,I,DP,G":
			$orden="intendente";
			break;
          }
		 
	 
	  
	  
	 $sql1="select sum(resultados_elecciones.intendente) as suma_intendentes,sum(resultados_elecciones.diputado_nacional) as suma_diputado_nacional,sum(resultados_elecciones.senador_nacional) as suma_senador_nacional,sum(resultados_elecciones.presidente) as suma_presidente from resultados_elecciones,listas_elecciones where listas_elecciones.numero_lista_provincial <>'100' and listas_elecciones.numero_lista_provincial <>'101' and  resultados_elecciones.lista=listas_elecciones.id_lista and resultados_elecciones.localidad='$id_loc_padron' and resultados_elecciones.eleccion='$eleccion'";
	 	 	 
	 $query_resultados1=mysqli_query($con,$sql1);
	 $rw_resultados1=mysqli_fetch_array($query_resultados1);
	 $suma_votos_presidente=$rw_resultados1['suma_presidente'];
	 $suma_votos_diputado=$rw_resultados1['suma_diputado_nacional'];
	 $suma_votos_senador=$rw_resultados1['suma_senador_nacional'];
	 $suma_votos_intendentes=$rw_resultados1['suma_intendentes'];
	 
	$sql="select resultados_elecciones.*, listas_elecciones.numero_lista_provincial,listas_elecciones.agrupacion_politica_provincial,listas_elecciones.color2,listas_elecciones.abreviatura_provincial from resultados_elecciones,listas_elecciones where listas_elecciones.numero_lista_provincial <>'100' and listas_elecciones.numero_lista_provincial <>'101' and  resultados_elecciones.lista=listas_elecciones.id_lista and resultados_elecciones.localidad='$id_loc_padron' and resultados_elecciones.eleccion='$eleccion' order by resultados_elecciones.". $orden ." desc limit 6";
	 $query_resultados=mysqli_query($con,$sql);
	
	
	while ($rw_resultados=mysqli_fetch_array($query_resultados)){
	
	  $lista1=$rw_resultados['abreviatura_provincial'];
	   
	   switch ($cargos) {
		  case "Pdte":
			$resultado=$rw_resultados['presidente'];
			$suma_votos= $suma_votos_presidente;
			break;
			
		  case "DN":
			$resultado=$rw_resultados['diputado_nacional'];
			$suma_votos= $suma_votos_diputado;
			break;
		  case "SN":
			$resultado=$rw_resultados['senador_nacional'];
			$suma_votos= $suma_votos_senador;
			break;
		  case "C,J,I,DP,G":
			$resultado=$rw_resultados['intendente'];
		   $suma_votos= $suma_votos_intendentes;
			break;
          }
	  
	  
	  $color =$rw_resultados['color2'];
	  $lbl_class='label label-'.$color;
	  $lista=$rw_resultados['lista'];
	  
	  $porcentaje=($resultado / $suma_votos) * 100;
	  $porcentaje=number_format($porcentaje,"1",",",".");
	  $resultado=number_format($resultado,"0",",","."); 
	  
	  if($resultado>0)
	  
	   $cadena_elecciones.= "<tr><td><h4><strong style='color:".$color."'>".$lista1 . "</strong></h4></td><td align='right'><h4><strong style='color:".$color."'>".  $porcentaje. "% (". $resultado .")" ."</strong></h4></td></tr>";
	  
	  }  // while 
	   
	
	 } // while
	
	 
	$cadena="<table class='table'><tr><td colspan='2' align='center'><h3><strong style='color:".$color_leyenda."'>". $nombre_localidad . "</strong></h3></td></tr>".$cadena_elecciones . "</table>";
  }
  else
  {
  $cadena="...";
  }
  echo $cadena; //$cadena;
?>
