$(function(){

	// Obtener path
	var path = $( "input[name='Path']" ).val().toString();
	// Eliminar caracter /
	path = path.replace( "/" , "" );
	// Guardar
	$( "input[name='Path']" ).val(path);

	$( "form" ).on( "submit", function(){
		// Obtener path
		var path = $( "input[name='Path']" ).val().toString();
		// Eliminar caracter /
		path = path.replace( "/" , "" );
		// Guardar
		$( "input[name='Path']" ).val(path);
	});

	$( "[name='services']" ).on( "change", function(){
		// Obtener la acción que corresponde
		var url = ($(this).is( ":checked" )) ? "AddService" : "RemoveService";
		// Concatenar el resot de la url
		url = "{Path}/Project/" + url;
		// Llamada ajax para la gestión de asociaciones
		$.ajax({
				type : "POST",
				url : url,
				data : { IdProject : $(this).data("project"),	IdService : $(this).data( "service" ) }
		})
		.done(function(data){
			//var obj = $.parseJSON(data);
		})
		.fail(function(data){
			//var obj = $.parseJSON(data);
		});
	});

	$( "[name='users']" ).on( "change", function(){
		// Obtener la acción que corresponde
		var url = ($(this).is( ":checked" )) ? "AddUser" : "RemoveUser";
		// Concatenar el resot de la url
		url = "{Path}/Project/" + url;
		// Llamada ajax para la gestión de asociaciones
		$.ajax({
				type : "POST",
				url : url,
				data : { IdProject : $(this).data("project"),	IdUser : $(this).data( "user" ) }
		})
		.done(function(data){
			//var obj = $.parseJSON(data);
		})
		.fail(function(data){
			//var obj = $.parseJSON(data);
		});
	});

});
