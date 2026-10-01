<?php

	/* Connect To Database*/
 require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
 require_once ("../../libraries/inventory.php");//Contiene funcion que conecta a la base de datos

 $query = "SELECT * FROM ministerios where ambito=1 order by id_cuenta_dependiente";
 $result = mysqli_query($con, $query);
//$output = array();
 $data = array();

 while($row = mysqli_fetch_array($result))
 { 
  $sub_data["id"] = $row["id"];
  $name=$row["denominacion"];
 //$name=$row["denominacion"];
  $sub_data["name"] = $name;
  $sub_data["codigo"] = $row["id"];
  $sub_data["id"] = $row["id"];
  $sub_data["tipo_saldo"] = $row["tipo_saldo"];
  $sub_data["imputable"]= $row["imputable"];
  $sub_data["text"] =$name;
  $sub_data["parent_id"] = $row["id_cuenta_dependiente"];
  $data[] = $sub_data;
 }
 
 foreach($data as $key => &$value)
 {
  $output[$value["id"]] = &$value;
 }

 foreach($data as $key => &$value)
 {
 if($value["parent_id"] && isset($output[$value["parent_id"]]))
 {
  $output[$value["parent_id"]]["nodes"][] = &$value;
 }
}
foreach($data as $key => &$value)
{
 if($value["parent_id"] && isset($output[$value["parent_id"]]))
 {
  unset($data[$key]);
 }
}
echo json_encode($data);
/*echo '<pre>';
print_r($data);
echo '</pre>';*/

?>