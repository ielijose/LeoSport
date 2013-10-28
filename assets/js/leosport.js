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
		
		Sell.apartado = apartar || false;

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
						var urlNext = "/factura.php?id=";
						if(apartar){
							urlNext = "apartado.php?id=";
						}
						window.location.href = urlNext + data.id;
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
			var x = parseInt(total.text());		
			total.text(parseInt(x + (precio)));
			input.data("old", cantidad);
		}
		
	});
	$(".spinner-down").on("click", function(){
		var input = $(this).parent().parent().children(".cantidad");

		var cantidad = input.val();
		var precio = input.data("precio");
		var old = input.data("old");

		if(cantidad>=0 && (old!=0)){
			var x = parseInt(total.text());		
			total.text(parseInt(x - (precio)));
			input.data("old", cantidad);
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

	$('#new-pay').validate({
		rules: {
			bs: {required: true, number : true},
			descripcion: {required: true}						
		},
		messages: {
			bs: { required: "Campo obligatorio." },
			descripcion: { required: "Campo obligatorio." }					
		},

		submitHandler: function (form) {											
			var datos = JSON.stringify({"factura_id": $('#fid').val(), "bs": $('#bs').val(), "descripcion": $('#descripcion').val() });
			console.log(datos);
			$.ajax({
				url: './api/load-pay', 
				type: 'POST', 
				data: datos,
				success : function(data){
					if(data.pay.id != "error"){
						form.reset();
						$(".btn-cancel").trigger("click");
						window.location.reload();
					}
				}
			});
		}		
	});

	$(".btn-sm-ap").on("click", function(){
		$("#add-client").submit();
	});
	
	$(".save-new-pay").on("click", function(){
		$("#new-pay").submit();
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

	$("#load-pay").on("click", function(){
		$('#cargar-pago').modal('show');
	});
	
})()