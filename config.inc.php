<?php
session_start();
//Definiciones de base de datos
	$dbHost='www.lacasadelasluces.com';
	$dbUser='wwlaca_sistema';
	$dbPassword='sistemaGeneral';
	$dbName='wwlaca_sistema_general';
//Definicion de rutas
	if(isset($_SERVER['HTTP_HOST'])){
		$rooturl = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/General2019/';
	}
	$rootpath = dirname(__FILE__);
	$includepath=$rootpath.'/include/';
	$smartypath=$rootpath.'/include/smarty/';
	$codepath=$rootpath.'/code/';
	$template_dir=$rootpath.'/templates/';
	$compile_dir=$rootpath.'/templates_c/';
//datos de la sesion
	$nombre_session='casaDev';
	$dur_session=0;//50000 modificado el Oscar 11.06.2018 para cerrar sesión al cerrar el explorador
	date_default_timezone_set('America/Mexico_City');
header('Content-Type: text/html; charset=utf-8');
?>