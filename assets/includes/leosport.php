<?php

$host = "localhost";
$database = "leosport";
$user = "git";
$pass = "git";

$mysqli = new mysqli($host,$user,$pass,$database);

if($mysqli->connect_error){die("Error en la conexion : ".$mysqli ->connect_errno."-".$mysqli ->connect_error);}


function getConnection() {
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="2512368";
	$dbname="leosport";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

function indent($json) {
	global $app;
    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;
    for ($i=0; $i<=$strLen; $i++) {
        $char = substr($json, $i, 1);
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }
        $result .= $char;
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
        $prevChar = $char;
    }
    return $result;
}

/* Default querys */

$queries = array();
// si
$queries['login'] = "SELECT id, username FROM usuarios WHERE username = ? AND password = ? AND status = 'on'"; 
$queries['inventory'] = "SELECT id, producto, cantidad, precio FROM productos WHERE cantidad>0";
$queries['bill'] = "SELECT f.id, f.`timestamp`, c.nombre, c.ci, c.direccion, u.username FROM facturas f JOIN clientes c ON c.id = f.cliente_id JOIN usuarios u ON u.id = f.usuario_id WHERE f.id = :id ";
$queries['products'] = "SELECT v.id, p.producto, v.cantidad, v.precio, p.id AS pid FROM ventas v INNER JOIN productos p ON p.id = v.producto_id WHERE v.factura_id = :id ";

$queries['sells'] = "SELECT f.id, f.`timestamp`, c.nombre, c.ci, c.direccion, u.username FROM facturas f JOIN clientes c ON c.id = f.cliente_id JOIN usuarios u ON u.id = f.usuario_id WHERE f.tipo=:tipo";
$queries['billsByDate'] = "SELECT * FROM facturas f WHERE f.`timestamp` LIKE :date AND tipo = 'venta' ";

?>