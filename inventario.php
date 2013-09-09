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
							<a data-toggle="modal" class="btn btn-info btn-sm pull-right" href="#add"><i class="icon-plus-sign-alt  bigger-110"></i>Agregar producto</a>
							<h1>Inventario</h1>
							
						</div><!-- /.page-header -->

						







						<div class="row">
									<div class="col-xs-12">
										<div class="table-responsive">
											<? if($productos->num_rows > 0){ ?>

											<table id="sample-table-2" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="center hidden-480">
															Código
														</th>
														<th>Producto</th>
														<th>Precio</th>
														<th>Cantidad</th>														
														<!--<th class="hidden-480">Status</th>
														<th>Acciones</th>-->

													</tr>
												</thead>

												<tbody>
													<? while($productos->fetch()){ ?>
													<tr>													
														<td class="center hidden-480"><? echo $id; ?></td>
														<td>
															<a href="#"><? echo $producto; ?></a>
														</td>
														<td class="center"><? echo $precio; ?></td>
														<td class="center"><? echo $cantidad; ?></td>

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







					<div id="add" class="modal" aria-hidden="true" style="display: none;">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header" data-target="#modal-step-contents">
									<h3 align="center">Ingrese los datos del producto</h3>
								</div>

								<div class="modal-body step-content" id="modal-step-contents">
									<div class="step-pane active" id="modal-step1">
										<div class="center">
											
											<form class="form-horizontal" id="add-product">
												<div class="form-group">
													<label for="producto" class="col-xs-12 col-sm-3 control-label no-padding-right">Nombre:</label>

													<div class="col-xs-12 col-sm-5">
														<span class="block input-icon input-icon-right">
															<input type="text" id="producto" name="producto" class="width-100">
															<i class="icon-coffee"></i>
														</span>
													</div>													
												</div>

												<div class="form-group">
													<label for="cantidad" class="col-xs-12 col-sm-3 control-label no-padding-right">Cantidad:</label>

													<div class="col-xs-12 col-sm-5">
														<span class="block input-icon input-icon-right">
															<input type="text" class="input-mini" name="cantidad" id="cantidad" />
														</span>
													</div>													
												</div>

												<div class="form-group">
													<label for="precio" class="col-xs-12 col-sm-3 control-label no-padding-right">Precio:</label>

													<div class="col-xs-12 col-sm-5">
														<span class="block input-icon input-icon-right">
															<input type="text" id="precio" name="precio" class="width-100">
															<i class="icon-dollar"></i>
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

									<button class="btn btn-danger btn-sm pull-left" data-dismiss="modal">
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


		<script type="text/javascript">
			jQuery(function($) {

				$('#add-product').validate({
					rules: {
						producto: {
							required: true
						},
						cantidad: {
							required: true,
							number: true
						},
						precio: {
							required: true,
						}						
					},
			
					messages: {
						producto: {
							required: "Campo obligatorio."
						},
						cantidad: {
							required: "Campo obligatorio."
						},
						precio: {
							required: "Campo obligatorio."
						}						
					},
			
					invalidHandler: function (event, validator) { //display error alert on form submit   
						$('.alert-danger', $('.login-form')).show();
					},
			
					highlight: function (e) {
						$(e).closest('.form-group').removeClass('has-info').addClass('has-error');
					},
			
					success: function (e) {
						$(e).closest('.form-group').removeClass('has-error').addClass('has-info');
						$(e).remove();
					},
			
					errorPlacement: function (error, element) {
						if(element.is(':checkbox') || element.is(':radio')) {
							var controls = element.closest('div[class*="col-"]');
							if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
							else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
						}
						else if(element.is('.select2')) {
							error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
						}
						else if(element.is('.chosen-select')) {
							error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
						}
						else error.insertAfter(element.parent());

					},
			
					submitHandler: function (form) {											
						var datos = $("#add-product").serialize();
						console.log(datos);
						$.ajax({
							url: './api/item/', 
							type: 'post', 
							data: datos,
							success : function(data){
								console.log(data);
							}
						})
					},
					invalidHandler: function (form) {
					}
				});

				$(".btn-sm-ap").on("click", function(){
					$("#add-product").submit();

				});
				
				$('#cantidad').ace_spinner({value:0,min:0,max:100000,step:1, on_sides: true, icon_up:'icon-plus smaller-75', icon_down:'icon-minus smaller-75', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
				
			})
		</script>
	</body>

<!-- Mirrored from 192.69.216.111/themes/preview/ace/ by HTTrack Website Copier/3.x [XR&CO'2013], Sun, 08 Sep 2013 18:34:09 GMT -->
</html>
