<?php 
require_once("../classes/Login.php");
$login = new Login();
if ($login->isUserLoggedIn() == true) 
{	  		 
    require_once ("../conexion.php");
   	
    $action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
    if($action == 'ajax'){
        $query     = mysqli_real_escape_string($con,(strip_tags($_REQUEST['query'], ENT_QUOTES)));
        $daterange = mysqli_real_escape_string($con,(strip_tags($_REQUEST['daterange'], ENT_QUOTES)));
        $motivo    = intval($_REQUEST['motivo']);

        // TABLAS
        $tables = "transferencias, localidades, objetivos_motivos";

        // CAMPOS: sumamos monto y mostramos localidad + motivo
        $campos = "SUM(transferencias.monto) AS monto,
                   localidades.localidad,
                   localidades.orden,
                   objetivos_motivos.motivo";

        // WHERE base con joins
        $sWhere  = " transferencias.id_localidad = localidades.id";
        $sWhere .= " AND transferencias.afectacion = objetivos_motivos.id";
        $sWhere .= " AND (transferencias.observaciones LIKE '%".$query."%'";
        $sWhere .= " OR objetivos_motivos.motivo LIKE '%".$query."%'";
        $sWhere .= " OR localidades.localidad LIKE '%".$query."%'";
        $sWhere .= " OR transferencias.monto LIKE '".$query."%')";

        // Filtro por motivo puntual
        if($motivo > 0){
            $sWhere .= " AND objetivos_motivos.id = '$motivo'";
        }

        // Filtro por rango de fechas
        if (!empty($daterange)){
            list ($f_inicio,$f_final) = explode(" - ", $daterange); // dd/mm/yyyy - dd/mm/yyyy

            list ($dia_inicio,$mes_inicio,$anio_inicio) = explode("/", $f_inicio);
            $fecha_inicial = "$anio_inicio-$mes_inicio-$dia_inicio 00:00:00";

            list($dia_fin,$mes_fin,$anio_fin) = explode("/", $f_final);
            $fecha_final = "$anio_fin-$mes_fin-$dia_fin 23:59:59";
		
            $sWhere .= " AND transferencias.fecha_registro BETWEEN '$fecha_inicial' AND '$fecha_final' ";
        }

        // AGRUPAR por LOCALIDAD y MOTIVO
        $sWhere .= " GROUP BY localidades.id, objetivos_motivos.id";
        $sWhere .= " ORDER BY localidades.orden, objetivos_motivos.motivo";

        // CONSULTA PRINCIPAL
        $query_sql = "SELECT $campos FROM $tables WHERE $sWhere";
        $query     = mysqli_query($con, $query_sql);

        if(!$query){
            echo mysqli_error($con);
            exit;
        }

        // Número de filas (localidad-motivo)
        $numrows = mysqli_num_rows($query);

        if($numrows > 0){ 
            // (Opcional) rango de fechas armado si lo necesitás
            if (!empty($daterange)){
                $desde_fechas = $dia_inicio . "-" . $mes_inicio . "-" . $anio_inicio . "/" . $dia_fin . "-" . $mes_fin . "-" . $anio_fin;
            }

            $delimiter = ";";
            $filename  = "InformeTransferencias_X_Motivos_" . date('Y-m-d') . ".csv";
    
            // puntero al archivo en memoria
            $f = fopen('php://memory', 'w');
	            
            // ENCABEZADOS de columnas
            $fields = array('Localidad', 'Motivo', 'Total Transferido');
            fputcsv($f, $fields, $delimiter);
            
            // filas de datos
            while($row = mysqli_fetch_array($query)){
                $localidad = utf8_decode($row['localidad']);
                $motivo_txt = utf8_decode($row['motivo']);
                $monto      = $row['monto'];

                $lineData   = array($localidad, $motivo_txt, $monto);
                fputcsv($f, $lineData, $delimiter);
            }
            
            // volver al inicio del "archivo"
            fseek($f, 0);
            
            // headers para descargar
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            
            // enviar todo
            fpassthru($f);
        }
    }
}
exit;