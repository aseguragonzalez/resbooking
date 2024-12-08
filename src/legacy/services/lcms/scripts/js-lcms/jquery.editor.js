$(function() {

// 	tinymce.init({ selector:'#texto' });

	$( "form" ).on( "submit", function(){
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
		// Configurar evento submit para copiar el contenido del editor al campo correspondiente
		var content = $( "#text-container" ).html().toString();
		// Reemplazar el caracter correspondiente
		// content = content.replace( /"/g, "'" );
		// Codificar en base64 el contenido
		content = $.base64.encode(content, true);
		// Guardar el contenido
    $( "#Content" ).val(content);

		$( ".img-selected" ).removeClass( "img-selected" );

		$( ".text-selected" ).removeClass( "text-selected" );

	});

	// Marcar la sección padre
	$( "select[name='Section'] option[value='" + $( "#Section" ).val() + "']" ).attr( "selected", "selected" );
	// Marcar la plantilla
	$( "select[name='Template'] option[value='" + $( "#Template" ).val() + "']" ).attr( "selected", "selected" );
	// Copiar el valor del enlace
	$( "#Link" ).val($( "[name='Link']" ).val());
	// Marcar si es borrador
	if($( "input[name='Draft']" ).val() == 0){
		$( "#Draft" ).removeAttr( "checked" );
	}
	// Marcar si el enlace es externo o no
	if($( "input[name='ExtLink']" ).val() == 1){
		$( "#ExtLink" ).attr( "checked" , "checked" );
	}

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

  // Asignar eventos de modificación a bloques de texto
  $( ".text-item" ).click(function(){
      // Eliminamos selección de objeto si existe
      $( "#end-text" ).click();
      // Actualizar la selección del objeto actual
      $(this).addClass( "text-selected" );
      // Marcamos la selección para mover
      $( "#move-text" ).click();
      // Visualizar Menú
      $( ".text-menu" ).css( "display" , "inline-block" );
  });

  // Asignar eventos de modificación a bloques de imagen
  $( ".image-content" ).click(function(){
      // Eliminamos selección de objeto si existe
      $( "#end-img" ).click();
      // Actualizar la selección del objeto actual
      $(this).addClass( "img-selected" );
      // Marcamos la selección para mover
      $( "#move-img" ).click();
      // Visualizar Menú
      $( ".img-menu" ).css( "display" , "inline-block" );
  });

//	$( "#text-container" ).droppable({ valid : "#images img" });

	$( "#resize-img" ).click(function(){
		if($( ".img-selected" ).length == 1){
		  // Marcar la opción del menú
		  $( ".selected" ).removeClass( "selected" );
		  $(this).addClass( "selected" );
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".img-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".img-selected" ).draggable( "destroy" ); }
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".img-selected" ).is( ":ui-resizable" );
		  if(!resizable){	$( ".img-selected" ).resizable();	}
		}
	});

	$( "#move-img" ).click(function(){
		if($( ".img-selected" ).length == 1){
		  // Marcar la opción del menú
		  $( ".selected" ).removeClass( "selected" );
		  $(this).addClass( "selected" );
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".img-selected" ).is( ":ui-resizable" );
		  if(resizable){	$( ".img-selected" ).resizable( "destroy" ); }
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".img-selected" ).is( ":ui-draggable" );
		  if(!draggable){ $( ".img-selected" ).draggable({ revert: "invalid" }); }
		}
	});

	$( "#end-img" ).click(function(){
		if($( ".img-selected" ).length != 0){
		  $( ".selected" ).removeClass( "selected" );
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".img-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".img-selected" ).draggable( "destroy" ); }
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".img-selected" ).is( ":ui-resizable" );
		  if(resizable){	$( ".img-selected" ).resizable( "destroy" ); }
		  // Eliminar selección del objeto
		  $( ".img-selected").removeClass( "img-selected" );
		  $( ".img-menu" ).css( "display", "none" );
		}
	});

	$( "#delete-img" ).click(function(){
		if($( ".img-selected" ).length == 1){
		  $( ".selected" ).removeClass( "selected" );
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".img-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".img-selected" ).draggable( "destroy" ); }
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".img-selected" ).is( ":ui-resizable" );
		  if(resizable){	$( ".img-selected" ).resizable( "destroy" ); }
		  // Eliminar objeto
		  $( ".img-selected" ).remove();
		  $( ".img-menu" ).css( "display" , "none" );
		}
	});

	$( "#resize-text" ).click(function(){
		if($( ".text-selected" ).length == 1){
		  // Marcar la opción del menú
		  $( ".selected" ).removeClass( "selected" );
		  $(this).addClass( "selected" );
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".text-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".text-selected" ).draggable( "destroy" ); }
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".text-selected" ).is( ":ui-resizable" );
		  if(!resizable){	$( ".text-selected" ).resizable();	}
		}
	});

	$( "#move-text" ).click(function(){
		if($( ".text-selected" ).length == 1){
		  // Marcar la opción del menú
		  $( ".selected" ).removeClass( "selected" );
		  $(this).addClass( "selected" );
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".text-selected" ).is( ":ui-resizable" );
		  if(resizable){	$( ".text-selected" ).resizable( "destroy" ); }
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".text-selected" ).is( ":ui-draggable" );
		  if(!draggable){ $( ".text-selected" ).draggable({ revert: "invalid" }); }
		}
	});

	$( "#edit-text" ).click(function(){
		if($( ".text-selected" ).length == 1){
		  // Marcar la opción del menú
		  $( ".selected" ).removeClass( "selected" );
		  $(this).addClass( "selected" );
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".text-selected" ).is( ":ui-resizable" );
		  if(resizable){ $( ".text-selected" ).resizable( "destroy" ); }
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".text-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".text-selected" ).draggable({ revert: "invalid" }); }
		  // Obtener el contenido del objeto seleccionado
		  var contenido = $(".text-selected").html();
		  // Copiar el contenido en el editor de texto
		  tinyMCE.get( "texto" ).setContent(contenido);
		}
	});

	$( "#end-text" ).click(function(){
		if($( ".text-selected" ).length != 0){
		  $( ".selected" ).removeClass( "selected" );
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".text-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".text-selected" ).draggable( "destroy" ); }
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".text-selected" ).is( ":ui-resizable" );
		  if(resizable){	$( ".text-selected" ).resizable( "destroy" );	}
		  // Eliminar selección del objeto
		  $( ".text-selected" ).removeClass( "text-selected" );

		  $( ".text-menu" ).css( "display" , "none" );
		}
	});

	$( "#delete-text" ).click(function(){
		if($( ".text-selected" ).length == 1){
		  $( ".selected" ).removeClass( "selected" );
		  // Modificar el comportamiento Draggable
		  var draggable = $( ".text-selected" ).is( ":ui-draggable" );
		  if(draggable){ $( ".text-selected" ).draggable( "destroy" ); }
		  // Modificar el comportamiento Resizable
		  var resizable = $( ".text-selected" ).is( ":ui-resizable" );
		  if(resizable){	$( ".text-selected" ).resizable( "destroy" );	}
		  // Eliminar objeto
		  $( ".text-selected" ).remove();

		  $( ".text-menu" ).css( "display" , "none" );
		}
	});


	$("#addImg").click(function(){

		$( ".img-selected" ).removeClass( "img-selected" );

		if($( ".prev-img-selected" ).length == 1){
		  // Clonar elemento seleccionado
		  var clone = $( ".prev-img-selected" ).clone();
		  // Agregar evento click
		  $(clone).click(function(){
		    // Eliminamos selección de objeto si existe
		    $( "#end-img" ).click();
		    // Actualizar la selección del objeto actual
		    $(this).addClass( "img-selected" );
		    // Marcamos la selección para mover
		    $( "#move-img" ).click();
		    // Visualizar Menú
		    $( ".img-menu" ).css( "display" , "inline-block" );
		  });
		  // Agregar elemento clonado al contenedor
		  $(clone).appendTo($( "#text-container" ));
		  // Eliminar seleccion
		  $( ".prev-img-selected" ).removeClass( "prev-img-selected" );
		}
		else{
		  alert( "Tiene que seleccionar una imagen en primer lugar" );
		}
	});

	$( "#addText" ).click(function(){

		var content = tinyMCE.get( "texto" ).getContent();

		// Estamos editando un texto ya seleccionado
		if($( ".text-selected" ).length == 1){
		  if(content != ""){
		    $( ".text-selected" ).html(content);
		  }
		  else{
		    $( ".text-selected" ).remove();
		  }
		}
		// Agregamos un texto nuevo al contenido
		else{
		  // Comprobamos si es una cadena vacía o n o
		  if(content != ""){
		    var obj = $( "<div />" ).addClass( "text-item" ).append(content);
		    $(obj).click(function(){
		      // Eliminamos selección de objeto si existe
		      $( "#end-text" ).click();
		      // Actualizar la selección del objeto actual
		      $(this).addClass( "text-selected" );
		      // Marcamos la selección para mover
		      $( "#move-text" ).click();
		      // Visualizar Menú
		      $( ".text-menu" ).css( "display" , "inline-block" );
		    });
		    $( "#text-container" ).append(obj);
		  }
		  else{
		    alert( "No puede agregar un area de texto sin contenido" );
		  }
		}
		tinyMCE.get( "texto" ).setContent("");
	});

	$( "#images img" ).click(function(){
		// Eliminar seleccion de los elementos seleccionados previos
		$( ".prev-img-selected" ).removeClass( "prev-img-selected" );
		// Agregar clase css para identificar elemento seleccionado
		$(this).addClass( "prev-img-selected" );
	});

});
