<?php
// Totales por elección y lista (todas las localidades)
$color_leyenda = "#000000";
$cadena_elecciones = "";
$cadena = "";
$suma_resultados = 0;
$resultado = 0;

require_once('Connections/conexionUsuarios.php');

// Trae todas las elecciones cargadas, más recientes primero
$query_elecciones = mysqli_query($con, "SELECT * FROM elecciones WHERE cargada=1 ORDER BY fecha DESC");

while ($row_elecciones = mysqli_fetch_array($query_elecciones)) {
    $anio   = $row_elecciones['fecha'];
    $cargos = $row_elecciones['cargos'];   // Pdte | DN | SN | C,J,I,DP,G
    $eleccion = $row_elecciones['id_eleccion'];
    $listas_comprar = $row_elecciones['listas_comprar']; // si lo usás luego, queda disponible
    list($comparar_uno, $comparar_dos) = explode(";", $listas_comprar);

    // Título de la elección
    $cadena_elecciones .= "<tr><td colspan='2' align='center'><h4><strong style='color:{$color_leyenda}'>"
                        . $row_elecciones['tipo'] . "</strong></h4></td></tr>";

    // Determinar campo de orden según cargos
    switch ($cargos) {
        case "Pdte":         $orden = "presidente";          break;
        case "DN":           $orden = "diputado_nacional";   break;
        case "SN":           $orden = "senador_nacional";    break;
        case "C,J,I,DP,G":   $orden = "intendente";          break;
        default:             $orden = "presidente";          break;
    }

    // Suma total de votos por elección (todas las localidades), filtrando listas 100/101
    $sql_totales = "
        SELECT
            SUM(r.intendente)          AS suma_intendentes,
            SUM(r.diputado_nacional)   AS suma_diputado_nacional,
            SUM(r.senador_nacional)    AS suma_senador_nacional,
            SUM(r.presidente)          AS suma_presidente
        FROM resultados_elecciones r
        JOIN listas_elecciones l ON r.lista = l.id_lista
        WHERE l.numero_lista_provincial NOT IN ('100','101')
          AND r.eleccion = '{$eleccion}'
    ";
    $rs_totales = mysqli_query($con, $sql_totales);
    $tot = mysqli_fetch_array($rs_totales);

    $suma_votos_presidente  = (int)$tot['suma_presidente'];
    $suma_votos_diputado    = (int)$tot['suma_diputado_nacional'];
    $suma_votos_senador     = (int)$tot['suma_senador_nacional'];
    $suma_votos_intendentes = (int)$tot['suma_intendentes'];

    // Top 6 listas por elección (acumulado provincial), ordenado por el cargo correspondiente
    $sql_top = "
        SELECT
            l.id_lista,
            l.abreviatura_provincial,
            l.color2,
            SUM(r.intendente)          AS intendente,
            SUM(r.diputado_nacional)   AS diputado_nacional,
            SUM(r.senador_nacional)    AS senador_nacional,
            SUM(r.presidente)          AS presidente
        FROM resultados_elecciones r
        JOIN listas_elecciones l ON r.lista = l.id_lista
        WHERE l.numero_lista_provincial NOT IN ('100','101')
          AND r.eleccion = '{$eleccion}'
        GROUP BY l.id_lista, l.abreviatura_provincial, l.color2
        ORDER BY {$orden} DESC
        LIMIT 6
    ";
    $rs_top = mysqli_query($con, $sql_top);

    while ($rw = mysqli_fetch_array($rs_top)) {
        $lista1 = $rw['abreviatura_provincial'];

        // Resultado y total según el cargo
        switch ($cargos) {
            case "Pdte":
                $resultado  = (int)$rw['presidente'];
                $suma_votos = max(1, $suma_votos_presidente);
                break;
            case "DN":
                $resultado  = (int)$rw['diputado_nacional'];
                $suma_votos = max(1, $suma_votos_diputado);
                break;
            case "SN":
                $resultado  = (int)$rw['senador_nacional'];
                $suma_votos = max(1, $suma_votos_senador);
                break;
            case "C,J,I,DP,G":
                $resultado  = (int)$rw['intendente'];
                $suma_votos = max(1, $suma_votos_intendentes);
                break;
            default:
                $resultado  = (int)$rw['presidente'];
                $suma_votos = max(1, $suma_votos_presidente);
                break;
        }

        $color = $rw['color2'];
        if ($resultado > 0) {
            $porcentaje = ($resultado / $suma_votos) * 100;
            $porcentaje = number_format($porcentaje, 1, ",", ".");
            $resultado_fmt = number_format($resultado, 0, ",", ".");

            $cadena_elecciones .= "<tr>
                <td><h4><strong style='color:{$color}'>{$lista1}</strong></h4></td>
                <td align='right'><h4><strong style='color:{$color}'>{$porcentaje}% ({$resultado_fmt})</strong></h4></td>
            </tr>";
        }
    }
}

// Encapsulado en tabla (sin nombre de localidad, porque es total provincial)
$cadena = "<table class='table'>
    <tr><td colspan='2' align='center'>
        <h3><strong style='color:{$color_leyenda}'>Totales provinciales</strong></h3>
    </td></tr>
    {$cadena_elecciones}
</table>";

echo $cadena;
?>
