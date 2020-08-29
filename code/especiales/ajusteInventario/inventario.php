<style>
	*{
		margin: 0;
	}
	#global{
		width: 100%;
		height: 100%;
		padding: 0;
		background-image: url('../../../img/img_casadelasluces/bg8.jpg');
		/*border: 2px solid green;*/
	}
	#contenido{
		width:60%;
		height: 65%;
		/*border:2px solid red;*/
	}
	.ex{
		width:100px;
		height:30px;
		vertical-align:middle;
		border-radius:5px;
		color:red;
		border: 1px solid white;
		background:transparennt;top:10px;
	}
	.ex:hover{
		background:green;
		/*color:green;*/
	}
	.titulo{
		color:white;
		font-size:16px;
	}
	a{
		text-decoration: none;
		color:white;
	}
	.informa{
		background: transparent;
		text-align: right;
		border:0;
		width: 100%;
		height: 100%;
		color:black;
		padding: 8px;
	}
	.guarda{
		padding: 10px;
		border:2px solid gray;
		border-radius: 15px;
		background: #A59D95;
		color: white;
	}
	.guarda:hover{
		background: white;
		color:green;
	}
	.fi{
		height: 40px;
	}
	.footer{
		background: #83B141;
		width: 100%;
		bottom: 0;
		height: 50px;
		position: fixed;
	}
	.emerge{
		background:rgba(0,0,0,.8);
		width:100%;
		height:100%;
		position:absolute;
		display: block;
		z-index: 100;
	}
</style>
<?php
	include('../../../conect.php');//incluimos libreria de conexion
	extract($_GET);
	if($sucursal_id==-1){
		$sucursal=1;	
	}else if($sucursal_id>=1){
		$sucursal=$sucursal_id;
	}
	if(isset($id_suc_adm)){
		$id_suc_adm=base64_decode($id_suc_adm);//decodificamos la variable
		$sucursal=$id_suc_adm;
	}
		$WHERE=' AND ma.id_sucursal='.$sucursal;
