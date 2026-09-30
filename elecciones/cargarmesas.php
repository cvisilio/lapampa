<?php
if (!isset($_SESSION)) { session_start(); }

$Listas = [];
$Listas[1]  = ["nombre"=>"FRENTE DEFENDEMOS LA PAMPA", "lista"=>"503", "representacion"=>"DN"];
$Listas[2]  = ["nombre"=>"ALIANZA LA LIBERTAD AVANZA", "lista"=>"501", "representacion"=>"DN"];
$Listas[3]  = ["nombre"=>"FRENTE DE IZQUIERDA Y DE TRABAJADORES-UNIDAD", "lista"=>"502", "representacion"=>"DN"];
$Listas[4]  = ["nombre"=>"CAMBIA LA PAMPA", "lista"=>"504", "representacion"=>"DN"];
$Listas[5]  = ["nombre"=>"MOVIMIENTO AL SOCIALISMO", "lista"=>"13",  "representacion"=>"DN"];
$Listas[6]  = ["nombre"=>"Blancos",         "lista"=>"", "representacion"=>"DN"];
$Listas[7]  = ["nombre"=>"Nulos",           "lista"=>"", "representacion"=>"DN"];
$Listas[8]  = ["nombre"=>"Recurridos",      "lista"=>"", "representacion"=>"DN"];
$Listas[9]  = ["nombre"=>"Impugnados",      "lista"=>"", "representacion"=>"DN"];

// Logout (sin cambios)
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}
if ((isset($_GET['doLogout'])) && ($_GET['doLogout']=="true")){
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username'], $_SESSION['MM_UserGroup'], $_SESSION['PrevUrl']);
  header("Location: index.php"); exit;
}

// Restricción (sin cambios relevantes)
$MM_authorizedUsers = "2,3,4";
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) {
  if (empty($UserName)) return false;
  $arrUsers  = explode(",", $strUsers);
  $arrGroups = explode(",", $strGroups);
  return in_array($UserName, $arrUsers) || in_array($UserGroup, $arrGroups);
}
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
  $MM_restrictGoTo = "index.php";
  $MM_qsChar = (strpos($MM_restrictGoTo, "?") !== false) ? "&" : "?";
  $MM_referrer = $_SERVER['REQUEST_URI'];
  header("Location: ". $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer)); exit;
}

