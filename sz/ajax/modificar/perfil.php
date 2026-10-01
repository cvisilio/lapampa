<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}
		if (empty($_POST['business_name'])){
			$errors[] = "Nombre del negocio está vacío";
		}else if (empty($_POST['number_id'])){
			$errors[] = "Número de registro está vacío";
		}else if (empty($_POST['cuit'])){
				$errors[] = "Número de CUIT está vacío";
		}else if (empty($_POST['web'])){
				$errors[] = "Dirección Web Está vacía";
			
		} else if (empty($_POST['email'])){
			$errors[] = "Email está vacío";
		} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Su dirección de correo electrónico no está en un formato de correo electrónico válida";
        } else if (empty($_POST['phone'])){
			$errors[] = "Teléfono está vacío";
		} else if (empty($_POST['address1'])){
			$errors[] = "La dirección está vacía";
		}  elseif (empty($_POST['city'])) {
            $errors[] = "La ciudad está vacía";
        } elseif (empty($_POST['state'])) {
            $errors[] = "Región/Provincia está vacío";
        } elseif (empty($_POST['postal_code'])) {
            $errors[] = "Código Postal está vacío";
       } elseif (empty($_POST['tipo_responsable'])) {
            $errors[] = "El tipo de Responsable está vacío";
			
        } elseif (empty($_POST['country_id'])) {
            $errors[] = "Selecciona el País";
        }    elseif (
			!empty($_POST['address1'])
			&& !empty($_POST['business_name'])
			&& !empty($_POST['number_id'])
			&& !empty($_POST['city'])
			&& !empty($_POST['state'])
			&& !empty($_POST['postal_code'])
			&& !empty($_POST['country_id'])
			&& !empty($_POST['mostrar_precios_sitio'])
			&& !empty($_POST['modo_bar'])
			&& filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)
						) {
		
			require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
                $business_name = mysqli_real_escape_string($con,(strip_tags($_POST["business_name"],ENT_QUOTES)));
				$number_id = mysqli_real_escape_string($con,(strip_tags($_POST["number_id"],ENT_QUOTES)));
				$cuit = mysqli_real_escape_string($con,(strip_tags($_POST["cuit"],ENT_QUOTES)));
				$web = mysqli_real_escape_string($con,(strip_tags($_POST["web"],ENT_QUOTES)));
				
				$email= mysqli_real_escape_string($con,(strip_tags($_POST["email"],ENT_QUOTES)));
				$phone= mysqli_real_escape_string($con,(strip_tags($_POST["phone"],ENT_QUOTES)));
				$tax= intval($_POST["tax"]);
				
				if (!empty($_POST['code_disqus']))
				$code_disqus = mysqli_real_escape_string($con,$_POST['code_disqus']);	
			   else
				$code_disqus="";
				
				$currency= intval($_POST["currency"]);
				$timezone=intval($_POST["timezone"]);
				$address1 = mysqli_real_escape_string($con,(strip_tags($_POST["address1"],ENT_QUOTES)));
				$city= mysqli_real_escape_string($con,(strip_tags($_POST["city"],ENT_QUOTES)));
                $state = mysqli_real_escape_string($con,(strip_tags($_POST["state"],ENT_QUOTES)));
				$postal_code=intval($_POST['postal_code']);
				$country_id=intval($_POST['country_id']);
				$tipo_responsable=intval($_POST['tipo_responsable']);
				$mostrar_precios_sitio=intval($_POST['mostrar_precios_sitio']);
				$modo_bar=intval($_POST['modo_bar']);
			    
				$id_cta_caja=intval($_POST['id_cta_caja']);
				$id_cta_debito_fiscal=intval($_POST['id_cta_debito_fiscal']);
				$id_cta_venta_productos=intval($_POST['id_cta_venta_productos']);
				$id_cta_deudores_ventas=intval($_POST['id_cta_deudores_ventas']);
				$id_cta_banco_tarjeta=intval($_POST['id_cta_banco_tarjeta']);
				$id_cta_valores_depositar=intval($_POST['id_cta_valores_depositar']);
				$id_cta_ret_ing_brutos=intval($_POST['id_cta_ret_ing_brutos']);
				$id_cta_credito_fiscal=intval($_POST['id_cta_credito_fiscal']);
				$id_cta_mercaderias=intval($_POST['id_cta_mercaderias']);
				$id_cta_proveedores=intval($_POST['id_cta_proveedores']);
            
				// update data
                    $sql = "UPDATE business_profile SET name='".$business_name."',cuit='".$cuit."',web='".$web."',number_id='".$number_id."',email='".$email."',code_disqus='".$code_disqus."',
					phone='".$phone."',tax='".$tax."', currency_id='".$currency."', timezone_id='".$timezone."', address='".$address1."', city='".$city."', state='".$state."',  postal_code='".$postal_code."', country_id='".$country_id."', tipo_responsable='".$tipo_responsable."', mostrar_precios_sitio='".$mostrar_precios_sitio ."', id_cuenta_caja='".$id_cta_caja."', id_cuenta_debito_fiscal='".$id_cta_debito_fiscal."', id_cuenta_venta_productos='".$id_cta_venta_productos."', id_cuenta_deudores_ventas='".$id_cta_deudores_ventas."', id_cuenta_banco_tarjeta_vta='".$id_cta_banco_tarjeta."', id_cuenta_valores_depositar='".$id_cta_valores_depositar."', id_cuenta_ret_ing_brutos='".$id_cta_ret_ing_brutos."', id_cuenta_credito_fiscal='".$id_cta_credito_fiscal."', id_cuenta_mercaderias='".$id_cta_mercaderias."', id_cuenta_proveedores='".$id_cta_proveedores ."', modo_bar='".$modo_bar ."' WHERE id='1' ";
                    $query = mysqli_query($con,$sql);

                    // if user has been update successfully
                    if ($query) {
                        $messages[] = "Los datos han sido actualizados exitosamente.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
			
		} else {
			$errors[] = " Desconocido";	
		}
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}
?>			