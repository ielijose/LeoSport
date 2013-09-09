<?php require_once "./assets/includes/leosport.php"; 


$action = $_POST['action'];

if (!isset($_SESSION)) {  session_start(); }

if($action=='login'){
	$username = $_POST['username'];
	$password = $_POST['password'];

	$login = $mysqli->prepare($queries['login']);
	$login->bind_param('ss', $username, $password);
	$login->execute();

	$login->bind_result($id, $username);

	if($login->fetch()){
		
		$next = 'index.php';
		$_SESSION['LS_admin'] = $username;			
		
		$_SESSION['LS_id'] = $id;
		$_SESSION['LS_name'] = utf8_encode($name);
	}else{
		$next = 'login.php?denied';
	}
	$mysqli->close();
	header("Location: $next");
	exit;
}
?>

<!DOCTYPE html>
<html lang="es">
	
	<? $page_title = "Entrar al sistema";
	include "assets/includes/head.php"; ?>

	<body class="login-layout">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">
							<div class="center">
								<h1>
									<i class="icon-dribbble orange"></i>
									<span class="blue">Leo</span>
									<span class="white">Sport</span>
								</h1>
								<h4 class="blue">&copy; Control de inventario</h4>
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">
												<i class="icon-user grey"></i>
												Ingresa tus credenciales
											</h4>

											<div class="space-6"></div>

											<form action="login.php" method="POST">
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" name="username" autocomplete="off" class="form-control" placeholder="Usuario" />
															<i class="icon-user"></i>
														</span>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" name="password" class="form-control" placeholder="Contraseña" />
															<i class="icon-lock"></i>
														</span>
													</label>

													<div class="space"></div>

													<div class="clearfix">
														<label class="inline">
															<input type="checkbox" class="ace" />
															<span class="lbl"> Recordarme</span>
														</label>

														<button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
															<i class="icon-key"></i>
															Entrar
														</button>
													</div>

													<div class="space-4"></div>
												</fieldset>
												<input type="hidden" name="action" value="login">
											</form>

										
										</div><!-- /widget-main -->

										
									</div><!-- /widget-body -->
								</div><!-- /login-box -->
								
							</div><!-- /position-relative -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		<!--[if !IE]> -->

		<script src="assets/js/jquery.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<![endif]-->

		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
<script type="text/javascript">
 window.jQuery || document.write("<script src='assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
</script>
<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>

		<!-- inline scripts related to this page -->

		<script type="text/javascript">
			function show_box(id) {
			 jQuery('.widget-box.visible').removeClass('visible');
			 jQuery('#'+id).addClass('visible');
			}
		</script>
	</body>
</html>
