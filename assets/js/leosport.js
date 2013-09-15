jQuery(function($) {	
	$(".ci-value").focus();			
	/* DATATABLE*/
	var inventario = $('#inventario').dataTable();

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

	$.each($(".cantidad"), function(k,v){
		disponible = $(this).data("disponible")
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

	$(".searchci").on("click", function(){
		var ci = $(".ci-value").val();
		searchClientByCi(ci);
	});



	$(".btn-cancel").on("click", function(){
		$(".ci-value").prop('disabled', false);
	});

	



			
	var total = 0;
	$("#inventario").on("click", ".addToCart",function(){
		var id = $(this).attr("rel");
		if($(this).is(":checked"))	{
			do{ 
				if(valor<0){
					alert("Error, el resultado es negativo \nVuelva a ingresar la cantidad...")
				}
				var cantidad = prompt("Cantidad de salida:",0);
				var old = $(this).parent('td').parent('tr').children('td').eq(4).html();
				var valor = (old*1)+(cantidad*1);
			}while(valor<0);

			$(this).parent('td').parent('tr').children('td').eq(4).html(valor);	
			$("#id"+id).val(valor);	
			if(valor >0){total++;}else if(valor == 0){$(this).removeAttr("checked");}
		}else{
			$(this).parent('td').parent('tr').children('td').eq(4).html(0);
			$("#id"+id).val(0);	
			total--;
		}
	});

	$("#inventario").on("change", ".cantidad",function(){
		var c = $(this).val();
		var m = $(this).data('disponible');

		if(c>m){
			$(this).val(m);
		}

		
	});

	$("#submit").on("click",function(){
		if(!$("#factura").val()){
			alert("Ingrese la factura");
			return;
		}
		if(total){ 
	        inventario.fnFilter('');
	        if(confirm("Esta seguro que desea efectuar la compra?")){
	            $("#sellForm").submit();
	        }
	    }else{ alert("No ha seleccionado ningun producto");}
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
						$("#client-info").slideDown("slow");						
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
		
	
})