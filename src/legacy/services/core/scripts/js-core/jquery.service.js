///<summary>
/// Asigna los valores en el formulario de edición
///</summary>
function setServiceData(obj){
	clearServiceData();
	$( "input[name='Id']" ).val($(obj).data( "id" ));
	$( "input[name='Name']" ).val( $(obj).data( "name" ) );
	$( "input[name='Path']" ).val( $(obj).data( "path" ) );
	$( "input[name='Platform']" ).val( $(obj).data( "platform" ) );
	$( "textarea[name='Description']" ).val( $(obj).data( "desc" ) );
}

///<summary>
/// "Limpia" el formulario para crear un nuevo role
///</summary>
function clearServiceData(){
	$( "input[name='Id']" ).val(0);
	$( "input[name='Name']" ).val( "" );
	$( "input[name='Path']" ).val( "/" );
	$( "input[name='Platform']" ).val( "" );
	$( "textarea[name='Description']" ).val( "" );
	$.each($( ".help-block" ), function(index, item){
		$(item).text($(item).data( "msg" ));
	});
	$( ".form-group" ).removeClass( "has-error" );
}

///<summary>
/// Asocia o elimina la asociación entre el role y el servicio
///</summary>
function changeRoleService(obj){
	// Obtener la acción que corresponde
	var action = ($(obj).is( ":checked" )) ? "AddRole" : "RemoveRole";
	// Llamada ajax para la gestión de asociaciones
	$.ajax({
			type : "POST",
			url : $(obj).data( "url" ) + action,
			data : {
				IdRole : $(obj).data("role"),
				IdService : $(obj).data( "service" )
			},
			success:function(){
				$( "#result" ).html( $( "#msg-result" ).html() );
			}
	});
}

///<summary>
/// Configura los checks de servicios relacionados
//</summary>
function setRoleService(){
	if($( "#relations" ).length == 1 && $( "#relations" ).val() != ""){
		var roles = $.parseJSON($( "#relations" ).val());
		$.each($(roles), function(index, item){
			var selector = "#chk_" + item.IdService + "_" + item.IdRole;
			$(selector).attr( "checked", "checked" );
		});
	}
}

function saveService(obj){
	// Evitar la ejecución normal
	event.preventDefault();
	// Obtener path
	var path = $( "input[name='Path']" ).val().toString();
	// Eliminar caracter /
	path = path.replace( "/" , "" );
	// Guardar
	$( "input[name='Path']" ).val(path);

	$.post(
		$(obj).attr( "action" ),
		$(obj).serialize(),
		function( data ) { model = $.parseJSON(data);
			$( "input[name='Id']" ).val(model.Entity.Id);
			$( "input[name='Name']" ).val(model.Entity.Name);
			$( "input[name='Path']" ).val( "/" + model.Entity.Path);
			$( "input[name='Platform']" ).val(model.Entity.Platform);
			$( "input[name='Description']" ).val(model.Description);
			// Setear mensajes
			$( "#eName" ).text( $( "#eName" ).data( "msg" ) + model.eName );
			$( "#ePath" ).text( $( "#ePath" ).data( "msg" ) + model.ePath );
			$( "#ePlatform" ).text( $( "#ePlatform" ).data( "msg" ) + model.ePlatform );
			$( "#eDesc" ).text( $( "#eDesc" ).data( "msg" ) + model.eDesc );
			$( "#eResult" ).text( $( "#eResult" ).data( "msg" ) + model.eResult );

			// Setear css
			if(model.eNameClass != ""){
				$( "#eName" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#eName" ).parent().removeClass( "has-error" );

			if(model.ePathClass != ""){
				$( "#ePath" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#ePath" ).parent().removeClass( "has-error" );

			if(model.ePlatformClass != ""){
				$( "#ePlatform" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#ePlatform" ).parent().removeClass( "has-error" );

			if(model.eDescClass != ""){
				$( "#eDesc" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#eDesc" ).parent().removeClass( "has-error" );

			$( "#eResult" )
				.parent()
				.removeClass( "has-success" )
				.removeClass( "has-error" )
				.addClass( model.eResultClass );

			$.get(
				$( "#frmService" ).data( "success" ),
				function( data ) {
					$( ".table-responsive" ).html( data );
			});
	});
}


$(function(){
	setRoleService();
});
