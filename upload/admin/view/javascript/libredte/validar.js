$(document).ready(function() {
	$('#input-libredte_backend_rut').Rut({
		on_error: function(){
			alert('El rut ingresado es incorrecto');$('#input-libredte_backend_rut').val('');$('#input-libredte_backend_rut').focus(); 
		},
       format_on: 'keyup' 
	});
});