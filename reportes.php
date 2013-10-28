<? include "assets/includes/config.php";  

// QUE? 
?>
<!DOCTYPE html>
<html lang="es">	
<link rel="stylesheet" href="assets/css/fullcalendar.css" />

<? $page_title="Reportes de ventas"; include "assets/includes/head.php"; ?>	
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
						<h1>
							Reportes
							<small>
								<i class="icon-double-angle-right"></i>
								Lleva el control de tus ventas día a día
							</small>
						</h1>
					</div><!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->

							<div class="row">
								<div class="col-sm-12">
									<div class="space"></div>

									<div id="calendar"></div>
								</div>

								
							</div>

							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div><!-- /.main-content -->

			
		</div><!-- /.main-container-inner -->

		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="icon-double-angle-up icon-only bigger-110"></i>
		</a>
	</div><!-- /.main-container -->

	<? include "assets/includes/scripts.php" ?>


	<script type="text/javascript">
	if("ontouchend" in document) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
	</script>
	<script src="assets/js/typeahead-bs2.min.js"></script>

	<!-- page specific plugin scripts -->

	<script src="assets/js/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
	<script src="assets/js/fullcalendar.min.js"></script>
	<script src="assets/js/bootbox.min.js"></script>

	<!-- ace scripts -->

	<script src="assets/js/ace-elements.min.js"></script>
	<script src="assets/js/ace.min.js"></script>

	<!-- inline scripts related to this page -->

	<script type="text/javascript">
	jQuery(function($) {



	/* initialize the calendar
	-----------------------------------------------------------------*/

	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();

	
	var calendar = $('#calendar').fullCalendar({
		buttonText: {
			prev: '<i class="icon-chevron-left"></i>',
			next: '<i class="icon-chevron-right"></i>'
		},
		
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		events: '/api/sellsByDay'
		,
		editable: true,
		selectable: true,
		selectHelper: true		
		,
		eventClick: function(calEvent, jsEvent, view) {

			var form = $("<form class='form-inline'><label>Change event name &nbsp;</label></form>");
			form.append("<input class='middle' autocomplete=off type=text value='" + calEvent.title + "' /> ");
			form.append("<button type='submit' class='btn btn-sm btn-success'><i class='icon-ok'></i> Save</button>");
			
			var div = bootbox.dialog({
				message: form,
				
				buttons: {
					"delete" : {
						"label" : "<i class='icon-trash'></i> Delete Event",
						"className" : "btn-sm btn-danger",
						"callback": function() {
							calendar.fullCalendar('removeEvents' , function(ev){
								return (ev._id == calEvent._id);
							})
						}
					} ,
					"close" : {
						"label" : "<i class='icon-remove'></i> Close",
						"className" : "btn-sm"
					} 
				}

			});
			
			form.on('submit', function(){
				calEvent.title = form.find("input[type=text]").val();
				calendar.fullCalendar('updateEvent', calEvent);
				div.modal("hide");
				return false;
			});
			
			
			//console.log(calEvent.id);
			//console.log(jsEvent);
			//console.log(view);

			// change the border color just for fun
			//$(this).css('border-color', 'red');

		}
		
	});

})
</script>
</body>

</html>
