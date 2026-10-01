<?php
	/*-------------------------
	Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
	session_start();
	/* Connect To Database*/
	
	for ($i=0;$i<=12;$i++){
     $array_resultados[$i]["concejal"]=0;
    }
	
	require_once ("../../config/db.php");
	require_once ("../../config/conexion.php");
	$eleccion=10;
	if (isset($_GET["id"])){
	$id=$_GET["id"];
	$id=intval($id);
	$sql="select * from localidades where id='$id'";
	$query=mysqli_query($con,$sql);
	$num=mysqli_num_rows($query);
	if ($num==1){
	 $rw=mysqli_fetch_array($query);
	 $name=$rw['localidad'];
	 $lista_ganadora=$rw['lista_ganadora'];
	 $id_loc_padron=$rw['id_loc_padron'];
	 $tipo_localidad=$rw['es_localidad'];
	 $telefono_municipio=$rw['telefono_municipio'];
	 $email_municipio=$rw['email_municipio'];
	 $celular_intendente=$rw['celular_intendente'];
	
	/* $sql="select max(posicion) as cantidad_concejales from candidatos_elecciones where localidad='$id_loc_padron' and eleccion='$eleccion' and cargo=9";
	 $query_cantidad=mysqli_query($con,$sql);
	 $rw_cantidad=mysqli_fetch_array($query_cantidad); 
	 $cantidad_concejales = $rw_cantidad['cantidad_concejales'];*/ ///en caso de sacarlo de la tabla candidatos, pero es conveniente, para no forzar consultas, tener la cantidad concejales en la tabla localidades 
	 
	// $sql="select * from candidatos_elecciones where localidad='$id_loc_padron' and eleccion='$eleccion' order by lista, posicion";
	
	$sql="select * from funcionarios where localidad='$id_loc_padron' and eleccion='$eleccion' and poder='4' order by lista, posicion";

	 $query_candidatos=mysqli_query($con,$sql);
	  
	  
	  while ($rw_candidatos=mysqli_fetch_array($query_candidatos)){
	  
	   $lista=$rw_candidatos['lista'];
	   $posicion=$rw_candidatos['posicion'];
	 
	   if($rw_candidatos['id_cargo'] ==8)
	    $intendente[$lista]=$rw_candidatos['nombre'];
	   elseif ($rw_candidatos['id_cargo'] ==9)
	    $concejales[$posicion][$lista]=$rw_candidatos['nombre'];
	   elseif ($rw_candidatos['id_cargo'] ==10)
	   	$juez_de_paz[$lista]=$rw_candidatos['nombre'];
	
	  }
	 
	  $cantidad_concejales = $rw['cantidad_concejales'];
	  	  	 
	 $sql="select resultados_elecciones.concejal,resultados_elecciones.intendente,resultados_elecciones.lista,listas_elecciones.numero_lista_provincial,listas_elecciones.agrupacion_politica_provincial,listas_elecciones.color,listas_elecciones.abreviatura_provincial from resultados_elecciones,listas_elecciones where resultados_elecciones.lista <>100 and resultados_elecciones.lista <>101 and  resultados_elecciones.lista=listas_elecciones.id_lista and resultados_elecciones.localidad='$id_loc_padron' and resultados_elecciones.eleccion='$eleccion' order by resultados_elecciones.concejal desc limit 3";
	 $query_resultados=mysqli_query($con,$sql);
		 
	 $j=0;
	 $x=0;
	 $resultado_consejal=0;
	 $lista="";
	 
	 while ($rw_resultados=mysqli_fetch_array($query_resultados)){
	
	  $lista1=$rw_resultados['numero_lista_provincial'] . "(". $rw_resultados['abreviatura_provincial'] . ")";
	  $resultado_consejal=$rw_resultados['concejal'];
	  $resultado_intendente=$rw_resultados['intendente'];
	  
	  $color =$rw_resultados['color'];
	  $lista=$rw_resultados['lista'];
	  
	  $resultado_elecciones_intendente[$x]['lista']=$lista1;
	  $resultado_elecciones_intendente[$x]['color']=$color;
	  $resultado_elecciones_intendente[$x]['resultado_intendente']=$resultado_intendente;
	  
	  $resultado_elecciones_intendente[$x]['resultado_concejal']=$resultado_concejal;
	  $x++;
	  
	  if($j==0){
	   $lista_ganadora1=$lista;
	   $color_ganador=$color;
	   }
	 
	
	 
	 if($resultado_consejal>0)
	  { 
	   for ($i=1;$i<=$cantidad_concejales;$i++){
	    $division =$resultado_consejal / $i;
	    $array_resultados[$j]['lista']=$lista1;
		$array_resultados[$j]['color']=$color;
	    $array_resultados[$j]['nombre']= $concejales[$i][$lista];
	    $array_resultados[$j]['concejal']=intval(floor($division)); // foor toma el entero
	    $j++;
	  }
	 
	  } // if resultado >0
	  
	  }
	 
	  
	 foreach ($array_resultados as $key => $row) {
        $aux1[$key] = $row['concejal'];
      }
 
      array_multisort($aux1, SORT_DESC, $array_resultados);

	}	
	}
	else {exit;}
