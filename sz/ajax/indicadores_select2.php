<?php
   require_once("../classes/Login.php");
   
$search = strip_tags(trim($_GET['q'])); 
// Do Prepared Query
$query2 = mysqli_query($con, "select indicadores_gestion.*,metas_gestion.codigo_meta from metas_gestion,indicadores_gestion where metas_gestion.id=indicadores_gestion.id_meta and indicadores_gestion.nombre_indicador LIKE '%$search%' LIMIT 40 order by metas_gestion.id,indicadores_gestion.codigo_indicador"); // 
// Do a quick fetchall on the results
$list = array();
while ($list=mysqli_fetch_array($query2)){
	$data[] = array('id' => $list['id'], 'text' => $list['nombre_indicador']);
}
// return the result in json
echo json_encode($data);
?>