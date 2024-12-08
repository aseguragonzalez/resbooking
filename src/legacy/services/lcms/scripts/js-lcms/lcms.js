///<summary>
/// Copia la cadena de texto contenida en el control1 en el control2 ( si está vacio )
/// convirtiendo los caracteres a minúsculas y reemplazando los espacios en blanco
/// por '-'. Los parámetros deben ser los id's de los input
///<summary>
function replicateLink(control1, control2){
	try{
		// Comprobar que existen ambos controles
		if($("#" + control1).length != 1
				|| $("#" + control2).length != 1
				|| $("#" + control2).val() != "" ){	return;	}

		// Obtener su valor
		var enlace = $("#" + control1).val();
		// Si es interno formateamos el texto
		enlace = enlace.toString();
		// Pasar a minúsculas
		enlace = enlace.toLowerCase();
		// Reemplazar espacios en blanco
		enlace = enlace.replace( / /g, "-" );
		// Guardar
		$("#" + control2).val(enlace);
	}
	catch(err){
		$("#" + control2).val( "" );
	}
}

///<summary>
/// Convierte la cadena de texto a minúsculas reemplazando
/// los espacios en blanco por '-'
///<summary>
function replaceLink(textoEnlace){
	try{
		// Si es interno formateamos el texto
		var enlace = textoEnlace.toString();
		// Pasar a minúsculas
		enlace = enlace.toLowerCase();
		// Reemplazar espacios en blanco
		textoEnlace = enlace.replace( / /g, "-" );
	}
	catch(err){
		textoEnlace = "";
	}
	return textoEnlace;
}
