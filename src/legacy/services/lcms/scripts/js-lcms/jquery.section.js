$(function(){

	$.each($( "input[name='draft']" ), function(index, item){
		if($(item).data( "draft" ) == 0){
			$(item).removeAttr( "checked" );
		}
	});

	$.each($( "input[name='root']" ), function(index, item){
		if($(item).data( "root" ) != ""){
			$(item).removeAttr( "checked" );
		}
	});

	$( "form" ).on( "submit", function(){
		// Obtener Link
		var lnk = $( "input[name='Link']" ).val().toString();
		// Obtener posición del último /
		var position = lnk.lastIndexOf( "/" );
		// Manipular el contenido
		if(position == 0){
			// Extraer último nodo
			lnk = lnk.substr(position + 1);
			// Agregar ruta
			if($( "#Root" ).val() != "null"){
				// optener el texto de la opción seleccionada
				var name = $( "#Root option:selected" ).text();
				// convertir a minúsculas sin espacio en blanco
				lnk = replaceLink(lnk);
				// Asignar el texto
				$( "input[name='Link']" ).val(name + "/" + lnk);
			}
		}
	});

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
