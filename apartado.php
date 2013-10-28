<? include "assets/includes/config.php"; 
$id = $_GET['id'];
$sql = "SELECT * FROM view_ventas v WHERE v.factura_id = :id"; 

$db = getConnection();
$stmt = $db->prepare($sql);
$stmt->bindParam("id", $id);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_OBJ);

#pagos

$sql = "SELECT * FROM pagos p WHERE p.factura_id = :id"; 

$db = getConnection();
$stmt = $db->prepare($sql);
$stmt->bindParam("id", $id);
$stmt->execute();
$pagos = $stmt->fetchAll(PDO::FETCH_OBJ);

#var
$total = $pagado = 0;


?>

<!DOCTYPE html>
<html lang="es">	
<? $page_title="Apartado #".$_GET['id']; include "assets/includes/head.php"; ?>
<style>
.ace-spinner{
	width: 120px !important;
}
.cantidad{
	margin-left: 0px !important; 
}
</style>
<body>
	<? include "assets/includes/nav.php"; ?>

	<div class="main-container" id="main-container">
		<script type="text/javascript">
		try{ace.settings.check('main-container' , 'fixed')}catch(e){}
		</script>

		<div class="main-container-inner">
			<a class="menu-toggler" id="menu-toggler" href="#">
				<span class="menu-text"></span>
			</a>

			<? include "assets/includes/sidebar.php"; ?>

			<div class="main-content">

				<? include "assets/includes/breadcrumbs.php"; ?>

				<div class="page-content">
					<div class="page-header">
						<a class="btn btn-info btn-sm pull-right" href="#cargar-pago" id="load-pay">
							<i class="icon-credit-card  bigger-110"></i>Cargar Pago
						</a>

						<h1>Apartado # <? echo $id; ?></h1>

						<noscript>
							Por favor habilita Javascript en tu navegador.
						</noscript>
					</div><!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<div class="table-responsive">
								<? if($productos){ ?>
								<h3> Productos</h3>

								<table id="" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class="center hidden-480">
												Código
											</th>
											<th>Producto</th>
											<th class="hidden-480">Precio</th>
											<th>Cantidad</th>
											<!--<th>Acciones</th>-->

										</tr>
									</thead>

									<tbody>
										<? 
										foreach ($productos as $producto) { 
											$total += ($producto->precio * $producto->cantidad);
											?>											
											<tr>													
												<td class="center hidden-480"><? echo $producto->id; ?></td>
												<td>
													<a href="#"><? echo $producto->producto; ?></a>
												</td>
												<td class="center hidden-480"><? echo $producto->precio; ?></td>
												<td class="center"><? echo $producto->cantidad; ?></td>
											</tr>
											<? } ?>
										</tbody>
									</table>
									<? } else { ?>
									<h2>No hay productos en el inventario.</h2>
									<? } ?>
								</div>
							</div>
						</div>	

						<div class="row">
							<div class="col-xs-12">
								<div class="table-responsive">
									<? if($pagos){ ?>
									<h3> Pagos</h3>

									<table id="" class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th class="center hidden-480">
													Código
												</th>
												<th>Pago</th>
												<th class="hidden-480">Descripcion</th>
												<th>Fecha</th>
												<!--<th>Acciones</th>-->

											</tr>
										</thead>

										<tbody>
											<? 
											$pagado = 0;
											foreach ($pagos as $pago) {
												$pagado += $pago->pago;
												?>	
												<tr>													
													<td class="center hidden-480"><? echo $pago->id; ?></td>
													<td>
														<a href="#"><? echo $pago->pago; ?></a>
													</td>
													<td class="center hidden-480"><? echo $pago->descripcion; ?></td>
													<td class="center"><? echo humanDate($pago->timestamp); ?></td>
												</tr>
												<? } ?>
											</tbody>
										</table>
										<? } else { ?>
										<h2>No hay pagos asociados.</h2>
										<? } ?>
									</div>
								</div>
							</div>	


							<!-- ERROR ITEM -->
							<hr>
							<div align="center">


								<div class="col-sm-4 " align="center">
									<span class="line-height-1 smaller-90"> Total </span>
									<br>
									<span class="btn btn-app btn-sm btn-light no-hover">										

										<span class="line-height-1 bigger-170 blue"> <? echo $total; ?> </span>
										<br>
										<span class="line-height-1 smaller-90"> Bs </span>
									</span>
								</div>

								<div class="col-sm-4" align="center">
									<span class="line-height-1 smaller-90"> Pagado </span>
									<br>
									<span class="btn btn-app btn-sm btn-light no-hover">										
										<span class="line-height-1 bigger-170 blue"> <? echo $pagado; ?> </span>
										<br>
										<span class="line-height-1 smaller-90"> Bs </span>
									</span>
								</div>

								<div class="col-sm-4 " align="center">
									<span class="line-height-1 smaller-90"> Pendiente </span>
									<br>
									<span class="btn btn-app btn-sm btn-light no-hover">
										<span class="line-height-1 bigger-170 blue" id="total"> <? echo $total -$pagado; ?></span>
										<br>
										<span class="line-height-1 smaller-90"> Bs </span>
									</span>
								</div>
							</div>



							<div id="cargar-pago" class="modal" aria-hidden="true" style="display: none;">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header" data-target="#modal-step-contents">
											<h3 align="center">Ingrese los datos del pago</h3>
										</div>

										<div class="modal-body step-content" id="modal-step-contents">
											<div class="step-pane active" id="modal-step1">
												<div class="center">

													<form class="form-horizontal" id="new-pay">
														<div class="form-group">
															<label for="bs" class="col-xs-12 col-sm-3 control-label no-padding-right">Bs:</label>

															<div class="col-xs-12 col-sm-5">
																<span class="block input-icon input-icon-right">
																	<input type="text" style="width:300px" id="bs" name="bs">
																	<i class="icon-dollar" style="margin-right: -120px"></i>
																</span>
															</div>													
														</div>

														<div class="form-group">
															<label for="descripcion" class="col-xs-12 col-sm-3 control-label no-padding-right">Descripción:&nbsp; </label>

															<div class="col-xs-12 col-sm-5">
																<span class="block input-icon input-icon-right">
																	<i class="icon-pencil" style="margin-right: -120px"></i>
																	<textarea id="descripcion" style="width:300px" rows="7" name="descripcion"></textarea>
																</span>
															</div>													
														</div>
														<input type="hidden" name="fid" id="fid"/>
													</form>
												</div>
											</div>

										</div>

										<div class="modal-footer wizard-actions">									

											<button class="btn btn-success btn-sm btn-sm-ap save-new-pay" type="submit" data-last="Guardar">
												Guardar										
												<i class="icon-ok icon-on-right"></i></button>

												<button class="btn btn-danger btn-sm pull-left btn-cancel" data-dismiss="modal">
													<i class="icon-remove"></i>
													Cancelar
												</button>
											</div>
										</div>
									</div>
								</div>				




								<!-- /.row -->
							</div><!-- /.page-content -->
						</div><!-- /.main-content -->

					</div><!-- /.main-container-inner -->

					<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
						<i class="icon-double-angle-up icon-only bigger-110"></i>
					</a>
				</div><!-- /.main-container -->

				<? include "assets/includes/scripts.php" ?>



				<!-- inline scripts related to this page -->

				<script src="assets/js/jquery-ui-1.10.3.custom.min.js"></script>
				<script src="assets/js/fuelux/fuelux.spinner.min.js"></script>
				<script src="assets/js/jquery.inputlimiter.1.3.1.min.js"></script>
				<script src="assets/js/jquery.validate.min.js"></script>

				<script src="assets/js/bootstrap.min.js"></script>
				<script src="assets/js/typeahead-bs2.min.js"></script>

				<script src="assets/js/jquery.dataTables.min.js"></script>
				<script src="assets/js/jquery.dataTables.bootstrap.js"></script>

				<script src="assets/js/ace-elements.min.js"></script>
				<script src="assets/js/ace.min.js"></script>
				<script type="text/javascript">
				var apartar = false;

				<? echo "var id = ".$_GET['id'].";"  ?>

				$("#fid").val(id);
				</script>
				<script type="text/javascript" src="assets/js/leosport.js">

				</script>
			</div>
		</body>
		</html>