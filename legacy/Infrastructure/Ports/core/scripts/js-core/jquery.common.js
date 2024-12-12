function deleteItem(obj){
	window.location = $(obj).data( "url" ) + $(obj).data( "id" );
}

function setId(selector, id){
	$( selector ).data( "id" , id);
}

function setDataModal(obj){
	$( "#modal-title" )
		.text($(obj).data( "title" ));
	$( "#modal-msg" )
		.text($(obj).data( "msg" ));
	$($(obj).data( "selector" ))
		.data( "url", $(obj).data( "url" ))
		.data( "id", $(obj).data( "id" ));
}

function navigateModal(obj){
	window.location = $(obj).data( "url" ) + $(obj).data( "id" );
}

$(function(){
	// Activar opción del menú
	$( $( "#menu-option" ).val() ).addClass( "active" );

	$.each($( "select" ), function(index, item){
		var value = $(item).data( "value" );
		if( value != "" && value != undefined ){
			$(item)
				.find( "option" )
				.removeAttr( "selected" );
			$(item).val(value);
			$(item)
				.find( "option[value='" + value + "']" )
				.attr( "selected", "selected" );
		}
	});

});