?>
<script language="JavaScript" src="../../../js/jquery-1.10.2.min.js"></script>
<script language="Javascript" src="js/funcionesAjusteInvntario.js"></script>
<div id="global" onclick="oculta_res_busc();">
<center>	
	<div id="emergente" class="emerge">
		<center>
		<div style="background:rgba(0,0,0,.7);border:1px solid red;top:170px;position:relative;width:60%;height:300px;border-radius:15px;">
			<div id="cont_vta_emerge">
				<p id="info_emerge" style="color:white;font-size:25px;" align="center">
					<br><br><br><br>
					<span style="color:white;font-size:30px;">Cargando inventario...</span><br>
					<img src="../../../img/img_casadelasluces/load.gif" height="100px" width="100px">
				</p>
			</div>
		</div>
		</center>
	</div>
	<div id="encabezado" style="width:100%;height:80px;background:#83B141;">
		<table style="padding:10px;" width="100%" >
			<tr style="padding:5px;">
				<td width="50%">
					<p>
						<?php include('buscador/buscador.php');?>
					</p>				
				</td>
				<td width="25%">
					<p style="color:white;">	
						Sucursal:<?php include('controlaSucursales.php');?>
					</p>
				</td>
				<td width="25%">
				
				<td width="25%">
					<p>
						<input type="button" value="Guardar Modificaciones" class="guarda" onclick="<?php echo 'guarda('.$sucursal.');';?>">
						<input type="hidden" id="cambios" >
					</p>
				</td>
			</tr>			
		</table>
	</div>
		<br><!--Damos un espacio-->
	<table border="0" id="enc" width="60%" style="position:absolute;left:20%;border-radius:5px;height:60px;background:rgba(225,0,0,.6);">
				<tr>
					<!--<td width="10%" align="center" class="titulo">Ubicación</td>-->
					<td width="10%" align="center" class="titulo">Ubic Alm</td>
					<td width="10%" align="center" class="titulo">Orden Lista</td>
					<td width="35.5%" align="center" class="titulo">Descripcion</td>
					<td width="10%" align="center" class="titulo">Temp</td>
					<td width="10%" align="center" class="titulo">Inv virtual</td>
					<td width="10%" align="center" class="titulo">Inv fisico</td>
					<td align="center" class="titulo">Diferencia</td>
					<td width="20px"></td>
				</tr>
			</table>
			<br><br><br>
	<div id="contenido">
		<div id="listado" style="text-align:center;width:100%;overflow:scroll;height:430px;">
		<center>
			<table id="formInv" width="100%">
			<?php
		/*Modificacion Oscar 22.02.2018*/
				$c_a="SELECT id_almacen FROM ec_almacen WHERE id_sucursal=$sucursal AND es_almacen=1";
				$eje=mysql_query($c_a)or die("Error al consultar almacen primario!!!\n\n".$c_a."\n\n".mysql_error());
				$alm=mysql_fetch_row($eje);
				$sql="SELECT 
							p.id_productos,p.nombre,
							IF(md.id_producto IS NULL,0,IF(SUM(md.cantidad*tm.afecta) IS NULL,0,SUM(md.cantidad*tm.afecta))),
							p.orden_lista,
					/*Implementación Oscar 27.02.2019 para agregar ubicación de almacén*/
							IF($sucursal=1,p.ubicacion_almacen,sp.ubicacion_almacen_sucursal)
					/*Fin de cambio Oscar 27.02.2019*/
						FROM ec_productos p /*ON i.id_producto=p.id_productos*/
					/**/
						LEFT JOIN sys_sucursales_producto sp ON p.id_productos=sp.id_producto AND sp.id_sucursal IN($sucursal) AND sp.estado_suc=1
					/**/
						LEFT JOIN ec_movimiento_detalle md ON sp.id_producto=md.id_producto
						LEFT JOIN ec_movimiento_almacen ma ON md.id_movimiento = ma.id_movimiento_almacen AND ma.id_almacen=$alm[0] $WHERE
						LEFT JOIN ec_tipos_movimiento tm ON ma.id_tipo_movimiento = tm.id_tipo_movimiento
						WHERE p.id_productos>2 AND p.es_maquilado=0 AND sp.es_externo=0";
		//fin de modificación 02.02.2018
						
				$sql.=' './*$WHERE*/" GROUP BY p.id_productos ORDER BY p.orden_lista ASC";
				$cons=mysql_query($sql);
		//die($sql);
				if(!$cons){
					die("Error al consultar invenario...\n".$sql."\n\n".mysql_error());
				}

		//declaramos contador
				$c=0;
				while($row=mysql_fetch_row($cons)){
					$c++;//incrementamos contador
				//
					if($c%2==0){
						$color='#FFFF99';
					}else{
						$color='#CCCCCC';
					}
			?>
					<tr id="<?php echo 'fila'.$c;?>" class="fi" style="background:<?php echo $color;?>;" onclick="<?php echo 'resalta('.$c.',0);';?>"
						value="<?php echo $row[0];?>" tabindex="<?php echo $c;?>">

				<!--Implementación Oscar 27.02.2019 para modificar la ubicación de almacen de la sucursal-->
						<td width="10%" align="center" id="<?php echo 'ubicacion_'.$c;?>" onclick="campo_temporal(<?php echo $c;?>);">
							<?php echo $row[4];?>								
						</td>
				<!--Fin de cambio Oscar 27.02.2019--->
						
						<td width="10%" id="mov_tmp_<?php echo $c;?>" align="right">
						<?php echo $row[3];?>
							<!--<input type="text" class="informa" style="text-align:center;" disabled>-->
						</td>
						<td width="35%">
							<input type="hidden" id="<?php echo '0,'.$c;?>" value="<?php echo $row[0];?>">
						<!--Implementación para actualización de inventario en tiempo real (Oscar 02.05.2018)-->
							<input type="hidden" id="<?php echo $row[0];?>" value="<?php echo $c;?>"><!--Guardamos el valor del contador y le asignamos id de prod al elemento-->
						<!--Fin de cambio-->
							<input type="text" id="<?php echo '1,'.$c;?>" value="<?php echo $row[1];?>" class="informa" style="text-align:left;" disabled/>
						</td>
					<!--Implementación para actualización de inventario en tiempo real (Oscar 02.05.2018)-->
						<td width="10%" align="right" id="<?php echo 'temporal_'.$c;?>">0</td>
					<!--Fin de cambio-->
						<td width="10%">
							<input type="text" id="<?php echo '2,'.$c;?>" value="<?php echo $row[2];?>" class="informa" disabled/>
						</td>
						<td width="10%">
							<input type="text" id="<?php echo '3,'.$c;?>" value="<?php echo $row[2];?>" class="informa" 
							onkeyup="<?php echo 'validar(event,'.$c.');';?>" tabindex="<?php echo $c;?>" onfocus="verificaTemporal(<?php echo $c;?>);">
						</td>
						<td width="15%">
							<input type="text" id="<?php echo '4,'.$c;?>" class="informa" value="0" disabled>
						</td>
					</tr>

			<?php
				}
			?>
			</table>
		</div><!--cierra div listado-->
<input type="hidden" id="tope" value="<?php echo $c;?>">
<!--variable de sucursales-->
<input type="hidden" id="id_de_sucursal" value="<?php echo $sucursal;?>">
	</div><!--Se cierra div #contenido-->
	<br>
<!--TOPE PARA NO GENERAR ERRORES-->
		<div class="footer" id="footer" style="padding:10px;">
			<p>
				<input type="button" class="guarda" id="panel" value="Panel Principal" onclick="link(1);">
			</p>
		</div>
</div>

