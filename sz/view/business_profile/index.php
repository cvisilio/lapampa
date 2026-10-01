<?php

/*Datos de la empresa*/
    $id=1;
	$sql1=mysqli_query($con,"SELECT * FROM business_profile where id=$id");
	$rw1=mysqli_fetch_array($sql1);
	$name=$rw1["name"];
	$number_id=$rw1['number_id'];
	$email=$rw1['email'];
	$phone=$rw1['phone'];
	$tax=$rw1['tax'];
	$currency_id=$rw1['currency_id'];
	$timezone_id=$rw1['timezone_id'];
	$address=$rw1['address'];
	$city=$rw1['city'];
	$postal_code=$rw1['postal_code'];
	$state=$rw1['state'];
	$country_id=$rw1['country_id'];
	$logo_url=$rw1['logo_url'];
	$code_disqus=$rw1['code_disqus'];
	$cuit=$rw1['cuit'];
	$web=$rw1['web'];
	$tipo_responsable=$rw1['tipo_responsable'];
	$mostrar_precios_sitio=$rw1['mostrar_precios_sitio'];
	
	//constantes para asientos del sistema
    $id_cta_caja=$rw1['id_cuenta_caja'];
	$id_cta_debito_fiscal=$rw1['id_cuenta_debito_fiscal']; 
	$id_cta_venta_productos=$rw1['id_cuenta_venta_productos'];
	$id_cta_deudores_ventas=$rw1['id_cuenta_deudores_ventas'];
	$id_cta_banco_tarjeta=$rw1['id_cuenta_banco_tarjeta_vta'];
	$id_cta_valores_depositar=$rw1['id_cuenta_valores_depositar'];
		
	$id_cta_ret_ing_brutos=$rw1['id_cuenta_ret_ing_brutos'];
	$id_cta_credito_fiscal=$rw1['id_cuenta_credito_fiscal'];
	$id_cta_mercaderias=$rw1['id_cuenta_mercaderias'];
	$id_cta_proveedores=$rw1['id_cuenta_proveedores'];
	$modo_bar= $rw1['modo_bar'];
	
	$tabla="business_profile";
	$campo_imagen="logo_url";
	$carpeta_imagenes="logo";
   	
	/*Fin datos empresa*/
