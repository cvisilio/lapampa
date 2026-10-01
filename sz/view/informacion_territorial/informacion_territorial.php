<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// si llegan por GET o POST
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

?>


<!DOCTYPE html>
<html>
  <head>
   <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informaci&oacute;n Territorial</title>
    
	<?php include 'head.php'; ?>
  
  <style>

/* Contenedor robusto para botones en el panel derecho */
.panel-actions {
  display: flex !important;
  flex-wrap: wrap !important;
  align-items: center !important;
  justify-content: flex-start !important;
  width: 100% !important;
  position: relative !important;
  text-align: left !important;
  margin-top: .5rem !important;
  clear: both !important;
}

/* Cada botón dentro con separación propia */
.panel-actions .btn {
  position: static !important;
  float: none !important;
  display: inline-flex !important;
  margin-right: .5rem !important;
  margin-bottom: .5rem !important;
  white-space: nowrap !important;
}

/* Asegura que nada lo empuje hacia la derecha */
#leyendaMicro {
  clear: both !important;
}

</style>  
    
  </head>
  <body class="hold-transition <?php echo $skin;?> sidebar-mini">

    <div class="wrapper">
      <header class="main-header">
		<?php include 'main-header.php'; ?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
          <?php 
		 
		  include 'main-sidebar.php'; ?>
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
		<?php if ($permisos_ver==1){?>
        <section class="content-header">
       
  <div class="container-fluid">
  <div class="row">
    <!-- MAPA (tamaño medio-grande) -->
    <div class="col-lg-8 mb-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="h5 mb-0">Provincia de La Pampa</div>
          </div>

          <div class="d-flex justify-content-center">
            <div class="map-wrap" style="position:relative; width:90%; max-width:800px;">
              <canvas id="mapa" width="800" height="800"
                      style="width:100%; height:auto; border:1px solid #e9ecef; border-radius:.5rem; display:block;"></canvas>
              <div id="tooltip" class="badge badge-light"
                   style="position:absolute; display:none; pointer-events:none;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PANEL DERECHO -->
    <div class="col-lg-4 mb-3">
      <div class="card shadow-sm h-100">
        <div class="card-body" id="panelTotalProv">

          <!-- Filtros de Producción -->
          <div class="border rounded p-3 mb-3">
            <div class="form-row">
              <div class="form-group col-12">
                <label class="small text-muted mb-1" for="selectSistema">Sistema de producción</label>
                <select id="selectSistema" class="form-control">
                  <option value="">Seleccioná un sistema...</option>
                  <?php
				
                  $qSis = "SELECT id, grupo FROM grupos_informacion WHERE id_sistema_informacion = $id ORDER BY grupo";
		   if ($rsSis = mysqli_query($con, $qSis)):
                      while ($s = mysqli_fetch_assoc($rsSis)):
                  ?>
                    <option value="<?php echo (int)$s['id']; ?>">
                      <?php echo htmlspecialchars($s['grupo'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                  <?php
                      endwhile;
                    endif;
                  ?>
                </select>
              </div>
            
              <div class="form-group col-12">
                <label class="small text-muted mb-1" for="selectRubro">Rubro</label>
                <select id="selectRubro" class="form-control" disabled>
                  <option value="">Elegí primero un sistema...</option>
                </select>
              </div>
            </div>

       <div class="panel-actions">
          <button id="btnVerVolumenes"
                  type="button"
                  class="btn btn-primary btn-sm"
                  disabled>
            Ver volúmenes
          </button>

          <button id="btnModoColorVolumen"
                  type="button"
                  class="btn btn-outline-warning btn-sm"
                  title="Alterna entre colorear por intensidad de volumen o por micro-región"
                  aria-label="Alternar modo de color del mapa"
                  disabled>
            <i class="fa fa-adjust mr-1" aria-hidden="true"></i>
            <span class="js-modo-color-text">Color por volumen</span>
          </button>
        
          <button id="btnMicro"
                  type="button"
                  class="btn btn-outline-secondary btn-sm">
            Pintar micro-regiones
          </button>
        </div>
        
        <div id="leyendaMicro" class="small mt-2"></div>
        
        <small class="text-muted d-inline-block mt-2">
          Se filtra por localidad si hay una seleccionada.
        </small>
        
        <button id="btnSoloLocalidades" class="btn btn-outline-secondary btn-sm">Solo localidades</button>
        <button id="btnSoloComisiones" class="btn btn-outline-secondary btn-sm">Solo comisiones</button>
        <button id="btnTodos" class="btn btn-light btn-sm">Todas</button>

            
          <!-- Resultados -->
          <div id="panelVolumenes" class="mb-3"></div>

          <!-- Selector de Localidad -->
          <div class="border-top pt-3">
            <label for="selectLocalidad" class="mr-2 small text-muted">Localidad</label>
            <select id="selectLocalidad" name="selectLocalidad" class="form-control">
              <option value="0">Toda la Provincia</option>
              <option value="1">25 de Mayo</option>
              <option value="2">Abramo</option>
              <option value="3">Adolfo Van Praet</option>
              <option value="4">Agustoni</option>
              <option value="5">Algarrobo del &Aacute;guila</option>
              <option value="7">Alpachiri</option>
              <option value="8">Alta Italia</option>
              <option value="9">Anguil</option>
              <option value="10">Arata</option>
              <option value="11">Ataliva Roca</option>
              <option value="12">Bernardo Larroud&eacute;</option>
              <option value="13">Bernasconi</option>
              <option value="14">Caleuf&uacute;</option>
              <option value="15">Carro Quemado</option>
              <option value="1019">Casa de Piedra</option>
              <option value="16">Catril&oacute;</option>
              <option value="17">Ceballos</option>
              <option value="18">Chacharramendi</option>
              <option value="23">Col. Santa Mar&iacute;a</option>
              <option value="81">Col. Santa Teresa</option>
              <option value="21">Colonia Bar&oacute;n</option>
              <option value="24">Conhello</option>
              <option value="20">Cnel Hilario Lagos</option>
              <option value="27">Cuchillo C&oacute;</option>
              <option value="28">Doblas</option>
              <option value="29">Dorila</option>
              <option value="30">Eduardo Castex</option>
              <option value="32">Embajador Martini</option>
              <option value="35">Falucho</option>
              <option value="38">General Acha</option>
              <option value="39">General Campos</option>
              <option value="36">General Pico</option>
              <option value="41">General San Mart&iacute;n</option>
              <option value="37">Gobernador Duval</option>
              <option value="42">Guatrach&eacute;</option>
              <option value="43">Ingeniero Luiggi</option>
              <option value="45">Intendente Alvear</option>
              <option value="46">Jacinto Arauz</option>
              <option value="47">La Adela</option>
              <option value="49">La Humada</option>
              <option value="50">La Maruja</option>
              <option value="51">La Reforma</option>
              <option value="52">Limay Mahuida</option>
              <option value="53">Lonquimay</option>
              <option value="54">Loventu&eacute;</option>
              <option value="55">Luan Toro</option>
              <option value="56">Macach&iacute;n</option>
              <option value="57">Maisonnave</option>
              <option value="58">Mauricio Mayer</option>
              <option value="59">Metileo</option>
              <option value="60">Miguel Can&eacute;</option>
              <option value="61">Miguel Riglos</option>
              <option value="62">Monte Nievas</option>
              <option value="65">Parera</option>
              <option value="66">Per&uacute;</option>
              <option value="67">Pichi Huinca</option>
              <option value="68">Puelches</option>
              <option value="69">Puel&eacute;n</option>
              <option value="70">Quehu&eacute;</option>
              <option value="71">Quem&uacute; Quem&uacute;</option>
              <option value="72">Quetrequ&eacute;n</option>
              <option value="73">Rancul</option>
              <option value="74">Realic&oacute;</option>
              <option value="75">Relmo</option>
              <option value="76">Rol&oacute;n</option>
              <option value="77">Rucanelo</option>
              <option value="79">Santa Isabel</option>
              <option value="80">Santa Rosa</option>
              <option value="82">Sarah</option>
              <option value="83">Speluzzi</option>
              <option value="84">Tel&eacute;n</option>
              <option value="85">Toay</option>
              <option value="86">Tom&aacute;s M Anchorena</option>
              <option value="88">Trenel</option>
              <option value="89">Unanue</option>
              <option value="90">Uriburu</option>
              <option value="91">V&eacute;rtiz</option>
              <option value="92">Victorica</option>
              <option value="93">Villa Mirasol</option>
              <option value="94">Winifreda</option>
            </select>
            
       </div> <!-- /.card-body -->
      </div>   <!-- /.card -->
    </div>     <!-- /.col-lg-4 -->
  </div>       <!-- /.row -->
</div>         <!-- /.container-fluid -->

</section>
<?php } ?>
</div><!-- /.content-wrapper -->

 <?php include 'footer.php'; ?>
    </div><!-- ./wrapper -->
 <?php include 'js.php'; ?>
    
</body>
</html>

<script>
// === Labels redondel blanco ===
const LABEL_FONT      = '14px system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, sans-serif';
const LABEL_TEXT      = '#000000';
const LABEL_BG        = '#ffffff';
const LABEL_STROKE    = 'rgba(0,0,0,.25)';
const LABEL_PAD       = 6;
const LABEL_MIN_DIAM  = 22;

function drawLabelBubble(x, y, text){
  ctx.save();
  ctx.font = LABEL_FONT;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';

  const m = ctx.measureText(text);
  const textH = (m.actualBoundingBoxAscent || 7) + (m.actualBoundingBoxDescent || 3);
  const radius = Math.max(LABEL_MIN_DIAM/2, Math.max(m.width/2, textH/2) + LABEL_PAD);

  ctx.beginPath();
  ctx.arc(x, y, radius, 0, Math.PI * 2);
  ctx.fillStyle = LABEL_BG;
  ctx.fill();
  ctx.lineWidth = 1;
  ctx.strokeStyle = LABEL_STROKE;
  ctx.stroke();

  ctx.fillStyle = LABEL_TEXT;
  ctx.fillText(text, x, y);

  ctx.restore();
}
</script>

<script>
// ===== Estado global =====
let microOn   = false;      // modo micro-regiones
let microMap  = null;       // { id_localidad: region_am }
let showLabels= false;
let labelMode = 'micro';    // 'micro' | 'volumen'

const LOCALIDAD_FILL = '#4CAF50';  // verde para localidades
const COMISION_FILL  = '#007BFF';  // azul para comisiones
const INACTIVE_FILL  = '#e9ecef';  // gris atenuado

// Filtro por tipo de localidad
let tipoLocalidadActivo = 0;     // 0 = sin filtro, 1 = localidades, 2 = comisiones
let tipoLocalidadMap    = null;  // { id_loc_padron: true }

// Paleta micro-regiones
const MICRO_COLORS = [
  '#00000000',
  '#035D54',
  '#00CC66',
  '#B6B692',
  '#FFCC33',
  '#878749',
  '#7F5E97',
  '#5E9D82',
  '#C4C299',
  '#A66770',
  '#0168AD'
];

// Escala de color para modo volúmenes (bajo -> alto): amarillo -> naranja -> rojo
const VOLUME_COLOR_LOW  = '#ffe066';
const VOLUME_COLOR_MID  = '#ff922b';
const VOLUME_COLOR_HIGH = '#e03131';

// Volúmenes por micro
let volumeMap    = null;
let volumeUnidad = '';
let volumeYear   = null;
let volumenColorMode = 'volumen'; // 'volumen' | 'micro'


// Config canvas
const BASE_FILL    = '#cfd4da';
const BASE_STROKE  = '#ffffff';
const HOVER_STROKE = '#888';
const PIN_FILL     = '#ffffff';
const PIN_STROKE   = '#202020';
const PIN_HEAD     = '#e03131';
const PIN_RADIUS   = 7;
const PIN_HEIGHT   = 20;

const canvas          = document.getElementById('mapa');
const ctx             = canvas.getContext('2d');
const tooltip         = document.getElementById('tooltip');
const selectLocalidad = document.getElementById('selectLocalidad');
const btnMicro        = document.getElementById('btnMicro');
const leyenda         = document.getElementById('leyendaMicro');

let shapes            = [];
let selectedLocalidad = null;
const panel           = document.getElementById('panelTotalProv');
let initialPanelHTML  = panel ? panel.innerHTML : '';

// Helpers
function shortNumber(n){
  const v = Number(n);
  if (!isFinite(v)) return '';
  const abs = Math.abs(v);
  if (abs >= 1e9) return (v/1e9).toFixed(1).replace(/\.0$/,'')+'B';
  if (abs >= 1e6) return (v/1e6).toFixed(1).replace(/\.0$/,'')+'M';
  if (abs >= 1e3) return (v/1e3).toFixed(1).replace(/\.0$/,'')+'k';
  return v.toLocaleString('es-AR');
}

function normalizeStr(s){
  return (s || '').toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .replace(/\s+/g,' ')
    .trim().toLowerCase();
}

function getCanvasCoords(evt){
  const rect = canvas.getBoundingClientRect();
  const scaleX = canvas.width  / rect.width;
  const scaleY = canvas.height / rect.height;
  return {
    x: (evt.clientX - rect.left) * scaleX,
    y: (evt.clientY - rect.top)  * scaleY
  };
}

function polygonCentroid(points) {
  let area = 0, cx = 0, cy = 0;
  const n = points.length;
  for (let i = 0; i < n; i++) {
    const [x1, y1] = points[i];
    const [x2, y2] = points[(i + 1) % n];
    const cross = x1 * y2 - x2 * y1;
    area += cross;
    cx += (x1 + x2) * cross;
    cy += (y1 + y2) * cross;
  }
  area *= 0.5;
  if (Math.abs(area) < 1e-7) {
    let sx = 0, sy = 0;
    points.forEach(([x, y]) => { sx += x; sy += y; });
    return [sx / n, sy / n];
  }
  cx /= (6 * area);
  cy /= (6 * area);
  return [cx, cy];
}

function computeRegionCentroids(offsetY = 15){
  if (!microMap || !shapes.length) return {};
  const acc = {};
  for (const obj of shapes){
    if (!obj.id) continue;
    const reg = microMap[obj.id];
    if (!reg || !obj.centroid) continue;
    const [x,y] = obj.centroid;
    if (!acc[reg]) acc[reg] = { sx:0, sy:0, n:0 };
    acc[reg].sx += x;
    acc[reg].sy += y;
    acc[reg].n  += 1;
  }
  const out = {};
  for (const r in acc){
    const {sx, sy, n} = acc[r];
    if (n > 0) out[r] = [ sx/n, sy/n + offsetY ];
  }
  return out;
}

function hexToRgb(hex){
  const clean = (hex || '').replace('#', '');
  if (clean.length !== 6) return [0, 0, 0];
  return [
    parseInt(clean.slice(0, 2), 16),
    parseInt(clean.slice(2, 4), 16),
    parseInt(clean.slice(4, 6), 16)
  ];
}

function mixHex(lowHex, highHex, t){
  const [lr, lg, lb] = hexToRgb(lowHex);
  const [hr, hg, hb] = hexToRgb(highHex);
  const ratio = Math.max(0, Math.min(1, Number(t) || 0));
  const r = Math.round(lr + (hr - lr) * ratio);
  const g = Math.round(lg + (hg - lg) * ratio);
  const b = Math.round(lb + (hb - lb) * ratio);
  return `rgb(${r}, ${g}, ${b})`;
}

function getVolumeColorByRatio(t){
  const ratio = Math.max(0, Math.min(1, Number(t) || 0));
  if (ratio <= 0.5) {
    return mixHex(VOLUME_COLOR_LOW, VOLUME_COLOR_MID, ratio / 0.5);
  }
  return mixHex(VOLUME_COLOR_MID, VOLUME_COLOR_HIGH, (ratio - 0.5) / 0.5);
}

function updateModoColorButtonUI(){
  const btnModoColorVolumen = document.getElementById('btnModoColorVolumen');
  if (!btnModoColorVolumen) return;
  const text = (volumenColorMode === 'volumen') ? 'Color por volumen' : 'Color por micro';
  const spanText = btnModoColorVolumen.querySelector('.js-modo-color-text');
  if (spanText) spanText.textContent = text;
  btnModoColorVolumen.title = (volumenColorMode === 'volumen')
    ? 'Mostrando intensidad de volumen. Clic para cambiar a color por micro-región.'
    : 'Mostrando color por micro-región. Clic para cambiar a intensidad de volumen.';
  btnModoColorVolumen.classList.remove('btn-outline-warning', 'btn-warning');
  btnModoColorVolumen.classList.add(
    volumenColorMode === 'volumen' ? 'btn-warning' : 'btn-outline-warning'
  );
}

function drawPin(x, y) {
  ctx.save();
  ctx.beginPath();
  ctx.moveTo(x, y);
  ctx.quadraticCurveTo(x - PIN_RADIUS, y - (PIN_HEIGHT * 0.35), x, y - PIN_HEIGHT);
  ctx.quadraticCurveTo(x + PIN_RADIUS, y - (PIN_HEIGHT * 0.35), x, y);
  ctx.closePath();
  ctx.fillStyle = PIN_HEAD;
  ctx.strokeStyle = PIN_STROKE;
  ctx.lineWidth = 1.5;
  ctx.fill();
  ctx.stroke();

  ctx.beginPath();
  ctx.arc(x, y - (PIN_HEIGHT * 0.55), PIN_RADIUS, 0, Math.PI * 2);
  ctx.fillStyle = PIN_FILL;
  ctx.strokeStyle = PIN_STROKE;
  ctx.lineWidth = 1.2;
  ctx.fill();
  ctx.stroke();

  ctx.beginPath();
  ctx.arc(x, y - (PIN_HEIGHT * 0.55), PIN_RADIUS * 0.35, 0, Math.PI * 2);
  ctx.fillStyle = PIN_HEAD;
  ctx.fill();
  ctx.restore();
}

function informacion1(id_localidad){
  if (!panel) return;
  $.ajax({
    type: "POST",
    url: 'ver_localidad.php',
    data: { id_localidad: String(id_localidad) },
    success: function(html) { panel.innerHTML = html; },
    error: function() { alert('No se pudo obtener datos de la localidad'); }
  });
}

function irALocalidad(idLocalidad){
  if (!idLocalidad || idLocalidad <= 0){
    if (panel) panel.innerHTML = initialPanelHTML;
    return;
  }
  informacion1(idLocalidad);
}

function drawLabels(){
  if (!showLabels) return;
  ctx.save();
  ctx.font = LABEL_FONT;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';

  if (labelMode === 'volumen') {
    if (!microOn || !microMap || !volumeMap) { ctx.restore(); return; }
    const regionPts = computeRegionCentroids();
    for (const regStr in regionPts){
      const reg = parseInt(regStr, 10);
      const [x, y] = regionPts[regStr];
      const val = volumeMap[reg];
      if (val == null) continue;
      drawLabelBubble(x, y, shortNumber(val));
    }
    ctx.restore();
    return;
  }

  if (labelMode === 'micro') {
    if (!microOn || !microMap) { ctx.restore(); return; }
    const regionPts = computeRegionCentroids();
    for (const regStr in regionPts){
      const [x,y] = regionPts[regStr];
      drawLabelBubble(x, y, String(parseInt(regStr, 10)));
    }
    ctx.restore();
    return;
  }

  ctx.restore();
}

// ===== Dibujo mapa =====
function drawMap(hoverObj = null){
  ctx.clearRect(0,0,canvas.width,canvas.height);
  let volumeMin = null;
  let volumeMax = null;

  if (labelMode === 'volumen' && volumeMap) {
    const values = Object.values(volumeMap)
      .map((v) => Number(v))
      .filter((v) => Number.isFinite(v));
    if (values.length) {
      volumeMin = Math.min(...values);
      volumeMax = Math.max(...values);
    }
  }

 for (const obj of shapes){
  let isActive = true;

  // Si hay filtro por tipo de localidad, solo quedan fuertes las que estén en el mapa de ese tipo
  if (tipoLocalidadActivo && tipoLocalidadMap && obj.id) {
    isActive = !!tipoLocalidadMap[obj.id];
  }

  let fill;

  // 1) Si NO es activa bajo el filtro → gris
  if (!isActive) {
    fill = INACTIVE_FILL;

  // 2) Si está en modo volúmenes, pintamos por intensidad de valor
  } else if (
    microOn && microMap && obj.id && microMap[obj.id] &&
    labelMode === 'volumen' && volumeMap && volumenColorMode === 'volumen'
  ) {
    const reg = microMap[obj.id];
    const val = Number(volumeMap[reg]);
    if (Number.isFinite(val) && volumeMin !== null && volumeMax !== null) {
      const ratio = volumeMax === volumeMin ? 1 : (val - volumeMin) / (volumeMax - volumeMin);
      fill = getVolumeColorByRatio(ratio);
    } else {
      fill = '#eef4ee';
    }

  // 3) Si está activo pintar micro-regiones → mandan los colores de micro
  } else if (microOn && microMap && obj.id && microMap[obj.id]) {
    const reg = microMap[obj.id];
    fill = MICRO_COLORS[reg] || BASE_FILL;

  // 4) Si filtro = Solo localidades
  } else if (tipoLocalidadActivo === 3) {
    fill = LOCALIDAD_FILL;

  // 5) Si filtro = Solo comisiones
  } else if (tipoLocalidadActivo === 2) {
    fill = COMISION_FILL;

  // 6) Sin filtro → color base
  } else {
    fill = BASE_FILL;
  }

  ctx.fillStyle = fill;
  ctx.strokeStyle = BASE_STROKE;
  ctx.lineWidth = 1;
  ctx.fill(obj.path);
  ctx.stroke(obj.path);
}

  if (selectedLocalidad){
    const sel = shapes.find(s => s.name === selectedLocalidad);
    if (sel && sel.centroid){
      drawPin(sel.centroid[0], sel.centroid[1]);
    }
  }

  drawLabels();
}

// ===== Leyenda micro =====
function renderLeyenda(legend){
  if (!leyenda) return;
  const base = Array.from({length:10}, (_,i)=>({id:i+1, present:true}));
  const src = (legend && legend.length) ? legend : base;

  let html = '<div class="d-flex flex-wrap align-items-center">';
  src.forEach(item=>{
    if (!item.present) return;
    const color = MICRO_COLORS[item.id] || '#ddd';
    html += `
      <div class="d-flex align-items-center mr-3 mb-1">
        <span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:${color};border:1px solid #999;margin-right:6px"></span>
        <span>Microregión ${item.id}</span>
      </div>`;
  });
  html += '</div>';
  leyenda.innerHTML = html;
}

async function ensureMicroMap(){
  if (microMap) return true;
  try{
    const url = 'view/informacion_territorial/ajax_get_microregiones.php';
    const r = await fetch(url, { method:'POST' });
    if (!r.ok) throw new Error('HTTP '+r.status);
    const data = await r.json();
    if (!data || !data.ok) throw new Error('Respuesta inválida');
    microMap = data.map || {};
    renderLeyenda(data.legend || []);
    return true;
  }catch(e){
    console.error('Microregiones error:', e);
    alert('No se pudieron cargar las micro-regiones');
    return false;
  }
}

async function setTipoLocalidad(tipo) {
  console.log('setTipoLocalidad llamado con:', tipo);

  if (tipo === 0 || tipo === '0' || !tipo) {
    tipoLocalidadActivo = 0;
    tipoLocalidadMap    = null;
    drawMap();
    return;
  }

  tipo = parseInt(tipo, 10);
  if (tipo !== 2 && tipo !== 3) {
    console.warn('tipo_localidad inválido:', tipo);
    return;
  }

  try {
    const url = 'view/informacion_territorial/ajax_get_localidades.php?tipo_localidad='
              + encodeURIComponent(tipo);

    const r = await fetch(url);
    if (!r.ok) {
      const txt = await r.text();
      console.error('HTTP no OK:', r.status, txt);
      throw new Error('HTTP ' + r.status);
    }

    const data = await r.json();
    if (!data.ok) {
      console.error('Backend ok = false:', data);
      throw new Error(data.error || 'Respuesta inválida');
    }

    const map = {};
    (data.listado || []).forEach(item => {
      const id = parseInt(item.id, 10);
      if (!isNaN(id) && id > 0) map[id] = true;
    });

    tipoLocalidadActivo = tipo;
    tipoLocalidadMap    = map;
    console.log('Aplicado filtro tipo', tipo, 'IDs:', Object.keys(map).length);
    drawMap();

  } catch (e) {
    console.error('Error cargando localidades por tipo:', e);
    alert('No se pudieron cargar los datos para este tipo de localidad.');
  }
}

// Enganche de botones (solo UNA VEZ)
const btnSoloLocalidades = document.getElementById('btnSoloLocalidades');
const btnSoloComisiones  = document.getElementById('btnSoloComisiones');
const btnTodos           = document.getElementById('btnTodos');

if (btnSoloLocalidades) {
  btnSoloLocalidades.addEventListener('click', () => setTipoLocalidad(3));
}
if (btnSoloComisiones) {
  btnSoloComisiones.addEventListener('click', () => setTipoLocalidad(2));
}
if (btnTodos) {
  btnTodos.addEventListener('click', () => setTipoLocalidad(0));
}


// ===== Carga de polígonos =====
fetch('view/informacion_territorial/poligonos_la_pampa_departamentos_final.json')
  .then(r=>r.json())
  .then((dataPolys)=>{
    const nameToId = {};
    if (selectLocalidad){
      [...selectLocalidad.options].forEach(opt=>{
        const id = parseInt(opt.value,10);
        const label = (opt.text || '').trim();
        if (!isNaN(id) && id>0){
          nameToId[ normalizeStr(label) ] = id;
        }
      });
    }

    let minX=Infinity,maxX=-Infinity,minY=Infinity,maxY=-Infinity;
    dataPolys.forEach(p=>{
      p.coords.forEach(([x,y])=>{
        if(x<minX) minX=x; if(x>maxX) maxX=x;
        if(y<minY) minY=y; if(y>maxY) maxY=y;
      });
    });

    const W = canvas.width, H = canvas.height;
    const scale = Math.min( W/(maxX-minX), H/(maxY-minY) ) * 0.97;
    const offX = (W - (maxX-minX)*scale)/2;
    const offY = (H - (maxY-minY)*scale)/2;

    shapes = dataPolys.map(p=>{
      const pts = p.coords.map(([x,y])=>[(x - minX)*scale + offX, (y - minY)*scale + offY]);
      const path = new Path2D();
      pts.forEach(([x,y],i)=>{ if(i===0) path.moveTo(x,y); else path.lineTo(x,y); });
      path.closePath();
      const [cx,cy] = polygonCentroid(pts);
      const probableId = nameToId[ normalizeStr(p.name) ] || null;
      return { name: p.name, id: probableId, coords: pts, path, centroid:[cx,cy] };
    });

    // Eventos
    canvas.addEventListener('mousemove', (e)=>{
      const {x,y} = getCanvasCoords(e);
      let hit = null;
      for (const obj of shapes){ if (ctx.isPointInPath(obj.path,x,y)){ hit = obj; break; } }
      if (hit){
        drawMap(hit);
        let extra = '';
        if (microOn && hit.id && microMap && microMap[hit.id]){
          const reg = microMap[hit.id];
          if (labelMode === 'volumen' && volumeMap && volumeMap[reg] != null){
            extra = ` — Micro ${reg}: ${shortNumber(volumeMap[reg])}${volumeUnidad ? ' '+volumeUnidad : ''}${volumeYear ? ' ('+volumeYear+')' : ''}`;
          } else {
            extra = ` — Microregión ${reg}`;
          }
        }
        tooltip.innerText = hit.name + extra;
        tooltip.style.display = 'block';
        const rect = canvas.getBoundingClientRect();
        const localX = e.clientX - rect.left;
        const localY = e.clientY - rect.top;
        tooltip.style.left = `${localX + 12}px`;
        tooltip.style.top  = `${localY - 12 - tooltip.offsetHeight}px`;
        canvas.style.cursor = 'pointer';
      } else {
        tooltip.style.display = 'none';
        canvas.style.cursor = 'default';
        drawMap();
      }
    });

    canvas.addEventListener('mouseleave', ()=>{ tooltip.style.display='none'; drawMap(); });

    canvas.addEventListener('click', (e)=>{
      const {x,y} = getCanvasCoords(e);
      let hit = null;
      for (const obj of shapes){ if (ctx.isPointInPath(obj.path,x,y)){ hit = obj; break; } }
      if (!hit) return;
      selectedLocalidad = hit.name;
      drawMap();
      if (hit.id && selectLocalidad){
        selectLocalidad.value = String(hit.id);
        irALocalidad(hit.id);
      }
    });

    if (selectLocalidad){
      selectLocalidad.addEventListener('change', ()=>{
        const val = parseInt(selectLocalidad.value,10);
        if (isNaN(val) || val<=0){
          selectedLocalidad = null;
          drawMap();
          if (panel) panel.innerHTML = initialPanelHTML;
          return;
        }
        const shape = shapes.find(s => s.id === val);
        selectedLocalidad = shape ? shape.name : null;
        drawMap();
        irALocalidad(val);
      });
    }

    drawMap();

    // Botón micro-regiones
    if (btnMicro){
      if (leyenda) leyenda.style.display = 'none';
      btnMicro.addEventListener('click', async ()=>{
        const ok = await ensureMicroMap();
        if (!ok) return;
        microOn   = !microOn;
        showLabels= microOn;
        labelMode = 'micro';
        btnMicro.classList.toggle('btn-warning', microOn);
        btnMicro.textContent = microOn ? 'Ocultar micro-regiones' : 'Pintar micro-regiones';
        if (leyenda) leyenda.style.display = microOn ? 'block' : 'none';
        drawMap();
      });
    }
  })
  .catch(err=>{
    console.error('Error inicializando mapa:', err);
    ctx.fillStyle='#fafafa';
    ctx.fillRect(0,0,canvas.width,canvas.height);
    ctx.fillStyle='#999';
    ctx.fillText('No se pudo cargar el mapa', 10, 20);
  });

 function updateTipoButtonsUI() {
  if (!btnSoloLocalidades || !btnSoloComisiones || !btnTodos) return;

  btnSoloLocalidades.classList.remove('btn-success');
  btnSoloComisiones.classList.remove('btn-primary');
  btnTodos.classList.remove('btn-secondary');

  btnSoloLocalidades.classList.add('btn-outline-secondary');
  btnSoloComisiones.classList.add('btn-outline-secondary');
  btnTodos.classList.add('btn-light');

  if (tipoLocalidadActivo === 1) {
    btnSoloLocalidades.classList.remove('btn-outline-secondary');
    btnSoloLocalidades.classList.add('btn-success');
  } else if (tipoLocalidadActivo === 2) {
    btnSoloComisiones.classList.remove('btn-outline-secondary');
    btnSoloComisiones.classList.add('btn-primary');
  } else {
    btnTodos.classList.remove('btn-light');
    btnTodos.classList.add('btn-secondary');
  }
}

function resetRubroSelect(selectRubro, message){
  if (!selectRubro) return;
  const text = message || 'Elegí primero un sistema...';
  selectRubro.innerHTML = `<option value="">${text}</option>`;
  selectRubro.disabled = true;
}

function renderPanelVolumenes(data){
  const panelVolumenes = document.getElementById('panelVolumenes');
  if (!panelVolumenes) return;

  const rows = Array.isArray(data?.rows) ? data.rows : [];
  const unidad = (data?.unidad || '').toString().trim();

  if (!rows.length) {
    panelVolumenes.innerHTML = '<div class="small text-muted">No hay volúmenes para el filtro seleccionado.</div>';
    return;
  }

  const top = rows.slice(0, 8);
  let html = '<div class="small"><strong>Volúmenes por micro-región</strong></div>';
  html += '<ul class="small mb-2 pl-3">';
  top.forEach((row) => {
    const micro = Number(row.microregion) || 0;
    const vol = shortNumber(Number(row.volumen) || 0);
    html += `<li>Micro ${micro}: ${vol}${unidad ? ` ${unidad}` : ''}</li>`;
  });
  html += '</ul>';

  if (rows.length > top.length) {
    html += `<div class="small text-muted">Mostrando ${top.length} de ${rows.length} filas</div>`;
  }
  if (data?.by_micro) {
    const values = Object.values(data.by_micro)
      .map((v) => Number(v))
      .filter((v) => Number.isFinite(v));
    if (values.length) {
      const min = Math.min(...values);
      const max = Math.max(...values);
      html += '<div class="small mt-2"><strong>Escala de color (volumen)</strong></div>';
      html += '<div style="height:10px;border-radius:4px;background:linear-gradient(90deg, '
        + VOLUME_COLOR_LOW + ' 0%, ' + VOLUME_COLOR_MID + ' 50%, ' + VOLUME_COLOR_HIGH + ' 100%);border:1px solid #cfcfcf;"></div>';
      html += `<div class="small text-muted d-flex justify-content-between"><span>Bajo: ${shortNumber(min)}${unidad ? ` ${unidad}` : ''}</span><span>Alto: ${shortNumber(max)}${unidad ? ` ${unidad}` : ''}</span></div>`;
    }
  }

  panelVolumenes.innerHTML = html;
}

async function cargarVolumenesEnMapa(){
  const selectRubro = document.getElementById('selectRubro');
  const selectLocalidadEl = document.getElementById('selectLocalidad');
  const rubroId = Number(selectRubro?.value || 0);
  const localidadId = Number(selectLocalidadEl?.value || 0);

  if (!rubroId) {
    alert('Seleccioná un rubro para ver volúmenes.');
    return;
  }

  const ok = await ensureMicroMap();
  if (!ok) return;

  try {
    const body = new URLSearchParams();
    body.set('rubro_id', String(rubroId));
    body.set('localidad_id', String(localidadId > 0 ? localidadId : 0));

    const response = await fetch('view/informacion_territorial/ajax_get_volumenes.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body: body.toString()
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const data = await response.json();
    if (!data || !data.ok) throw new Error(data?.error || 'Sin datos');

    volumeMap = data.by_micro || {};
    volumeUnidad = data.unidad || '';
    volumeYear = data.anio || null;

    microOn = true;
    showLabels = true;
    labelMode = 'volumen';
    volumenColorMode = 'volumen';

    if (btnMicro) {
      btnMicro.classList.add('btn-warning');
      btnMicro.textContent = 'Ocultar micro-regiones';
    }
    const btnModoColorVolumen = document.getElementById('btnModoColorVolumen');
    if (btnModoColorVolumen) btnModoColorVolumen.disabled = false;
    updateModoColorButtonUI();
    if (leyenda) leyenda.style.display = 'block';

    renderPanelVolumenes(data);
    drawMap();
  } catch (error) {
    console.error('Error cargando volúmenes:', error);
    volumeMap = null;
    volumeUnidad = '';
    volumeYear = null;
    renderPanelVolumenes({ rows: [] });
    drawMap();
    alert('No se pudieron cargar los volúmenes para el rubro seleccionado.');
  }
}

async function cargarRubrosPorSistema(sistemaId){
  const selectRubro = document.getElementById('selectRubro');
  const btnVerVolumenes = document.getElementById('btnVerVolumenes');

  if (btnVerVolumenes) btnVerVolumenes.disabled = true;

  if (!sistemaId) {
    resetRubroSelect(selectRubro, 'Elegí primero un sistema...');
    return;
  }

  resetRubroSelect(selectRubro, 'Cargando rubros...');

  try {
    const body = new URLSearchParams();
    body.set('sistema_id', String(sistemaId));

    const response = await fetch('view/informacion_territorial/ajax_get_rubros.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body: body.toString()
    });

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();
    if (!data || !data.ok) throw new Error(data?.error || 'Respuesta inválida');

    const rubros = Array.isArray(data.rubros) ? data.rubros : [];
    if (!rubros.length) {
      resetRubroSelect(selectRubro, 'No hay rubros para este sistema');
      return;
    }

    let html = '<option value="">Seleccioná un rubro...</option>';
    rubros.forEach((rubro) => {
      const id = Number(rubro.id) || 0;
      const nombre = (rubro.nombre || '').toString().trim();
      if (id > 0 && nombre) {
        html += `<option value="${id}">${nombre}</option>`;
      }
    });

    selectRubro.innerHTML = html;
    selectRubro.disabled = false;
  } catch (error) {
    console.error('Error cargando rubros:', error);
    resetRubroSelect(selectRubro, 'Error al cargar rubros');
  }
}

document.addEventListener('change', function(event){
  const target = event.target;
  if (!target) return;

  if (target.id === 'selectSistema') {
    cargarRubrosPorSistema(target.value);
    const btnModoColorVolumen = document.getElementById('btnModoColorVolumen');
    if (btnModoColorVolumen) btnModoColorVolumen.disabled = true;
    return;
  }

  if (target.id === 'selectRubro') {
    const btnVerVolumenes = document.getElementById('btnVerVolumenes');
    if (btnVerVolumenes) btnVerVolumenes.disabled = !target.value;
    const btnModoColorVolumen = document.getElementById('btnModoColorVolumen');
    if (btnModoColorVolumen) btnModoColorVolumen.disabled = true;
    volumeMap = null;
    volumeUnidad = '';
    volumeYear = null;
    volumenColorMode = 'volumen';
    updateModoColorButtonUI();
    if (labelMode === 'volumen') labelMode = 'micro';
    const panelVolumenes = document.getElementById('panelVolumenes');
    if (panelVolumenes) panelVolumenes.innerHTML = '';
    drawMap();
  }
});

document.addEventListener('click', function(event){
  const target = event.target;
  if (!target) return;

  if (target.id === 'btnVerVolumenes') {
    cargarVolumenesEnMapa();
    return;
  }

  if (target.id === 'btnModoColorVolumen') {
    if (!volumeMap || labelMode !== 'volumen') return;
    volumenColorMode = (volumenColorMode === 'volumen') ? 'micro' : 'volumen';
    updateModoColorButtonUI();
    drawMap();
  }
});

</script>
