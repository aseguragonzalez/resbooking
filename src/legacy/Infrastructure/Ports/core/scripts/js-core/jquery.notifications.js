$(function(){

	// Edición del evento submit
	$( "form" ).on( "submit", function(){
		// Obtener el contenido del campo plantilla
		var template = $( "#Template" ).val();
		// validar que no sea nulo
		if(template != "" && template != undefined){
			// Codificar en base64
			var base64 = $.base64.encode(template, true);
			// Guardar en el campo oculto
			$( "input[name='Template']" ).val(base64);
		}
	});

	// decodificar la plantilla si corresponde
	var template =	$( "input[name='Template']" ).val();
	// Validar si hay template
	if(template != "" && template != undefined){
		// Decodificar el contenido
		var base64decode = $.base64.decode(template, true);
		// Asignar contenido
		$( "#Template" ).val(base64decode);
	}

});
