<?php	
require "leosport.php";
date_default_timezone_set('America/Caracas');
setlocale(LC_TIME, 'spanish'); 
setlocale(LC_ALL,'es_VE');



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


function toPrice($price){
  return "Bs ". number_format($price, !($price == (int)$price) * 2, ',', '.');
}

// 27 10 13

function humanDate($date){
  $time = strtotime($date);

  $date = date("D, d M Y", $time);

  $date = str_replace("Mon", "Lunes", $date);
  $date = str_replace("Tue", "Martes", $date);
  $date = str_replace("Wed", "Miércoles", $date);
  $date = str_replace("Thu", "Jueves", $date);
  $date = str_replace("Fri", "Viernes", $date);
  $date = str_replace("Sat", "Sabado", $date);
  $date = str_replace("Sun", "Domingo", $date);

  $date = str_replace("Jan", "Ene", $date);
  $date = str_replace("Apr", "Abr", $date);
  $date = str_replace("Dec", "Dic", $date);

  return $date;
}
?>