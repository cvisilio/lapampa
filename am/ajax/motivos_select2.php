<?php
   require_once("../classes/Login.php");
   
$search = strip_tags(trim($_GET['q'])); 
// Do Prepared Query
$query2 = mysqli_query($con, "SELECT * FROM objetivos_motivos WHERE motivo LIKE '%$search%' LIMIT 40");
// Do a quick fetchall on the results
$list = array();
while ($list=mysqli_fetch_array($query2)){
	$data[] = array('id' => $list['id'], 'text' => $list['motivo']);
}
// return the result in json
echo json_encode($data);
?>