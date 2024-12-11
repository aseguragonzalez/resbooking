$(function (){

	var section = "select[name='Section'] option[value='" + $( "#Section" ).val() + "']";
	$( section ).attr( "selected", "selected" );

	var template = "select[name='Template'] option[value='" + $( "#Template" ).val() + "']";
	$( template ).attr( "selected", "selected" );
	// Copiar el valor del enlace
	$( "#Link" ).val($( "[name='Link']" ).val());

	if($( "input[name='Draft']" ).val() == 0){
		$( "#Draft" ).removeAttr( "checked" );
	}

	// Asignar eventos de formulario
	$( "form" ).on( "submit", function(){
		// Comprobar estado de la noticia
		var draft = ($( "#Draft" ).is( ":checked" )) ? 1 : 0;
		// Establecer valor
		$( "input[name='Draft']" ).val(draft);
		// Obtener estado del tipo de enlace
		var extLink = $( "#ExtLink" ).is( ":checked" ) ? 1: 0;
		// Asignar estado al campo oculto
		$( "input[name='ExtLink']" ).val(extLink);
		// Codificar en base64
		var content = $.base64.encode($( "#Content" ).val(), true);
		// Guardar la información de la noticia
		$( "input[name='Content']" ).val(content);
		// Obtener el valor del enlace
		var linkValue = $( "#Link" ).val();
		// Guardar el valor de campo de texto
		$( "input[name='Link']" ).val(linkValue);
	});

	// formatear el contenido del Link al abandonar la casilla
	$( "#Link" ).on( "blur", function(){
		// Comprobar si es un enlace interno
		var extLink = $( "#ExtLink" ).is( ":checked" ) ? 1: 0;
		// Si es externo salimos
		if(extLink == 1) { return; }
		// Obtener texto del enlace formateado
		var enlace = replaceLink($( "#Link" ).val());
		// Guardar
		$( "#Link" ).val(enlace);
	});

	// Proponer como enlace el texto de LinkText formateado correctamente
	$( "#LinkText" ).on( "blur", function(){
		// Comprobar si es un enlace interno
		var extLink = $( "#ExtLink" ).is( ":checked" ) ? 1: 0;
		// Si es externo salimos
		if(extLink == 1) { return; }
		// Replicar el texto
		replicateLink("LinkText", "Link");
	});

});
