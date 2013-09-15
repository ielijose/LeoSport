<?php

$host = "localhost";
$database = "leosport";
$user = "root";
$pass = "2512368";

$mysqli = new mysqli($host,$user,$pass,$database);

if($mysqli->connect_error){die("Error en la conexion : ".$mysqli ->connect_errno."-".$mysqli ->connect_error);}


/* Default querys */

$queries = array();

$queries['login'] = "SELECT id, username, type, name FROM usuarios WHERE username = ? AND password = ? AND status = 'on'"; 
$queries['hierarchies'] = "SELECT id, grado, descripcion FROM jerarquias ORDER BY id ASC";

$queries['profile'] = "SELECT j.descripcion AS jerarquia, p.apellidos, p.nombres, p.ci, p.fechanac, p.fechagrad, p.promocion, p.carnet,p.procedencia, p.foto, p.date FROM personal p LEFT JOIN jerarquias j ON p.jerarquia_id = j.id WHERE p.id = ?";
$queries['history'] = "SELECT h.indate, h.outdate, d.destacamento FROM historial h LEFT JOIN destacamentos d ON h.destacamento_id = d.id WHERE h.personal_id  = ?";
$queries['list'] = "SELECT p.id, j.descripcion AS jerarquia, 
		(SELECT d.destacamento 
		 FROM historial h
		 INNER JOIN destacamentos d ON h.destacamento_id = d.id
		 WHERE h.personal_id = p.id 
		 ORDER BY h.id DESC
		 LIMIT 1) AS destacamento, p.apellidos, p.nombres, p.ci, p.carnet 
FROM personal p 
LEFT JOIN jerarquias j ON p.jerarquia_id = j.id";


$queries['lasthistory'] = "SELECT h.indate, h.outdate, d.destacamento FROM historial h INNER JOIN destacamentos d ON h.destacamento_id = d.id WHERE h.personal_id = ? ORDER BY h.id DESC LIMIT 1";

?>