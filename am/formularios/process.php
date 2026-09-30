<?php 
 session_start();
 
	if (isset($_SESSION['id_localidad'])){
	 $return_arr = array();
	  
	 $id_localidad= $_SESSION['id_localidad'];
	 
	 $mes1=date("m");
	 
	 require_once ("../config/db.php");
     require_once ("../config/conexion.php");
	 
	 $cantidad_funcionarios=intval($_POST["cantidad_funcionarios"]);
	 $neto_funcionarios1=floatval($_POST["neto_funcionarios1"]);
	 $neto_funcionarios2=floatval($_POST["neto_funcionarios2"]);
	 $iss_funcionarios1=floatval($_POST["neto_iss_funcionarios1"]);
	 $iss_funcionarios2=floatval($_POST["neto_iss_funcionarios2"]);
	 
	 $cantidad_empleados=intval($_POST["cantidad_empleados"]);
	 $neto_empleados1=floatval($_POST["neto_empleados1"]);
	 $neto_empleados2=floatval($_POST["neto_empleados2"]);
	 $iss_empleados1=floatval($_POST["neto_iss_empleados1"]);
	 $iss_empleados2=floatval($_POST["neto_iss_empleados2"]);
	 
	 $cantidad_contratados=intval($_POST["cantidad_contratados"]);
	 $neto_contratados1=floatval($_POST["neto_contratados1"]); 
	 $neto_contratados2=floatval($_POST["neto_contratados2"]);
	 
	 $gastos_operativos1= floatval($_POST["gastos_operativos1"]);
	 $gastos_operativos2= floatval($_POST["gastos_operativos2"]);
	 
	 $saldo_disponible1= floatval($_POST["saldo_disponible1"]);
	 $saldo_disponible2= floatval($_POST["saldo_disponible2"]);
	 
	 $recursos_propios1= floatval($_POST["recursos_propios1"]);
	 $recursos_propios2= floatval($_POST["recursos_propios2"]);
	 $observaciones=mysqli_real_escape_string($con,(strip_tags($_POST["observaciones"],ENT_QUOTES)));
	
	 $sql = "UPDATE proyecciones_localidades SET cantidad_funcionarios='".$cantidad_funcionarios."', neto_funcionarios1='".$neto_funcionarios1."', neto_funcionarios2='".$neto_funcionarios2."', iss_funcionarios1='".$iss_funcionarios1."', iss_funcionarios2='".$iss_funcionarios2."', cantidad_empleados='".$cantidad_empleados."', neto_empleados1='".$neto_empleados1."', neto_empleados2='".$neto_empleados2."', iss_empleados1='".$iss_empleados1."', iss_empleados1='".$iss_empleados1."', iss_empleados2='".$iss_empleados2."', cantidad_contratados='".$cantidad_contratados."', neto_contratados1='".$neto_contratados1."', neto_contratados2='".$neto_contratados2."', gastos_operativos1='".$gastos_operativos1."', gastos_operativos2='".$gastos_operativos2."', saldo_disponible1='".$saldo_disponible1."', saldo_disponible2='".$saldo_disponible2."', recursos_propios1='".$recursos_propios1."', recursos_propios2='".$recursos_propios2."', observaciones='".$observaciones."' WHERE mes ='".$mes1."'" . " and id_localidad='".$id_localidad."'";
    $query = mysqli_query($con,$sql);

    // if user has been added successfully
    if ($query) {
	  $row_array['sucedio']=1;
    } else {
      $row_array['sucedio']=0;
    }
 
 array_push($return_arr,$row_array);	
 echo json_encode($return_arr);
 
 } //if (isset($_POST['id']))

?>			
