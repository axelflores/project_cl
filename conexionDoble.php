<?php
//incluimos la libreria de configuraciones generales
	include('config.inc.php');//incluimos la libreria general de funciones
	include($codepath."/general/funciones.php");
//definimos zona horaria
	date_default_timezone_set('America/Mexico_City');
	$hostLocal='www.lacasadelasluces.com';
	$userLocal='wwlaca_sistema';
	$passLocal='sistemaGeneral';
	$nombreLocal='wwlaca_sistema_general';
	$local=@mysql_connect($hostLocal, $userLocal, $passLocal);
	//comprobamos conexion local
	if(!$local){	//si no hay conexion
		echo 'no hay conexion local';//finaliza programa
	}else{
	//echo'conexion local'.$nombreLocal;
	}
	$dblocal=@mysql_select_db($nombreLocal);
	if(!$dblocal){
		echo 'BD local no encontrada';
	}else{
	}
/***********************************CONEXIONES BD FORANEA*******************************************/
	$hostLinea='www.lacasadelasluces.com';
	$userLinea='wwlaca_sistema';
	$passLinea='sistemaGeneral';
	$nombreLinea='wwlaca_sistema_general';
	$linea=@mysql_connect($hostLinea,$userLinea,$passLinea);
	$indicador="";	if(!$linea){
die('Sin conexión a Línea');
}
	$dblinea=@mysql_select_db($nombreLinea);	if(!$dblinea){
		echo('BD en linea no encontrada');
	}else{
		//echo '<br>bd en linea encontrada';
	}
	require('include/sesiones.php');

	header('Content-Type: text/html; charset=utf-8');
?>