<?php
	include("../../../../conectMin.php");
//recibimos el id de la órden de compra
	$id_oc=$_GET['href'];
//preparamos info inicial
	$sql="SELECT oc.folio,prov.nombre_comercial,prov.id_proveedor FROM ec_ordenes_compra oc
		LEFT JOIN ec_proveedor prov ON oc.id_proveedor=prov.id_proveedor WHERE oc.id_orden_compra=$id_oc";
	$eje=mysql_query($sql)or die("Error al consultar e folio de OC!!!\n\n".$sql);
	$row=mysql_fetch_row($eje);
	$folio=$row[0];
	$proveedor=$row[1];
	$id_proveedor=$row[2];
	echo '<input type="hidden" value="'.$id_proveedor.'" id="id_prov">';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Recepción de órdenes de Compra</title>
<script type="text/javascript" src="../../../../js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="funcionesRecepcPed.js"></script>
<style type="text/css">
	*{
		margin:0;
	}
	.global{
		background-image:url("../../../../img/img_casadelasluces/bg8.jpg");
		height: 635px;
		width: 100%;
	}
	.enc{
		padding: 10px; 	 
		height:50px;
		background: #718B1E;
	}
	.enc_grid{
		background: rgba(225,0,0,.5);
		color: white;
		padding: 10px;
	}
	#entrada_temporal{
		width:96%;
		height:30px;
		text-align: right;
	}
	.check{
  		/*-webkit-transform: scale(2); /* Safari and Chrome */
  		display:none;
	}
	#input_buscador{padding:10px;width: 250px;border-radius: 15px;}
	#res_busc{
		background: white;
		width:375px;
		height: 200px;
		z-index:2;
		position:absolute;
		overflow: auto;
		display:none;
	}
	.subtitulo{
		font-size:25px;
		padding: 10px;
	}
	.footer{
		position:absolute;
		bottom: 0;
		height:80px;
		width: 100%;
		background:  #718B1E;
	}
	.btn_footer{
		border-radius: 10px;
		padding: 10px;
		border:1px solid white;
		background: silver;
		text-decoration: none;
		color: black;
	}
	.btn_footer:hover{
		background: rgba(0,0,0,.6);
		color: white;
	}
	.info_inicial{
		width:120px;
		padding: 10px;
		border-radius: 10px;
		font-size:16px;
		text-align: right;
		color: black;
	}
	.contenido_tabla{
		height: 350px;
		overflow: scroll;
		width: 90%;
	}
	.opc_busc{
		height: 30px;
	}
	.opc_busc:hover{
		background:rgba(92, 124, 14,.7);
		color:white;
	}
	#res_busc_folio{position: absolute;overflow: auto;width: 22%;height: 250px;z-index: 2;background: white;left: 46%;display: none;color: black;}
