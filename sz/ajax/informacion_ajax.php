<?php
if (!isset($_SESSION)) { session_start(); }
/* Conexión a BD */

 require_once ("../config/db.php");
 require_once ("../config/conexion.php");

/* Control de permisos */
include("../config/permisos.php");

$user_id = $_SESSION['user_id'];
get_cadena($user_id);

/* IMPORTANTE: poné acá el nombre EXACTO del módulo como lo tengas en tu tabla de permisos */
$modulo = "Rubros";
permisos($modulo, $cadena_permisos);

/* ---- ELIMINAR ---- */
if (isset($_REQUEST["id"])) {
    $id = intval($_REQUEST["id"]);

    if ($permisos_eliminar == 1) {
        // Verifico si el sistema está siendo usado en grupos_informacion
        $q_valid = mysqli_query($con, "SELECT id FROM grupos_informacion WHERE id_sistema_informacion = '".$id."' LIMIT 1");
        $count_valid = mysqli_num_rows($q_valid);

        if ($count_valid == 0) {
            if (mysqli_query($con, "DELETE FROM sistemas_informacion WHERE id = '".$id."'")) {
                $aviso  = "Bien hecho!";
                $msj    = "Registro eliminado satisfactoriamente.";
                $classM = "alert alert-success";
                $times  = "&times;";
            } else {
                $aviso  = "Aviso!";
                $msj    = "Error al eliminar el registro: ".mysqli_error($con);
                $classM = "alert alert-danger";
                $times  = "&times;";
            }
        } else {
            $aviso  = "Aviso!";
            $msj    = "No se puede eliminar. El sistema está vinculado a uno o más grupos de información.";
            $classM = "alert alert-warning";
            $times  = "&times;";
        }
    } else {
        $aviso  = "Acceso denegado!";
        $msj    = "No cuentas con los permisos necesarios para eliminar en este módulo.";
        $classM = "alert alert-danger";
        $times  = "&times;";
    }
}

/* ---- LISTAR (AJAX) ---- */
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';

if ($action == 'ajax') {

    $query = mysqli_real_escape_string($con, (strip_tags($_REQUEST['query'] ?? '', ENT_QUOTES)));

    $tables = "sistemas_informacion";
    $campos = "id, nombre, descripcion, por_microregion";

    // Filtro
    $sWhere = " 1=1 ";
    if ($query != '') {
        $sWhere .= " AND (nombre LIKE '%".$query."%' OR descripcion LIKE '%".$query."%')";
    }

    // paginación
    include 'pagination.php';

    $page      = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page  = intval($_REQUEST['per_page'] ?? 15);
    $adjacents = 4;
    $offset    = ($page - 1) * $per_page;

    // contar
    $count_query = mysqli_query($con, "SELECT COUNT(*) AS numrows FROM $tables WHERE $sWhere");
    if ($row = mysqli_fetch_array($count_query)) {
        $numrows = $row['numrows'];
    } else {
        $numrows = 0;
    }

    $total_pages = ($numrows > 0) ? ceil($numrows / $per_page) : 1;
    $reload = './sistemas_informacion.php';

    // consulta principal
    $sql = "SELECT $campos FROM $tables WHERE $sWhere ORDER BY id DESC LIMIT $offset,$per_page";
    $query_sistemas = mysqli_query($con, $sql);

    // mostrar mensaje de eliminar
    if (isset($_REQUEST["id"])) {
        ?>
        <div class="<?php echo $classM; ?>">
            <button type="button" class="close" data-dismiss="alert"><?php echo $times; ?></button>
            <strong><?php echo $aviso; ?></strong> <?php echo $msj; ?>
        </div>
        <?php
    }

    if ($numrows > 0) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Listado de Sistemas de Información</h3>
                    </div>
                    <div class="box-body" style="overflow: visible;">
                        <table class="table table-condensed table-hover table-striped" style="margin-bottom:0;">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 20%;">Nombre</th>
                                <th style="width: 45%;">Descripción</th>
                                <th style="width: 15%;">Por microregión</th>
                                <th style="width: 15%;" class="text-right">Acciones</th>
                            </tr>
                            <?php
                            $finales = 0;
                            while ($row = mysqli_fetch_array($query_sistemas)) {
                                $id              = $row['id'];
								
                                $nombre          = $row['nombre'];
                                $descripcion     = $row['descripcion'];
                                $por_microregion = (int)$row['por_microregion'];
                                $finales++;
                                ?>
                                <tr>
                                   <td><a href="informacion_territorial.php?id=<?php echo $id; ?>"><?php echo $id; ?></a></td>
                                   
                                    <td><?php echo htmlspecialchars($nombre); ?></td>
                                    <td><?php echo htmlspecialchars($descripcion); ?></td>
                                    <td>
                                        <?php if ($por_microregion == 1): ?>
                                            <span class="label label-success">Sí</span>
                                        <?php else: ?>
                                            <span class="label label-default">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right" style="position: relative;">
                                        <div class="btn-group pull-right" style="position: static;">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                Acciones <span class="fa fa-caret-down"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right" style="z-index:9999;">
                                                <?php if ($permisos_editar == 1) { ?>
                                                    <li>
                                                        <a href="#" data-toggle="modal" data-target="#modal_update" onclick="editar('<?php echo $id; ?>');">
                                                            <i class="fa fa-edit"></i> Editar
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if ($permisos_eliminar == 1) { ?>
                                                    <li>
                                                        <a href="#" onclick="eliminar('<?php echo $id; ?>')">
                                                            <i class="fa fa-trash"></i> Borrar
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            } // while
                            ?>
                        </table>
                    </div>
                    <div class="box-footer clearfix" style="overflow: visible;">
                        <?php
                        $inicios = $offset + 1;
                        $finales = $offset + $finales;
                        echo "Mostrando $inicios al $finales de $numrows registros";
                        echo paginate($reload, $page, $total_pages, $adjacents);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-info">
            <strong>Sin resultados</strong> No se encontraron sistemas de información.
        </div>
        <?php
    }
}
?>
