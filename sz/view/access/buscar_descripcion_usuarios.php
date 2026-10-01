<?php
//session_start();
//$sucursal=$_SESSION['sucursal'];
require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
require_once ("../../libraries/inventory.php");//Contiene funcion que conecta a la base de datos

if (isset($_GET['term'])) {
    $q = mysqli_real_escape_string($con, strip_tags($_REQUEST['term'], ENT_QUOTES));	
    $return_arr = array();

    $fetch = mysqli_query($con,"SELECT * FROM users WHERE fullname LIKE '%$q%' LIMIT 20"); 

    while ($row = mysqli_fetch_array($fetch)) {
        $row_array['codigo'] = $row['user_id'];	
        $row_array['product_id'] = $row['user_id'];
        $row_array['descripcion'] = $row['fullname'];
        $row_array['label'] = $row['fullname']; // para autocomplete
        $row_array['value'] = $row['fullname']; // para autocomplete

        $return_arr[] = $row_array;
    }

    echo json_encode($return_arr);
}
?>