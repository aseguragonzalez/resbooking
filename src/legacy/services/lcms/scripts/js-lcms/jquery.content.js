function GetContentType(type){
	var texto = "-";
	switch (type){
		case 1:
		texto = "News";
		break;
		case 2:
		texto = "Content";
		break;
		case 3:
		texto = "Gallery";
		break;
	}
	return texto;
}

function lnkCrearContenido(obj){
		var id = $( "select[name='type'] option:selected" ).val();
		var url = $(obj).attr( "href" ) + "/" + id;
		$(obj).attr( "href", url);
		return true;
}

function lnkCargaContenidos(obj){
		var id = $( "select[name='section'] option:selected" ).val();
		var url = $(obj).attr( "href" ) + "/" + id;
		$(obj).attr( "href", url);
		return true;
}

$(function(){

	// Seleccionar la sección de trabajo
	$( "select[name='section'] option[value='" + $( "#section" ).val() + "']" ).attr( "selected", "selected");

	// Columna estado del contenido
	$.each($( "input[name='draft']" ), function(index, item){
		if($(item).data("draft") == 0){
			$(item).removeAttr( "checked" );
		}
	});

	// Columna tipología de contenido
	$.each($( "input[name='type']"), function(index, item){
		var value = parseInt($(item).val());
		var texto =	GetContentType(value);
		$(item).parent().append(texto);
	});

	// Columna de descripción
	$.each($( "input[name='description']"), function(index, item){
		var value = $(item).val().toString();
		if(value.length > 15){
			// Sólo agregamos los 12 primeros caracteres
			var texto =	value.substring(0,11) + "...";
			$(item).parent().append(texto);
			// Agregar el texto como title
			$(item).parent().attr( "title", value);
		}
		else{
			$(item).parent().append(value);
		}
	});

	// Columna de keywords
	$.each($( "input[name='keywords']"), function(index, item){
		var value = $(item).val().toString();
		if(value.length > 15){
			// Sólo agregamos los 12 primeros caracteres
			var texto =	value.substring(0,11) + "...";
			$(item).parent().append(texto);
			// Agregar el texto como title
			$(item).parent().attr( "title", value);
		}
		else{
			$(item).parent().append(value);
		}
	});

	// Columna de títulos
	$.each($( "input[name='title']"), function(index, item){
		var value = $(item).val().toString();
		if(value.length > 15){
			// Sólo agregamos los 12 primeros caracteres
			var texto =	value.substring(0,11) + "...";
			$(item).parent().append(texto);
			// Agregar el texto como title
			$(item).parent().attr( "title", value);
		}
		else{
			$(item).parent().append(value);
		}
	});

});
