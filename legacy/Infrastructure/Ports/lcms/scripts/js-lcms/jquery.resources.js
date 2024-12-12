$(function(){


	$('#myTab a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	})

	$( "form" ).on( "submit", function(){
		if($(this).find( "input[name='NewName']" ).length == 1){
			// Obtener nuevo nombre
			var name = $(this).find( "input[name='NewName']" ).val().toString();
			// Comprobar si tiene extensión asociada
			var pos = name.lastIndexOf( "." );
			// En caso de no tener extensión, agregamos la original
			if(pos == -1){
				// Obtener nombre anterior
				var oldName = $(this).find( "input[name='File']" ).val().toString();
				// extraer la extensión
				pos = oldName.lastIndexOf( "." );
				var ext = oldName.substring(pos);
				// Asignar la extensión al nuevo número
				name += ext;
			}
			// Pasar a minúsculas
			name = name.toLowerCase();
			// Eliminar espacios en blanco
			name = name.replace( / /g, "-");
			// Guardar el valor
			$(this).find( "input[name='NewName']" ).val(name);
		}
	});

});
