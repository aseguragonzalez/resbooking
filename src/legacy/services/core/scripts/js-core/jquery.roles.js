///<summary>
/// Asigna los valores en el formulario de edición
///</summary>
function setRoleData(obj){
	clearRoleData();
	$( "input[name='Id']" ).val($(obj).data( "id" ));
	$( "input[name='Name']" ).val($(obj).data( "name" ));
	$( "textarea[name='Description']" ).val($(obj).data( "desc" ));
}

///<summary>
/// "Limpia" el formulario para crear un nuevo role
///</summary>
function clearRoleData(){
	$( "input[name='Id']" ).val(0);
	$( "input[name='Name']" ).val( "" );
	$( "textarea[name='Description']" ).val( "" );
	$.each($( ".help-block" ), function(index, item){
		$(item).text($(item).data( "msg" ));
	});
	$( ".form-group" ).removeClass( "has-error" );
}

///<summary>
/// Asocia o elimina la asociación entre el rol y el servicio
///</summary>
function changeServiceRole(obj){
	// Obtener la acción que corresponde
	var action = ($(obj).is( ":checked" )) ? "Add" : "Remove";
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
function setServiceRole(){
	if($( "#relations" ).length == 1 && $( "#relations" ).val() != ""){
		var servicios = $.parseJSON($( "#relations" ).val());
		$.each($(servicios), function(index, item){
			var selector = "#chk_" + item.IdRole + "_" + item.IdService;
			$(selector).attr( "checked", "checked" );
		});
	}
}

///<summary>
/// Procedimiento para guardar los datos del formulario
//</summary>
function saveRole(obj){
	event.preventDefault();
	$.post(
		$(obj).attr( "action" ),
		$(obj).serialize(),
		function( data ) { model = $.parseJSON(data);
			$( "input[name='Id']" ).val(model.Entity.Id);
			$( "input[name='Name']" ).val(model.Entity.Name);
			$( "input[name='Description']" ).val(model.Description);
			// Setear mensajes
			$( "#eName" ).text( $( "#eName" ).data( "msg" ) + model.eName );
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
				$( "#frmRole" ).data( "success" ),
				function( data ) {
					$( ".table-responsive" ).html( data );
			});
	});
}

$(function(){
	// Setear los checks
	setServiceRole();
});
