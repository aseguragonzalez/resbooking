$(function(){

	$( "form" ).on( "submit", function(){
		var $images = "";
		$.each($( ".image-check" ), function(index,item) {
			$images += ($(item).is( ":checked" )) ? $(item).data( "name" ) + ";" : "";
		});
		// Codificar en base64
		var content = $.base64.encode($images, true);
		// Almacenar el contenido
		$( "#Content" ).val(content);
		// Obtener estado del borrador del check
		var draft = $( "#Draft" ).is( ":checked" ) ? 1: 0;
		// Asignar estado al campo oculto
		$( "input[name='Draft']" ).val(draft);
		// Obtener estado del tipo de enlace
		var extLink = $( "#ExtLink" ).is( ":checked" ) ? 1: 0;
		// Asignar estado al campo oculto
		$( "input[name='ExtLink']" ).val(extLink);
		// Obtener el valor del enlace
		var linkValue = $( "#Link" ).val();
		// Guardar el valor de campo de texto
		$( "input[name='Link']" ).val(linkValue);
	});

	// Marcar los checks ya seleccionados
	var imgNames = $( "#Content" ).val().toString().split(";");

	$.each(imgNames, function(index, item){
		$( "input[data-name='" + item + "']" ).attr( "checked", "checked" );
	});

	// Marcar la sección padre
	$( "select[name='Section'] option[value='" + $( "#Section" ).val() + "']" ).attr( "selected", "selected" );

	// Marcar la plantilla
	$( "select[name='Template'] option[value='" + $( "#Template" ).val() + "']" ).attr( "selected", "selected" );

	// Marcar si es borrador
	if($( "input[name='Draft']" ).val() == 0){
		$( "#Draft" ).removeAttr( "checked" );
	}
	// Copiar el valor del enlace
	$( "#Link" ).val($( "[name='Link']" ).val());

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
