<?php

function add_inventory($product_id,$product_quantity){
		global $con;//Variable de conexion
		$sql=mysqli_query($con,"select * from inventory where product_id='".$product_id."'");//Consulta para verificar si el producto se encuentra reguistrado en  el inventario
		$count=mysqli_num_rows($sql);
		if ($count==0){
			$insert=mysqli_query($con,"insert into inventory (product_id, product_quantity) values ('$product_id','$product_quantity')");//Ingresa un nuevo producto al inventario
		} else {
			$sql2=mysqli_query($con,"select * from inventory where product_id='".$product_id."'");
			$rw=mysqli_fetch_array($sql2);
			$old_qty=$rw['product_quantity'];//Cantidad encontrada en el inventario
			$new_qty=$old_qty+$product_quantity;//Nueva cantidad en el inventario
			$update=mysqli_query($con,"UPDATE inventory SET product_quantity='".$new_qty."' WHERE product_id='".$product_id."'");//Actualizo la nueva cantidad en el inventario
		}
	}
	
	function adjustment_inventory($product_id,$product_quantity){
		global $con;//Variable de conexion
		$sql=mysqli_query($con,"select * from inventory where product_id='".$product_id."'");//Consulta para verificar si el producto se encuentra reguistrado en  el inventario
		$count=mysqli_num_rows($sql);
		if ($count==0){
			$insert=mysqli_query($con,"insert into inventory (product_id, product_quantity) values ('$product_id','$product_quantity')");//Ingresa un nuevo producto al inventario
		} else {
			$update=mysqli_query($con,"UPDATE inventory SET product_quantity='".$product_quantity."' WHERE product_id='".$product_id."'");//Actualizo la nueva cantidad en el inventario
		}
	}
	
	function remove_inventory($product_id,$product_quantity){
		global $con;//Variable de conexion
		$sql=mysqli_query($con,"select * from inventory where product_id='".$product_id."'");
		$rw=mysqli_fetch_array($sql);
		$old_qty=$rw['product_quantity'];//Cantidad encontrada en el inventario
		$new_qty=$old_qty-$product_quantity;//Nueva cantidad en el inventario
		$update=mysqli_query($con,"UPDATE inventory SET product_quantity='".$new_qty."' WHERE product_id='".$product_id."'");//Actualizo la nueva cantidad en el inventario
	}
	function update_buying_price($product_id,$buying_price){
		global $con;//Variable de conexion
		$update=mysqli_query($con,"UPDATE products SET buying_price='".$buying_price."' WHERE product_id='".$product_id."'");
		
		// desde acá es para actualizar precios de los productos compuestos cuyos ingredientes tienen a el producto que se está modificando en estos momentos	
					$sql2 = "UPDATE productos_complementarios_tmp SET unit_price='".$buying_price."' WHERE product_id='$product_id'";
                    $query2 = mysqli_query($con,$sql2);
					
					
					$sql_tmp=mysqli_query($con,"SELECT DISTINCT product_id_raiz FROM productos_complementarios_tmp where product_id='$product_id'");
		            while ($rw_tmp=mysqli_fetch_array($sql_tmp)){
					 $product_id1=$rw_tmp['product_id_raiz'];
									   
				     $sql4=mysqli_query($con,"SELECT SUM(qty*unit_price) as total from productos_complementarios_tmp where product_id_raiz='$product_id1'");
                      $rw4=mysqli_fetch_array($sql4); 
					 
					 $buying_price1=$rw4['total'];
					 $total_neto=number_format($buying_price1,2,'.','');
				
		             $total_iva=($total_neto*$tax) / 100;
		             $total_iva=number_format($total_iva,2,'.','');
		             $total_venta = $total_neto + $total_iva;
		             $total_venta=number_format($total_venta,2,'.',''); 					 
					 
					 $sql3 = "UPDATE products SET buying_price=$total_venta,sellingprice=(($total_venta*profit/100)+$total_venta+costo_produccion+costo_produccion2), sellingprice2=(($total_venta*profit2/100)+$total_venta+costo_produccion+costo_produccion2) WHERE product_id='$product_id1'";
                    $query3 = mysqli_query($con,$sql3);
		            }
		
		       // hasta acá actualización de los productos compuestos
		
				
		
	}
	
	function update_sellingprice($product_id,$buying_price){
		global $con;//Variable de conexion
		$sql=mysqli_query($con,"select profit,profit2 from products where product_id='$product_id'");
		$rw=mysqli_fetch_array($sql);
		$utilidad=intval($rw['profit']);
		$utilidad2=intval($rw['profit2']);

		$utilidad=($buying_price * $utilidad) /100;
		$utilidad2=($buying_price2 * $utilidad2) /100;
		$precio_venta=$buying_price + $utilidad;
		$precio_venta2=$buying_price2 + $utilidad2;
		$sellingprice=number_format($precio_venta,2,'.','');
		$sellingprice2=number_format($precio_venta2,2,'.','');
		
		
		$update=mysqli_query($con,"UPDATE products SET sellingprice='".$sellingprice."',sellingprice2='".$sellingprice2."' WHERE product_id='".$product_id."'");
	}
	
	function get_stock($product_id){
		global $con;//Variable de conexion
		$sql=mysqli_query($con,"SELECT 	product_quantity FROM inventory WHERE product_id='".$product_id."'");
		$rw=mysqli_fetch_array($sql);
		$stock=number_format($rw['product_quantity'],2);
		return $stock;
	}
	//Agrega un nuevo registro a la tabla product_tmp
	function add_tmp($product_id, $qty, $unit_price, $user_id, $mesa){
		global $con;
		$sql=mysqli_query($con,"insert into product_tmp 
		(id_tmp, product_id, qty, unit_price, user_id, mesa)
		values (NULL, '$product_id','$qty','$unit_price','$user_id','$mesa')");
	}
	
	
	function add_tmp_complementario($product_id, $product_id_raiz){
		global $con; // articulos complementarios para armar KIT
		$sql=mysqli_query($con,"insert into agenda_usuarios 
		(usuario, usuario_que_ve)
		values ('$product_id','$product_id_raiz')");
	}
	
	
	function add_tmp_compra($product_id, $qty, $unit_price, $user_id){
		global $con;
		$sql=mysqli_query($con,"insert into purchase_product_tmp 
		(id_tmp, product_id, qty, unit_price, user_id)
		values (NULL, '$product_id','$qty','$unit_price','$user_id')");
	}
	
	function add_tmp_presupuesto($product_id, $qty, $unit_price, $user_id){
		global $con;
		$sql=mysqli_query($con,"insert into presupuesto_product_tmp 
		(id_tmp, product_id, qty, unit_price, user_id)
		values (NULL, '$product_id','$qty','$unit_price','$user_id')");
	}
	
	function add_tmp_pedido($product_id, $qty, $unit_price, $user_id){
		global $con;
		$sql=mysqli_query($con,"insert into pedido_product_tmp 
		(id_tmp, product_id, qty, unit_price, user_id)
		values (NULL, '$product_id','$qty','$unit_price','$user_id')");
	}
	
	function add_tmp_nota_credito($product_id, $qty, $unit_price, $user_id){
		global $con;
		$sql=mysqli_query($con,"insert into nota_credito_product_tmp 
		(id_tmp, product_id, qty, unit_price, user_id)
		values (NULL, '$product_id','$qty','$unit_price','$user_id')");
		
	}
	
	//Elimina un registro de la tabla product_tmp
	function remove_tmp($id_tmp){
		global $con;
		$sql=mysqli_query($con,"DELETE FROM product_tmp WHERE id_tmp='$id_tmp'");
	}
	function remove_tmp_complementario($id_tmp){ // articulos complementarios para armar KIT
		global $con;
		$sql=mysqli_query($con,"DELETE FROM agenda_usuarios WHERE id='$id_tmp'");
	}
	
	function actualizar_tmp_complementario($id_raiz,$cantidad_actualizar){ // articulos complementarios para armar KIT
		global $con;
         if ($cantidad_actualizar ==0)
		  $cantidad_actualizar =1;
		  
		 $sql = "UPDATE productos_complementarios_tmp SET qty=round((qty * '$cantidad_actualizar'),2) WHERE productos_complementarios_tmp.product_id_raiz='".$id_raiz."' ";
		 
		$sql=mysqli_query($con,$sql);
	}
	
	function remove_tmp_compra($id_tmp){
		global $con;
		$sql=mysqli_query($con,"DELETE FROM purchase_product_tmp WHERE id_tmp='$id_tmp'");
	}
	
	function remove_tmp_presupuesto($id_tmp){
		global $con;
		$sql=mysqli_query($con,"DELETE FROM presupuesto_product_tmp WHERE id_tmp='$id_tmp'");
	}
	
	function remove_tmp_nota_credito($id_tmp){
		global $con;
		$sql=mysqli_query($con,"DELETE FROM nota_credito_product_tmp WHERE id_tmp='$id_tmp'");
	}
	
	
	function remove_tmp_pedido($id_tmp){
		global $con;
		$sql=mysqli_query($con,"DELETE FROM pedido_product_tmp WHERE id_tmp='$id_tmp'");
	}
	
	//Guarda una venta
	function add_sale($sale_number, $customer_id, $sale_by,$sale_date, $condicion_venta,$tipo_comprobante,$descuento,$tipo_factura){
		global $con;
		
		$sum=mysqli_query($con,"select sum(qty*unit_price) as subtotal from product_tmp where user_id='$sale_by'");
		$rw_sum=mysqli_fetch_array($sum);
		$sumador_total=$rw_sum['subtotal'];
		$tax= get_tax();
		
		$total_neto=number_format($sumador_total,2,'.','');
		
		if ($descuento>0)
		  $total_descuento=($total_neto*$descuento) / 100;
		else
		 {
		  $total_descuento=0;
		  $descuento="";
		 }
		$total_neto=$total_neto-$total_descuento;
		$total_iva=($total_neto*$tax) / 100;
		$total_iva=number_format($total_iva,2,'.','');
		$total_venta = $total_neto + $total_iva;
		$total_venta=number_format($total_venta,2,'.','');
		$sale_id=next_insert_id('sales');
		$sql="INSERT INTO sales
		(sale_id, sale_number, customer_id, sale_by, subtotal, tax, total, sale_date, condicion_venta, tipo_comprobante,porcentaje_dto,descuento,tipo_factura) 
		VALUES ('$sale_id', '$sale_number', '$customer_id', '$sale_by', '$total_neto', '$total_iva', '$total_venta', '$sale_date', '$condicion_venta', '$tipo_comprobante','$descuento','$total_descuento','$tipo_factura');";
		$query=mysqli_query($con,$sql);
		
		
		$sql=mysqli_query($con, "select * from business_profile where business_profile.id=1");	
		$rw=mysqli_fetch_array($sql);
		$moneda=$rw['currency_id'];
		
		$id_cuenta_caja=$rw['id_cuenta_caja'];  // debe
		
		$id_cuenta_debito_fiscal=$rw['id_cuenta_debito_fiscal']; // haber
		
		$id_cuenta_venta_productos=$rw['id_cuenta_venta_productos']; // haber
		$id_cuenta_deudores_ventas=$rw['id_cuenta_deudores_ventas']; // debe
		$id_cuenta_banco_tarjeta=$rw['id_cuenta_banco_tarjeta_vta'];
		$id_cuenta_valores_depositar=$rw['id_cuenta_valores_depositar'];
			
		
		//agregar asiento contable
		$fecha_hora=date("Y-m-d H:i:s");
		$origen=1; //1= automático, 2=manual
		$id_ejercicio=1;
		$id_periodo=1;
		$clase= 1;
		$estado= 1;
		
		//asiento por venta de producto
		$concepto = "Fact. Nro.: " . $sale_id;
		$id_cuenta = $id_cuenta_venta_productos;
		$debe = 0;
		$haber = $total_neto;
		$sql="INSERT INTO asientos (clase, estado, fecha_hora, concepto, debe, haber, moneda, usuario, id_cuenta, id_periodo, id_ejercicio, origen) VALUES ('$clase', '$estado', '$fecha_hora', '$concepto', '$debe', '$haber', '$moneda', '$sale_by', '$id_cuenta', '$id_periodo', '$id_ejercicio', '$origen');";
		$query_new = mysqli_query($con,$sql);
		
		//$sql3=mysqli_query($con,"select id from asientos order by id desc limit 0,1");
		//$rw=mysqli_fetch_array($sql3); 
	//	$id_asiento_ref=$rw['id']; // obtengo el id asiento para insertarlo en los demas asientos como el asiento de referencia
		
		// asiento debito fiscal
		
		$id_cuenta = $id_cuenta_debito_fiscal;
		$debe = 0;
		$haber = $total_iva;
		$sql="INSERT INTO asientos (clase, estado, fecha_hora, concepto, debe, haber, moneda, usuario, id_cuenta, id_periodo, id_ejercicio, origen, id_asiento_ref) VALUES ('$clase', '$estado', '$fecha_hora', '$concepto', '$debe', '$haber', '$moneda', '$sale_by', '$id_cuenta', '$id_periodo', '$id_ejercicio', '$origen', '$id_asiento_ref');";
		$query_new = mysqli_query($con,$sql);
		
		//asiento por condicion de venta
		switch ($condicion_venta){
		 case 1:
		  $id_cuenta =$id_cuenta_caja;
		  $debe = $total_venta;
		  $haber = 0;
		  break;
		 case 2:
		  $id_cuenta =$id_cuenta_deudores_ventas;
		  $debe = $total_venta;
		  $haber = 0;
		  break;
		 case 3:
		  $id_cuenta =$id_cuenta_banco_tarjeta;
		  $debe = $total_venta;
		  $haber = 0;
		  break;
		 case 4:
		  $id_cuenta =$id_cuenta_banco_tarjeta;
		  $debe = $total_venta;
		  $haber = 0;
		  break;
		 case 5:
          $id_cuenta =$id_cuenta_valores_depositar;
		  $debe = $total_venta;
		  $haber = 0;
		  break;
		}
		
		$sql="INSERT INTO asientos (clase, estado, fecha_hora, concepto, debe, haber, moneda, usuario, id_cuenta, id_periodo, id_ejercicio, origen, id_asiento_ref) VALUES ('$clase', '$estado', '$fecha_hora', '$concepto', '$debe', '$haber', '$moneda', '$sale_by', '$id_cuenta', '$id_periodo', '$id_ejercicio', '$origen', '$id_asiento_ref');";
		$query_new = mysqli_query($con,$sql);
		
		/*Agregar movimiento del cliente si es a cta corriente*/
		
		 if ($condicion_venta==2) 
		  { 
		  
		  $sql="Select * from movimientos_clientes where id_cliente = '$customer_id'  order by id DESC LIMIT 1";
		  $query=mysqli_query($con,$sql);
		  if ($rw_total = mysqli_fetch_array($query))
		   $sumador_total=$rw_total['saldoactual']; 
		  else
		   $sumador_total=0;
		   
		   // se obtiene el saldo actual para sumarle el importe, de esta manera queda tipo libro de movimientos
		  $saldo_actual= $sumador_total + $total_venta;
		  $tipo_movimiento=2;
		  	$sql="INSERT INTO movimientos_clientes
		(id_cliente, tipo_movimiento, fecha_movimiento, importe, id_factura_notacredito_debito, saldoactual, sale_by) 
		VALUES ('$customer_id', '$tipo_movimiento', '$sale_date', '$total_venta', '$sale_id','$saldo_actual', $sale_by);";
		$query=mysqli_query($con,$sql);
		
		
		 
		  }
		
		/**/
		
		$sql_tmp=mysqli_query($con,"select * from product_tmp where user_id='$sale_by'");
		while ($rw_tmp=mysqli_fetch_array($sql_tmp)){
			$id_tmp=$rw_tmp['id_tmp'];
			$product_id=$rw_tmp['product_id'];
			$qty=$rw_tmp['qty'];
			$unit_price=$rw_tmp['unit_price'];
			add_sale_product($sale_id,$product_id,$qty,$unit_price);//Agrego un registro  a la tabla sale_product
			remove_inventory($product_id,$qty );//Disminuye la cantidad en el inventario;
			remove_tmp($id_tmp);//Elimina el item de la tabla temporal
		}
		
	}
	
	function add_presupuesto($sale_number, $customer_id, $sale_by,$sale_date, $condicion_venta,$tipo_comprobante,$descuento){
		global $con;
		$sum=mysqli_query($con,"select sum(qty*unit_price) as subtotal from presupuesto_product_tmp where user_id='$sale_by'");
		$rw_sum=mysqli_fetch_array($sum);
		$sumador_total=$rw_sum['subtotal'];
		$tax= get_tax();
		
		$total_neto=number_format($sumador_total,2,'.','');
		
		if ($descuento>0)
		  $total_descuento=($total_neto*$descuento) / 100;
		else
		 {
		  $total_descuento=0;
		  $descuento="";
		 }
		$total_neto=$total_neto-$total_descuento;
		$total_iva=($total_neto*$tax) / 100;
		$total_iva=number_format($total_iva,2,'.','');
		$total_venta = $total_neto + $total_iva;
		$total_venta=number_format($total_venta,2,'.','');
		$sale_id=next_insert_id('presupuestos');
		$sql="INSERT INTO presupuestos
		(sale_id, sale_number, customer_id, sale_by, subtotal, tax, total, sale_date, condicion_venta, tipo_comprobante,porcentaje_dto, descuento) 
		VALUES ('$sale_id', '$sale_number', '$customer_id', '$sale_by', '$total_neto', '$total_iva', '$total_venta', '$sale_date', '$condicion_venta', '$tipo_comprobante','$descuento','$total_descuento');";
		$query=mysqli_query($con,$sql);
		
		
		
		$sql_tmp=mysqli_query($con,"select * from presupuesto_product_tmp where user_id='$sale_by'");
		while ($rw_tmp=mysqli_fetch_array($sql_tmp)){
			$id_tmp=$rw_tmp['id_tmp'];
			$product_id=$rw_tmp['product_id'];
			$qty=$rw_tmp['qty'];
			$unit_price=$rw_tmp['unit_price'];
			add_presupuesto_product($sale_id,$product_id,$qty,$unit_price);//Agrego un registro  a la tabla sale_product
			//remove_inventory($product_id,$qty );// en presupuesto no es necesario  - Disminuye la cantidad en el inventario;
			remove_tmp_presupuesto($id_tmp);//Elimina el item de la tabla temporal
		}
		
	}
	
	
	
	function add_nota_credito($sale_number, $customer_id, $sale_by,$sale_date, $condicion_venta,$tipo_comprobante,$sale_number_referencia,$tipo_nota_credito){
		global $con;
		$sum=mysqli_query($con,"select sum(qty*unit_price) as subtotal from nota_credito_product_tmp where user_id='$sale_by'");
		$rw_sum=mysqli_fetch_array($sum);
		$sumador_total=$rw_sum['subtotal'];
		$tax= get_tax();
		
		$total_neto=number_format($sumador_total,2,'.','');
		
		$total_descuento=0; // agregar
		
		if ($descuento>0)
		  $total_descuento=($total_neto*$descuento) / 100;
		else
		 {
		  $total_descuento=0;
		  $descuento="";
		 }
		$total_neto=$total_neto-$total_descuento;
		$total_iva=($total_neto*$tax) / 100;
		$total_iva=number_format($total_iva,2,'.','');
		$total_venta = $total_neto + $total_iva;
		$total_venta=number_format($total_venta,2,'.','');
		$sale_id=next_insert_id('notas_creditos');
		$sql="INSERT INTO notas_creditos
		(sale_id, sale_id_referencia,sale_number, customer_id, sale_by, subtotal, tax, total, sale_date, condicion_venta, tipo_comprobante,tipo_nota_credito) 
		VALUES ('$sale_id','$sale_number_referencia', '$sale_number', '$customer_id', '$sale_by', '$total_neto', '$total_iva', '$total_venta', '$sale_date', '$condicion_venta', '$tipo_comprobante', '$tipo_nota_credito');";
		$query=mysqli_query($con,$sql);
		
		/*Agregar movimiento del cliente si es a cta corriente*/
		 if ($condicion_venta==2) 
		  { 
		  
		  $sql="Select * from movimientos_clientes where id_cliente = '$customer_id'  order by id DESC LIMIT 1";
		  $query=mysqli_query($con,$sql);
		  if ($rw_total = mysqli_fetch_array($query))
		   $sumador_total=$rw_total['saldoactual']; 
		  else
		   $sumador_total=0;
		   
		   // se obtiene el saldo actual para sumarle el importe, de esta manera queda tipo libro de movimientos
		  $saldo_actual= $sumador_total - $total_venta;
		   $tipo_movimiento=6;
		  
		  	$sql="INSERT INTO movimientos_clientes
		(id_cliente, tipo_movimiento, fecha_movimiento, importe, id_factura_notacredito_debito, saldoactual, sale_by) 
		VALUES ('$customer_id', '$condicion_venta', '$sale_date', '$total_venta', '$sale_id','$saldo_actual', $sale_by);";
		$query=mysqli_query($con,$sql);
		 
		  }
		
		/**/
		
		
		$sql_tmp=mysqli_query($con,"select * from nota_credito_product_tmp where user_id='$sale_by'");
		while ($rw_tmp=mysqli_fetch_array($sql_tmp)){
			$id_tmp=$rw_tmp['id_tmp'];
			$product_id=$rw_tmp['product_id'];
			$qty=$rw_tmp['qty'];
			$unit_price=$rw_tmp['unit_price'];
			add_nota_credito_product($sale_id,$product_id,$qty,$unit_price);//Agrego un registro  a la tabla sale_product
			add_inventory($product_id,$qty );//  Aumenta la cantidad en el inventario;
			remove_tmp_nota_credito($id_tmp);//Elimina el item de la tabla temporal
		}
		
	}
	
	
	
	function add_pedido($sale_number, $customer_id, $sale_by,$sale_date, $condicion_venta,$tipo_comprobante){
		global $con;
		$sum=mysqli_query($con,"select sum(qty*unit_price) as subtotal from pedido_product_tmp where user_id='$sale_by'");
		$rw_sum=mysqli_fetch_array($sum);
		$sumador_total=$rw_sum['subtotal'];
		$tax= get_tax();
		
		$total_neto=number_format($sumador_total,2,'.','');
		
		$total_descuento=0; // agregar
		
		if ($descuento>0)
		  $total_descuento=($total_neto*$descuento) / 100;
		else
		 {
		  $total_descuento=0;
		  $descuento="";
		 }
		$total_neto=$total_neto-$total_descuento;
		$total_iva=($total_neto*$tax) / 100;
		$total_iva=number_format($total_iva,2,'.','');
		$total_venta = $total_neto + $total_iva;
		$total_venta=number_format($total_venta,2,'.','');
		
		$sale_id=next_insert_id('estimates');
		$sql="INSERT INTO estimates
		(id_cotizacion, numero_cotizacion, user_id, subtotal, tax, total, fecha_cotizacion) 
		VALUES ('$sale_id', '$sale_number', '$customer_id', '$total_neto', '$total_iva', '$total_venta', '$sale_date');";
		$query=mysqli_query($con,$sql);
		
		
		
		$sql_tmp=mysqli_query($con,"select * from pedido_product_tmp where user_id='$sale_by'");
		while ($rw_tmp=mysqli_fetch_array($sql_tmp)){
			$id_tmp=$rw_tmp['id_tmp'];
			$product_id=$rw_tmp['product_id'];
			$qty=$rw_tmp['qty'];
			$unit_price=$rw_tmp['unit_price'];
			add_pedido_product($sale_id,$product_id,$qty,$unit_price);//Agrego un registro  a la tabla sale_product
			//remove_inventory($product_id,$qty );// en presupuesto no es necesario  - Disminuye la cantidad en el inventario;
			remove_tmp_pedido($id_tmp);//Elimina el item de la tabla temporal
		}
		
	}
	
	
	function get_tax(){
		global $con;
		$sql=mysqli_query($con,"SELECT tax FROM  business_profile where  business_profile.id=1");
		$row=mysqli_fetch_array($sql);
		$tax=$row["tax"];
		return $tax;
	}
	
	function next_insert_id($table){
		global $con;
		$next="SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".DB_NAME."' AND   TABLE_NAME   = '$table'";
		$query_next=mysqli_query($con,$next);
		$rw_next=mysqli_fetch_array($query_next);
		$next_insert=$rw_next['AUTO_INCREMENT'];
	    return $next_insert;
	}
	
	
	
	function add_sale_product($sale_id,$product_id,$qty,$unit_price){
		global $con;
		$sql="INSERT INTO sale_product (sale_product_id, sale_id, product_id, qty, unit_price)
		VALUES (NULL, '$sale_id', '$product_id', '$qty', '$unit_price');";
		$query=mysqli_query($con,$sql);
	}
	
	
	function add_presupuesto_product($sale_id,$product_id,$qty,$unit_price){
		global $con;
		$sql="INSERT INTO presupuesto_product (sale_product_id, sale_id, product_id, qty, unit_price)
		VALUES (NULL, '$sale_id', '$product_id', '$qty', '$unit_price');";
		$query=mysqli_query($con,$sql);
	}
	
	function add_nota_credito_product($sale_id,$product_id,$qty,$unit_price){
		global $con;
		$sql="INSERT INTO nota_credito_product (sale_product_id, sale_id, product_id, qty, unit_price)
		VALUES (NULL, '$sale_id', '$product_id', '$qty', '$unit_price');";
		$query=mysqli_query($con,$sql);
	}
	
	function add_pedido_product($sale_id,$product_id,$qty,$unit_price){
		global $con;
		$sql="INSERT INTO detail_estimate (id_detalle, numero_cotizacion, id_producto, cantidad, precio_unitario)
		VALUES (NULL, '$sale_id', '$product_id', '$qty', '$unit_price');";
		$query=mysqli_query($con,$sql);
	}
	
	function is_valid_sale($sale_number){
		global $con;
		$sql=mysqli_query($con,"select sale_number from sales where sale_number='$sale_number'");
		$count = mysqli_num_rows($sql);
		if ($count==0){
			return true;
		} else {
			return false;
		}
		
	}
	
function is_valid_presupuesto($sale_number){
		global $con;
		$sql=mysqli_query($con,"select sale_number from presupuestos where sale_number='$sale_number'");
		$count = mysqli_num_rows($sql);
		if ($count==0){
			return true;
		} else {
			return false;
		}
		
	}
	
 function is_valid_nota_credito($sale_number){
		global $con;
		$sql=mysqli_query($con,"select sale_number from notas_creditos where sale_number='$sale_number'");
		$count = mysqli_num_rows($sql);
		if ($count==0){
			return true;
		} else {
			return false;
		}
		
	}	

function is_valid_pedido($sale_number){
		global $con;
		$sql=mysqli_query($con,"select numero_cotizacion from estimates where numero_cotizacion='$sale_number'");
		$count = mysqli_num_rows($sql);
		if ($count==0){
			return true;
		} else {
			return false;
		}
		
	}
 		
	
	function nex_sale_number(){
		global $con;
		$sale_number= 0;
		$sql=mysqli_query($con,"select sale_number from sales order by sale_id desc limit 0,1");
		$rw=mysqli_fetch_array($sql); 
		$sale_number=$rw['sale_number'];
		$nex_sale_number=$sale_number+1;
		
		return $nex_sale_number;
		
	}
	
	function nex_sale_number_presupuesto(){
		global $con;
		$sale_number=0;
		$sql=mysqli_query($con,"select sale_number from presupuestos order by sale_id desc limit 0,1");
		$rw=mysqli_fetch_array($sql); 
		$sale_number=$rw['sale_number'];
		$nex_sale_number_presupuesto=$sale_number+1;
		
		return $nex_sale_number_presupuesto;
		
	}
	
function nex_sale_number_nota_credito(){
		global $con;
		$sale_number=0;
		$sql=mysqli_query($con,"select sale_number from notas_creditos order by sale_id desc limit 0,1");
		$rw=mysqli_fetch_array($sql); 
		$sale_number=$rw['sale_number'];
		$nex_sale_number_presupuesto=$sale_number+1;
		
		return $nex_sale_number_presupuesto;
		
	}	
	
	function nex_sale_number_pedido(){
		global $con;
		$sale_number=0;
		$sql=mysqli_query($con,"select numero_cotizacion from estimates order by id_cotizacion desc limit 0,1");
		$rw=mysqli_fetch_array($sql); 
		$sale_number=$rw['sale_number'];
		$nex_sale_number_presupuesto=$sale_number+1;
		
		return $nex_sale_number_presupuesto;
		
	}
	
	function nex_purchase_number(){
		global $con;
		$sql=mysqli_query($con,"select purchase_order_number from purchases order by purchase_id desc limit 0,1");
		$rw=mysqli_fetch_array($sql); 
		$purchase_number=$rw['purchase_order_number'];
		$nex_purchase_number=$purchase_number+1;
		
		return $nex_purchase_number;
		
	}
	
	function count_tmp($user_id){
		global $con;
		$sql=mysqli_query($con,"select product_id from purchase_product_tmp where user_id='$user_id'");
		$count=mysqli_num_rows($sql); 
		return $count;
	}
	
	//Guarda una compra
	function add_purchase($order_number, $supplier_id, $purchase_by,$purchase_date,$numero_factura,$cae,$descuento,$fecha_vto,$fecha_contable,$condicion_venta,$tipo_comprobante,$tipo_factura){
		global $con; 
		$sum=mysqli_query($con,"select sum(qty*unit_price) as subtotal from purchase_product_tmp where user_id='$purchase_by'");
		$rw_sum=mysqli_fetch_array($sum);
		$sumador_total=$rw_sum['subtotal'];
		$tax= get_tax();
		$total_neto=number_format($sumador_total,2,'.','');
			
		if ($descuento>0)
		  $total_descuento=($total_neto*$descuento) / 100;
		else
		 {
		  $total_descuento=0;
		  $descuento="";
		 }
		$total_neto=$total_neto-$total_descuento;
		$total_iva=($total_neto*$tax) / 100;
		$total_iva=number_format($total_iva,2,'.','');
		$total_compra = $total_neto + $total_iva;
		$total_compra=number_format($total_compra,2,'.','');
		$purchase_id=next_insert_id('purchases');
		
		$sql="INSERT INTO purchases
		(purchase_id, purchase_order_number	, supplier_id, purchase_by, subtotal, tax, total, purchase_date, numero_factura, cae, porcentaje_dto, descuento, fecha_vto, fecha_contable,condicion_venta,tipo_comprobante,tipo_factura) 
		VALUES ('$purchase_id', '$order_number', '$supplier_id', '$purchase_by', '$total_neto', '$total_iva', '$total_compra', '$purchase_date', '$numero_factura', '$cae', '$descuento', '$total_descuento', '$fecha_vto', '$fecha_contable', '$condicion_venta', '$tipo_comprobante', '$tipo_factura');";
		$query=mysqli_query($con,$sql);
		if ($query){
		 $true=1;
		} else {
		 $true=0;
		}
		$sql_tmp=mysqli_query($con,"select * from purchase_product_tmp where user_id='$purchase_by'");
		while ($rw_tmp=mysqli_fetch_array($sql_tmp)){
			$id_tmp=$rw_tmp['id_tmp'];
			$product_id=$rw_tmp['product_id'];
			$qty=$rw_tmp['qty'];
			$unit_price=$rw_tmp['unit_price'];
			add_purchase_product($purchase_id,$product_id,$qty,$unit_price);//Agrego un registro  a la tabla purchase_product
			add_inventory($product_id,$qty);//Agrego la cantidad en el inventario;
			update_buying_price($product_id,$unit_price);//Actualizo precio de compra
			update_sellingprice($product_id,$unit_price);//Actualizo precio de venta
			remove_tmp_compra($id_tmp);//Elimina el item de la tabla temporal
		}
		
		//agregar asiento contable
		$sql=mysqli_query($con, "select * from business_profile where business_profile.id=1");	
		$rw=mysqli_fetch_array($sql);
		$moneda=$rw['currency_id'];
		
		$id_cuenta_caja=$rw['id_cuenta_caja'];  
				
		//$id_cuenta_banco_tarjeta=$rw['id_cuenta_banco_tarjeta_compra'];
		$id_cuenta_banco_tarjeta=$rw['id_cuenta_banco_tarjeta_vta'];
				
		$id_cuenta_valores_depositar=$rw['id_cuenta_valores_depositar'];
		
		$id_cuenta_ret_ing_brutos=$rw['id_cuenta_ret_ing_brutos'];
		$id_cuenta_credito_fiscal=$rw['id_cuenta_credito_fiscal'];
		$id_cuenta_mercaderias=$rw['id_cuenta_mercaderias'];
		$id_cuenta_proveedores=$rw['id_cuenta_proveedores'];
		
		
		$fecha_hora=$fecha_contable .date("H:i:s");
		
		$origen=1; //1= automático, 2=manual
		$id_ejercicio=1;
		$id_periodo=1;
		$clase= 1;
		$estado= 1;
		
		//asiento por compra de producto
		$concepto = "Compra. Nro.: " . $order_number;
		$id_cuenta = $id_cuenta_mercaderias;
		$debe = $total_neto;
		$haber = 0;
		$sql="INSERT INTO asientos (clase, estado, fecha_hora, concepto, debe, haber, moneda, usuario, id_cuenta, id_periodo, id_ejercicio, origen) VALUES ('$clase', '$estado', '$fecha_hora', '$concepto', '$debe', '$haber', '$moneda', '$sale_by', '$id_cuenta', '$id_periodo', '$id_ejercicio', '$origen');";
		$query_new = mysqli_query($con,$sql);
		
		//$sql3=mysqli_query($con,"select id from asientos order by id desc limit 0,1");
		//$rw=mysqli_fetch_array($sql3); 
	//	$id_asiento_ref=$rw['id']; // obtengo el id asiento para insertarlo en los demas asientos como el asiento de referencia
		
		// asiento debito fiscal
		
		$id_cuenta = $id_cuenta_credito_fiscal;
		$debe = $total_iva;
		$haber = 0;
		$sql="INSERT INTO asientos (clase, estado, fecha_hora, concepto, debe, haber, moneda, usuario, id_cuenta, id_periodo, id_ejercicio, origen, id_asiento_ref) VALUES ('$clase', '$estado', '$fecha_hora', '$concepto', '$debe', '$haber', '$moneda', '$sale_by', '$id_cuenta', '$id_periodo', '$id_ejercicio', '$origen', '$id_asiento_ref');";
		$query_new = mysqli_query($con,$sql);
		
		//asiento por condicion de venta
		switch ($condicion_venta){
		 case 1:
		  $id_cuenta =$id_cuenta_caja;
		  $debe = 0;
		  $haber = $total_compra;
		  break;
		 case 2:
		  $id_cuenta =$id_cuenta_proveedores;
		  $debe = 0;
		  $haber = $total_compra;
		  break;
		 case 3:
		  $id_cuenta =$id_cuenta_banco_tarjeta;
		  $debe = 0;
		  $haber = $total_compra;
		  break;
		 case 4:
		  $id_cuenta =$id_cuenta_banco_tarjeta;
		  $debe = 0;
		  $haber = $total_compra;
		  break;
		 case 5:
          $id_cuenta =$id_cuenta_valores_depositar;
		  $debe = 0;
		  $haber = $total_compra;
		  break;
		 
		}
		
		$sql="INSERT INTO asientos (clase, estado, fecha_hora, concepto, debe, haber, moneda, usuario, id_cuenta, id_periodo, id_ejercicio, origen, id_asiento_ref) VALUES ('$clase', '$estado', '$fecha_hora', '$concepto', '$debe', '$haber', '$moneda', '$sale_by', '$id_cuenta', '$id_periodo', '$id_ejercicio', '$origen', '$id_asiento_ref');";
		$query_new = mysqli_query($con,$sql);
		
		/*Agregar movimiento del proveedor si es a cta corriente*/
		
		 if ($condicion_venta==2) 
		  { 
		  
		  $sql="Select * from movimientos_proveedores where id_proveedor = '$supplier_id'  order by id DESC LIMIT 1";
		  $query=mysqli_query($con,$sql);
		  if ($rw_total = mysqli_fetch_array($query))
		   $sumador_total=$rw_total['saldoactual']; 
		  else
		   $sumador_total=0;
		   
		   // se obtiene el saldo actual para sumarle el importe, de esta manera queda tipo libro de movimientos
		  $saldo_actual= $sumador_total + $total_compra;
		  $tipo_movimiento=2;
		  $fecha_hora=date("Y-m-d H:i:s");
		  
		  $sql="INSERT INTO movimientos_proveedores
		(id_proveedor, tipo_movimiento, fecha_movimiento, importe, id_factura_notacredito_debito, saldoactual, sale_by) 
		VALUES ('$supplier_id', '$tipo_movimiento', '$fecha_hora', '$total_compra', '$purchase_id','$saldo_actual', '$sale_by');";
		$query=mysqli_query($con,$sql);
		
		
		 
		  }
		
		/**/		
		
		return $true;
		
	}
	
	
	function add_purchase_product($purchase_id,$product_id,$qty,$unit_price){
		global $con;
		$sql="INSERT INTO purchase_product (purchase_product_id, purchase_id, product_id, qty, unit_price)
		VALUES (NULL, '$purchase_id', '$product_id', '$qty', '$unit_price');";
		$query=mysqli_query($con,$sql);
	}
?>