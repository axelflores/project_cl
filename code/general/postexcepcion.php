<?php

	if($tabla == 'ec_sincronizacion')
	{
		if($accion == 'actualizar')
		{
			
			//Detectamos el sistema operativo
			$sOp=php_uname() ;
			
			if(strstr($sOp, "Linux"))
				$sOp="Linux";
			
			if($sOp == "Linux")
			{
				}
			
						
				
			
		}
	}

/*implementacion de Oscar 30.08.2019*/
	//die($tabla.'\n'.$no_tabla.'\n'.$accion);
	if($tabla=='ec_oc_recepcion' && $no_tabla==0 && ($accion=='insertar' || $accion=='actualizar') ){//die("accion:".$accion);
	//consultamos el id de proveedor de la remision
		$sql="SELECT id_proveedor FROM ec_oc_recepcion WHERE id_oc_recepcion=$llave";
		$res=mysql_query($sql);   
        if(!$res){
            mysql_query("ROLLBACK");
            Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
        }
        $row=mysql_fetch_row($res);
        $id_proveedor=$row[0];
    //consultamos los detalles de la remision
        $sql="SELECT id_producto,presentacion_caja,precio_pieza,porcentaje_descuento FROM ec_oc_recepcion_detalle WHERE id_oc_recepcion=$llave";
		$res=mysql_query($sql);   
        if(!$res){
            mysql_query("ROLLBACK");
            Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
        }
    //actualizamos los proveedores producto
        while($row=mysql_fetch_row($res)){
        	$precio_caja=$row[1]*$row[2];
        	$sql="UPDATE ec_proveedor_producto SET precio=$precio_caja,presentacion_caja=$row[1],precio_pieza=$row[2] WHERE id_producto=$row[0] AND id_proveedor=$id_proveedor";
        	$eje=mysql_query($sql);
        	if(!$eje){
            	mysql_query("ROLLBACK");
            	Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
        	}
        //si tiene descuento
        	if($row[3]>0){
        		$sql="UPDATE ec_productos SET precio_venta_mayoreo=$row[3] WHERE id_productos=$row[0]";
        		$eje=mysql_query($sql);
        		if(!$eje){
            		mysql_query("ROLLBACK");
            		Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
        		}
        	}
        }
	}
/*Fin de cambio OScar 30.08.2019*/

/*implementacion de Oscar 28.08.2019 para generar la clave única del usuario*/
	if($tabla == 'sys_users' && ($no_tabla == 0)){
        if($accion == 'insertar')
        {
    		$sql="UPDATE sys_users SET codigo_barras_usuario=CONCAT('".$llave."',DATE_FORMAT(NOW(), '%Y%m%d%h%i%s')),id_equivalente=id_usuario WHERE id_usuario=$llave";
    		$res=mysql_query($sql);   
            if(!$res)
            {
                mysql_query("ROLLBACK");
                Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
            }
    	}
	}
