<?php

session_cache_limiter(false);
session_start();

header("Content-Type: application/json");
require 'Slim/Slim.php';

$app = new Slim();

$app->get('/categorias', 'getCategorias');
$app->get('/estados', 'getEstados');
$app->get('/index', 'getIndex');

$app->get('/viewnmore/:id',	'getIndexByType');

$app->get('/search/:query', 'getSearch');
$app->get('/searched/:id',	'getSearched');
$app->get('/inventory/:id',	'getInventory');
$app->get('/gallery/:id',	'getGallery');
$app->get('/classified/:id',	'getClassified');
$app->get('/employ/:id', 'getEmploy');

$app->get('/publicidad', 'getPublicity');
$app->get('/publicidad/:id', 'getPublicityById');


$app->post("/register", "register");

$app->get('/validateusername/:user', 'validateUsername');
$app->post('/contact', 'contact');
$app->get('/datalist',	'getDataList');

$app->post('/upload/:type', 'upload');
$app->get('/upload', 'getUpload');

/* --------------- */

$app->post('/item', 'addItem');



$app->run();

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
        echo '{"producto": ' . json_encode($post) . '}';
    } catch(PDOException $e) {
        echo '{"producto":{"id":"error", "text":'. $e->getMessage() .'}}';
    }
}



/* ----------------- */

function getCategorias() {
	$sql = "select * FROM categorias ORDER BY categoria";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$categorias = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"categorias": ' . json_encode($categorias) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getEstados() {
	$sql = "select * FROM estados ORDER BY nombre";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$estados = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"estados": ' . json_encode($estados) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getIndex() {
	$queryc="SELECT id, titulo, descplain, valor, estado FROM view_class LIMIT 8";
	$querye="SELECT id, cargo, descripcion, type, ubicacion, customer_id, image FROM view_employ LIMIT 4";
	$queryp="SELECT id, empresa, estado, descplain FROM view_pys LIMIT 8";

	$items = array();
	try {
		$db = getConnection();
		$stmt_class  = $db->query($queryc);  
		$stmt_employ = $db->query($querye); 
		$stmt_pys 	 = $db->query($queryp);
		/* ----------------- */
		for($j=1;$j<=20;$j++){
		  $item=null;

		  switch($j%5){
		    case 1:case 2:
		      while($pys = $stmt_pys->fetch(PDO::FETCH_OBJ)){
		      	$item = createItem($pys, 'pys');break;
		      }
		      break;

		    case 3:case 4:
			  while($classified = $stmt_class->fetch(PDO::FETCH_OBJ)){
			  	$item = createItem($classified, 'classified');break;
		      }
		      break;

		    case 0:
		      while($employ = $stmt_employ->fetch(PDO::FETCH_OBJ)){ 
		        $item = createItem($employ, 'employ');break;
		      }
		      break;
		  }
		  if($item)
		  	array_push($items, $item); 
		}

	echo '{"items": ' . json_encode($items) . '}';
		
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}	
}

function getIndexByType($id) {

	switch ($id){
		case 'pys':
			$sql="SELECT id, empresa, estado, descplain FROM view_pys";
			break;
		case 'class':
			$sql="SELECT id, titulo, descplain, valor, estado FROM view_class";
			break;
		case 'employ':
			$sql="SELECT id, cargo, descripcion, type, ubicacion, customer_id, image FROM view_employ";
			break;
	}
	
	$items = array();
	try {
		$db = getConnection();
		$stmt  = $db->query($sql); 

		while($res = $stmt->fetch(PDO::FETCH_OBJ)){
			$item = createItem($res, $id);
			if($item)
				array_push($items, $item);
		} 
		

	echo '{"items": ' . json_encode($items) . '}';
		
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}	
}

$counttown = array();

/* REGISTROS */

