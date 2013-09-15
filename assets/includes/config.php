<?php
	
require "leosport.php";


if (!isset($_SESSION)) {  session_start(); }
header("Cache-Control: no-store, no-cache, must-revalidate");

$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";
$MM_restrictGoTo = "./login.php?denied";
$invalid = false;
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  $isValid = False; 
  if (!empty($UserName)) { 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { $isValid = true; } 
    if (in_array($UserGroup, $arrGroups)) { $isValid = true; } 
    if (($strUsers == "") && true) { $isValid = true; } 
  } return $isValid; 
}

if (!((isset($_SESSION['LS_admin'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['LS_admin'], $_SESSION['LS_admin'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  $invalid = true;
}

if($invalid){
	header("Location: ". $MM_restrictGoTo); 
	exit;
}


if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

function parseFecha($fecha, $insert = true){

  if($fecha == "0000-00-00"){
    return "<strong>Hasta la actualidad.</strong>";
  }

  $meses=array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

  if($insert){
    $datos = explode('/', $fecha);
    $date = $datos[2]."-".$datos[1]."-".$datos[0];  
  }else{
    $datos = explode('-', $fecha);
    $date = $datos[2]." de ".$meses[$datos[1]*1]." del ".$datos[0]; 
  }
  
  return $date;
}


function mesesDesde($fecha){    
    $meses = 0;
    $ano1 = date("Y");$mes1 = date("m");$dia1 = date("d");
    $date = explode("-", $fecha);

    $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); $timestamp2 = mktime(0,0,0,$date[1],$date[2],$date[0]); 
    $seg = $timestamp1 - $timestamp2; 
    $dias = $seg / (60 * 60 * 24); $dias = abs($dias); $d = $dias = floor($dias); 

    while($d>30){ $meses++; $d = $d-30; }

    if($meses>0 && $d > 0){
      if($meses==1){  return $meses." mes y ".$d." dias";
      }else{          return $meses." meses y ".$d." dias"; }
     
    }else if($meses>0){ return $meses." meses";
    }else if($d>0){     return $d." dias";
    }else{              return "Hoy";}
}

function getDateById($id, $type){
    global $mysqli, $queries;

    $date = $mysqli->prepare($queries['lasthistory']);
    $date->bind_param('i', $id);
    $date->execute();

    $date->bind_result($indate, $outdate, $destacamento);
    $date->fetch();
    $date->close();

    $return = array('indate' => $indate , 'outdate' => $outdate , 'destacamento' => $destacamento);

    return $return;
}


/* ADMIN */

function mesesDesde2($fecha){    
    $meses = 0;
    $ano1 = date("Y");$mes1 = date("m");$dia1 = date("d");
    $date = explode("-", $fecha);

    $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); $timestamp2 = mktime(0,0,0,$date[1],$date[2],$date[0]); 
    $seg = $timestamp1 - $timestamp2; 
    $dias = $seg / (60 * 60 * 24); $dias = abs($dias); $d = $dias = floor($dias); 

    while($d>30){ $meses++; $d = $d-30; }

    return $meses;
}



?>