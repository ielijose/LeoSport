<?php
session_cache_limiter(false);
session_start();

require 'Slim/Slim.php';

$app = new Slim();

$app->get('/items', 'getItems');
$app->post('/item/', 'addItem');

$app->post('/client', 'addClient');

$app->post('/sell', 'sell');

$app->get('/client/search/:ci', 'searchClient');
$app->get('/sellsByDay', 'sellsByDay');

$app->get('/getSellByDay/:date', 'getSellByDay');

$app->post('/load-pay', 'loadPay');


$app->run();

/* ------------------- */
function addItem() {
    $request = Slim::getInstance()->request();
    $post = json_decode($request->getBody());
  
    $query = "INSERT INTO productos (producto, cantidad, precio) VALUES (:producto, :cantidad, :precio)";

    try {
        $db = getConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam("producto", $post->producto);
        $stmt->bindParam("cantidad", $post->cantidad);
        $stmt->bindParam("precio", $post->precio);
        $stmt->execute();
        $post->id = $db->lastInsertId();

		$db = null;
        echo indent('{"producto": ' . json_encode($post) . '}');
    } catch(PDOException $e) {
        echo indent('{"producto":{"id":"error", "text":'. $e->getMessage() .'}}');
    }
}

function getItems() {
	$sql = "select * FROM productos ORDER BY id";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$items = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo indent('{"items": ' . json_encode($items) . '}');
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addClient() {
    $request = Slim::getInstance()->request();
    $post = json_decode($request->getBody());
  
    $query = "INSERT INTO clientes (ci, nombre, direccion) VALUES (:ci, :nombre, :direccion)";

    try {
        $db = getConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam("ci", $post->ci);
        $stmt->bindParam("nombre", $post->nombre);
        $stmt->bindParam("direccion", $post->direccion);
        $stmt->execute();
        $post->id = $db->lastInsertId();

		$db = null;
        echo indent('{"client": ' . json_encode($post) . '}');
    } catch(PDOException $e) {
        echo indent('{"client":{"id":"error", "text":'. $e->getMessage() .'}}');
    }
}

function loadPay() {
    $request = Slim::getInstance()->request();
    $post = json_decode($request->getBody());
 
    $query = "INSERT INTO pagos (factura_id, pago, descripcion, usuario_id) 
                VALUES (:factura_id, :pago,  :descripcion, :usuario_id)";
    $post->usuario_id = (isset($_SESSION['LS_id'])) ? $_SESSION['LS_id'] : 0;

    try {
        $db = getConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam("factura_id", $post->factura_id);
        $stmt->bindParam("pago", $post->bs);
        $stmt->bindParam("descripcion", $post->descripcion);
        $stmt->bindParam("usuario_id", $post->usuario_id);
        $stmt->execute();
        $post->id = $db->lastInsertId();

        $db = null;
        echo indent('{"pay": ' . json_encode($post) . '}');
    } catch(PDOException $e) {
        echo indent('{"pay":{"id":"error", "text":'. $e->getMessage() .'}}');
    }
}

function searchClient($ci){
	$sql = "SELECT c.id, c.ci, c.nombre, c.direccion, (SELECT COUNT(1) FROM facturas f WHERE f.cliente_id = c.id) AS compras FROM clientes c WHERE ci = :ci "; 
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("ci", $ci);
		$stmt->execute();
		$data = $stmt->fetchObject();
		$db = null;
	
		echo indent('{"data": ' . json_encode($data) . '}');
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function sell(){
    $request = Slim::getInstance()->request();
    $post = json_decode($request->getBody());

    $createBill = "INSERT INTO facturas (cliente_id, usuario_id, tipo) VALUES (:cid, :uid, :tipo)";
    $uid = (isset($_SESSION['LS_id'])) ? $_SESSION['LS_id'] : 0;
    $cid = ($post->client) ? $post->client : 0;

    $tipo = ($post->apartado == true) ? "apartado" : "venta";

    try {
        $db = getConnection();
        $stmt = $db->prepare($createBill);
        $stmt->bindParam("cid", $cid);
        $stmt->bindParam("uid", $uid);
        $stmt->bindParam("tipo", $tipo);
        $stmt->execute();
        $billId = $db->lastInsertId();
        $db = null;  

        /* Items */ 

        $json = $post->items;

        $createItem = "INSERT INTO ventas (factura_id, producto_id, cantidad, precio) VALUES (:fid, :pid, :count, :price)";
        $updateItem = "UPDATE productos SET cantidad = cantidad - :cantidad WHERE id = :id";

        foreach ($post->items as $item) {
            $id         = $item->id;
            $cantidad   = $item->cantidad;
            $disponible = $item->disponible;
            $precio     = $item->precio;        

            try {
                $db = getConnection();
                $stmt = $db->prepare($createItem);
                $stmt->bindParam("fid", $billId);
                $stmt->bindParam("pid", $id);
                $stmt->bindParam("count", $cantidad);
                $stmt->bindParam("price", $precio);
                $stmt->execute();

                /* Restar */
                $stmt = $db->prepare($updateItem);
                $stmt->bindParam("cantidad", $cantidad);
                $stmt->bindParam("id", $id);
                $stmt->execute();

                $db = null;            
                
            } catch(PDOException $e) {}
        }

        $data['error'] = "false";
        $data['id'] = $billId;
        $data['url'] = "factura.php?id=".$billId;

        echo indent('{"data": ' . json_encode($data) . '}'); 

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
    

    //echo json_encode($json);
}

function sellsByDay(){
    $sql = "SELECT DISTINCT (SUBSTR(`timestamp`, 1, 10)) AS start FROM facturas f UNION SELECT DISTINCT (SUBSTR(`timestamp`, 1, 10)) AS start FROM pagos "; 
    try {
        $db = getConnection();
        $stmt = $db->query($sql);  
        $fechas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $json = array();
    
        foreach ($fechas as $index => $fecha) {
            $ventas = getSellByDay($fecha->start."%");
            $pagos = getPaysByDay($fecha->start."%");

            if($ventas != "Bs 0"){
                $venta = new stdClass();
                $venta->title .= "Ventas: ".$ventas;

                $venta->url = "reporte.php?date=".$fecha->start;
                $venta->start = strtotime($fecha->start." 23:01:01");  
                $venta->className = 'label-success';
                $venta->allDay = true;

                $json[count($json)] = $venta;

            }
            if($pagos != "Bs 0"){
                $pago = new stdClass();
                $pago->title .= "Pagos: ".$pagos;
                $pago->url = "reporte.php?date=".$fecha->start;
                $pago->start = strtotime($fecha->start." 23:01:01");  
                $pago->className = 'label-info';
                $pago->allDay = true;

                $json[count($json)] = $pago;
            }
            
        }

        echo indent(json_encode($json));

    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}'; 
    }
}

function getSellByDay($date){
    $billsByDate = "SELECT * FROM facturas f WHERE f.`timestamp` LIKE :date AND tipo = 'venta'";
    $productsSQL = "SELECT v.id, p.producto, v.cantidad, v.precio, p.id AS pid FROM ventas v INNER JOIN productos p ON p.id = v.producto_id WHERE v.factura_id = :id ";

    $dateHuman = date("d-m-Y ", strtotime($date));
    $date.="%";

    $db = getConnection();
    $stmt = $db->prepare($billsByDate);
    $stmt->bindParam("date", $date);
    $stmt->execute();
    $facturas = $stmt->fetchAll(PDO::FETCH_OBJ);

    $products = array();
    $total = 0;

    foreach ($facturas as $index => $factura) {
        $p = $db->prepare($productsSQL);
        $p->bindParam("id", $factura->id);
        $p->execute();
        $productos = $p->fetchAll(PDO::FETCH_OBJ); 

        foreach ($productos as $key => $item) {     
            $pid = $item->producto;
            $cantidad = $item->cantidad;
            $total += ($item->precio * $cantidad);
            
            if(isset($products[$pid])){
                $products[$pid] += $cantidad;
            }else{
                $products[$pid] = $cantidad;
            }
        }
    }

    return toPrice($total);
}

function getPaysByDay($date){
    $paysByDate = "SELECT * FROM pagos p WHERE p.`timestamp` LIKE :date";

    $dateHuman = date("d-m-Y ", strtotime($date));
    $date.="%";

    $db = getConnection();
    $stmt = $db->prepare($paysByDate);
    $stmt->bindParam("date", $date);
    $stmt->execute();
    $pagos = $stmt->fetchAll(PDO::FETCH_OBJ);

    $products = array();
    $total = 0;

    foreach ($pagos as $index => $pago) {
        $total += $pago->pago;    
    }

    return toPrice($total);
}


/* ----------------- */

/* GC */

function getConnection() {
	$dbhost="localhost";
	$dbuser="git";
	$dbpass="git";
	$dbname="leosport";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}


/* UNTILS */

function indent($json) {
	global $app;
	$app->contentType('application/json');
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

function toPrice($price){
	return "Bs ". number_format($price, !($price == (int)$price) * 2, ',', '.');
}

?>