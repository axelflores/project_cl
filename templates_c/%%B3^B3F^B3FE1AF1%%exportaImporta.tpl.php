<?php /* Smarty version 2.6.13, created on 2020-04-13 23:09:42
         compiled from especiales/exportaImporta.tpl */ ?>
<link href="estilo_final.css" rel="stylesheet" type="text/css" />
<link href="css/demos.css" rel="stylesheet" type="text/css" />

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_header.tpl", 'smarty_include_vars' => array('pagetitle' => ($this->_tpl_vars['contentheader']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
  <div id="campos">  
<div id="titulo">6.10 Respaldos BD</div>
<br><br>

<div id="filtros">

	<span style="color:#FF0000;font-size:18px"><?php echo $this->_tpl_vars['mensajes']; ?>
</span>

	<form id="form1" name="form1" method="post" action="exportaImporta.php" enctype="multipart/form-data" onsubmit="return confirm('Esta operación borrará todo el contenido de la base de datos y no será reversible\n\n¿Desea ccontinuar con la operación?')">
	<input type="hidden" name="procesa" value="SI">
		<fieldset>
			<legend>Exportar</legend>
			<table>
				<tr>
					<td>Exportar base de datos</td>
					<td>
						<input type="button" value="Generar" class="boton" onclick="generaRespaldo()">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<a target="_blank" id="descargaVin"></a>
					</td>
				</tr>
			</table>
		</fieldset>    	
		<br>
		<fieldset>
			<legend>Importar</legend>
			<table>
				<tr>
					<td>
						Archivo de respaldo
						<input type="file" name="file" >
					</td>
				</tr>
				<tr>	
					<td align="right">
						<input type="submit" value="Importar" class="boton">
					</td>
				</tr>
			</table>
		</fieldset>    	
	</form>
</div>

<script>

<?php echo '
	function generaRespaldo(){
		var url="../ajax/generaRespaldo.php";
		
		var res=ajaxR(url);
		
		var aux=res.split("|");
		
		if(aux[0] == \'exito\')
		{
			var obj=document.getElementById("descargaVin");
			obj.innerHTML="Descarga respaldo";
			obj.href=aux[1];
		}
		else
		{
			alert(res);
		}
		
	}
	
'; ?>
	

</script>
	
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "_footer.tpl", 'smarty_include_vars' => array('pagetitle' => ($this->_tpl_vars['contentheader']))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> 