<?php
if (!isset($_SESSION)) { session_start(); }
 require_once ("../config/db.php");
 require_once ("../config/conexion.php");
	
$action=isset($_REQUEST['action'])?$_REQUEST['action']:'';
if ($action=='ajax'){
if (!empty($_REQUEST['id'])){ $id=intval($_REQUEST['id']); mysqli_query($con,"DELETE FROM volumenes_rubros_microregiones WHERE id=$id"); echo '<div class="alert alert-success">Registro eliminado.</div>'; }
$r = isset($_REQUEST['q_rubro']) && $_REQUEST['q_rubro']!=='' ? intval($_REQUEST['q_rubro']) : null;
$m = isset($_REQUEST['q_micro']) && $_REQUEST['q_micro']!=='' ? intval($_REQUEST['q_micro']) : null;
$l = isset($_REQUEST['q_loc']) ? trim($_REQUEST['q_loc']) : '';
$page=isset($_REQUEST['page'])?intval($_REQUEST['page']):1; $per_page=isset($_REQUEST['per_page'])?intval($_REQUEST['per_page']):15; $offset=($page-1)*$per_page;
$w=' WHERE 1 ';
if(!is_null($r)) $w.=' AND vrm.rubro='.(int)$r;
if(!is_null($m)) $w.=' AND vrm.microregion='.(int)$m;
if($l!=='') {
  $l_esc = mysqli_real_escape_string($con, $l);
  $w.=" AND l.localidad LIKE '%".$l_esc."%'";
}
$count_sql = "SELECT COUNT(*) n
              FROM volumenes_rubros_microregiones vrm
              LEFT JOIN localidades l ON l.id = vrm.id_localidad
              $w";
$count=mysqli_fetch_assoc(mysqli_query($con,$count_sql))['n'];
$rs=mysqli_query($con,"SELECT vrm.*, r.name AS rubro_nombre, um.unidad_medida, l.localidad AS localidad_nombre
                       FROM volumenes_rubros_microregiones vrm
                       LEFT JOIN rubros r ON r.id = vrm.rubro
                       LEFT JOIN unidades_medida um ON um.id = r.unidad_medida
                       LEFT JOIN localidades l ON l.id = vrm.id_localidad
                       $w
                       ORDER BY vrm.id DESC LIMIT $offset,$per_page");
echo '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>ID</th><th>Rubro</th><th>Unidad</th><th>Microregión</th><th>Localidad</th><th>Volumen</th><th>Color</th><th class="text-right">Acciones</th></tr></thead><tbody>';
while($v=mysqli_fetch_assoc($rs)){
  $localidad_mostrar = '';
  if ((int)$v['id_localidad'] > 0) {
    $localidad_mostrar = $v['localidad_nombre'] ?? '';
  }
echo '<tr>';
echo '<td>'.(int)$v['id'].'</td><td>'.htmlspecialchars($v['rubro_nombre'] ?? '').'</td><td>'.htmlspecialchars($v['unidad_medida'] ?? '').'</td><td>'.(int)$v['microregion'].'</td><td>'.htmlspecialchars($localidad_mostrar).'</td><td>'.(float)$v['volumen'].'</td><td>'.htmlspecialchars($v['color'] ?? '').'</td>';
echo '<td class="text-right">';
echo '<button class="btn btn-default btn-sm" onclick="editar_vol('.(int)$v['id'].')"><i class="fa fa-edit"></i></button> ';
echo '<button class="btn btn-danger btn-sm" onclick="eliminar('.(int)$v['id'].')"><i class="fa fa-trash"></i></button>';
echo '</td></tr>';
}
echo '</tbody></table></div>';
$pages=max(1, ceil($count/$per_page)); echo '<nav><ul class="pagination">'; for($i=1;$i<=$pages;$i++){ $a=($i==$page)?'class="active"':''; echo "<li $a><a href=\"#\" onclick=\"load($i)\">$i</a></li>"; } echo '</ul></nav>';
}
?>