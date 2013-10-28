<? include "assets/includes/config.php"; 

$productos = $mysqli->prepare($queries['inventory']);
$productos->execute();
$productos->store_result();

$productos->bind_result($id, $producto, $cantidad, $precio);


$productos->store_result();
$numrows = $productos->num_rows;

?>

<!DOCTYPE html>
<html lang="es">	
	<? $page_title="Vender"; include "assets/includes/head.php"; ?>
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
							<a class="btn btn-info btn-sm pull-right" href="#" id="facturar">
								<i class="icon-plus-sign-alt  bigger-110"></i>Facturar
							</a>

							<h1>Vender</h1>

							<noscript>
								Por favor habilita Javascript en tu navegador.
							</noscript>
							
						</div><!-- /.page-header -->

						

						<div class="widget-box ">
							<div class="widget-header widget-header-small">
								<h5 class="lighter">Cliente</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main">
									<div class="row">
										<div class="col-xs-12 col-sm-12">											
												<div class="input-group search-ci-box col-sm-6">
													<input type="text" class="form-control ci-value" placeholder="Cédula de identidad">
													<span class="input-group-btn">
														<button type="button" class="btn btn-purple btn-sm searchci">
															Buscar
															<i class="icon-search icon-on-right bigger-110"></i>
														</button>
													</span>
												</div>
											<!-- data -->


												<div class="profile-user-info profile-user-info-striped col-sm-8" style="display: none" id="client-info" >
													<div class="profile-info-row">
														<div class="profile-info-name"> Nombre </div>

														<div class="profile-info-value">
															<span class="editable editable-click data-nombre"></span>
														</div>
													</div>

													<div class="profile-info-row">
														<div class="profile-info-name"> CI </div>

														<div class="profile-info-value">
															<span class="editable editable-click data-ci"></span>
														</div>
													</div>

													<div class="profile-info-row">
														<div class="profile-info-name"> Dirección </div>

														<div class="profile-info-value">
															<i class="icon-map-marker light-orange bigger-110"></i>
															<span class="editable editable-click data-direccion"></span>
														</div>
													</div>						

													<div class="profile-info-row">
														<div class="profile-info-name"> Compras </div>

														<div class="profile-info-value">
															<span class="editable editable-click data-compras"></span>
														</div>
													</div>

												</div>

												<div class="col-sm-2 pull-right" align="center">
														<span class="btn btn-app btn-sm btn-light no-hover">
															<span class="line-height-1 bigger-170 blue" id="total"> 0 </span>
															<br>
															<span class="line-height-1 smaller-90"> Bs </span>
														</span>
													</div>

												

												<input type="hidden" id="id" class="data-id" value="0">

											<!-- data -->
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- ERROR ITEM -->
						<hr>

						<div class="alert alert-block alert-danger alert-items" style="display:none">
							<button type="button" class="close" data-dismiss="alert">
								<i class="icon-remove"></i>
							</button>

							<i class="icon-cancel red"></i>

							No ha seleccionado ningun artículo del inventario.
						</div>
						





						<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive">
											<? if($productos->num_rows > 0){ ?>

											<table id="inventario" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="center hidden-480">
															Código
														</th>
														<th>Producto</th>
														<th class="hidden-480">Precio</th>
														<th>Disponible</th>
														<th>Cantidad</th>														
														<!--<th>Acciones</th>-->

													</tr>
												</thead>

												<tbody>
													<? while($productos->fetch()){ ?>
													<tr>													
														<td class="center hidden-480"><? echo $id; ?></td>
														<td>
															<a href="#"><? echo $producto; ?></a>
														</td>
														<td class="center hidden-480"><? echo $precio; ?></td>
														<td class="center"><? echo $cantidad; ?></td>
														
														<td class="center">
															<span class="block input-icon input-icon-right">
																<input type="text" class="input-mini cantidad" disabled="disabled" 
																data-disponible="<? echo $cantidad; ?>" data-id="<? echo $id; ?>" 
																data-precio="<? echo $precio; ?>" data-old="0"/>
															</span>
														</td>														

														<!--<td class="hidden-480">
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

														
														</td>-->
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







					<div id="addClient" class="modal" aria-hidden="true" style="display: none;">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header" data-target="#modal-step-contents">
									<h3 align="center">Ingrese los datos del cliente</h3>
								</div>

								<div class="modal-body step-content" id="modal-step-contents">
									<div class="step-pane active" id="modal-step1">
										<div class="center">
											
											<form class="form-horizontal" id="add-client">
												<div class="form-group">
													<label for="producto" class="col-xs-12 col-sm-3 control-label no-padding-right">Nombre:&nbsp;</label>

													<div class="col-xs-12 col-sm-5">
														<span class="block input-icon input-icon-right">
															<input type="text" style="width:300px" id="nombre" name="nombre">
															<i class="icon-user" style="margin-right: -120px"></i>
														</span>
													</div>													
												</div>

												<div class="form-group">
													<label for="cantidad" class="col-xs-12 col-sm-3 control-label no-padding-right">Cédula:</label>

													<div class="col-xs-12 col-sm-5">
														<span class="block input-icon input-icon-right">
															<input type="text" disabled="disabled" style="width:300px" id="ci" name="ci">
														</span>
													</div>													
												</div>

												<div class="form-group">
													<label for="precio" class="col-xs-12 col-sm-3 control-label no-padding-right">Dirección:&nbsp; </label>

													<div class="col-xs-12 col-sm-5">
														<span class="block input-icon input-icon-right">
															<i class="icon-map-marker" style="margin-right: -120px"></i>
															<textarea id="direccion" style="width:300px" rows="7" name="direccion"></textarea>
														</span>
													</div>													
												</div>
											</form>



										</div>
									</div>

									
								</div>

								<div class="modal-footer wizard-actions">									

									<button class="btn btn-success btn-sm btn-sm-ap" type="submit" data-last="Guardar">
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

		<script src="assets/js/typeahead-bs2.min.js"></script>

		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/jquery.dataTables.bootstrap.js"></script>

		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script type="text/javascript">
			var apartar = false;
		</script>
		<script type="text/javascript" src="assets/js/leosport.js">
			
		</script>
	</body>

<!-- Mirrored from 192.69.216.111/themes/preview/ace/ by HTTrack Website Copier/3.x [XR&CO'2013], Sun, 08 Sep 2013 18:34:09 GMT -->
</html>
