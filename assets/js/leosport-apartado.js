(function(){
	"use strict";	

	var client = false;

	$(".ci-value").focus();			
	/* DATATABLE inventario*/	
	var $inventario = $('#inventario').dataTable({ "aLengthMenu": [[-1],["Todos"]], "iDisplayLength" : -1 });

	// solo numeros en el campo ci
	$(".ci-value").on("keypress", function(e){
		var theEvent = e || window.event;
		var key = theEvent.keyCode || theEvent.which;
		if(key==13){
			$(".searchci").trigger("click");
		}
		key = String.fromCharCode( key );

		var regex = /[0-9]/;
		if( !regex.test(key) ) {
			theEvent.returnValue = false;
			if(theEvent.preventDefault) 
				theEvent.preventDefault();
		}
	});
	//crea los botones de sumar a la cantidad
	$.each($(".cantidad"), function(k,v){
		var disponible = $(this).data("disponible");
		$(this).ace_spinner({
			value:0,
			min:0,
			max: disponible,
			step:1, 
			on_sides: true, 
			icon_up:'icon-plus smaller-75', 
			icon_down:'icon-minus smaller-75', 
			btn_up_class:'btn-success',
			btn_down_class:'btn-danger'
		});
	});

	/* ci search */
	//al darle click al btn de buscar cliente
	$(".searchci").on("click", function(){
		var ci = $(".ci-value").val();
		searchClientByCi(ci);
	});
	//al cerrar la modal
	$(".btn-cancel").on("click", function(){ 
		$(".ci-value").prop('disabled', false);
	});
	// evitar que se vendan mas productos de los que hay en existencia
	$("#inventario").on("change", ".cantidad",function(){
		var c = $(this).val();
		var m = $(this).data('disponible');
		if(c>m){
			$(this).val(0);
		}
	});

	// facturar

	$("#facturar").on("click", function(){
		$inventario.fnFilter('');

		var Sell = new Object();
		Sell.client = $("#id").val();
		Sell.items = [];

		$.each($(".cantidad"), function(k,v){
			var Item = new Object();

			Item.id = $(this).data("id");
			Item.cantidad = $(this).val();
			Item.disponible = $(this).data("disponible");
			Item.precio = $(this).data("precio");
			
			if(Item.cantidad > 0)
				Sell.items.push(Item);
		});

		var data = JSON.stringify(Sell);
		//console.log(data);

		if(client && Sell.items.length){
			$(".alert-items").slideUp("slow");
			
			$.ajax({
				url : "/api/sell",
				data: data,
				type: "POST",
				success: function(data){
					data = data.data;
					if(data.error === "false"){
						window.location.href = "/factura.php?id=" + data.id;
					}
				}
			});

		}else{
			if(!client){
				$(".ci-value").focus();				
			}
			if(!Sell.items.length){
				$(".alert-items").slideDown("slow");
			}			
		}
	});

	var total = $("#total");	

	$(".spinner-up").on("click", function(){
		var input = $(this).parent().parent().children(".cantidad");	

		var cantidad = input.val();
		var precio = input.data("precio");
		var disponible = input.data("disponible");
		var old = input.data("old");

		if(cantidad<=disponible && (old!=disponible)){
			var x = parseInt(total.val()) || 0;		
			total.val(parseInt(x + (precio)));
			input.data("old", cantidad);
			calcularPago();
			
		}
		
	});
	$(".spinner-down").on("click", function(){
		var input = $(this).parent().parent().children(".cantidad");

		var cantidad = input.val();
		var precio = input.data("precio");
		var old = input.data("old");

		if(cantidad>=0 && (old!=0)){
			var x = parseInt(total.val()) || 0;			
			total.val(parseInt(x - (precio)));
			input.data("old", cantidad);
			calcularPago();
		}
	});
	
	/* VALIDAR new-client */

	$('#add-client').validate({
		rules: {
			nombre: {required: true},
			direccion: {required: true}						
		},
		messages: {
			nombre: { required: "Campo obligatorio." },
			direccion: { required: "Campo obligatorio." }					
		},

		submitHandler: function (form) {											
			var datos = formToJSON();
			console.log(datos);
			$.ajax({
				url: './api/client', 
				type: 'POST', 
				data: datos,
				success : function(data){
					if(data.client.id != "error"){
						$(".btn-cancel").trigger("click");
						searchClientByCi(data.client.ci);
					}
				}
			});
		}		
	});

	$(".btn-sm-ap").on("click", function(){
		$("#add-client").submit();
	});
	

	/* FUNCTIONS */

	function searchClientByCi(ci){
		if(ci.length == 0){
			$(".ci-value").focus();
			return;
		}
		$(".ci-value").prop('disabled', true);

		$.ajax({
			dataType: 'JSON',
			url: '/api/client/search/'+ci,
			async: false,
			success: function(json) {
				if(json.data!=false){
					$(".search-ci-box").slideUp("slow", function(){
						$.each(json.data, function(i, j){
							$(".data-"+i).text(j);
						});
						$("#id").val( json.data.id );
						$("#client-info").slideDown("slow");	
						client = true;
					});					
				}else{
					$('#addClient').modal('show');
					$("#ci").val(ci);
					$("#nombre").focus();
				}
			},
			error: function() {
				alert("Error!");
			}					
		});
	}

	function formToJSON() {
	    return JSON.stringify({
	        "nombre": $('#nombre').val(),
	        "ci": $('#ci').val(),
	        "direccion": $('#direccion').val()
	        });
	}
	/* APARTADO */
	var abono = $("#abono"), total = $("#total"), resta = $("#resta");
	abono.on("change", function(){
		calcularPago();
	});

	var calcularPago = function(){
		var a = abono.val() || 0,
			t = total.val() || 0,
			r = resta.val() || 0;

		resta.val(t-a);
	};
	
})()