/*Fin de cambio Oscar 28.08.2019*/


    if($tabla == 'ec_pedidos' && ($no_tabla == 0 || $no_tabla == 1))
    {
        if($accion == 'insertar')
        {
            $sql="SELECT surtir_aut_pedidos, (
			      	SELECT id_almacen 
			      	FROM ec_almacen
			      	WHERE es_almacen AND id_almacen>-1 AND id_sucursal = $user_sucursal
			      	HAVING MIN(prioridad) 
			      ) FROM ec_conf_pedidos";
            $res=mysql_query($sql);   
            if(!$res)
            {
                mysql_query("ROLLBACK");
                Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
            }
            
            $row=mysql_fetch_row($res);
            
            if($row[0] == '1')
            {
                $sql="INSERT INTO ec_movimiento_almacen(id_tipo_movimiento, id_usuario, id_sucursal, fecha, hora, observaciones, id_pedido, id_orden_compra, lote, id_maquila, id_transferencia, id_almacen)
                                             VALUES(2, $user_id, $user_sucursal, NOW(), NOW(), '', $llave, -1, '', -1, -1,$row[1])";
                                             
                                             
                $res=mysql_query($sql);   
                if(!$res)
                {
                    mysql_query("ROLLBACK");
                    Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
                }
                
                $id_me=mysql_insert_id(); 
                
                
                //Insertamos los productos
                $sql="INSERT INTO ec_movimiento_detalle(id_movimiento, id_producto, cantidad, cantidad_surtida, id_pedido_detalle, id_oc_detalle)
                                                 
                                                    SELECT
                                                    $id_me,
                                                    id_producto,
                                                    cantidad,
                                                    cantidad,
                                                    id_pedido_detalle,
                                                    -1
                                                    FROM ec_pedidos_detalle
                                                    WHERE id_pedido='$llave'
                                                 ";
                                                 
                $res=mysql_query($sql);   
                if(!$res)
                {
                    mysql_query("ROLLBACK");
                    Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
                }
                
                $sql="UPDATE ec_pedidos SET id_estatus=5 WHERE id_pedido='$llave'";
                
                $res=mysql_query($sql);   
                if(!$res)
                {
                    mysql_query("ROLLBACK");
                    Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
                }
                     
            }
        }
    }

	if($tabla == 'ec_precios')
    {
		if($accion == 'actualizar')
		{
			$sql="UPDATE ec_precios SET ultima_actualizacion=NOW() WHERE id_precio=$llave";
			mysql_query($sql) or die("Error en: $sql");
		}
	}
    

	if($tabla == 'ec_devolucion_transferencia')
	{
	
		if($accion == 'insertar')
		{	
		
			$sql="SELECT prefijo FROM sys_sucursales WHERE id_sucursal=$user_sucursal";
			
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			$row=mysql_fetch_row($res);
		
		
			$folio="DT".$row[0].date('Ymd').$llave;
			
			$sql="UPDATE ec_devolucion_transferencia SET folio='$folio' WHERE id_devolucion_transferencia='$llave'";
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			
			//Actualizamos el inventario
			
			
			//Salida del almacen y sucursal
			
			$sql="	INSERT INTO ec_movimiento_almacen
					(
						SELECT
						null,
						6,
						$user_id,
						t.id_sucursal_destino,
						NOW(),
						NOW(),
						CONCAT('SALIDA POR DEVOLUCION DE TRANSFERENCIA ', dt.folio),
						-1,
						-1,
						'',
						-1,
						-1,
						t.id_almacen_destino,
						NULL,
						'0000-00-00 00:00:00',
						NOW()
						FROM ec_devolucion_transferencia dt
						JOIN ec_transferencias t ON dt.id_transferencia = t.id_transferencia
						WHERE id_devolucion_transferencia=$llave
					)";
			
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			$id_mov=mysql_insert_id();
			
			//Insertamos el detalle
			$sql="	INSERT INTO ec_movimiento_detalle
					(
						SELECT
						null,
						'$id_mov',
						id_producto,
						cantidad,
						cantidad,
						-1,
						-1
						FROM ec_transferencia_producto_dev
						WHERE id_transferencia = $llave
					)";
					
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}		
			
			
			$id_al=0;
			
			//Buscamos el primer almacen de no ventas
			$sql="	SELECT
					id_almacen
					FROM ec_almacen
					WHERE id_sucursal=$id_sucursal_destino
					AND es_almacen=0
					ORDER BY prioridad";
					
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			if(mysql_num_rows($res) > 0)
			{
				$row=mysql_fetch_row($res);
				$id_al=$row[0];
			}
			//Buscamos el almacen de origen
			else
			{
				$sql="	SELECT
						id_almacen_origen
						FROM ec_transferencias
						WHERE id_transferencia=$id_transferencia";
				$res=mysql_query($sql);	  
				if(!$res)
				{
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}		
				$row=mysql_fetch_row($res);
				$id_al=$row[0];
						
			}
			
			//Insertamos el movimiento de almacen destino
			
			$sql="	INSERT INTO ec_movimiento_almacen
					(
						SELECT
						null,
						5,
						$user_id,
						t.id_sucursal_origen,
						NOW(),
						NOW(),
						CONCAT('ENTRADA POR DEVOLUCION DE TRANSFERENCIA ', dt.folio),
						-1,
						-1,
						'',
						-1,
						-1,
						$id_al,
						NULL,
						'0000-00-00',
						NOW()
						FROM ec_devolucion_transferencia dt
						JOIN ec_transferencias t ON dt.id_transferencia = t.id_transferencia
						WHERE id_devolucion_transferencia=$llave
					)";
			
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			$id_mov=mysql_insert_id();
			
			
			//Insertamos el detalle
			$sql="	INSERT INTO ec_movimiento_detalle
					(
						SELECT
						null,
						$id_mov,
						id_producto,
						cantidad,
						cantidad,
						-1,
						-1
						FROM ec_transferencia_producto_dev
						WHERE id_transferencia = $llave
					)";
					
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
		}	
	}



	if($tabla == 'ec_transferencias')
	{
	
		if($accion == 'actualizar' || $accion == 'insertar')
		{
			$sql="UPDATE ec_transferencias SET ultima_actualizacion=DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s') WHERE id_transferencia=$llave";
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
		}
	
		
		if($accion == 'insertar')
		{
			$sql="SELECT prefijo FROM sys_sucursales WHERE id_sucursal=$user_sucursal";
			
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			$row=mysql_fetch_row($res);
			
			
			$folio="TR".$row[0].date('Ymd').$llave;
			
			$sql="UPDATE ec_transferencias SET folio='$folio' WHERE id_transferencia='$llave'";
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			
			if($id_tipo == '5')
			{
			
			
				//Actualizamos el detalle

				$sql="UPDATE ec_transferencia_productos SET cantidad_salida=cantidad, cantidad_salida_pres=cantidad_presentacion WHERE id_transferencia=$llave";	
			
				$res=mysql_query($sql);	  
				if(!$res)
				{
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}
			
			
		//Insertamos el movimiento de salida de inventario
				$sql="INSERT INTO ec_movimiento_almacen(id_tipo_movimiento, id_usuario, id_sucursal, fecha, hora, observaciones, id_pedido, id_orden_compra, lote, id_maquila, id_transferencia, id_almacen)
												 VALUES(6, $user_id, $id_sucursal_origen, NOW(), NOW(), 'SALIDA DE TRANSFERENCIA', -1, -1, '', -1, $llave, $id_almacen_origen)";
											 
											 
				$res=mysql_query($sql);	  
				if(!$res)
				{
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}
			
				$id_ms=mysql_insert_id();		
			
				//Insertamos los productos
				$sql="INSERT INTO ec_movimiento_detalle(id_movimiento, id_producto, cantidad, cantidad_surtida, id_pedido_detalle, id_oc_detalle)
											 
											 	SELECT
											 	$id_ms,
											 	id_producto_or,
											 	cantidad_salida,
											 	cantidad_salida,
											 	-1,
											 	-1
											 	FROM ec_transferencia_productos
											 	WHERE id_transferencia='$llave'
											 ";
											 die($sql);
											 
				$res=mysql_query($sql);	  
				if(!$res)
				{
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}
			
			
				//Actualizamos el estatus de la transferencia

				$sql="UPDATE ec_transferencias SET id_estado=3 WHERE id_transferencia=$llave";	
			
				$res=mysql_query($sql);	  
				if(!$res)
				{
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}
				
				
				
			}
			
			  											 						 

		}
		
		if($accion == 'actualizar' && $no_tabla == 1){
	//Actualizamos el estatus de la transferencia
			$sql="UPDATE ec_transferencias SET id_estado=3 WHERE id_transferencia=$llave";	
			
			$res=mysql_query($sql);	  
			if(!$res){
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}else{
				mysql_query("COMMIT");
				//include('../especiales/sincronizacion/sincronizaTransferencia.php');
				if(subeTransfer($llave)){
					//echo 'ok';
				}
			}
		}
		
		
		if($accion == 'actualizar' && ($no_tabla == 2 || $no_tabla == 3)){		
	//Buscamos si existe algun caso de resolucion
			$sql="	SELECT
					COUNT(1)
					FROM ec_transferencia_productos
					WHERE id_transferencia=$llave
					AND cantidad_salida <> cantidad_entrada";
					
					
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			$row=mysql_fetch_row($res);		
		
			if($row[0] <= 0){
				$sql="UPDATE ec_transferencias SET id_estado=6 WHERE id_transferencia=$llave";	
				$res=mysql_query($sql);	  
				if(!$res){
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}else{
					mysql_query("COMMIT");//guardamos la transaccion
					//$sql1="SELECT id_global from ec_transferencias  where id_transferencia=$llave";
					//$ejeAux=mysql_query($sql1) or die(mysql_error().'<br>'.$sql1);
					//$idGT=mysql_fetch_row($ejeAux);
			//actualizamos sincronizacion en linea
						//if (include('../especiales/sincronizacion/sincronizaTransferencia.php')){
							//echo 'id_transfer global: '.$idGT[0];
						//	menu(2,$idGT[0]);
							//actualizaTransLinea($idGT[0]);
						//}else{
							//Muestraerror($smarty, "", "3", mysql_error(), $sql1, "contenido.php");
						//}		
				}
			}else{//actualizamos a resolucion
		//Actualizamos el estatus de la transferencia
				$sql="UPDATE ec_transferencias SET id_estado=5 WHERE id_transferencia=$llave";	
				$res=mysql_query($sql);	  
				if(!$res){
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}

			}//termina else
		
		}
	}


	if($tabla == "ec_productos")
	{
		//echo "Tipo: $tipo";
		
		
		//print_r($campos2);
		
		if($campos2[4][10] == '1')
		{
			$campos2[6][11]="barra_tres";
			$campos2[6][8]=1;
		}
		
		if($campos2[5][10] == '1')
		{
			$campos2[7][11]="barra_tres";
			$campos2[7][8]=1;
		}
	/*implementacion Oscar 13.09.2019 para actualizar el detalle de recpeciones y ordenes de compra cuando tiene el precio en 0*/
		$sql_per_esp="SELECT IF(ver=1 OR modificar=1,1,0) FROM sys_permisos WHERE id_perfil=$perfil_usuario AND id_menu=194";
		$eje=mysql_query($sql_per_esp);
		if(!$eje){
			mysql_query("ROLLBACK");
			Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
		}
		$r_perm=mysql_fetch_row($eje);

		if($r_perm[0]==1){
			$sql="SELECT id_proveedor,precio_pieza,presentacion_caja FROM ec_proveedor_producto WHERE id_producto=$llave";
			$eje_1=mysql_fetch_row($sql);
			while($r_1=mysql_fetch_row($eje_1)){
				$sql=" UPDATE ec_oc_recepcion_detalle rd
    				INNER JOIN ec_oc_recepcion r ON rd.id_oc_recepcion=r.id_oc_recepcion
    				SET rd.precio_pieza=$r_1[1],rd.monto=(rd.piezas_recibidas*$r[1])
    				WHERE r.id_proveedor=$r_1[0]
    				AND rd.id_producto=$llave
    				AND rd.precio_pieza=0;";
				$eje_2=mysql_query($sql);
				if(!$eje_2){
					mysql_query("ROLLBACK");
					Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
				}
			}//fin de while
		}

	/*Fin de cambio Oscar 13.09.2019*/
	}
	
	
	if($tabla == 'ec_pedidos' && $no_tabla == 3)
	{
		
		//echo "SI $tipo $no_tabla";
		
		if($tipo == 1)
		{
			//echo "SI 02";
			
			//print_r($campos2);
			
			$campos[1][11]="barra_tres";
			$campos[1][8]=1;
			$campos[23][8]=1;
			
			
			$campos[3][11]="barra_tres";
			$campos[3][8]=1;
			$campos[4][11]="barra_tres";
			$campos[4][8]=1;
			
			
			$campos[5][10]=date('Y-m-d');
			
			
			$sql="SELECT multifacturacion, id_razon_social FROM sys_sucursales WHERE id_sucursal=".$sucursal_id;
			
			$res=mysql_query($sql);    
            if(!$res)
            {
                mysql_query("ROLLBACK");
                Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
            }
            
            $row=mysql_fetch_row($res);
            if($row[0] == 1)
            {
                
            }
            else
             {
                 
                 $campos[4][8]=0;
                 $campos[4][11]="barra";
                 $campos[4][10]=$row[1];
             }
			
		}
	}


	if($tabla == 'ec_cuentas_por_pagar')
	{
		if($accion == 'actualizar')
		{
			//buscamos el total abonado
			$sql="SELECT
			      IF(SUM(monto) IS NULL, 0, SUM(monto)),
			      MAX(fecha)
			      FROM ec_oc_pagos
			      WHERE id_oc=$id_oc";
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			$row=mysql_fetch_row($res);
			$abono=$row[0];
			$fecmax=$row[1];
			
			$sql="UPDATE ec_cuentas_por_pagar
			      SET
			      abonado=$abono,
			      fecha_ultimo_pago='$fecmax'
			      WHERE id_cxp=$llave";
				  
			if(!mysql_query($sql))
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}	
			
			$campos[3][10]=$abono;		
		}
	}
	
	if($tabla == 'ec_cuentas_por_cobrar')
	{
		if($accion == 'actualizar')
		{
			//buscamos el total abonado
			$sql="SELECT
			      IF(SUM(monto) IS NULL, 0, SUM(monto)),
			      MAX(fecha)
			      FROM ec_pedido_pagos
			      WHERE id_pedido=$id_pedido";
			$res=mysql_query($sql);	  
			if(!$res)
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}
			
			$row=mysql_fetch_row($res);
			$abono=$row[0];
			$fecmax=$row[1];
			
			$sql="UPDATE ec_cuentas_por_cobrar
			      SET
			      abonado=$abono,
			      fecha_ultimo_cobro='$fecmax'
			      WHERE id_cxc=$llave";
				  
			if(!mysql_query($sql))
			{
				mysql_query("ROLLBACK");
				Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
			}	
			
			$campos[3][10]=$abono;		
		}
	}

	/*    
Código deshabiltado por Oscar 30.08.2019 porque si se deja duplicaría movimientos de almacen

	if($tabla == 'ec_ordenes_compra' && $no_tabla == 1)
    {
        if($accion == 'insertar')
        {
        	/*fin de cambio Oscar 08.11.2018*
            $sql="	SELECT
					sutir_oc_aut,
					(
			      		SELECT
						id_almacen 
				      	FROM ec_almacen
				      	WHERE es_almacen=1
						AND id_almacen > -1
						AND id_sucursal = $user_sucursal
			      		 
					)
					FROM ec_conf_oc";
            $res=mysql_query($sql);   
            if(!$res)
            {
                mysql_query("ROLLBACK");
                Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
            }
            
            $row=mysql_fetch_row($res);


			if($row[1] == '')
				Muestraerror($smarty, "", "SN", "No hay un almac&eacute;n primo configurado para esta sucursal", "NA", "postexcepcion.php");
            
            if($row[0] == '1')
            {
                $sql="INSERT INTO ec_movimiento_almacen(id_tipo_movimiento, id_usuario, id_sucursal, fecha, hora, observaciones, id_pedido, id_orden_compra, lote, id_maquila, id_transferencia, id_almacen)
                                             VALUES(1, $user_id, $user_sucursal, NOW(), NOW(), '', -1, $llave, '', -1, -1, $row[1])";
                                             
                                             
                $res=mysql_query($sql);   
                if(!$res)
                {
                    mysql_query("ROLLBACK");
                    Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
                }
                
                $id_me=mysql_insert_id(); 
                
                
                //Insertamos los productos
                $sql="INSERT INTO ec_movimiento_detalle(id_movimiento, id_producto, cantidad, cantidad_surtida, id_pedido_detalle, id_oc_detalle)
                                                 
                                                    SELECT
                                                    $id_me,
                                                    id_producto,
                                                    cantidad,
                                                    cantidad,
                                                    -1,
                                                    id_oc_detalle
                                                    FROM ec_oc_detalle
                                                    WHERE id_orden_compra='$llave'
                                                 ";
                                                 
                $res=mysql_query($sql);   
                if(!$res)
                {
                    mysql_query("ROLLBACK");
                    Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
                }
                
                $sql="UPDATE ec_ordenes_compra SET id_estatus_oc=4 WHERE id_orden_compra='$llave'";
                
                $res=mysql_query($sql);   
                if(!$res)
                {
                    mysql_query("ROLLBACK");
                    Muestraerror($smarty, "", "3", mysql_error(), $sql, "contenido.php");
                }
                     
            }
        }
    }*/
    


?>