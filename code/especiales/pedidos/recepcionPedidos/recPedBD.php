<?php
	include("../../../../conectMin.php");
	$fl=$_POST['flag'];
	$id_orden=$_POST['oc'];
/*Implementacion Oscar 23.07.2019 para modificar la ubicacion directo en la tabla de pedidos*/
	if($fl=='ubicacion'){
		$val=$_POST['valor'];
		$id_prod=$_POST['id'];
		$sql="UPDATE ec_productos SET ubicacion_almacen='$val',sincronizar=1 WHERE id_productos=$id_prod";
		$eje=mysql_query($sql)or die("Error al actualizar la ubicacion del almacen!!!\n".mysql_error()."\n".$sql);
		die('ok');
	}

	if($fl=='descuento'){
		$val=$_POST['valor'];
		$id_prod=$_POST['id'];
		$sql="UPDATE ec_productos SET precio_venta_mayoreo='$val',sincronizar=1 WHERE id_productos=$id_prod";
		$eje=mysql_query($sql)or die("Error al actualizar la ubicacion del almacen!!!\n".mysql_error()."\n".$sql);
		die('ok');
	}
/**/
/*implementación Oscar 11.02.2018 para buscar folios de notas de proveedor*/
//buscador de folios
	if($fl=='busca_folios'){
		$id_proveedor=$_POST['id_pro'];
		$sql="SELECT 
				ocr.id_oc_recepcion,/*0*/
				ocr.folio_referencia_proveedor,/*1*/
				ocr.monto_nota_proveedor,/*2*/
				prov.nombre_comercial,/*3*/
				ocr.piezas_remision,/*4*/
				ocr.piezas_recepcion/*5*/
			FROM ec_oc_recepcion ocr
			LEFT JOIN ec_proveedor prov ON ocr.id_proveedor=prov.id_proveedor
			WHERE ocr.id_oc_recepcion>0 AND prov.id_proveedor=$id_proveedor AND (";
	//agudizamos busqueda por coincidencias
		$clave=explode(" ",$_POST['txt']);
		for($i=0;$i<sizeof($clave);$i++){
			if($i>0){
				$sql.=" AND ";
			}
			$sql.="ocr.folio_referencia_proveedor LIKE '%".$clave[$i]."%'";
		}//fin de for i
		$sql.=")";//cerramos el AND de la consulta
		//die('ok|'.$sql);
		$eje=mysql_query($sql)or die("Error al consultar coincidencias de folio!!!<br>".$sql."<br>".mysql_error());

		echo 'ok|';
		if(mysql_num_rows($eje)<=0){
			die('sin coincidencias');
		}
		echo '<table width="100%">';
		$c=0;
		while($r=mysql_fetch_row($eje)){
			$c++;
			echo '<tr tabindex="'.$c.'" onclick="carga_folio_recepcion('.$r[0].',\''.$r[1].'\','.$r[2].','.$r[4].','.$r[5].');">';
				echo '<td width="100%">'.$r[1].' - '.$r[3].' $'.$r[2].'</td>';
			echo '<tr>';
		}	
		die('</table>');
	}
/*fin de cambio Oscar 11.2.2018*/

//buscador de productos
	if($fl==1){
	//armamos la consulta
		$sql="SELECT p.id_productos,p.nombre 
		FROM ec_productos p 
		LEFT JOIN ec_oc_detalle ocd ON p.id_productos=ocd.id_producto
		LEFT JOIN ec_ordenes_compra oc ON ocd.id_orden_compra=oc.id_orden_compra
		WHERE oc.id_orden_compra=$id_orden AND (";
	//precisamos la búsqueda
		$clave=explode(" ",$_POST['txt']);

		for($i=0;$i<sizeof($clave);$i++){
			if($clave[$i]!='' && $clave[$i]!=null){
				if($i>0){
					$sql.=" AND ";
				}
				$sql.="p.nombre LIKE '%".$clave[$i]."%'";
			}
		}//fin de for i
	//cerramos el parentesis de las condiciones
		$sql.=")";
	//ejecutamos consulta
	$eje=mysql_query($sql)or die("Error al buscar coincidencias!!\n\n".$sql."\n\n".mysql_error());
//regresamos resultados
	echo 'ok|<table width="100%">';
	$tab=0;
	while($row=mysql_fetch_row($eje)){
		$tab++;
		echo '<tr tabindex="'.$tab.'" id="opc_'.$tab.'" class="opc_busc" onkeyup="valida_opc(event,'.$tab.');" onclick="valida_opc(\'click\','.$tab.');">';
			echo '<td style="display:none;" id="val_opc_'.$tab.'">'.$row[0].'</td>';
			echo '<td>'.$row[1].'</td>';
		echo '</tr>';	
	}	
	echo '</table>';
	echo '<input type="hidden" id="opc_totales" value="'.$tab.'">';
	}//fin de if $fl==1 (si es buscador)

//insertar recepción
	if($fl==2){
		$ref_prov=$_POST['ref'];
		$id_proveedor=$_POST['id_prov'];
		$id_recepcion=$_POST['id'];
		$monto_recepcion=$_POST['mt_nota'];
		mysql_query("BEGIN");//marcamos el inicio de la transaccion

	//insertamos el detalle de la Recepción
		$dat=$_POST['datos'];
		$dato=explode("|", $dat);

	//validamos que el numero de piezas no sea mayor al marcado en la remision 
		$sql="SELECT piezas_remision,piezas_recepcion FROM ec_oc_recepcion WHERE id_oc_recepcion=$id_recepcion";
		$eje=mysql_query($sql)or die("Error al consultar el numero de piezas de la remision!!!");
		$r=mysql_fetch_row($eje);
		$piezas_de_remision=$r[0];
		$piezas_ya_recibidas=$r[1];
		for($i=0;$i<sizeof($dato);$i++){
			$d=explode("~",$dato[$i]);
			if($d[0]!='invalida'){
				$piezas_ya_recibidas+=$d[1];
			}
		}
		if($piezas_ya_recibidas>$piezas_de_remision){
			die("No se pueden recibir mas piezas de las establecidas en la Remisión\n\nPiezas en remisión: ".$piezas_de_remision."\nPiezas recibidas: ".
				$piezas_ya_recibidas);
		}

		//print_r($dato);
		//die("");
		for($i=0;$i<sizeof($dato);$i++){
			$sql="";
			$d=explode("~",$dato[$i]);
		//verificamos si el producto ya existe en la recepcion
			$sql="SELECT id_oc_recepcion_detalle FROM ec_oc_recepcion_detalle 
			WHERE id_oc_recepcion=$id_recepcion AND id_producto=IF('$d[0]'='invalida','$d[1]','$d[0]')";
			//echo $sql;
			$eje=mysql_query($sql);
			if(!$eje){
				$error=mysql_error();
				mysql_query("ROLLBACK");//cancelamos transacción
				die("Error al insertar detalle de Recepción de Órden de Compra!!!\n\n".$sql."\n\n".$error);
			}
			$nvo=1;
			if(mysql_num_rows($eje)==1){
		//echo 'num: '.mysql_num_rows($eje);
				$nvo=0;
				$r=mysql_fetch_row($eje);
				$id_recepcion_detalle=$r[0];
			}

			//if($d[0]=='invalida'){
			//si es invalidar
				if($nvo==0){
					$sql="UPDATE ec_oc_recepcion_detalle SET piezas_recibidas=(piezas_recibidas+IF('$d[0]'='invalida',0,'$d[1]'))/*,
							monto=(precio_pieza*piezas_recibidas)-((precio_pieza*piezas_recibidas)*porcentaje_descuento)*/
						WHERE id_oc_recepcion_detalle=$id_recepcion_detalle";	
						//die($sql);
				}else if($d[3]!=0||$d[1]!=0||$d[0]!='invalida'){
					$sql="INSERT INTO ec_oc_recepcion_detalle VALUES(null,/*1*/
					$id_recepcion,/*2*/
					IF('$d[0]'='invalida',$d[1],'$d[0]'),/*3*/
					IF('$d[0]'='invalida',0,'$d[1]'),/*4*/
					IF('$d[0]'='invalida',0,'$d[4]'),/*5*/
					IF('$d[0]'='invalida',0,'$d[2]'),/*6*/
					IF('$d[0]'='invalida',0,'$d[3]'),/*7*/
					IF('$d[0]'='invalida',0,1),/*8*/
					IF('$d[0]'='invalida','Se recibió en ceros',''),/*9*/
					IF('$d[0]'='invalida',0,'$d[5]')/*10*/)";
				}
			if($sql!=""){
			//ejecutamos la consulta que inserta el detalle
				$eje=mysql_query($sql);
				if(!$eje){
					$error=mysql_error();
					mysql_query("ROLLBACK");//cancelamos transacción
					die("Error al insertar detalle de Recepción de Órden de Compra!!!\n\n".$sql."\n\n".$error);
				}
			//actualizamos lo recibido a la orden de compra
				$observaciones='se recibio en 0';
				if($d[0]=='invalida'){
					$sql="DELETE FROM ec_oc_detalle WHERE id_producto=$d[1] AND id_orden_compra=$id_orden";
					$eje=mysql_query($sql);
					if(!$eje){
						$error=mysql_error();
						mysql_query("ROLLBACK");//cancelamos transacción
						die("Error al eliminar del detalle de Orden de Compra!!!\n\n".$sql."\n\n".$error);
					}
					$d[0]=$d[1];
					$d[1]=0;
				}

				$sql="UPDATE ec_oc_detalle SET cantidad_surtido=(cantidad_surtido+$d[1]) WHERE id_producto=$d[0] AND id_orden_compra=$id_orden";
				$eje=mysql_query($sql);
				if(!$eje){
					$error=mysql_error();
					mysql_query("ROLLBACK");//cancelamos transacción
					die("Error al actualizar piezas recibidas en la Orden de Compra!!!\n\n".$sql."\n\n".$error);
				}
		/*implementacion Oscar 16.08.2019*/
		//die($d[6]);
				if($d[0]!='invalida' && $d[6]!=''){
				//consultamos la clave de proveedor
					$sql="SELECT clave_proveedor FROM ec_proveedor_producto WHERE id_proveedor_producto=$d[6]";
					//die($sql);
					$eje=mysql_query($sql);
					if(!$eje){
						$error=mysql_error();
						mysql_query("ROLLBACK");//cancelamos transacción
						die("Error al consultar el codigo de proveedor-producto!!!\n\n".$sql."\n\n".$error);
					}
					$r_1=mysql_fetch_row($eje);
			//introducimos el nuevo código de proveedor si no existe
				//corroboramos si esta clave ya existe en la tabla de productos; de lo contrario la insertamos
					$sql="SELECT COUNT(*) FROM ec_productos WHERE id_productos=$d[0] AND clave LIKE '%$r_1[0]%'";
					$eje=mysql_query($sql);
					if(!$eje){
						$error=mysql_error();
						mysql_query("ROLLBACK");//cancelamos transacción
						die("Error al verificar el codigo de proveedor en la tabla de productos!!!\n\n".$sql."\n\n".$error);
					}
					$r_2=mysql_fetch_row($eje);
					if($r_2[0]==0){
					//actualizamos el codigo de proveedor producto en la tabla de productos
						$sql="UPDATE ec_productos SET clave=CONCAT(clave,',','$r_1[0]') WHERE id_productos=$d[0]";
						$eje=mysql_query($sql);
						if(!$eje){
							$error=mysql_error();
							mysql_query("ROLLBACK");//cancelamos transacción
							die("Error al actualizar el codigo alfanumerico en la tabla de productos!!!\n\n".$sql."\n\n".$error);
						}
					}
					//die($sql);
				//actualizamos el proveedor producto
					$precio_caja=$d[2]*$d[4];
					$sql="UPDATE ec_proveedor_producto SET precio_pieza=$d[2],presentacion_caja=$d[4],precio=$precio_caja WHERE id_proveedor_producto=$d[5]";
					$eje_prov=mysql_query($sql);
					if(!$eje_prov){
						$error=mysql_error();
						mysql_query("ROLLBACK");//cancelamos transacción
						die("Error al actualizar los parametros de proveedor producto!!!\n\n".$sql."\n\n".$error);
					}

				}//fin de si el registro es valido
		/*fin de cambio Oscar 16.08.2019*/
			}//fin de if la consulta no esta vacía
		}//fin de for i
	//actualizamos las piezas recibidas
		$sql="UPDATE ec_oc_recepcion 
				SET piezas_recepcion=(SELECT SUM( IF(id_oc_recepcion_detalle IS NULL,0,piezas_recibidas) )
				FROM ec_oc_recepcion_detalle WHERE id_oc_recepcion=$id_recepcion ) 
			WHERE id_oc_recepcion=$id_recepcion";
		$eje=mysql_query($sql);
		if(!$eje){
			$error=mysql_error();
			mysql_query("ROLLBACK");//cancelamos la transaccion
			die("Error al actualizar las piezas recibidas en la remisión!!!\n".$error."\n".$sql);
		}
	//insertamos la relacion entre la orden de compra y la recepcion
		$sql="INSERT INTO ec_relaciones_oc_recepcion VALUES(null,$id_orden,$id_recepcion,now())";
		$eje=mysql_query($sql);
		if(!$eje){
			$error=mysql_error();
			mysql_query("ROLLBACK");//cancelamos la transaccion
			die("Error al insertar la relación entre la recepcion y la orden de compra!!!\n".$error."\n".$sql);
		}
	//actualizamos el status de la orden de compra
		$sql="UPDATE ec_ordenes_compra 
				SET id_estatus_oc=IF( 
							(SELECT SUM(cantidad)-SUM(cantidad_surtido) FROM ec_oc_detalle WHERE id_orden_compra=$id_orden)=0
							OR
							(SELECT SUM(cantidad)-SUM(cantidad_surtido) FROM ec_oc_detalle WHERE id_orden_compra=$id_orden) IS NULL,
							4,
							3
				)
			WHERE id_orden_compra=$id_orden";
		$eje=mysql_query($sql);
		if(!$eje){
			$error=mysql_error();
			mysql_query("ROLLBACK");//cancelamos la transaccion
			die("Error al actualizar el status de orden de compra!!!\n".$error."\n".$sql);
		}
		mysql_query("COMMIT");//autorizamos transacción
		echo 'ok|';
	}//fin de if $fl==2 (Recibir pedido)
?>