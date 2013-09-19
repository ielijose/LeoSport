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

    $createBill = "INSERT INTO facturas (cliente_id, usuario_id) VALUES (:cid, :uid)";
    $uid = (isset($_SESSION['LS_id'])) ? $_SESSION['LS_id'] : 0;
    $cid = ($post->client) ? $post->client : 0;

    try {
        $db = getConnection();
        $stmt = $db->prepare($createBill);
        $stmt->bindParam("cid", $cid);
        $stmt->bindParam("uid", $uid);
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
/* ----------------- */

/* GC */

function getConnection() {
<<<<<<< HEAD
	$dbhost="localhost";
=======
	$dbhost="127.0.0.1";
>>>>>>> ffd1b716ceb57c368d20646cbfdaed89dc35b952
	$dbuser="";
	$dbpass="";
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
	$re = NULL;
	if($price == 0 || $price == -1){
		$re = 'A convenir';
	}else{
		$re = 'Bs. '.intval(($price*100))/100;		
	    number_format($price, 0, ',', '.');	
	}
	
	return $re;
}

?>