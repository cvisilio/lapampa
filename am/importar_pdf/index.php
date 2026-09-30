<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>


<?php 
   require_once("classes/Login.php");
   
    $login = new Login();
	if ($login->isUserLoggedIn() == true) 
	{	
	
include_once("conexion.php");
if(!empty($_GET['import_status'])) {
    switch($_GET['import_status']) {
        case 'success':
            $message_stauts_class = 'alert-success';
            $import_status_message = 'Importacion correcta.';
            break;
        case 'error':
            $message_stauts_class = 'alert-danger';
            $import_status_message = 'Error al importar.';
            break;
        case 'invalid_file':
            $message_stauts_class = 'alert-danger';
            $import_status_message = 'Archivo no valido. Sube un PDF, Excel (.xlsx / .xls) o CSV.';
            break;
        case 'no_file':
            $message_stauts_class = 'alert-warning';
            $import_status_message = 'No se recibio el archivo.';
            break;
        case 'needs_unsecured':
            $message_stauts_class = 'alert-warning';
            $import_status_message = 'El PDF esta protegido o no se pudo leer el texto. Genera una copia sin restricciones (Guardar como / Imprimir a PDF) y vuelve a subirla.';
            break;
        case 'needs_ocr':
            $message_stauts_class = 'alert-warning';
            $import_status_message = 'El PDF no tiene texto seleccionable (parece escaneo). Usa OCR o pedi otro archivo.';
            break;
        default:
            $message_stauts_class = '';
            $import_status_message = '';
    }
    if (!empty($_GET['why']) && is_string($_GET['why'])) {
        $detail = trim($_GET['why']);
        if ($detail !== '') {
            $import_status_message .= ' ' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8');
        }
    }
}
?>
<title>Importar Transferencia</title>
<script type="text/javascript" src="script/validation.min.js"></script>
<script type="text/javascript" src="script/login.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css" media="screen">

<div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
               
                <?php 
				$importar_menu_pdf=1;
				include("menu.php");?>
               </div>
            </div>

	<h2>Importar extracto (PDF o Excel)</h2>	
    <?php if (!empty($_SESSION['pdf_import_token'])) { ?>
    <p><a href="importar_pdf/resultado.php" class="btn btn-success btn-sm">Ver &Uacute;ltima importaci&oacute;n y exportar Excel</a></p>
    <?php } ?>
    <?php if(!empty($import_status_message)){
        echo '<div class="alert '.$message_stauts_class.'">'.$import_status_message.'</div>';
    } ?>
    <div class="panel panel-default">        
        <div class="panel-body">
			<br>
			<div class="row">
				<form action="importar_pdf/import.php" method="post" enctype="multipart/form-data" id="import_form">				
						<div class="col-md-12" style="margin-bottom:12px;">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="transfer_recibidas_solo_cuit_localidad" value="1" checked>
									Transferencias recibidas = al CUIT de localidad
								</label>
							</div>
							<p class="text-muted small" style="margin:4px 0 0 22px;">Si est&aacute; tildado: solo se suman importes cuyo <strong>concepto o comprobante</strong> incluya el mismo CUIT que el titular del PDF (no se miran fecha, importe ni saldo para eso). Si no est&aacute; tildado: se suman todas las l&iacute;neas del bloque por d&iacute;a.</p>
						</div>
						<div class="col-md-5">
						<input type="file" name="documento1" id="documento1" accept=".pdf,.xlsx,.xls,.csv,application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv" />
						<p class="text-muted small" style="margin-top:8px;">Si el PDF est&aacute; protegido, el sistema intenta liberarlo autom&aacute;ticamente con Python.</p>
						</div>
						<div class="col-md-3">
						<input type="password" class="form-control" name="pdf_password" id="pdf_password" placeholder="Clave PDF (opcional)" autocomplete="off">
						</div>
						<div class="col-md-4">
						<input type="submit" class="btn btn-primary" name="import_data" value="IMPORTAR">
						</div>			
				</form>
			</div>
			<br>
			<div class="row">
				<table class="table table-bordered">
					<thead>
						<tr>
						  <th>Localidad</th>
                          <th>Fecha</th>
						  <th>monto</th>
					    </tr>
					</thead>
					<tbody>
					<?php
					/*    $tables="importes_pdf, localidades";
	$campos="importes_pdf.*, localidades.localidad";
	$sWhere=" importes_pdf.id_localidad=localidades.id and importes_pdf.indice_anio=2025";
	                    $query = mysqli_query($con,"SELECT $campos FROM  $tables where $sWhere order by importes_pdf.id DESC LIMIT 80");
				
					if(mysqli_num_rows($query)) { 
						while( $rows = mysqli_fetch_assoc($query)) { 
						 $fecha_registro=$rows['fecha_registro']; 
						// list($date,$hora)=explode(" ",$fecha_registro);
						// list($Y,$m,$d)=explode("-",$date);
						// $fecha=$d."-".$m."-".$Y;	
						*/
						?>
						<tr>
						  <td><?php //echo $rows['localidad']; ?></td>
				          <td><?php //echo $fecha; ?></td>           
			    		  <td align="right"><?php //echo "$". number_format($rows['monto'],0,",",".");?></td>
						</tr>
						<?php // } } else { ?>  
						<tr><td colspan="5">No hay registros.....</td></tr>
						<?php // } ?>					
					</tbody>
				</table>
			</div>	
        </div>
    </div>		
	
 </div>

<?php } 
else
	{
	 header("location: login.php");
	 exit;		
	}

?>