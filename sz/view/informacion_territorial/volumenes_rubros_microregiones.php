<?php

if (!isset($_SESSION)) { session_start(); }

$permisos_editar = isset($permisos_editar)?$permisos_editar:1;
$permisos_ver = isset($permisos_ver)?$permisos_ver:1;
?>
<!DOCTYPE html>
<html>
<head><?php include('head.php');?></head>
<body class="hold-transition skin-blue sidebar-mini">
<?php if ($permisos_editar==1){ include('modal/agregar_volumen_rubro_microregion.php'); }?>
<div class="wrapper">
<header class="main-header"><?php include('main-header.php');?></header>
<aside class="main-sidebar"><?php include('main-sidebar.php');?></aside>
<div class="content-wrapper">
<?php if ($permisos_ver==1){ ?>
<section class="content-header">
<div class="row">
<div class="col-xs-2"><input type="number" class="form-control" id="q_rubro" placeholder="Rubro" onkeyup="load(1);"></div>
<div class="col-xs-2"><input type="number" class="form-control" id="q_micro" placeholder="Microregión" onkeyup="load(1);"></div>
<div class="col-xs-2"><input type="text" class="form-control" id="q_loc" placeholder="Localidad" onkeyup="load(1);"></div>
<div class="col-xs-1"><div id="loader" class="text-center"></div></div>
<div class="col-xs-5">
<div class="btn-group pull-right">
<?php if ($permisos_editar==1){?><button class="btn btn-default" data-toggle="modal" data-target="#modal_register_vol"><i class="fa fa-plus"></i> Nuevo</button><?php }?>
</div>
</div>
<input type='hidden' id='per_page' value='15'>
</div>
</section>
<section class="content">
<div id="resultados_ajax"></div>
<div class="outer_div"></div>
<div class="outer_div2"></div>
</section>
<?php } else { ?><section class="content"><div class="alert alert-danger">Sin permisos</div></section><?php } ?>
</div>
<?php include('footer.php');?>
</div>
<?php include('js.php');?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.js"></script>
<script>
$(function(){ load(1); });
function load(page){
var p=$('#per_page').val(); var r=$('#q_rubro').val(); var m=$('#q_micro').val(); var l=$('#q_loc').val();
$('#loader').fadeIn('slow');
$.get('ajax/volumenes_ajax.php',{action:'ajax',page:page,per_page:p,q_rubro:r,q_micro:m,q_loc:l}, function(d){ $('.outer_div').html(d); $('#loader').html(''); });
}
function eliminar(id){ if(confirm('Eliminar registro?')){ $.get('ajax/volumenes_ajax.php',{action:'ajax',id:id}, function(d){ $('.outer_div').html(d); }); } }
$('#new_register_vol').validator().on('submit',function(e){ if(!e.isDefaultPrevented()){ e.preventDefault(); $.post('ajax/registro/agregar_volumen_rubro_microregion.php', $(this).serialize(), function(d){ $('#resultados_ajax').html(d); load(1); $('#modal_register_vol').modal('hide'); }); }});
function editar_vol(id){ $.get('modal/editar/volumen_rubro_microregion.php',{id:id}, function(html){ $('.outer_div2').html(html); $('#modal_update_vol').modal('show'); bind_update(); }); }
function bind_update(){ $('#update_register_vol').validator().on('submit',function(e){ if(!e.isDefaultPrevented()){ e.preventDefault(); $.post('ajax/modificar/volumen_rubro_microregion.php', $(this).serialize(), function(d){ $('#resultados_ajax').html(d); load(1); $('#modal_update_vol').modal('hide'); }); }}); }
</script>
</body>
</html>