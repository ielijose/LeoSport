<? include "assets/includes/config.php"; 
#header('Content-type: application/json');

$date = (isset($_GET['date'])) ? $_GET['date'] : date("Y-m-d");
$dateHuman = date("d-m-Y ", strtotime($date)); 

$date.="%";

$db = getConnection();
$stmt = $db->prepare($queries['billsByDate']);
$stmt->bindParam("date", $date);
$stmt->execute();
$facturas = $stmt->fetchAll(PDO::FETCH_OBJ);

//echo indent(json_encode($facturas)); // faltaba el Json_encode()

$products = array();// aqui pienso guardar los productos 
$total = 0;
$bills = "";

foreach ($facturas as $index => $factura) {//recorro todas las facturas de ese dia
	$p = $db->prepare($queries['products']);
	$p->bindParam("id", $factura->id);
	$p->execute();
	$productos = $p->fetchAll(PDO::FETCH_OBJ); // saco los productos qe se vndieron

	$bills.= '<a target="_blank" href="factura.php?id='.$factura->id.'">'.$factura->id.'</a>, ';

	foreach ($productos as $key => $item) {		
		$pid = $item->producto;
		$cantidad = $item->cantidad;
		$total += ($item->precio * $cantidad);
		$products[$pid] += $cantidad;
	}
}

/* */

$sql = "SELECT * FROM pagos p WHERE p.timestamp LIKE :date";

$stmt = $db->prepare($sql);
$stmt->bindParam("date", $date);
$stmt->execute();
$pagos = $stmt->fetchAll(PDO::FETCH_OBJ);


foreach ($pagos as $key => $item) {		
	$total += ($item->pago);
}
/* : */

$total = toPrice($total);

$bills = substr($bills, 0, -2);

?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">

	<link rel='stylesheet' type='text/css' href='assets/factura/css/style.css' />
	<link rel='stylesheet' type='text/css' href='assets/factura/css/print.css' media="print" />
	<script type='text/javascript' src='assets/factura/js/jquery-1.3.2.min.js'></script>
	<script type='text/javascript' src='assets/factura/js/example.js'></script>
	
	<title>Reporte</title>
</head>
<body>

	<div id="page-wrap">

		<textarea id="header">REPORTE DE VENTAS</textarea>
		
		<div id="identity">
			
			<p id="address">Comercial e Inversiones LeoSport C.A.
				<br/>
				C.C. Ciudad Santa Rita - Local #6
				<br/>
				Santa Rita, Zulia, Venezuela
				<br/>
				Teléfono: (0424) 602-9989</p>

				<div id="logo">


					<img id="image" src="/assets/factura/images/logo.png" alt="logo" />
				</div>

			</div>
			
			<div style="clear:both"></div>

			<div id="customer">
				
				<p id="customer-title">
					<? if($bills) {?>
					Facturas en el reporte: <br> 
					<small> <? echo $bills; ?> </small><br/>
					<? } ?>
				</p>

				<table id="meta">				
					<tr>

						<td class="meta-head">Fecha</td>
						<td><p id="date"><? echo $dateHuman; ?></p></td>
					</tr>
					<tr>
						<td class="meta-head">Total</td>
						<td><div class="due"><? echo $total; ?></div></td>
					</tr>

				</table>
				
			</div>
			
			<table id="items">

				<? if($products) { ?>
				<tr>
					<td colspan="3"><h3 align="center"> Ventas</h3></td> 
				</tr>

				<tr>
					<th style="max-width:15px;">N°</th>
					<th>Producto</th>
					<th>Cantidad</th>
				</tr>

				<? $i = 0; foreach ($products as $producto => $cantidad) { $i++; ?>			
				<tr class="item-row">
					<td class="item-no"><? echo $i; ?></td>
					<td class="item-name">
						<p> <? echo $producto; ?></p>
					</td>					
					<td><p class="qty center"><? echo $cantidad; ?></p></td>
				</tr>
				<? } ?>
				<? } ?>

				<!-- PAGOS -->
				<tr>
					<td colspan="3"><h3 align="center"> Pagos</h3></td> 
				</tr>
				

				<? if($pagos) { ?>

				<tr>
					<th style="max-width:15px;">Factura N°</th>					
					<th>Descripcion</th>
					<th>Pago</th>
				</tr>

				<? foreach ($pagos as $key => $pago) { ?>			
				<tr class="item-row">
					<td class="item-no"><? echo $pago->factura_id; ?></td>									
					<td><p class="qty"><? echo $pago->descripcion; ?></p></td>

					<td class="item-name center">
						<p> <? echo $pago->pago; ?></p>
					</td>	
				</tr>
				<? } ?>
				<? } ?>






				<tr id="hiderow">
					<td colspan="5"></td>
				</tr>
			<!--
			<tr>
				<td colspan="3" class="blank"> </td>
				<td colspan="1" class="total-line">Subtotal</td>
				<td class="total-value"><div id="subtotal">$875.00</div></td>
			</tr>
			<tr>

				<td colspan="3" class="blank"> </td>
				<td colspan="1" class="total-line">Total</td>
				<td class="total-value"><div id="total">$875.00</div></td>
			</tr>
			<tr>
				<td colspan="3" class="blank"> </td>
				<td colspan="1" class="total-line">Amount Paid</td>

				<td class="total-value"><textarea id="paid">$0.00</textarea></td>
			</tr>

		-->
		<tr>
			<td colspan="2" class="total-line balance"></td>
			<td class="total-value balance"><div class="due">Total: <? echo $total; ?></div></td>
		</tr>

	</table>

	<div id="terms">
		<h5 class="impreso">IMPRESO EL DIA <? echo date("d-m-Y") ?> a las <? echo date("g:i:s a"); ?></h5>
		<p></p>
	</div>

	<div class="print" align="center">
		<a href="javascript:window.print()" title="Imprimir"><img id="image" src="/assets/factura/images/print.png" alt="Imprimir" /></a>
	</div>

</div>

</body>
</html>