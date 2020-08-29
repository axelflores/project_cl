<?php
	//header("Content-type: application/pdf");
	//librerias de funcion	
	include("../../include/fpdf153/nclasepdf.php");	
	//CONECCION Y PERMISOS A LA BASE DE DATOS
	include("../../conectMin.php");
	
	$mesNombre=array(
						1=>'Enero',
						2=>'Febrero',
						3=>'Marzo',
						4=>'Abril',
						5=>'Mayo',
						6=>'Junio',
						7=>'Julio',
						8=>'Agosto',
						9=>'Septiembre',
						10=>'Octubre',
						11=>'Noviembre',
						12=>'Diciembre'						
					);
	$impDoc=0;$fpdf=true;
	extract($_GET);
	extract($_POST);
	if($tdoc == 'REQ')
	{
		$orient_doc="P";
		$unid_doc="cm";
		$alto_doc=27.9;
		$ancho_doc=21.6;
		$ftam=10;
		$tamano_doc=array($ancho_doc,$alto_doc);
		$ypag=27.9;
		$impDoc++;
		include("requisicion.php");
		$tipoimpresion="Requisicion";
	}
	if($tdoc == 'OC')
	{
		$orient_doc="P";
		$unid_doc="cm";
		$alto_doc=27.9;
		$ancho_doc=21.6;
		$ftam=10;
		$tamano_doc=array($ancho_doc,$alto_doc);
		$ypag=27.9;
		$impDoc++;
		include("ordencompra.php");
		$tipoimpresion="Requisicion";
	}
	if($tdoc == 'PED')
	{
		$orient_doc="P";
		$unid_doc="cm";
		$alto_doc=27.9;
		$ancho_doc=21.6;
		$ftam=10;
		$tamano_doc=array($ancho_doc,$alto_doc);
		$ypag=27.9;
		$impDoc++;
		include("pedido.php");
		$tipoimpresion="Requisicion";
	}
	if($tdoc == 'NV')
	{
		$orient_doc="P";
		$unid_doc="cm";
		$alto_doc=27.9;
		$ancho_doc=21.6;
		$ftam=10;
		$tamano_doc=array($ancho_doc,$alto_doc);
		$ypag=27.9;
		$impDoc++;
		include("notaventa.php");
		$tipoimpresion="Requisicion";
	}
	if($tdoc == 'MA')
	{
		$orient_doc="P";
		$unid_doc="cm";
		$alto_doc=27.9;
		$ancho_doc=21.6;
		$ftam=10;
		$tamano_doc=array($ancho_doc,$alto_doc);
		$ypag=27.9;
		$impDoc++;
		include("movimiento.php");
		$tipoimpresion="Requisicion";
	}
	
	
	if($tdoc == 'transferencia')
	{
		$orient_doc="P";
		$unid_doc="cm";
		$alto_doc=27.9;
		$ancho_doc=21.6;
		$ftam=10;
		$tamano_doc=array($ancho_doc,$alto_doc);
		$ypag=27.9;
		$impDoc++;
		include("transferencia.php");
		$tipoimpresion="Requisicion";
	//implementación de Oscar 24.05.2018 para marcar transferencia com impresa
		mysql_query("UPDATE ec_transferencias SET impresa=1 WHERE id_transferencia=$id")or die("Error al actualizar transferencia como impresa!!!\n\n".$sql."\n\n".mysql_error());
	//fin de cambio
	}
	
	
	
	if($impDoc>0)
	{
		//grabaBitacora('8','','0','0',$_SESSION["USR"]->userid,'0',$tipoimpresion,'');
		//Determina el nombre del archivo temporal
		$file=basename(tempnam(getcwd(),'tmp'));
		//Salva elPDF en un archivo
		$pdf->Output($file);
		//Redireccionamiento por Javascript
		echo "<HTML><SCRIPT>document.location='getpdf.php?f=$file';</SCRIPT></HTML>";
	}/**/
?>