$(function(){

	$( "form" ).on( "submit", function(){
		// Obtener el el estado : Borrador|Publicado
		var value = ($( "#chkDraft" ).is( ":checked" )) ? 1 : 0;
		// Guardar el estado
		$( "input[name='Draft']" ).val(value);
		// Obtener Link
		var lnk = $( "input[name='Link']" ).val().toString();
		// Obtener posición del último /
		var position = lnk.lastIndexOf( "/" );
		// Manipular el contenido
		if(position != -1){
			// Extraer último nodo
			lnk = lnk.substr(position + 1);
		}

		// Agregar ruta
		if($( "select[name='Root'] option:selected" ).val() != "null"){
			// optener el texto de la opción seleccionada
			var name = $( "select[name='Root'] option:selected" ).text();
			// convertir a minúsculas sin espacio en blanco
			lnk = replaceLink(lnk);
			// Asignar el texto
			$( "input[name='Link']" ).val(name + "/" + lnk);
		}

	});

	// Setear Root seleccionado
	$( "select[name='Root'] option[value='" + $( "#Root" ).val() + "']" ).attr( "selected", "selected" );
	// Setear Plantilla seleccionada
	$( "select[name='Template'] option[value='" + $( "#Template" ).val() + "']" ).attr( "selected", "selected" );
	// Setear estado : Borrador | Publicado
	if($( "input[name='Draft']" ).val() == 0){
		$( "#chkDraft" ).removeAttr( "checked" );
	}

	// Setear link
	var lnk = $( "input[name='Link']" ).val().toString();
	// Obtener posición del último /
	var position = lnk.lastIndexOf( "/" );
	// Manipular el contenido
	if(position != -1){
		// Extraer último nodo
		lnk = lnk.substr(position + 1);
	}
	// Guardar el valor del nombre enlace
	$( "input[name='Link']" ).val(lnk);

	// Setear eventos
	$( "input[name='Name']" ).on( "blur", function() {
		// Obtener el valor
		var enlace = $( "input[name='Name']" ).val();
		// Reemplazar los caracteres
		enlace = replaceLink(enlace);
		// Guardar valor
		$( "input[name='Link']" ).val(enlace);
	});

	$( "input[name='Link']" ).on( "blur", function() {
		// Obtener el valor
		var enlace = $( "input[name='Link']" ).val();
		// Reemplazar los caracteres
		enlace = replaceLink(enlace);
		// Guardar valor
		$( "input[name='Link']" ).val(enlace);
	});
});
