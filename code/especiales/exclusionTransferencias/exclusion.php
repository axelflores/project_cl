<?php
	include('../../../conectMin.php');
//armammos la consulta
	$sql="SELECT 
			et.id_exclusion_transferencia,
			et.id_producto,
			p.orden_lista,
			p.nombre,
			et.observaciones,
			CONCAT(et.fecha,'<br>',et.hora)
		FROM ec_exclusiones_transferencia et
		LEFT JOIN ec_productos p ON et.id_producto=p.id_productos
		WHERE 1";
	$eje=mysql_query($sql)or die("Error al consultar los productos excluidos!!!\n\n".$sql."\n\n".mysql_error());

?>
<style type="text/css">
	.global{background-image: url('../../../img/img_casadelasluces/bg8.jpg');width: 100%;height:100%;padding: 0;margin:0;position: absolute;top:0;left:0;}

	.enc{position: relative;top:0;height:80px;background:#83B141;}
	
	#busc{padding:10px;position: relative;top:10px; width:30%; left:1%;border-radius: 8px;}

	#res_busc{position:relative;width:30%;top:20px;left:1%;background: white;height: 300px;z-index:3;display: none;overflow: hidden;}
	
	#contenido{width: 100%;}
	
	th{padding: 10px;background: rgba(225,0,0,.6);color: white;}

	#contenido>p{font-size: 22px; left:15px;position: relative;}

	#cont_tabla{width: 101%;height: 400px;border:1px solid;position: relative;top:-3px;overflow: scroll;}

	.oculto{display: none;}

	.footer{position: absolute;height:70px;width:100%;background:#83B141;bottom:0;}

	.bt_regresar{text-decoration:none;border:1px solid;color:black;padding: 8px;background: gray;border-radius: 5px;}
	.bt_regresar:hover{background:rgba(0,0,0,.8);color: white;}
</style>

<!DOCTYPE html>
<html>
<head>
	<title>Exclusiones de Transferencias</title>
<script type="text/javascript" src="../../../js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="funciones.js"></script>
</head>
<body>
	<div class="global">
		<div class="enc">
			<input type="text" id="busc" onkeyup="busca(event);"> <img src="../../../img/especiales/buscar.png" width="40px" style="position:relative;top:25px;left:20px;">
			<div id="res_busc"></div>
		</div>
	<!--contenido-->
		<div id="contenido">
		<p><b>Listado de Productos Excluidos</b></p>
		<center>
			<table style="width:80%">
				<tr>
					<th width="15%">Orden Lista</th>
					<th width="30%">Producto</th>
					<th width="25%">Observaciones</th>
					<th width="15%">Fecha</th>
					<th width="15%">Quitar</th>
				</tr>
				<tr>
					<td colspan="5">
						<div id="cont_tabla">
							<?php
								echo '<table width="100%" id="tabla_exclusion">';
									$c=0;//inicializamos el contador en 0
									while($r=mysql_fetch_row($eje)){
										$c++;//incrementamos contador
									//asignamos el color
										if($c%2==0){
											$color="#E6E8AB";
										}else{
											$color="#BAD8E6";
										}
										echo '<tr id="fila_'.$c.'" style="background:'.$color.';" tabindex="'.$c.'" onclick="resalta_fila('.$c.');">';
											echo '<td class="oculto" id="0_'.$c.'">'.$r[0].'</td>';
											echo '<td class="oculto" id="1_'.$c.'">'.$r[1].'</td>';
											echo '<td width="15%" id="2_'.$c.'" align="center">'.$r[2].'</td>';
											echo '<td width="30%" id="3_'.$c.'">'.$r[3].'</td>';
											echo '<td width="25%" id="4_'.$c.'" onclick="edita_celda('.$c.');">'.$r[4].'</td>';
											echo '<td width="15%" id="5_'.$c.'" align="center">'.$r[5].'</td>';
											echo '<td width="14%" align="center"><a href="javascript:elimina('.$c.');"><img src="../../../img/especiales/delete.png" width="40px;"></a></td>';
										echo '</tr>';
									}
								echo '</table>'; 	
							?>
						</div>
					</td> 
				</tr>
			</table>
			<input type="hidden" id="filasTotales" value="<?php echo $c;?>">
		</center>
		</div>
	<!--fin de cantenido-->

		<div class="footer">
			<p align="center">
				<a href="../../../index.php?" class="bt_regresar">Regresar al Panel</a>
			</p>
		</div>
	</div>
</body>
</html>
