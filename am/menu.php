<?php 
if(!isset($_SESSION['nivel']))
session_start();
$nivel_usuario=$_SESSION['nivel'];
// Enlaces del menú: desde importar_pdf/, importar_datos/ o formularios/ hay que subir un nivel (evita login.php?logout en subcarpeta)
$sn = $_SERVER['SCRIPT_NAME'] ?? '';
$menu_href_prefix = '';
if (preg_match('#/(importar_pdf|importar_datos|formularios)/#', $sn)) {
    $menu_href_prefix = '../';
}
?>

<style>
.topnav {
  overflow: hidden;
  background-color:#435d7d;
}

.topnav a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 14px;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #04AA6D;
  color: white;
}

.topnav .icon {
  display: none;
}

@media screen and (max-width: 600px) {
  .topnav a:not(:first-child) {display: none;}
  .topnav a.icon {
    float: right;
    display: block;
  }
}

@media screen and (max-width: 600px) {
  .topnav.responsive {position: relative;}
  .topnav.responsive .icon {
    position: absolute;
    right: 0;
    top: 0;
  }
  .topnav.responsive a {
    float: none;
    display: block;
    text-align: left;
  }
}
</style>
</head>
<body>

<div class="topnav" id="myTopnav">
 <!-- <a href="index.php" class="<?php // if (isset($home) and $home==1){echo "active";}?>">Inicio</a> -->
  <a href="<?php echo $menu_href_prefix; ?>localidades.php" class="<?php if (isset($localidades_menu) and $localidades_menu==1){echo "active";}?>">Transferir</a>
  <a href="<?php echo $menu_href_prefix; ?>compromisos.php" class="<?php if (isset($compromisos_menu) and $compromisos_menu==1){echo "active";}?>">Compromisos</a>
  <a href="<?php echo $menu_href_prefix; ?>transferencias.php" class="<?php if (isset($transferencias_menu) and $transferencias_menu==1){echo "active";}?>">Transferencias</a>
  <a href="<?php echo $menu_href_prefix; ?>motivos.php" class="<?php if (isset($motivos_menu) and $motivos_menu==1){echo "active";}?>">Motivos</a>
  
  <a href="<?php echo $menu_href_prefix; ?>disponibilidades.php" class="<?php if (isset($disponibilidades_menu) and $disponibilidades_menu==1){echo "active";}?>">Disponibilidades</a>
  
  <a href="<?php echo $menu_href_prefix; ?>indices.php" class="<?php if (isset($indices_menu) and $indices_menu==1){echo "active";}?>">&Iacute;ndices</a>
  
  <?php if($nivel_usuario==4){ ?>
  <a href="<?php echo $menu_href_prefix; ?>importar.php" class="<?php if (isset($importar_menu) and $importar_menu==1){echo "active";}?>">Importar</a>
  <a href="<?php echo $menu_href_prefix; ?>importar_pdf.php" class="<?php if (isset($importar_menu_pdf) and $importar_menu_pdf==1){echo "active";}?>">Importar PDF</a>
  <?php } ?>
  
  
  <a href="<?php echo $menu_href_prefix; ?>login.php?logout">Salir</a>
  <a href="javascript:void(0);" class="icon" onClick="myFunction()">
    <i class="fa fa-bars"></i>
  </a>
</div>

<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>