?>
<!DOCTYPE html>
<html>
  <head>
	<?php include("head.php");?>
  </head>
  <body class="hold-transition <?php echo $skin;?> sidebar-mini">
    <div class="wrapper">
      <header class="main-header">
		<?php include("main-header.php");?>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
		<?php include("main-sidebar.php");?>
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
		<?php if ($permisos_editar==1){?>
        <section class="content-header">
		  <h1>Perfil de la empresa</h1>
		
		</section>
		<!-- Main content -->
        <section class="content">
		<div class="row">
		
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			<div id="load_img">
              <img class="img-responsive" src="<?php echo $logo_url;?>" alt="Bussines profile picture">
			  </div>

              <h3 class="profile-username text-center"><?php echo $name;?></h3>

              <p class="text-muted text-center mail-text"><?php echo $email;?></p>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          
        </div>
        <!-- /.col -->
        <div class="col-md-9">
		<form class="form-horizontal" method="post" enctype="multipart/form-data" name="profi">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#details" data-toggle="tab" aria-expanded="false">Detalles</a></li>
              <li class=""><a href="#address" data-toggle="tab" aria-expanded="false">Direcci&oacute;n</a></li>
              
              <li class=""><a href="#sistema" data-toggle="tab" aria-expanded="false">Cuentas del Sistema</a></li>
              
            </ul>
            <div class="tab-content">
              <div id="resultados_ajax"></div>
           
              <div class="tab-pane active" id="details">
                
                  <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Nombre</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Nombre de la empresa" value="<?php echo $name;?>">
                    </div>
                  </div>
				  <div class="form-group">
                    <label for="number_id" class="col-sm-3 control-label">Número de registro </label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="number_id" name="number_id" placeholder="Número de registro" value="<?php echo $number_id;?>">
                    </div>
                  </div>
                  
                   <div class="form-group">
                    <label for="cuit" class="col-sm-3 control-label">CUIT </label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="cuit" name="cuit" placeholder="Identificación Tributaria" value="<?php echo $cuit;?>">
                    </div>
                  </div>
                  
                  <div class="form-group">
                      <label for="tipo_responsable" class="col-sm-3 control-label">Tipo Responsable</label>
                      <div class="col-sm-9">
                      <select class="form-control" name="tipo_responsable" id="tipo_responsable" required>
						<option value="">Selecciona</option>
						<?php 
							$sql=mysqli_query($con,"select * from tipos_responsables_impuesto order by id");
							while ($rw=mysqli_fetch_array($sql)){
								$id=$rw['codigo'];
								$name=$rw['abreviatura'];
							
							if ($tipo_responsable==$id){$selected1="selected";}else{$selected1="";}
							?>
							<option value="<?php echo $id;?>" <?php echo
							 $selected1;?>><?php echo $name;?></option>
							<?php
							}
						?>
					  </select>
                    </div>      
                   </div> 
                  
                        
                  
                  
                  <div class="form-group">
                    <label for="phone" class="col-sm-3 control-label">Teléfono</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="phone" name="phone" placeholder="Teléfono" value="<?php echo $phone;?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="tax" class="col-sm-3 control-label">IVA %</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="tax" name="tax" placeholder="Impuesto" value="<?php echo $tax;?>" maxlength=2>
                    </div>
                  </div>
                  
                
				  <div class="form-group">
                    <label for="currency" class="col-sm-3 control-label">Moneda</label>

                    <div class="col-sm-9">
					<?php 
					$query_currencies=mysqli_query($con,"select * from currencies");
					?>
						<select class='form-control select2' name="currency" id="currency">
						<?php
							while ($rw_currencies=mysqli_fetch_array($query_currencies)){
								?>
								<option value="<?php echo $rw_currencies['id'];?>" <?php if ($rw_currencies['id']==$currency_id){echo "selected";}else {echo "";}?>><?php echo $rw_currencies['name'];?></option>
								<?php 
							}
						?>
							
						</select>
                    </div>
                  </div>
				  <div class="form-group">
                    <label for="timezone" class="col-sm-3 control-label">Zona horaria</label>

                    <div class="col-sm-9">
						<?php 
					$query_timezones=mysqli_query($con,"select * from timezones order by name");
					?>
						<select class='form-control select2' name="timezone" id="timezone">
						<?php
							while ($rw_timezones=mysqli_fetch_array($query_timezones)){
								?>
								<option value="<?php echo $rw_timezones['id'];?>" <?php if ($timezone_id==$rw_timezones['id']){echo "selected";}else {echo "";}?>><?php echo $rw_timezones['name'];?></option>
								<?php 
							}
						?>
							
						</select>
                    </div>
                  </div>
                  
                   <div class="form-group">
                      <label for="mostrar_precios_sitio" class="col-sm-3 control-label">-------</label>
                      <div class="col-sm-9">
                      <select class="form-control" name="mostrar_precios_sitio" id="mostrar_precios_sitio" required>
						<option value="1" <?php if ($mostrar_precios_sitio==1){echo "selected";}else {echo "";}?>>Siempre</option>
						<option value="2" <?php if ($mostrar_precios_sitio==2){echo "selected";}else {echo "";}?>>Solo Registrados</option>
                        <option value="3" <?php if ($mostrar_precios_sitio==3){echo "selected";}else {echo "";}?>>Nunca</option>
					  </select>
                    </div>      
                   </div>
                   
                 <div class="form-group">
                      <label for="modo_bar" class="col-sm-3 control-label">----</label>
                      <div class="col-sm-9">
                      <select class="form-control" name="modo_bar" id="modo_bar" required>
						<option value="2" <?php if ($modo_bar==2){echo "selected";}else {echo "";}?>>No</option>
						<option value="1" <?php if ($modo_bar==1){echo "selected";}else {echo "";}?>>Si</option>
                       
					  </select>
                    </div>      
                   </div>           
                  
                  <div class="form-group">
                    <label for="image" class="col-sm-3 control-label">Logo</label>

                    <div class="col-sm-9">
                      <input type="file" name="imagefile" id="imagefile" onChange="upload_image(<?php echo $id ?>);">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="button" class="btn btn-primary" onClick="return updateProfile();">Guardar datos</button>
                    </div>
                  </div>
                
              </div>
              <!-- /.tab-pane -->
			   <div class="tab-pane" id="address">
               
                <div class="form-group">
                    <label for="web" class="col-sm-3 control-label">Direcci&oacute;n WEB</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="web" name="web" placeholder="Dirección Web" value="<?php echo $web;?>">
                    </div>
                  </div>
                  
                  
                  <div class="form-group">
                    <label for="email" class="col-sm-3 control-label">Correo electrónico</label>
                    <div class="col-sm-9">
                      <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" value="<?php echo $email;?>">
                    </div>
                  </div>
               
               
                  <div class="form-group">
                    <label for="address1" class="col-sm-3 control-label">Calle</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="address1" name="address1" placeholder="Calle" value="<?php echo $address;?>">
                    </div>
                  </div>
				  <div class="form-group">
                    <label for="city" class="col-sm-3 control-label">Ciudad</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="city" name="city" placeholder="Ciudad" value="<?php echo $city;?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="state" class="col-sm-3 control-label">Región/Provincia</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="state" name="state" placeholder="Región/Provincia" value="<?php echo $state;?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="postal_code" class="col-sm-3 control-label">Código Postal</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Código Postal" value="<?php echo $postal_code;?>">
                    </div>
                  </div>
                  <div class="form-group">
				  
                    <label for="country_id" class="col-sm-3 control-label">País</label>

                    <div class="col-sm-9">
						<?php 
						$query_countries=mysqli_query($con,"select * from countries order by name");
						?>
						
						<select class="form-control select2" name="country_id" id="country_id" style='width:100%'>
							<?php
								while ($rw_countries=mysqli_fetch_array($query_countries)){
									?>
									<option value="<?php echo $rw_countries['id'];?>" <?php if ($country_id==$rw_countries['id']){echo "selected";}else {echo "";}?>><?php echo utf8_encode($rw_countries['name']);?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
				  
				  <div class="form-group">
				<label for="code_disqus" class="col-sm-3  control-label">Código Disqus</label>
				<div class="col-sm-9">
				  <textarea class="form-control" placeholder="Pega el código universal de disqus" rows="5" id="code_disqus" name="code_disqus"><?php echo $code_disqus;?></textarea>
				</div>
			  </div>
                  
                  
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="button" class="btn btn-primary" name="update" onClick="return updateProfile();">Guardar datos</button>
                    </div>
                  </div>
                
              </div>
              <!-- /.tab-pane -->
              
             <!--sistema --> 
             
             <!-- /.tab-pane -->
			   <div class="tab-pane" id="sistema">
               
               <?php 
						$sql1=mysqli_query($con,"select * from plandecuentas where imputable=1 order by denominacion");
						?>
                        
                  <div class="form-group">
                    <label for="id_cta_caja" class="col-sm-3 control-label">Caja</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_caja" id="id_cta_caja" style='width:100%'>
							<?php
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_caja==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
                  
                   <div class="form-group">
                    <label for="id_cta_debito_fiscal" class="col-sm-3 control-label">D&eacute;bito Fiscal</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_debito_fiscal" id="id_cta_debito_fiscal" style='width:100%'>
							<?php
							  mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_debito_fiscal==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
				
                     <div class="form-group">
                    <label for="id_cta_venta_productos" class="col-sm-3 control-label">Venta Productos</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_venta_productos" id="id_cta_venta_productos" style='width:100%'>
							<?php
							  mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_venta_productos==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
             
                  <div class="form-group">
                    <label for="id_cta_deudores_ventas" class="col-sm-3 control-label">Deudores Ventas</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_deudores_ventas" id="id_cta_deudores_ventas" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_deudores_ventas==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
             
               <div class="form-group">
                    <label for="id_cta_banco_tarjeta" class="col-sm-3 control-label">Banco Venta Tarjeta</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_banco_tarjeta" id="id_cta_banco_tarjeta" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_banco_tarjeta==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
        
             <div class="form-group">
                    <label for="id_cta_valores_depositar" class="col-sm-3 control-label">Valores a Depositar</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_valores_depositar" id="id_cta_valores_depositar" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_valores_depositar==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>                                         
            
                 <div class="form-group">
                    <label for="id_cta_ret_ing_brutos" class="col-sm-3 control-label">Ret. Ing. Brutos</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_ret_ing_brutos" id="id_cta_ret_ing_brutos" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_ret_ing_brutos==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
                  
                    <div class="form-group">
                    <label for="id_cta_credito_fiscal" class="col-sm-3 control-label">Cr&eacute;dito Fiscal</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_credito_fiscal" id="id_cta_credito_fiscal" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_credito_fiscal==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
                  
                    <div class="form-group">
                    <label for="id_cta_mercaderias" class="col-sm-3 control-label">Mercader&iacute;as</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_mercaderias" id="id_cta_mercaderias" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_mercaderias==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>
            
                 <div class="form-group">
                    <label for="id_cta_proveedores" class="col-sm-3 control-label">Proveedores</label>
                      <div class="col-sm-9">
				     	<select class="form-control select2" name="id_cta_proveedores" id="id_cta_proveedores" style='width:100%'>
							<?php
								mysqli_data_seek($sql1,0);
								while ($rw1=mysqli_fetch_array($sql1)){
									?>
									<option value="<?php echo $rw1['id'];?>" <?php if ($id_cta_proveedores==$rw1['id']){echo "selected";}else {echo "";}?>><?php echo $rw1['denominacion'];?></option>
									<?php 
								}
							?>
						</select>
                    </div>
                  </div>            
                  
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <button type="button" class="btn btn-primary" name="update" onClick="return updateProfile();">Guardar datos</button>
                    </div>
                  </div>
                
              </div>
              <!-- /.tab-pane -->
             
             <!-- sistema -->             
              
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
		  </form>
        </div>
        <!-- /.col -->
      </div>
     
        </section><!-- /.content -->
		<?php 
		} else{
		?>	
		<section class="content">
			<div class="alert alert-danger">
				<h3>Acceso denegado! </h3>
				<p>No cuentas con los permisos necesario para acceder a este módulo.</p>
			</div>
		</section>		
		<?php
		}
		?>
      </div><!-- /.content-wrapper -->
      <?php include("footer.php");?>
    </div><!-- ./wrapper -->
	<?php include("js.php");?>
	 <script src="plugins/select2/select2.full.min.js"></script>
	<script>
		$(function () {
        //Initialize Select2 Elements
		$(".select2").select2();
		
	});
		function updateProfile(){
			var business_name=$("#business_name").val();
			var number_id=$("#number_id").val();
			var cuit=$("#cuit").val();
			var web=$("#web").val();
			var email=$("#email").val();
			var phone=$("#phone").val();
			var tax=$("#tax").val();
			var currency=$("#currency").val();
			var timezone=$("#timezone").val();
			var address1=$("#address1").val();
			var code_disqus=$("#code_disqus").val();
			var city=$("#city").val();
			var state=$("#state").val();
			var postal_code=$("#postal_code").val();
			var country_id=$("#country_id").val();
			var tipo_responsable=$("#tipo_responsable").val();
			var mostrar_precios_sitio=$("#mostrar_precios_sitio").val();
			var modo_bar=$("#modo_bar").val();
			
			var id_cta_caja=$("#id_cta_caja").val();
			var id_cta_debito_fiscal=$("#id_cta_debito_fiscal").val();
			var id_cta_venta_productos=$("#id_cta_venta_productos").val();
			var id_cta_deudores_ventas=$("#id_cta_deudores_ventas").val();
			var id_cta_banco_tarjeta=$("#id_cta_banco_tarjeta").val();
			var id_cta_valores_depositar=$("#id_cta_valores_depositar").val();
			var id_cta_ret_ing_brutos=$("#id_cta_ret_ing_brutos").val();
			var id_cta_credito_fiscal=$("#id_cta_credito_fiscal").val();
			var id_cta_mercaderias=$("#id_cta_mercaderias").val();
			var id_cta_proveedores=$("#id_cta_proveedores").val();
			
			
			
			var parametros = {"business_name":business_name,"number_id":number_id,"cuit":cuit,"web":web,"email":email,"phone":phone,"tax":tax,
			"currency":currency, "timezone":timezone,"address1":address1,"city":city,"state":state,"postal_code":postal_code,"country_id":country_id,"code_disqus":code_disqus,"tipo_responsable":tipo_responsable,"mostrar_precios_sitio":mostrar_precios_sitio,"id_cta_caja":id_cta_caja,"id_cta_debito_fiscal":id_cta_debito_fiscal,"id_cta_venta_productos":id_cta_venta_productos,"id_cta_deudores_ventas":id_cta_deudores_ventas,"id_cta_banco_tarjeta":id_cta_banco_tarjeta,"id_cta_valores_depositar":id_cta_valores_depositar,"id_cta_ret_ing_brutos":id_cta_ret_ing_brutos,"id_cta_credito_fiscal":id_cta_credito_fiscal,"id_cta_mercaderias":id_cta_mercaderias,"id_cta_proveedores":id_cta_proveedores,"modo_bar":modo_bar};
			 $.ajax({
				type: "POST",
				url: "./ajax/modificar/perfil.php",
				data: parametros,
				 beforeSend: function(objeto){
					$("#resultados_ajax").html("Mensaje: Cargando...");
				  },
				success: function(datos){
				$("#resultados_ajax").html(datos);
				$(".profile-username").html(business_name);
				$(".mail-text").html(email);
				
			  }
			});
			
			
		}
	</script>
	<script>
		function upload_image(id){
			    var tabla = "business_profile";
				var campo = "logo_url";
				var carpeta = "logo";
				
				var inputFileImage = document.getElementById("imagefile");
				var file = inputFileImage.files[0];
				if( (typeof file === "object") && (file !== null) )
				{
					$("#load_img").text('Cargando...');	
					var data = new FormData();
					data.append('imagefile',file);
					data.append('id',id);
					data.append('tabla',tabla);
					data.append('campo',campo);
					data.append('carpeta',carpeta);
															
					$.ajax({
						url: "ajax/imagen_ajax.php",        // Url to which the request is send
						type: "POST",             // Type of request to be send, called as method
						data: data, 			  // Data sent to server, a set of key/value pairs (i.e. form fields and values)
						contentType: false,       // The content type used when sending data to the server.
						cache: false,             // To unable request pages to be cached
						processData:false,        // To send DOMDocument or non processed data file it is set to false
						success: function(data)   // A function to be called if request succeeds
						{
							$("#load_img").html(data);
							
						}
					});	
				}
				
				
			}
    </script>
  </body>
</html>