</style>
</head>
<body>
	<div class="global">
		<div class="enc" onclick="document.getElementById('res_busc_folio').style.display='none';">
			<input type="hidden" id="id_recepcion" value="0">
			<table width="95%" style="position: absolute;top:0;">
				<tr>
					<td>
						<input type="text" id="input_buscador" onkeyup="busca_txt(event);">
						<div id="res_busc"></div>
					</td>
					<td align="center" style="color:white;font-size:18px;">
						Proveedor:<br><input type="text" class="info_inicial" value="<?php echo $proveedor;?>" disabled>
					</td>
					<td align="center" style="color:white;font-size:18px;">
						Folio OC:<br><input type="text" class="info_inicial" value="<?php echo $folio;?>" disabled>
					</td>
					<td align="center" style="color:white;font-size:18px;">
						Folio de Nota:<br><input type="text" class="info_inicial" id="ref_nota_1" style="width:150px;" onkeyup="busca_folio(event,this);">
						<div id="res_busc_folio"></div>						
					</td>
					<td  align="center" style="color:white;font-size:18px;">
						Monto de Nota:<br><input type="number" class="info_inicial" id="monto_nota" style="width:150px;" disabled>
					</td>
					<td  align="center" style="color:white;font-size:18px;">
						Piezas en Remision:<br><input type="number" class="info_inicial" id="pzas_remision" style="width:150px;" disabled>
					</td>
					<td  align="center" style="color:white;font-size:18px;">
						Piezas Recibidas:<br><input type="number" class="info_inicial" id="pzas_recibidas" style="width:150px;" disabled>
					</td>
				</tr>
			</table>
		</div id="contenido">
		<center>
		<p align="left" class="subtitulo"><b>Productos</b></p>
			<table width="90%" onclick="document.getElementById('res_busc_folio').style.display='none';">
				<tr>
					<th class="enc_grid" width="7%">Ubic</th>
					<th class="enc_grid"  width="20%">Descricpión</th>
					<th class="enc_grid" width="8%">Pendiente de Recibir</th>
					<th class="enc_grid" width="8%">Presentación por Caja</th>
					<th class="enc_grid" width="8%">Cajas Recibidas</th>
					<th class="enc_grid" width="8%">Piezas Recibidas</th>
					<th class="enc_grid" width="8%">Precio Pieza</th>
					<th class="enc_grid" width="8%">% Desc</th>
					<th class="enc_grid" width="8%">Total Piezas</th>
					<th class="enc_grid" width="8%">Monto</th>
					<th class="enc_grid">Quitar</th>
				</tr>
				</table>
				<div class="contenido_tabla">
				<table width="100%" onclick="document.getElementById('res_busc_folio').style.display='none';">
				<?php
					
					$sql="SELECT
							/*0*/ax1.id_oc_detalle,
							/*1*/ax1.id_producto,
  							/*2*/ax1.nombre,
    						/*3*/ax1.cantidad,
    						/*4*/ax1.recibido,
    						/*5*/0,
    						/*6*/0,
							/*7*/pp.presentacion_caja,
							/*8*/pp.precio_pieza,
							/*9*/ax1.ubicacion_almacen,
							/*10*/ax1.id_proveedor_producto  
								FROM(
									SELECT 
										ocd.id_oc_detalle,
	  				 				 	ocd.id_producto,
	   								 	p.nombre,
	   								 	p.ubicacion_almacen,
	   								 	ocd.cantidad,
	   								 	ocd.cantidad_surtido AS recibido,
	   						 			ocd.id_proveedor_producto
	   						 		FROM ec_productos p
	    							LEFT JOIN ec_oc_detalle ocd ON p.id_productos=ocd.id_producto
	  							 	LEFT JOIN ec_ordenes_compra oc ON ocd.id_orden_compra=oc.id_orden_compra 
	  							 	WHERE oc.id_orden_compra=$id_oc 
	  							 	AND oc.observaciones=''
	    							GROUP BY ocd.id_producto
	  							 	ORDER BY ocd.id_oc_detalle ASC
    							)ax1 
							LEFT JOIN ec_proveedor_producto pp ON ax1.id_producto=pp.id_producto
							LEFT JOIN ec_ordenes_compra oc2 ON pp.id_proveedor=oc2.id_proveedor AND oc2.id_orden_compra=$id_oc
							WHERE ax1.cantidad>ax1.recibido
							AND pp.id_proveedor_producto=ax1.id_proveedor_producto
							GROUP BY ax1.id_producto
	  						ORDER BY ax1.id_oc_detalle ASC";
						//	die($sql);
					$eje=mysql_query($sql)or die("Error al consultar info del detalle de la órden de compra!!!\n\n".$sql."\n\n".mysql_error());
					$c=0;
					while($r=mysql_fetch_row($eje)){
						$c++;//incrementamos el contador
						if($c%2==0){
							$color="#E6E8AB";
							}else{
								$color="#BAD8E6";
							}
						echo '<tr style="background:'.$color.';" tabindex="'.$c.'" id="fila_'.$c.'">';
						//ubicacion del almacen
							echo '<td id="-1_'.$c.'" width="7%" onclick="editaCelda(-1,'.$c.');">'.$r[9].'</td>';
						//id del detalle de oc
							echo '<td id="0_'.$c.'" style="display:none;">'.$r[0].'</td>';
						//id del producto
							echo '<td id="1_'.$c.'" style="display:none;">'.$r[1].'</td>';
						//nombre del producto
							echo '<td id="2_'.$c.'" style="padding:10px;" width="20%">'.$r[2].'</td>';
						//pendiente de recibir
							echo '<td id="3_'.$c.'" align="right" width="8%" title="Se pidieron '.$r[3].' piezas, se han recibido '.$r[4].' piezas">'.($r[3]-$r[4]).'</td>';//title="Se han recibido '.$r[4].' piezas, faltan '.$r[3]-$r[4].' piezas por recibir"
						//presentación por caja
							echo '<td id="4_'.$c.'" align="right" width="8%" onclick="editaCelda(4,'.$c.');">'.$r[7].'</td>';
						//cajas recibidas
							echo '<td id="5_'.$c.'" align="right" width="8%" onclick="editaCelda(5,'.$c.');">'.$r[5].'</td>';
						//piezas recibidas
							echo '<td id="6_'.$c.'" align="right" width="8%" onclick="editaCelda(6,'.$c.');">'.$r[6].'</td>';
						//precio pieza
							echo '<td id="7_'.$c.'" align="right" width="8%"  onclick="editaCelda(7,'.$c.')">'.$r[8].'</td>';
						//porcentaje de descuento
							echo '<td id="11_'.$c.'" align="right" width="8%"  onclick="editaCelda(11,'.$c.')">0</td>';
						//monto
							echo '<td id="9_'.$c.'" align="right" width="8%" >0</td>';
						//monto
							echo '<td id="8_'.$c.'" align="right" width="8%"  onclick="editaCelda(8,'.$c.')">0</td>';
						//id de proveedor_producto
							echo '<td id="12_'.$c.'" style="display:none;">'.$r[10].'</td>';
						//quitar/cancelar
							echo '<td align="center" width="7.25%">';//onclick="editaCelda(-1,'.$c.')"
							echo'<input type="checkbox" id="10_'.$c.'" class="check"><img src="../../../../img/especiales/cierra.png" width="40" onclick="quitar_fila('.$c.');"></td>';
						echo '</tr>';
					}
				?>
				</tbody>
			</table>
			</div><!--Cerramos el div de la tabla-->
		</center>
		
		<input type="hidden" id="filas_totales" value="<?php echo $c;?>">		
		<input type="hidden" id="id_oc" value="<?php echo $id_oc;?>">

		<div>
		<div class="footer">
		<br>
			<table width="100%">
				<tr>
					<td width="33.3%" align="center">
						<a href="../../../../index.php" class="btn_footer"><b>Regresar al panel</b></a>
					</td>
					<td width="33.3%" align="center">
						<button type="button" onclick="guarda_recepcion();" style="padding:10px;border-radius:10px;">
							<img src="../../../../img/especiales/save.png" width="20px"><br>
							Guardar
						</button>
					</td>
					<td width="33.3%" align="center">
						<a href="../../../general/listados.php?tabla=ZWNfb3JkZW5lc19jb21wcmE=&no_tabla=Mg==" class="btn_footer"><b>Ver listado</b></a>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>