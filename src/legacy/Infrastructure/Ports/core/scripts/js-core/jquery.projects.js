///<summary>
/// Asigna los valores en el formulario de edición
///</summary>
function setProjectData(obj){
	clearProjectData();
	$( "input[name='Id']" ).val($(obj).data( "id" ));
	$( "input[name='Name']" ).val($(obj).data( "name" ));
	$( "textarea[name='Description']" ).val($(obj).data( "desc" ));
	$( "input[name='Path']" ).val("/" + $(obj).data( "path" )).attr( "disabled" , "disabled" );
	var date = $(obj).data( "date" ).toString().split( "-" );
	var today = date[2] + "-" + date[1] + "-" + date[0];
	$( "input[name='Date']" ).val(today).attr( "disabled" , "disabled" );
}

///<summary>
/// "Limpia" el formulario para crear un nuevo proyecto
///</summary>
function clearProjectData(){
	$( "input[name='Id']" ).val(0);
	$( "input[name='Name']" ).val( "" );
	$( "textarea[name='Description']" ).val( "" );
	$( "input[name='Path']" ).val( "" ).removeAttr( "disabled" );
	$( "input[name='Date']" ).val( "" ).removeAttr( "disabled" );
	$.each($( ".help-block" ), function(index, item){
		$(item).text($(item).data( "msg" ));
	});
	$( ".form-group" ).removeClass( "has-error" );
}

///<summary>
/// Procedimiento para guardar los datos del formulario
//</summary>
function saveProject(obj){

	event.preventDefault();
	// Obtener path
	var path = $(obj).find( "input[name='Path']" ).val().toString();
	// Eliminar caracter /
	path = path.replace( "/" , "" );
	// Guardar
	$(obj).find( "input[name='Path']" ).val(path);

	$.post(
		$(obj).attr( "action" ),
		$(obj).serialize(),
		function( data ) {
			model = $.parseJSON(data);
			var date = model.Entity.Date.split( "-" );
			var today = date[2] + "-" + date[1] + "-" + date[0];
			$( "input[name='Id']" ).val(model.Entity.Id);
			$( "input[name='Name']" ).val(model.Entity.Name);
			$( "textarea[name='Description']" ).val(model.Entity.Description);
			$(obj).find( "input[name='Path']" ).val("/" + model.Entity.Path);
			$( "input[name='Date']" ).val(today);
			if(model.Entity.Id != 0){
				$(obj).find( "input[name='Path']" ).attr( "disabled" , "disabled" );
				$( "input[name='Date']" ).attr( "disabled" , "disabled" );
			}
			// Setear mensajes
			$( "#eName" ).text( $( "#eName" ).data( "msg" ) + model.eName );
			$( "#eDesc" ).text( $( "#eDesc" ).data( "msg" ) + model.eDesc );
			$( "#ePath" ).text( $( "#Path" ).data( "msg" ) + model.ePath );
			$( "#eDate" ).text( $( "#eDate" ).data( "msg" ) + model.eDate );
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

			if(model.ePathClass != ""){
				$( "#ePath" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#ePath" ).parent().removeClass( "has-error" );

			if(model.eDateClass != ""){
				$( "#eDate" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#eDate" ).parent().removeClass( "has-error" );

			$( "#eResult" )
				.parent()
				.removeClass( "has-success" )
				.removeClass( "has-error" )
				.addClass( model.eResultClass );

			$.get(
				$( "#frmProject" ).data( "success" ),
				function( data ) {
					$( ".table-responsive" ).html( data );
			});
	});
}

///<summary>
/// Establecer la relación entre usuario y proyecto
//</summary>
function setUserProject(obj){
	// Obtener la acción que corresponde
	var url = ($(obj).is( ":checked" )) ? "AddUser" : "RemoveUser";
	// Concatenar el resot de la url
	url = $(obj).data( "url" ) + url;
	// Llamada ajax para la gestión de asociaciones
	$.ajax({
			type : "POST",
			url : url,
			data : {
				IdProject : $(obj).data("project"),
				IdUser : $(obj).data( "user" )
			}
	});
}

///<summary>
/// Establecer la relación entre servicio y proyecto
//</summary>
function setServiceProject(obj){
	// Obtener la acción que corresponde
	var url = ($(obj).is( ":checked" )) ? "AddService" : "RemoveService";
	// Concatenar el resot de la url
	url = $(obj).data( "url" ) + url;
	// Llamada ajax para la gestión de asociaciones
	$.ajax({
			type : "POST",
			url : url,
			data : {
				IdProject : $(obj).data("project"),
				IdService : $(obj).data( "service" )
			}
	});
}


$(function(){

	if($( "#servicesproject" ).length == 1
		&& $( "#servicesproject" ).val() != undefined
		&& $( "#servicesproject" ).val() != ""){
		// des-serializar array
		var array = $.parseJSON($( "#servicesproject" ).val());
		// marcar los checks
		$.each(array, function(index, item){
			$( "input[data-service='" + item.IdService +  "']" )
				.attr( "checked", "checked" );
		});
	}

	if($( "#usersproject" ).length == 1
		&& $( "#usersproject" ).val() != undefined
		&& $( "#usersproject" ).val() != ""){
		// des-serializar array
		var array = $.parseJSON($( "#usersproject" ).val());
		// marcar los checks
		$.each(array, function(index, item){
			$( "input[data-user='" + item.IdUser +  "']" )
				.attr( "checked", "checked" );
		});
	}

});
