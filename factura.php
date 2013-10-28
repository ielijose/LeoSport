<? include "assets/includes/config.php"; 
#header('Content-type: application/json');

$factura_id = $_GET['id'];

$db = getConnection();
$stmt = $db->prepare($queries['bill']);
$stmt->bindParam("id", $factura_id);
$stmt->execute();
$factura = $stmt->fetch(PDO::FETCH_OBJ);

$stmt = $db->prepare($queries['products']);
$stmt->bindParam("id", $factura_id);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_OBJ);

$total = 0;
foreach ($productos as $key => $value) {
	$total += ($value->precio * $value->cantidad) ;
}

$total = toPrice($total);

?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">

	<link rel='stylesheet' type='text/css' href='assets/factura/css/style.css' />
	<link rel='stylesheet' type='text/css' href='assets/factura/css/print.css' media="print" />
	<script type='text/javascript' src='assets/factura/js/jquery-1.3.2.min.js'></script>
	<script type='text/javascript' src='assets/factura/js/example.js'></script>

	<title>Factura</title>
</head>
<body>

	<div id="page-wrap">

		<textarea id="header">FACTURA</textarea>
		
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
				<? echo $factura->nombre; ?> <br> 
				<small><? echo $factura->ci; ?></small><br/>
				<small class="addr"><? echo $factura->direccion; ?></small>
			</p>

			<table id="meta">
				<tr>
					<td class="meta-head">Factura #</td>
					<td><p><? echo str_pad($factura->id, 6, "0", STR_PAD_LEFT); ?></p></td>
				</tr>
				<tr>

					<td class="meta-head">Fecha</td>
					<td><p id="date"><? echo date("d-m-Y - g:i:s a", strtotime($factura->timestamp)); ?></p></td>
				</tr>
				<tr>
					<td class="meta-head">Total</td>
					<td><div class="due"><? echo $total; ?></div></td>
				</tr>

			</table>
				
		</div>
			
		<table id="items">
			
			<tr>
				<th style="max-width:15px;">N°</th>
				<th>Producto</th>
				<th>Precio</th>
				<th>Cantidad</th>
				<th>Total</th>
			</tr>

			<? foreach ($productos as $key => $item) { ?>			
				<tr class="item-row">
					<td class="item-no"><? echo $key+1; ?></td>
					<td class="item-name">
							<p> <? echo $item->producto; ?></p>
					</td>					
					<td><p class="cost"><? echo toPrice($item->precio); ?></p></td>
					<td><p class="qty center"><? echo $item->cantidad; ?></p></td>
					<td><span class="price"><? echo toPrice($item->precio * $item->cantidad); ?></span></td>
				</tr>
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
				<td colspan="3" class="blank"> </td>
				<td colspan="1" class="total-line balance">Total a pagar</td>
				<td class="total-value balance"><div class="due"><? echo $total; ?></div></td>
			</tr>
			
		</table>
		
		<div id="terms">
			<h5>GRACIAS POR SU COMPRA</h5>
			<p></p>
		</div>

		<div class="print" align="center">
			<a href="javascript:window.print()" title="Imprimir"><img id="image" src="/assets/factura/images/print.png" alt="Imprimir" /></a>
		</div>
		
	</div>
	
</body>
</html>