function setContentForm(){

	tinymce.init({selector:'#texto'});

	$( "form" ).on( "submit", function(){
		// Obtener estado del borrador del check
		var draft = $( "#Draft" ).is( ":checked" ) ? 1: 0;
		// Asignar estado al campo oculto
		$( "input[name='Draft']" ).val(draft);
		// Configurar evento submit para copiar el contenido del editor al campo correspondiente
		var content = $( "#text-container" ).html().toString();

		content = content.replace( /"/g, "'" );

		content = $.base64.encode(content, true);

    $( "#Content" ).val(content);

		$( ".img-selected").removeClass( "img-selected" );

	  $( ".text-selected" ).removeClass( "text-selected" );

	});

	// Marcar la sección padre
	$( "select[name='Section'] option[value='" + $( "#Section" ).val() + "']" ).attr( "selected", "selected" );
	// Marcar la plantilla
	$( "select[name='Template'] option[value='" + $( "#Template" ).val() + "']" ).attr( "selected", "selected" );
	// Marcar si es borrador
	if($( "input[name='Draft']" ).val() == 0){
		$( "#Draft" ).removeAttr( "checked" );
	}

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

}
