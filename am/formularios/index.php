<?php 
 session_start();
 if (isset($_SESSION['id_localidad']))
  { 
  
  $id_localidad = $_SESSION['id_localidad'];
  $localidad =  $_SESSION['localidad'];
 
  require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
  require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos
	// escaping, additionally removing everything that could be (html/javascript-) code
	
  $mes1=date("m");
  switch ($mes1) {
   case 1:
    $nombre_mes1="Enero";
	$nombre_mes2="Febrero";
    break;
   case 2:
    $nombre_mes1="Febrero";
	$nombre_mes2="Marzo";
    break;
   case 3:
    $nombre_mes1="Marzo";
	$nombre_mes2="Abril";
    break;
   case 4:
    $nombre_mes1="Abril";
	$nombre_mes2="Mayo";
    break;
   case 5:
    $nombre_mes1="Mayo";
	$nombre_mes2="Junio";
    break;
   case 6:
     $nombre_mes1="Junio";
	 $nombre_mes2="Julio";
    break;	
   case 7:
     $nombre_mes1="Julio";
	 $nombre_mes2="Agosto";
    break;	
   case 8:
     $nombre_mes1="Agosto";
	 $nombre_mes2="Septiembre";
    break;	
   case 9:
     $nombre_mes1="Septiembre";
	 $nombre_mes2="Octubre";
    break;
   case 10:
     $nombre_mes1="Octubre";
	 $nombre_mes2="Nomviembre";
    break;
   case 11:
     $nombre_mes1="Nomviembre";
	 $nombre_mes2="Diciembre";
    break;
   case 12:
     $nombre_mes1="Diciembre";
	 $nombre_mes2="Enero 2021";
     break;														
   }
 	
    
    $sql1=mysqli_query($con,"SELECT * FROM proyecciones_localidades where id_localidad='$id_localidad' and mes='$mes1'");
	
	$rw1=mysqli_fetch_array($sql1);
	 
	 $cantidad_funcionarios= $rw1["cantidad_funcionarios"];
	 $neto_funcionarios1= $rw1["neto_funcionarios1"];
	 $neto_funcionarios2= $rw1["neto_funcionarios2"];
	 $iss_funcionarios1= $rw1["iss_funcionarios1"];
	 $iss_funcionarios2= $rw1["iss_funcionarios2"];
	 
	 $cantidad_empleados= $rw1["cantidad_empleados"];
	 $neto_empleados1= $rw1["neto_empleados1"];
	 $neto_empleados2= $rw1["neto_empleados2"];
	 $iss_empleados1= $rw1["iss_empleados1"];
	 $iss_empleados2= $rw1["iss_empleados2"];
	 
	 $cantidad_contratados= $rw1["cantidad_contratados"];
	 $neto_contratados1= $rw1["neto_contratados1"]; 
	 $neto_contratados2= $rw1["neto_contratados2"];
	 
	 $gastos_operativos1= $rw1["gastos_operativos1"];
	 $gastos_operativos2= $rw1["gastos_operativos2"];
	 
	 $saldo_disponible1= $rw1["saldo_disponible1"];
	 $saldo_disponible2= $rw1["saldo_disponible2"];
	 
	 $recursos_propios1= $rw1["recursos_propios1"];
	 $recursos_propios2= $rw1["recursos_propios2"]; 
	 
	 $observaciones= $rw1["observaciones"];
	

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1"/>
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	
	<title>Formulario Carga - Flujo de Caja</title>
	<!-- set your website meta description and keywords -->
	<meta name="robots" content="noindex">
    <meta name="googlebot" content="noindex">
	<!-- set your website favicon -->
	<link href="favicon.html" rel="icon">	
	
	<!-- Bootstrap Stylesheets -->
	<link rel="stylesheet" href="formularios/css/bootstrap.min.css">
	<!-- Font Awesome Stylesheets -->
	<link rel="stylesheet" href="formularios/css/font-awesome.min.css">
<!-- Template Main Stylesheets -->
	<link rel="stylesheet" href="formularios/css/contact-form.css" type="text/css">	
	
</head>

<body>
	
	<section id="contact-form-section" class="form-content-wrap">
		<div class="container">
			<div class="row">
				<div class="tab-content">
					<div class="col-sm-12">
						<div class="item-wrap">
							<div class="row">
								<div class="col-md-12">
							
                                 
									<div class="item-content colBottomMargin">
										<div class="item-info">
											<h2 class="item-title text-center"><?php echo $localidad; ?></h2>
                                            
											
										</div><!--End item-info -->
										
								   </div><!--End item-content -->
								</div><!--End col -->
                         
                                
								<div class="col-md-12">
								
                                <!-- 	<form id="contactForm" name="contactform" data-toggle="validator" class="popup-form"> -->
								
								<form id="contactForm" name="contactform" data-toggle="validator" class="popup-form">
                                
												<div class="row">
                                            
											
                               <div class="col-sm-2" align="center"><strong> Cant. </strong></div><div class="col-sm-4" align="center"><strong> Erogaciones</strong> </div>   <div class="col-sm-3" align="center"><strong> <?php echo $nombre_mes1; ?> </strong></div>  <div class="col-sm-3" align="center"><strong>  <?php echo $nombre_mes2; ?></strong> </div>            		          		                        </div>
                               <br />   
                               <div class="row">
												
                                                <!-- 1ra fila  carga-->
                                                	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
														<input name="cantidad_funcionarios" id="cantidad_funcionarios" placeholder=""  pattern="[0-9]+" class="form-control" type="number" value="<?php echo $cantidad_funcionarios; ?>" required data-error=" Ingresa Valor"> 
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label">Funcionarios - Neto </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
							<!-- pattern="[0-9]+([.\,][0-9]+)?" -->							<input name="neto_funcionarios1" id="neto_funcionarios1"  placeholder="" pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $neto_funcionarios1;?>"  required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_funcionarios2" id="neto_funcionarios2" placeholder="" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $neto_funcionarios2; ?>" pattern="\d+(\.\d{1,2})?" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
                                                    
                                                  <!-- Fin 1ra fila carga -->
                                                  
                                                    
                                 <!-- 2da fila  carga-->
                                                	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
													<label for="cuit" class="col-sm-2 control-label"> </label>
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label">Importe a ISS (funcionarios) </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_iss_funcionarios1" id="neto_iss_funcionarios1" placeholder="" pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $iss_funcionarios1;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_iss_funcionarios2" id="neto_iss_funcionarios2" placeholder="" pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $iss_funcionarios2;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
                                                    
                                                  <!-- Fin 2da fila carga -->                      
                                                    
                      
                                     <!-- 3ra fila  carga-->
                                                	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
														<input name="cantidad_empleados" id="cantidad_empleados" placeholder=""  class="form-control" type="number" value="<?php echo $cantidad_empleados; ?>" required data-error=" Ingresa Valor"> 
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label">Empleados - Neto </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_empleados1" id="neto_empleados1" placeholder="" pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $neto_empleados1;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_empleados2" id="neto_empleados2" placeholder="" pattern="\d+(\.\d{1,2})?"  class="form-control" type="text" onkeyup="test(this)" value="<?php echo $neto_empleados2 ;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
                                                    
                                                  <!-- Fin 3ra fila carga -->
                                                  
                                                    
                                 <!-- 4ta fila  carga-->
                                 
                                 
                                                	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
													<label for="cuit" class="col-sm-2 control-label"> </label>
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label">Importe a ISS (Empleados) </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_iss_empleados1" id="neto_iss_empleados1" placeholder=""  pattern="\d+(\.\d{1,2})?"  class="form-control" type="text" onkeyup="test(this)" value="<?php echo $iss_empleados1;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_iss_empleados2" id="neto_iss_empleados2" placeholder=""  pattern="\d+(\.\d{1,2})?"  class="form-control" type="text" onkeyup="test(this)" value="<?php echo $iss_empleados2;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
                                                    
                                                  <!-- Fin 4ta fila carga -->    
                                           
                     
                       <!-- 5ta fila  carga-->
                                                	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
														<input name="cantidad_contratados" id="cantidad_contratados" placeholder="" class="form-control" type="number" value="<?php echo $cantidad_contratados; ?>" required data-error=" Ingresa Valor"> 
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label">Contratados - Neto </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_contratados1" id="neto_contratados1" placeholder=""  pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $neto_contratados1; ?>"  required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="neto_contratados2" id="neto_contratados2" placeholder=""  pattern="\d+(\.\d{1,2})?"  class="form-control" type="text" onkeyup="test(this)" value="<?php echo $neto_contratados2;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
                                                    
                                                  <!-- Fin 5ta fila carga -->
                                                  
                                                    
                                 <!-- 6ta fila  carga-->
                                 
                                 
                                                	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
													<label for="cuit" class="col-sm-2 control-label"> </label>
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label">Gastos Operativos Estimados </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="gastos_operativos1" id="gastos_operativos1" placeholder=""  pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $gastos_operativos1;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="gastos_operativos2" id="gastos_operativos2" placeholder=""  pattern="\d+(\.\d{1,2})?"  class="form-control" type="text" onkeyup="test(this)" value="<?php echo $gastos_operativos2;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
                                                    
                                                  <!-- Fin 6ta fila carga -->
                                       
                                        
                                        <!-- Recursos -->
                                 
                                 
                                                	<div class="form-group col-sm-2">
																										<label for="recursos" class="col-sm-2 control-label"> </label>
													
													</div><!-- end form-group --> 
                                                    
                                               <div class="form-group col-sm-4" align="center">
																										<label for="recursos2 class="col-sm-4" control-label"><strong> Recursos </strong> </label>
														
													</div><!-- end form-group -->    
                                                    
                                             <div class="form-group col-sm-3">
																										<label for="recursos3 class="col-sm-3" control-label"> </label>
													
													</div><!-- end form-group -->  
                                               
                                                <div class="form-group col-sm-3">
																										<label for="recursos4 class="col-sm-3" control-label"> </label>
														
													</div><!-- end form-group -->                           
                         <!-- fin fila Recursos -->
                         
                           <!-- Saldo Disponible fila  carga-->
                             	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
													<label for="cuit" class="col-sm-2 control-label"> </label>
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label"><a href="#ancla1">*</a> Saldo disponible en Banco + Plazos fijos a vencer + Caja </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="saldo_disponible1" id="saldo_disponible1" placeholder=""  pattern="\d+(\.\d{1,2})?"  class="form-control" type="text" onkeyup="test(this)" value="<?php echo $saldo_disponible1;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="saldo_disponible2" id="saldo_disponible2" placeholder=""  pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $saldo_disponible2;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->             
                           <!-- Fin Saldo Disponible fila  carga --> 
                        
                   
                        <!-- Recuros Propios por Cobrar fila  carga-->
                             	<div class="form-group col-sm-2">
														<div class="help-block with-errors"></div>
                                                        
													<label for="cuit" class="col-sm-2 control-label"> </label>
														<!--<div class="input-group-icon"><i class="fa fa-user"></i></div> -->
													</div><!-- end form-group -->
												
													  <label for="cuit" class="col-sm-4 control-label"><a href="#ancla1">**</a> Recuros Propios por Cobrar </label>
                                                  													
                                                    <div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="recursos_propios1" id="recursos_propios1" placeholder=""  pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" value="<?php echo $recursos_propios1;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->
													<div class="form-group col-sm-3">
														<div class="help-block with-errors"></div>
														<input name="recursos_propios2" id="recursos_propios2" placeholder=""  pattern="\d+(\.\d{1,2})?" class="form-control" type="text" onkeyup="test(this)" onkeyup="test(this)" value="<?php echo $recursos_propios2;?>" required data-error=" Ingresa Valor">
														<div class="input-group-icon"><i class="fa fa-usd"></i></div> 
													</div><!-- end form-group -->             
                           <!-- Fin Recuros Propios por Cobrar fila  carga -->            
                                                                                     
                                                                                   
                                                    
													<div class="form-group col-sm-12">
														<div class="help-block with-errors"></div>
														<textarea rows="3" name="observaciones" id="observaciones" placeholder="Escriba su comentario aquí" class="form-control"><?php echo $observaciones; ?></textarea>
														<div class="textarea input-group-icon"><i class="fa fa-pencil"></i></div>
													</div><!-- end form-group -->
													
													<div class="form-group last col-sm-12">
														<button type="submit" id="submit" class="btn btn-custom"><i class='fa fa-envelope'></i> Enviar</button>
                                                        <div id="msgContactSubmit" class="hidden"></div>	
                                                      
													</div><!-- end form-group -->	
											
													<span class="sub-text"><!-- * Campos requeridos--></span>
													<div class="clearfix"></div>
												</div><!-- end row -->
											</form><!-- end form -->
				  
								
								</div>
							</div><!--End row -->
							
							
							<!-- Popup end -->
							
						</div><!-- end item-wrap -->
					</div><!--End col -->
				</div><!--End tab-content -->
			</div><!--End row -->
            
            	<div class="row">
                 <div class="col-md-12" id="ancla1">
                  <p>Respecto a Erogaciones de Sueldos: en todas sus aperturas informar tal cual la de mayo informada en abril y para junio estimar SAC.  </p>
                  <p>Gastos operativos: informar los que hacen a los gastos ordinarios que van a tener que ejecutar hasta fin de mes (No Obras, u otros distintos al funcionamiento de la comuna).  </p>
                  <p> 
                  * Aquí informar saldo de libros bco + PF y saldo de Caja, si hay dinero comprometido para obras indicar el monto correspondiente
                  </p>
                  <p>** Recursos que van a ingresar desde el momento de hacer el informe hasta fin del mes de mayo y estimar todo el mes de junio </p> 
                 </div>
                </div> 
            
		</div><!--End container -->
	</section>
	
	
	<div class="colBottomMargin">
		&nbsp;<div class="colBottomMargin">&nbsp;</div>
	</div>	
	
	<div id="footer" class="footer">
		<div class="container">			
			
			<div class="row">					
				<div class="footer-top col-sm-12">
					<p class="text-center copyright">&copy; <?php echo date("Y");?> <a href="login.php?logout" class="footer-site-link">.</a>  -  <a href="login.php?logout">Salir</a></p>
				</div><!-- end col --> 
			</div><!-- end row -->
			
		</div><!--End container -->
	</div>
	
	<a href="#" class="scrollup"><i class="fa fa-arrow-circle-up"></i></a>

<?php } 
else
 {
 exit;	
 }
?>

	<!-- jQuery Library -->
	<script src="formularios/js/jquery-3.2.1.min.js"></script>	
	<!-- Popper js -->
	<script src="formularios/js/popper.min.js"></script>
	<!-- Bootstrap Js -->
	<script src="formularios/js/bootstrap.min.js"></script>
	<!-- Form Validator -->
	<script src="formularios/js/validator.min.js"></script>
	<!-- Contact Form Js -->
	 <script src="formularios/js/contact-form.js"></script>

<script>
 function test(e) {
 e.value=e.value.replace(/\,/g, '.')
}
</script>


	
</body>
</html>
