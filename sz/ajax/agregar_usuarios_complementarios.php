<?php
	/*-------------------------
Autor: Carlo Visilio
	Web: factupyme.com.ar
	Mail: cvisilio@gmail.com
	---------------------------*/
session_start();

$usuario_id_raiz=$_SESSION['usuario_id'];  
if (isset($_POST['id'])){$id=intval($_POST['id']);}

	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	require_once ("../libraries/inventory.php");//Contiene funcion que controla stock en el inventario
	
	
	
if (!empty($id))
 {
	add_tmp_complementario($usuario_id_raiz,$id);
 }

if (isset($_GET['id']))//codigo elimina un elemento de la DB
{
$id_tmp=intval($_GET['id']);	
remove_tmp_complementario($id_tmp);
}

	
?>
<table class="table">
<tr>
	<th>C&oacute;digo</th>
  
    <th>Usuario</th>
	
	<th></th>
</tr>
<?php
	
	$sql=mysqli_query($con, "select agenda_usuarios.*,users.fullname from users, agenda_usuarios where agenda_usuarios.usuario_que_ve=users.user_id and agenda_usuarios.usuario='$usuario_id_raiz'");
	$nums=0;
	
	while ($row=mysqli_fetch_array($sql))
	{
	$user_id=$row['usuario_que_ve'];
	$id_tmp1=$row["id"];
	$user_name=$row['fullname'];
	
		?>
		<tr>
			<td><?php echo $user_id;?></td>
			<td><?php echo $user_name;?></td>
			
			<td ><span class="pull-right"><a href="#" onclick="eliminar('<?php echo $id_tmp1; ?>')"><i class="glyphicon glyphicon-trash"></i></a></span></td>
		</tr>		
		<?php
		$nums++;
	}
	    
	
?>
</table>