require_once('Connections/conexionUsuarios.php');
$IP = $_SERVER["REMOTE_ADDR"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Estadísticas Elecciones La Pampa</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<style>
  :root { /* paleta sobria */
    --bg: #0f172a;         /* slate-900 */
    --panel: #ffffff;
    --primary: #1e40af;    /* blue-800 */
    --primary-weak:#e0e7ff;/* indigo-100 */
    --text:#111827;        /* gray-900 */
    --muted:#6b7280;       /* gray-500 */
    --zebra1:#f8fafc;      /* slate-50 */
    --zebra2:#eef2ff;      /* indigo-50 */
  }
  body { background:#f3f4f6; color:var(--text); }
  .brand-bar{
    background:linear-gradient(90deg,var(--primary) 0%, #3b82f6 100%);
    color:#fff; padding:10px 0; margin-bottom:20px;
  }
  .brand-bar .brand-title{ font-size:18px; font-weight:600; letter-spacing:.3px; }
  .brand-actions a{ color:#fff; text-decoration:underline; }
  .panel-elevated{
    border:none; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,.08);
  }
  .panel-heading{
    background:#fff !important; border-bottom:1px solid #e5e7eb !important;
    border-top-left-radius:10px; border-top-right-radius:10px;
  }
  .panel-title{ font-weight:600; color:var(--primary); }
  .form-inline .form-control{ width:120px; }
  .meta-line{ font-size:12px; color:var(--muted); }
  .table-clean{ margin:0; }
  .table-clean thead th{
    background:var(--primary-weak); color:#111; border:none;
    text-transform:uppercase; font-size:12px; letter-spacing:.5px;
  }
  .table-clean tbody tr:nth-child(odd){ background:var(--zebra1); }
  .table-clean tbody tr:nth-child(even){ background:var(--zebra2); }
  .table-clean td, .table-clean th{ vertical-align:middle !important; }
  .badge-lista{ background:#111827; }
  .sticky-submit{
    position:sticky; bottom:0; background:#fff; border-top:1px solid #e5e7eb;
    padding:12px; display:flex; justify-content:center; z-index:5; border-bottom-left-radius:10px; border-bottom-right-radius:10px;
  }
  .input-mini{
    max-width:110px;
    text-align:center;
  }
  .help-inline{ font-size:12px; color:var(--muted); }
  .sr-only { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0; }
</style>

</head>
<body>

<!-- Barra superior -->
<div class="brand-bar">
  <div class="container">
    <div class="row">
      <div class="col-xs-4 brand-title">La Pampa · Carga de Mesas</div>
      <div class="col-xs-4 text-center brand-actions">
         <a href="vermesasfaltantes.php"><strong>Ver Mesas Faltantes</strong></a> |  <a href="vermesascargadas.php"><strong>Ver Mesas Cargadas</strong></a>
      </div>
      <div class="col-xs-4 text-right brand-actions">
        <a href="<?php echo $logoutAction; ?>"><strong>Desconectar</strong></a>
      </div>
    </div>
  </div>
</div>

<div class="container">

  <div class="panel panel-default panel-elevated">
  <!--  <div class="panel-heading"> -->
     <!--   <h3 class="panel-title">Carga rápida por mesa</h3> -->
     <!--   <div class="meta-line">IP: <?php //echo htmlspecialchars($IP); ?></div> -->
    <!--  </div> -->

    <form action="guardardatos.php" method="post" id="form1" autocomplete="off">
      <div class="panel-body">

        <!-- Mesa -->
        <div class="row" style="margin-bottom:10px">
          <div class="col-sm-3">
            <label for="txtMesa" class="control-label">Nº de Mesa</label>
            <input
              type="number"
              class="form-control"
              id="txtMesa"
              name="txtMesa"
              inputmode="numeric"
              min="1"
              max="915"
              step="1"
              required
              autofocus
              placeholder="">
            <span class="help-inline">Rango válido: 1 a 915</span>
          </div>
        </div>

        <!-- Tabla de carga -->
        <div class="table-responsive">
          <table class="table table-clean">
            <thead>
              <tr>
                <th class="text-center" style="width:10%">Nº</th>
                <th>Lista / Agrupación</th>
                <th class="text-center" style="width:20%">Diputado</th>
              </tr>
            </thead>
            <tbody>
              <?php for($i=1; $i<=9; $i++): ?>
                <?php
                  $esDN = (strpos($Listas[$i]["representacion"], "DN") !== false);
                  $nro  = htmlspecialchars($Listas[$i]['lista']);
                  $nom  = htmlspecialchars($Listas[$i]['nombre']);
                ?>
                <tr>
                  <td class="text-center">
                    <?php if($nro !== ''): ?>
                      <span class="badge badge-lista"><?php echo $nro; ?></span>
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div id="id_label_nombre_<?php echo $i; ?>" class="h5" style="margin:0"><?php echo $nom; ?></div>
                   <!-- <div class="help-inline">Representación: <?php //echo htmlspecialchars($Listas[$i]["representacion"]); ?></div> -->
                  </td>
                  <td class="text-center">
                    <?php if($esDN): ?>
                      <label for="txtL<?php echo $i; ?>DP" class="sr-only">Votos Diputado <?php echo $nom; ?></label>
                      <input
                        type="number"
                        class="form-control input-mini js-salto"
                        id="txtL<?php echo $i; ?>DP"
                        name="txtL<?php echo $i; ?>DP"
                        inputmode="numeric"
                        min="0"
                        max="350"
                        step="1"
                        placeholder="0">
                    <?php else: ?>
                      <span class="text-muted">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>

      </div>

      <!-- Footer fijo con botón -->
      <div class="sticky-submit">
        <button type="submit" id="enviardatos" class="btn btn-primary btn-lg">
          Enviar Datos
        </button>
      </div>
    </form>
  </div>
</div>

<script>
/* Enter = pasar al siguiente campo con clase .js-salto */
(function(){
  function focusNext(current){
    var inputs = Array.prototype.slice.call(document.querySelectorAll('.js-salto'));
    var idx = inputs.indexOf(current);
    if (idx > -1 && idx < inputs.length - 1) {
      inputs[idx+1].focus();
      inputs[idx+1].select && inputs[idx+1].select();
    } else if (idx === inputs.length - 1) {
      document.getElementById('enviardatos').focus();
    }
  }
  document.addEventListener('keydown', function(e){
    var t = e.target;
    if (t.classList && t.classList.contains('js-salto') && e.key === 'Enter') {
      e.preventDefault();
      focusNext(t);
    }
  });

  // Sanitiza: evita negativos y no numéricos
  $(document).on('input', '.js-salto, #txtMesa', function(){
    var v = this.value.replace(/[^\d]/g,'');
    this.value = v;
    var max = parseInt(this.getAttribute('max'),10);
    if (max && v !== '' && parseInt(v,10) > max) this.value = max;
  });
})();


// Evitar submit con Enter en el campo de Mesa y saltar al primer input de votos
(function(){
  var mesa = document.getElementById('txtMesa');
  if (mesa) {
    mesa.addEventListener('keydown', function(e){
      if (e.key === 'Enter') {
        e.preventDefault();
        var firstVote = document.querySelector('.js-salto');
        if (firstVote) {
          firstVote.focus();
          if (firstVote.select) firstVote.select();
        }
      }
    });
  }
})();


(function(){
  var form = document.getElementById('form1');
  if (form) {
    form.addEventListener('keydown', function(e){
      if (e.key === 'Enter') {
        // Dejamos que el botón submit funcione normal
        var isSubmitBtn = (e.target.id === 'enviardatos') || (e.target.type === 'submit');
        if (!isSubmitBtn) e.preventDefault();
      }
    });
  }
})();

</script>


</body>
</html>
