<?php
//abrimos el archivo txt si existe
$path = "conexion_inicial.txt";
if(file_exists($path)){
	$file = fopen($path,"r");
	$line=fgets($file);
	fclose($file);
    $config=explode("<>",$line);
}

	$archivo_path = "conexion_inicial.txt";
	if(file_exists($archivo_path)){
		//echo 'si';
		$file = fopen($archivo_path,"r");
		$line=fgets($file);
		fclose($file);
	    $config=explode("<>",$line);
	    $conf_loc=explode("~",$config[0]);
	    $conf_ext=explode("~",$config[1]);
	    $conf_tk=explode("~",$config[2]);
	    $ruta_jar=$config[3];
	}
?>
<!DOCTYPE html>
<style type="text/css">
	#global{width:100%;height: 100%;position: absolute; top:0;left:0; background-image: url("img/especiales/fondo_config.jpg");}
	.entrada{padding: 12px;border-radius: 15px;width: 105%;}
	.descripcion{color:white;}
</style>
<html>
<head>
	<title>Configuración Inicial</title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
</head>
<body>
	<div id="global">
	<center><br><br>
	<table style="position:absolute;top:10%;left:5%;">
		<tr>
			<td align="center"><b class="descripcion">Host Local:</b></td>
			<td>
				<input type="text" id="host_loc" class="entrada" value="<?php echo base64_decode($conf_loc[0]);?>" placeholder="localhost/ www.dominio...">
			</td>
		</tr>
		<tr>
			<td align="center"><b class="descripcion">Ruta Local:</b></td>
			<td>
				<input type="text" id="ruta_loc" class="entrada" value="<?php echo base64_decode($conf_loc[1]);?>" placeholder="carpeta(s) del sistema">
			</td>
		</tr>
		<tr>
			<td align="center"><b class="descripcion">Nombre BD Local:</b></td>
			<td>
				<input type="text" id="nombre_bd_loc" value="<?php echo base64_decode($conf_loc[2]);?>" class="entrada">
			</td>
		</tr>
		<tr>
			<td align="center"><b class="descripcion">Usuario BD Local:</b></td>
			<td>
				<input type="text" id="usuario_bd_loc" value="<?php echo base64_decode($conf_loc[3]);?>" class="entrada">
			</td>
		</tr>
		<tr>
			<td align="center"><b class="descripcion">Password BD Local:</b></td>
			<td>
				<input type="text" id="pass_bd_loc" value="<?php echo base64_decode($conf_loc[4]);?>" class="entrada">
			</td>
		</tr>
	</table>
<br><br>
	<table style="position:absolute;top:10%;left:38%;">
		<tr>
			<td align="center"><b class="descripcion">Host Linea:</td>
			<td>
				<input type="text" id="host_lin" class="entrada" value="<?php echo base64_decode($conf_ext[0]);?>" placeholder="localhost/ www.dominio...">
			</td>
		</tr>
		<tr>
			<td align="center"><b class="descripcion">Ruta Linea:</b></td>
			<td>
				<input type="text" id="ruta_lin" class="entrada" value="<?php echo base64_decode($conf_ext[1]);?>" placeholder="carpeta(s) del sistema">
			</td>
		</tr>
		<tr>
			<td>Nombre de la BD Linea: </td>
			<td>
				<input type="text" id="nombre_bd_lin" value="<?php echo base64_decode($conf_ext[2]);?>" class="entrada">
			</td>
		</tr>
		<tr>
			<td>Usuario BD Linea: </td>
			<td>
				<input type="text" id="usuario_bd_lin" value="<?php echo base64_decode($conf_ext[3]);?>" class="entrada">
			</td>
		</tr>
		<tr>
			<td>Password BD Linea: </td>
			<td>
				<input type="password" id="pass_bd_lin" value="<?php echo base64_decode($conf_ext[4]);?>" class="entrada">
			</td>
		</tr>
	</table>
