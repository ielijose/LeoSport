<? include "/assets/includes/config.php"; 

$factura_id = $_GET['id'];

echo $factura_id;

var_dump($_GET);
exit;

$productos->execute();
$productos->store_result();

$productos->bind_result($id, $producto, $cantidad, $precio);


$productos->store_result();
$numrows = $productos->num_rows;

?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Factura</title>
</head>
<body>
	
</body>
</html>