?>

   
      <div class="panel panel-<?php echo $color_ganador;?>">
       <div class="panel-heading">
     
       <?php 
	   if($tipo_localidad==2)
	    {
	    $texto_tipo_intendente ="Presidente Comisi&oacute;n de Fomento: ";
	    $label_concejales_vocales="Vocales: ";
		}
	   else
	    {
		$label_concejales_vocales="Concejales: ";
		$texto_tipo_intendente ="Intendente : ";
	    }
	  echo $texto_tipo_intendente . $intendente[$lista_ganadora1];
	  
	  
	  ?>
      </div>
      
      <div class="panel-body">
   
      
      <?php
	
	  if($tipo_localidad>1){
	   
	   echo $label_concejales_vocales."<br/><br/>";
		for ($i=0;$i<$cantidad_concejales;$i++){
		 $lbl_class='label label-'. $array_resultados[$i]['color'];
		 $lugar=$i+1;
	  echo "<h4 class='". $lbl_class ."'>". $lugar . ") " . "Lista " .$array_resultados[$i]['lista'] . "  Nombre:". $array_resultados[$i]['nombre']. "</h4><br />";
		  } ?>
      
       </div>
        <div class="panel-footer">
       
	   <?php 
	 //  if($tipo_localidad==3)
	    echo "Juez de Paz : ". $juez_de_paz[$lista_ganadora1]; ?></div> 
       
      <?php }?>
 </div>
 
 <?php  
 $sql_suma="select sum(resultados_elecciones.concejal) as suma_concejal,sum(resultados_elecciones.intendente) as suma_intendente,sum(resultados_elecciones.gobernador) as suma_gobernador from resultados_elecciones where resultados_elecciones.lista <>100 and resultados_elecciones.lista <>101 and resultados_elecciones.localidad='$id_loc_padron' and resultados_elecciones.eleccion='$eleccion'";
 
 $query_resultados_suma=mysqli_query($con,$sql_suma);
 $rw_resultados_suma=mysqli_fetch_array($query_resultados_suma);
 $suma_positivos_concejal=$rw_resultados_suma['suma_concejal'];
 $suma_positivos_intendente=$rw_resultados_suma['suma_intendente'];
 $suma_positivos_gobernador=$rw_resultados_suma['suma_gobernador'];
 
      
		 
  echo "Resultado Intendente: ";     
	  
 for ($i=0;$i<3;$i++){
  if($resultado_elecciones_intendente[$i]['resultado_intendente'] >0){
		 $lbl_class='label label-'. $resultado_elecciones_intendente[$i]['color'];
		//$resultado_elecciones_intendente[$i]['lista'] . " : " . 
		$porcentaje=($resultado_elecciones_intendente[$i]['resultado_intendente'] / $suma_positivos_intendente) *100;
		
	  echo "<h4 class='". $lbl_class ."'>".  number_format($porcentaje,"2",",","."). "%</h4>"."   ";
	  }
	 } 
	
  	 
	  $lbl_class='label label-'. 'default';
	  echo "<br />";
	   echo "<br />";
	  echo "Cel. Intendente: ".  $celular_intendente;
	  echo "<br />";
	  echo "Tel. Municipio: ".  $telefono_municipio;
	  echo "<br />";
	  echo "Email Municipio: ".  $email_municipio;
	  
	  $anio="2021";
	  $mes="3";
	  
	  $sql_agente="select * from agentes where id_dependencia='$id' and anio='$anio' and mes='$mes'";
 
	 $query_resultados_agente=mysqli_query($con,$sql_agente);
	 $rw_resultados_agente=mysqli_fetch_array($query_resultados_agente);
	 $funcionarios=$rw_resultados_agente['funcionarios'];
	 $empleados=$rw_resultados_agente['empleados'];
	 $contratados=$rw_resultados_agente['contratados'];
	 
	 echo "<br />";
	 echo "<br />";
	 echo "Funcionarios: ".  $funcionarios;
	 echo "<br />";
	 echo "Empleados: ".  $empleados;
	 echo "<br />";
	 echo "Contratados: ".  $contratados;
	
	/* 
	echo  '<form class="form-horizontal" method="post" id="update_agentes" name="update_agentes" >'; 
	echo '<input type="text" class="form-control" id="cantidad_funcionarios" name="cantidad_funcionarios" placeholder="Ingresa cantidad funcionarios" value="" maxlength="5">';
	echo '<input type="text" class="form-control" id="cantidad_empleados" name="cantidad_empleados" placeholder="Ingresa cantidad empleados" value="" maxlength="5">';
	echo '<input type="text" class="form-control" id="cantidad_contratados" name="cantidad_contratados" placeholder="Ingresa cantidad contratados" value="" maxlength="5">';
	  
	echo '</form>';  
	*/
?>	   