<br><br>
	<table style="position:absolute;top:10%;left:70%;">
		<tr>
			<td>Ruta origen ticket: </td>
			<td><input type="text" id="ruta_ticket_origen" value="<?php echo ($conf_tk[0]);?>" class="entrada"></td>
		</tr>
		<tr>
			<td>Ruta destino ticket</td>
			<td><input type="text" id="ruta_ticket_destino" value="<?php echo ($conf_tk[1]);?>" class="entrada"></td>
		</tr>
		<tr>
			<td>Ruta de archivo jar</td>
			<td><input type="text" id="ruta_archivo_jar" value="<?php echo ($ruta_jar);?>" class="entrada"></td>
		</tr>
		<!--<tr>
			<td colspan="2" align="center"><br><br>
				<button onclick="genera_config();">Crear Configuracion</button>
			</td>
		</tr>-->
	</table>
		<button onclick="genera_config();" style="position:absolute;top:60%;left:46%;">Crear Configuracion</button>	
	</center>
	</div>
</body>
</html>

<script type="text/javascript">

	function genera_config(){
//recolectamos los datos de la configuración local
		var h_l=$("#host_loc").val();
		if(h_l.length<=0){
			alert("El campo de Host no puede ir vacío!!!");
			$("#host_loc").focus();
			return false;
		}
		var r_l=$("#ruta_loc").val();
		if(r_l.length<=0){
			alert("El campo de Ruta Local no puede ir vacío!!!");
			$("#ruta_loc").focus();
			return false;
		}
		var n_bd_l=$("#nombre_bd_loc").val();
		if(n_bd_l.length<=0){
			alert("El campo de Nombre de Base de datos no puede ir vacío!!!");
			$("#nombre_bd_loc").focus();
			return false;
		}
		var u_l=$("#usuario_bd_loc").val();
		if(u_l.length<=0){
			alert("El usuario de Base de Datos no puede ir vacío!!!");
			$("#usuario_bd_loc").focus();
			return false;
		}
		var p_l=$("#pass_bd_loc").val();

//recolectamos los datos de la configuración de bd linea
		var h_lin=$("#host_lin").val();
		if(h_lin.length<=0){
			alert("El campo de Host no puede ir vacío!!!");
			$("#host_lin").focus();
			return false;
		}
		var r_lin=$("#ruta_lin").val();
		if(r_lin.length<=0){
			alert("El campo de Ruta Local no puede ir vacío!!!");
			$("#ruta_lin").focus();
			return false;
		}
		var n_bd_lin=$("#nombre_bd_lin").val();
		if(n_bd_lin.length<=0){
			alert("El campo de Nombre de Base de datos no puede ir vacío!!!");
			$("#nombre_bd_lin").focus();
			return false;
		}
		var u_lin=$("#usuario_bd_lin").val();
		if(u_lin.length<=0){
			alert("El usuario de Base de Datos no puede ir vacío!!!");
			$("#usuario_bd_lin").focus();
			return false;
		}
		var p_lin=$("#pass_bd_lin").val();

		var ru_t_or=$("#ruta_ticket_origen").val();
		if(ru_t_or.length<=0){
			alert("La ruta de origen del ticket no puede ir vacía!!!");
			$("#ruta_ticket_origen").focus();
			return false;
		} 
		var ru_t_des=$("#ruta_ticket_destino").val();
		if(ru_t_des.length<=0){
			alert("La ruta de destino del ticket no puede ir vacía!!!");
			$("#ruta_ticket_destino").focus();
			return false;
		} 
		var ru_jar=$("#ruta_archivo_jar").val();
		if(ru_jar.length<=0){
			alert("La ruta del archivo jar no puede ir vacía!!!");
			$("#ruta_archivo_jar").focus();
			return false;
		} 
	//enviamos datos por ajax
		$.ajax({
			type:'post',
			url:'code/ajax/conf_inicial.php',
			cache:false,
			data:{
				host_local:h_l,
				ruta_local:r_l,
				nombre_local:n_bd_l,
				usuario_local:u_l,
				pass_local:p_l,
				host_linea:h_lin,
				ruta_linea:r_lin,
				nombre_linea:n_bd_lin,
				usuario_linea:u_lin,
				pass_linea:p_lin,
				ru_or:ru_t_or,
				ru_des:ru_t_des,
				archivo_jar:ru_jar/*ruta del archivo jar*/
			},
			success:function(dat){
				if(dat!='ok'){
					alert("Error, actualice la pantalla y vuelva a intentar!!!"+dat);
					return false;
				}else{
					alert("La configuración fue guardada exitosamente!!!");
					location.href='index.php?';
				}
			}
		});
	}

</script>