<? include "assets/includes/config.php"; 
$tipo = "venta";
$db = getConnection();
$stmt = $db->prepare($queries['sells']);
$stmt->bindParam("tipo", $tipo);
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="es">	
<? include "assets/includes/head.php"; ?>

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
						<a class="btn btn-info btn-sm pull-right" href="reporte.php"><i class="icon-plus-sign-alt  bigger-110"></i>Ventas de hoy</a>
						
						<h1>Ventas</h1>

					</div><!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<div class="table-responsive">
								<? if(!empty($ventas)){  ?>

								<table id="inventario" class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th class="center">N°</th>
											<th>Cliente</th>
											<th>Fecha</th>
										</tr>
									</thead>

									<tbody>
										<? foreach ($ventas as $index => $venta) { ?>
										<tr>													
											<td class="center hidden-480"><? echo str_pad($venta->id, 6, "0", STR_PAD_LEFT); ?></td>
											<td>
												<a href="factura.php?id=<? echo $venta->id; ?>"><? echo $venta->nombre ." (".$venta->ci.") ";  ?></a>
											</td>
											<td class="center"><? echo date("d-m-Y - g:i:s a", strtotime($venta->timestamp)); ?></td>

											<!--
											<td class="hidden-480">
												<span class="label label-sm label-warning">Expiring</span>
											</td> 

											<td>
												<div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
													<a class="blue" href="#" title="Ver">
														<i class="icon-zoom-in bigger-130"></i>
													</a>

													<a class="green" href="#" title="Editar">
														<i class="icon-pencil bigger-130"></i>
													</a>

													<a class="red" href="#" title="Eliminar">
														<i class="icon-trash bigger-130"></i>
													</a>
												</div>

											
											</td>
										-->
									</tr>
									<? } ?>



								</tbody>
							</table>
							<? } else { ?>
							<h2>No hay registros.</h2>
							<? } ?>
						</div>
					</div>
				</div>	
			
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


	<script src="assets/js/typeahead-bs2.min.js"></script>

	<script src="assets/js/jquery.dataTables.min.js"></script>
	<script src="assets/js/jquery.dataTables.bootstrap.js"></script>

	<script src="assets/js/ace-elements.min.js"></script>
	<script src="assets/js/ace.min.js"></script>


	<script type="text/javascript">
	jQuery(function($) {
		var inventario = $('#inventario').dataTable();
	})
</script>
</body>

</html>