function register() {
    $request = Slim::getInstance()->request();
    $post = json_decode($request->getBody());
    
    $mailsql = "INSERT INTO correos (correo, cliente, tipo) VALUES (:correo, :lastid, :type)";

    switch($post->type){ 	
    	case 'premium':
    		$sessionkey = uniqid('sofree_');
    		$dateout = date('Y-m-d H:i:s', strtotime('+1 year')) ; 
    		$query = "INSERT INTO clientes (empresa, rif, user, pass, seller, dateout, status) 
						VALUES (:nombre, :rif, :user, :pass, '-1','$dateout','2')";
			$t = "C";
    		break;
    	case 'classified':
    		$sessionkey = uniqid('soclass_');
    		$datein = date('Y-m-d H:i:s');
    		$query = "INSERT INTO cuentas (nombre, ci, user, pass, status, datein) 
						VALUES (:nombre, :rif, :user, :pass,'2', '$datein')";
			$t = "CLASS";
    		break;
    }    

    try {
        $db = getConnection();
        $stmt = $db->prepare($query);
        $stmt->bindParam("nombre", $post->nombre);
        $stmt->bindParam("rif", $post->rif);
        $stmt->bindParam("user", $post->user);
        $stmt->bindParam("pass", $post->pass);
        $stmt->execute();
        $post->id = $db->lastInsertId();
        #MAIL
        $stmt = $db->prepare($mailsql);
        $stmt->bindParam("correo", $post->correo);
        $stmt->bindParam("lastid", $post->id);
        $stmt->bindParam("type", $t);
        $stmt->execute();
        #SESSION KEY
        $sql = "INSERT INTO sessionkeys (sessionkey, customer_id) VALUES (:sessionkey, :lastid)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("sessionkey", $sessionkey);
        $stmt->bindParam("lastid", $post->id);
        $stmt->execute();
		
		sendmail($post->correo, $post->user, $post->pass, $sessionkey);

		$db = null;
        echo "success";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function contact() {
    $request = Slim::getInstance()->request();
    $post = json_decode($request->getBody());

    require_once('Slim/lib/PHPMailer/class.phpmailer.php');	

    $txt = "<h1>Contacto desde SabuesOOnline.com</h1><hr>";
	$txt.= "<strong>Nombre: </strong> ".$post->nombre."<br>";
	$txt.= "<strong>Telefono: </strong> ".$post->telefono."<br>";
	$txt.= "<strong>Correo: </strong> ".$post->correo."<br>";
	$txt.= "<strong>Mensaje: </strong> ".nl2br($post->mensaje)."<hr>";
	$txt.= "<a href='http://sabuesoonline.com/'>SabuesOOnline.com</a>";
	
	$mail = new PHPMailer();
	$mail->Host = "localhost";
	$mail->From = 'no-reply@sabuesoonline.com';
	$mail->FromName = 'SabuesOOnline.com';
	$mail->Subject = 'Contacto desde SabuesOOnline.com';
	$mail->IsHTML(true);
	$mail->AddBCC('admin@sabuesoonline.com', 'admin@sabuesoonline.com');
	$mail->AddBCC('ielijose@gmail.com', 'ielijose@gmail.com');

	$mail->AddAddress($post->mailto, $post->mailto);

	$mail->Body = $txt;
	echo "success";
	$mail->Send();
}


/* GC */

function getConnection() {
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="2512368";
	$dbname="leosport";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}
/* API FUNCTIONS */

function createItem($stmt, $type){
	$item = null;
	global $counttown;

	if(isset($stmt->municipio)){
		$mu = strtoupper($stmt->municipio);
		$counttown[$mu] = (isset($counttown[$mu])) ? $counttown[$mu]+1 : 1;
	}
	switch($type){
		case 'pys':
			$stmt->descplain = parseHtmlString($stmt->descplain, 200);
			$item['id'] = $stmt->id;
	        $item['image'] = $im = getImagenCliente($stmt->id,'logo');
	        $item['previewimage'] = (strpos($im,'thumbs')!== false) ? str_replace('/thumbs', '', $im) : $im;
	        $item['nombre'] = addslashes($stmt->empresa);
	        $item['estado'] = ($stmt->estado) ? $stmt->estado : ' ';
	        $item['permalink'] = "searched.php?id=".$stmt->id;  
	        $item['descripcion'] = ($stmt->descplain) ? $stmt->descplain : ' ';   
	        $item['valor'] = "";	        
			break;
		case 'classified':
		case 'class':
			$stmt->descplain = parseHtmlString($stmt->descplain, 200);
			$item['id'] = $stmt->id;
	        $item['image'] = getImgClass($stmt->id);
	        $item['previewimage'] = getImgClass($stmt->id,'nothumbs');
	        $item['nombre'] = addslashes($stmt->titulo);
	        $item['estado'] = ($stmt->estado) ? $stmt->estado : ' ';
	        $item['descripcion'] = ($stmt->descplain) ? $stmt->descplain : ' '; 
	        $item['valor'] = ($stmt->valor && $stmt->valor!="-1") ? " Bs.".$stmt->valor :"";
	        $item['permalink'] = "clasificado.php?id=".$stmt->id;	        
			break;
		case 'employ':
			$stmt->descripcion = parseHtmlString($stmt->descripcion, 200);
			$item['id'] = $stmt->id;		        
	        $item['nombre'] = $stmt->cargo;
	        $item['estado'] = $stmt->ubicacion;
	        $item['permalink'] = "empleo.php?id=".$stmt->id;
	        $item['descripcion'] = $stmt->descripcion;
	        $item['valor'] = "";
	        $path = "../UPLOAD/empleos/thumbs/";
	        switch($stmt->type){
	          case 'pys':
	            $default = getImagenCliente($stmt->customer_id,'logo');	            
	            $item['image'] = $im = (is_file($path.$stmt->image)) ? $path.$stmt->image : $default;
	            $item['previewimage'] = (strpos($im,'thumbs')!== false) ? str_replace('/thumbs', '', $im) : $im;
	            break;
	          case 'class':
	            $default = "../UPLOAD/empleo56.jpg";
	            $item['image'] = $im = (is_file($path.$stmt->image)) ? $path.$stmt->image : $default;   
	            $item['previewimage'] = (strpos($im,'thumbs')!== false) ? str_replace('/thumbs', '', $im) : $im;
	            break;
	        }	        
	        break;	        
	}
	return $item;
}

function getFormasPago($id, $tipo=1){
	$sql = "SELECT * FROM formaspago WHERE cliente = :id AND tipo = :tipo";

	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->bindParam("tipo", $tipo);
		$stmt->execute();
		$fp = $stmt->fetch(PDO::FETCH_OBJ);
	  	$db = null;

	  	if(!$fp) return "";

		$i = 0; 
	  	if($fp->cheque == 1){		 $f[$i] = "Cheque";			$i++;}
	  	if($fp->debito== 1){		 $f[$i] = "Debito";			$i++;}
	  	if($fp->efectivo == 1){	 $f[$i] = "Efectivo";			$i++;}
	  	if($fp->transferencia== 1){ $f[$i] = "Transferencia"; 	$i++;}
	  	if($fp->visa == 1){		 $f[$i] = "Visa";				$i++;}
	  	if($fp->mastercard == 1){	 $f[$i] = "MasterCard";		$i++;}
	  	if(strlen($fp->descOtros)){	 	 $f[$i] = $fp->descOtros;	$i++;}

	  	$j = 0;
	  	$formaspago = "";
	  	for($j = 0; $j<$i;$j++){
	  		$formaspago.= $f[$j].", ";
	  	}
	    $formaspago = substr($formaspago, 0, -2);

	    return ($formaspago) ? $formaspago : "";
	}catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getDataList(){
	$sql="(SELECT producto AS item FROM productos WHERE status=1 ORDER BY producto)
		UNION(SELECT empresa AS item FROM clientes WHERE status=1 ORDER BY empresa)
		UNION(SELECT titulo AS item FROM clasificados WHERE status=1 ORDER BY titulo)
		UNION(SELECT empresa AS item FROM directorio WHERE empresa<>'' ORDER BY empresa)";

	try{
		$db = getConnection();
		$stmt = $db->query($sql);  
		$dl = $stmt->fetchAll(PDO::FETCH_OBJ);

	  	echo indent(json_encode(array("datalist"=>$dl))); 


	}catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
 


/* FUNCIONES SABUESOONLINE */

function parseHtmlString($str, $max=0){	
	$str = trim($str);
	$str = str_replace("\"", "'", $str);
	if($max>0){
		if(strlen($str)>$max){
			$str = substr($str,0, $max)." ...";	
		}
	}
	return $str;
}

/* */
function getImagenCliente($id,$tipo='logo', $t=true){
	$defaultImagen = '../UPLOAD/default_'.$tipo.'.png';
	$ruta = "../UPLOAD/Clientes/".$tipo."s/";
	$thumb = $ruta.'thumbs/';
	
	$exts = array(".jpg", ".png", ".jpeg");
	foreach ($exts as $ex){
		$imagen = $tipo."_".$id.$ex;
		if(is_file($thumb.$imagen) && $t){
			return $thumb.$imagen;
		}else if(is_file($ruta.$imagen)){
			return $ruta.$imagen;
		}
	}
	return $defaultImagen;        	
}


function getImgClass($id, $ruta=''){
	$path = "../UPLOAD/clasificados/";
	$thumb = $path."thumbs/";
	$foto = false;
	$return = "../UPLOAD/default_logo.png";

	$sql = "SELECT foto FROM galeria WHERE cliente= :id AND tipo='2' LIMIT 1";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		
		if($row = $stmt->fetch(PDO::FETCH_OBJ)){
			$foto = $row->foto;
		}
		$db = null;

		if($foto!=false){
			if(file_exists($thumb.$foto)){ return $thumb.$foto;}
			if(file_exists($path.$foto)) { return $path.$foto;}
		}
		
		return $return;
		
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

/*UPLOAD */

function upload($type){
	define("FILE", 'upl');
	define("PATH", "../UPLOAD/Clientes/temp/");

	$allowed = array('png', 'jpg', 'gif', 'jpeg');
	if(isset($_FILES[FILE]) && $_FILES[FILE]['error'] == 0){
		$extension = pathinfo($_FILES[FILE]['name'], PATHINFO_EXTENSION);
		$file = uniqid().$_FILES[FILE]['name'];

		if(!in_array(strtolower($extension), $allowed)){
			echo '{"status":"error"}';
			exit;
		}

		switch ($type){
			case 'portada':
				if(move_uploaded_file($_FILES[FILE]['tmp_name'], PATH.$file)){					
					if(isset($_SESSION['upload']['portada']) && file_exists(PATH.$_SESSION['upload']['portada']) 
						&& $_SESSION['upload']['portada']!=$file && $_SESSION['upload']['portada']!=$_SESSION['upload']['logo']){
						unlink(PATH.$_SESSION['upload']['portada']);
					}

					resizeUpload(PATH.$file);
					$_SESSION['upload']['portada'] = $file;
					echo '{"status":"success"}';					
					exit;
				}
			break;

			case 'logo':
				if(move_uploaded_file($_FILES[FILE]['tmp_name'], PATH.$file)){
					if(isset($_SESSION['upload']['logo']) && file_exists(PATH.$_SESSION['upload']['logo']) 
						&& $_SESSION['upload']['logo']!=$file && $_SESSION['upload']['logo']!=$_SESSION['upload']['portada']){
						unlink(PATH.$_SESSION['upload']['logo']);
					}
					resizeUpload(PATH.$file);
					$_SESSION['upload']['logo'] = $file;
					echo '{"status":"success"}';					
					exit;
				}
			break;

			case 'galeria':
				$max = 500;				
				$count = (isset($_SESSION['upload']['gallery_count'])) ? $_SESSION['upload']['gallery_count'] : 0;

				if($count < $max){
					if(move_uploaded_file($_FILES[FILE]['tmp_name'], PATH.$file)){						
						resizeUpload(PATH.$file, true);
						$_SESSION['gallery'][$count] = $file;
						echo '{"status":"success", "file":"'.$file.'"}';				
						$count++;
						$_SESSION['upload']['gallery_count'] = $count;
						exit;
					}
				}
				
			break;
		}	
		
	}

	echo '{"status":"error"}';
}

function getUpload(){
	define("PATH", "../UPLOAD/Clientes/temp/");
	$register_portada = "/UPLOAD/register_portada.jpg";
	$register_logo = "/UPLOAD/register_logo.png";

	if(isset($_SESSION['upload'])){
		$upload = $_SESSION['upload'];
	}else{
		exit;
	}
	
	if(isset($upload['portada'])){
		if( !file_exists(PATH.$upload['portada']) ){
			$upload['portada'] = $register_portada;			
		}else{
			$upload['portada'] = PATH.$upload['portada'];
		}
	}

	if(isset($upload['logo'])){
		if(!file_exists(PATH.$upload['logo'])){
			$upload['logo'] = $register_logo;			
		}else{
			$upload['logo'] = PATH.$upload['logo'];
		}
	}

	if(isset($_SESSION['gallery'])){

		echo count($_SESSION['gallery']);

		var_dump($upload);
		exit;
		if(!file_exists(PATH.$upload['galeria'])){
			$upload['logo'] = $register_logo;			
		}else{
			$upload['logo'] = PATH.$upload['logo'];
		}
	}

	$json['upload'] = $upload;
	echo indent(json_encode($json));

}

function resizeUpload($file, $thumb=false){
	$max = 740;
	require "Slim/lib/SimpleImage/SimpleImage.php";
	/*SIMPLE IMAGE*/
	$simpleimage = new SimpleImage($file);	
	
	$height = $simpleimage->getHeight();
	$width = $simpleimage->getWidth();
	
	if($height>$max || $width>$max){
		if($height>$width){
			$simpleimage->resizeToHeight($max);
		}else if($width > $height){
			$simpleimage->resizeToWidth($max);
		}else if($width==$height){
			$simpleimage->resize($max,$max);
		}			
	}			
	$simpleimage->save($file);	
	/*THUMBs */
	if($thumb){
		$max = $max/10;
		/*SIMPLE IMAGE*/
		$simpleimage = new SimpleImage($file);	
		
		$height = $simpleimage->getHeight();
		$width = $simpleimage->getWidth();
		
		if($height>$max || $width>$max){
			if($height>$width){
				$simpleimage->resizeToHeight($max);
			}else if($width > $height){
				$simpleimage->resizeToWidth($max);
			}else if($width==$height){
				$simpleimage->resize($max,$max);
			}			
		}			
		$thumbfile = pathinfo($file, PATHINFO_DIRNAME)."/thumbs/".pathinfo($file, PATHINFO_BASENAME);
		$simpleimage->save($thumbfile);
	}
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

function sendmail($mailto, $user, $pass, $key=''){
	require_once('Slim/lib/PHPMailer/class.phpmailer.php');	

	$file = '../assets/templates/freeregistration.html';
	
	$fp = fopen($file,'r');
	$txt = fread($fp, 900000);
	fclose($fp);
	$txt = str_replace('{username}',$user, $txt);
	$txt = str_replace('{password}',$pass, $txt);
	if($key){
		$key = "http://sabuesoonline.com/panel/login.php?key=".$key; 
		$txt = str_replace('{link}',$key, $txt);
	}
	
	$mail = new PHPMailer();
	$mail->Host = "localhost";
	$mail->From = 'no-reply@sabuesoonline.com';
	$mail->FromName = 'SabuesOOnline.com';
	$mail->Subject = 'Bienvenido a SabuesOOnline.com';
	$mail->IsHTML(true);
	$mail->AddAddress($mailto, $mailto);
	 
	$mail->Body = $txt;
	$mail->Send();
}

function validateUsername($user){
	$sql = "SELECT user FROM clientes WHERE user = :user UNION 
	SELECT user FROM cuentas WHERE user = :user UNION 
	SELECT user FROM tbl_admin WHERE user = :user";

	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("user", $user);
		$stmt->execute();

		echo indent(json_encode($stmt->fetchAll(PDO::FETCH_OBJ)));

	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}

}

?>