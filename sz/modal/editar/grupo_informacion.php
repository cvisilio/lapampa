<?php
 require_once ("../../config/db.php");
 require_once ("../../config/conexion.php");
	
$id=intval($_GET['id']);
$r=mysqli_query($con,"SELECT * FROM grupos_informacion WHERE id=$id");
$g=mysqli_fetch_assoc($r);
$sis=mysqli_query($con,"SELECT id,nombre FROM sistemas_informacion ORDER BY nombre");
?>


<input type="hidden" name="id" value="<?php echo $g['id'];?>">
 
 <div class="form-group"><label class="col-sm-2 control-label">Grupo</label><div class="col-sm-8"><input type="text" name="grupo" id="grupo"    class="form-control" value="<?php echo htmlspecialchars($g['grupo']);?>" required>
 </div></div>

<div class="form-group"><label class="col-sm-2 control-label">Sistema</label><div class="col-sm-8">
<select name="id_sistema_informacion" class="form-control" required>
<?php while($s=mysqli_fetch_assoc($sis)){ $sel = ($s['id']==$g['id_sistema_informacion'])? 'selected':''; echo '<option '.$sel.' value="'.$s['id'].'">'.htmlspecialchars($s['nombre']).'</option>'; } ?>
</select>
</div></div>
