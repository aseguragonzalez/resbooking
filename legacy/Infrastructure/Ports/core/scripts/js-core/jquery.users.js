///<summary>
/// "Limpia" el formulario para crear un nuevo usuario
///</summary>
function clearUserData(){
	$( "input[name='Id']" ).val(0);
	$( "input[name='Username']" ).val( "" );
	$.each($( ".help-block" ), function(index, item){
		$(item).text($(item).data( "msg" ));
	});
	$( ".form-group" ).removeClass( "has-error" );
}

///<summary>
/// Asigna los valores en el formulario de edición
///</summary>
function setUserData(obj){
	clearUserData();
	$( "input[name='Id']" ).val($(obj).data( "id" ));
	$( "input[name='Username']" ).val($(obj).data( "username" ));
}

///<summary>
/// Resetear Password de usuario
///</summary>
function resetUserPass(obj){
	$.ajax({
			type : "POST",
			url : $(obj).data( "url" ) + $(obj).data( "id" )
	})
	.done(function(data){
		try{
			var obj = $.parseJSON(data);
			var msg = (obj.error)
				? "No se ha podido regenerar el password correctamente"
				: "El password se ha generado correctamente" ;
				$( "#msg-result-data" ).text(msg);
				$( "#result" ).html( $( "#msg-result" ).html() );
		}catch(err){
			msg = "Se ha producido un error interno en ejecución" ;
			$( "#msg-result-data" ).text(msg);
			$( "#result" ).html( $( "#msg-result" ).html() );
		}
	})
	.fail(function(data){
		msg = "Se ha producido un error interno. cod : 500" ;
		$( "#msg-result-data" ).text(msg);
		$( "#result" ).html( $( "#msg-result" ).html() );
	});
}

///<summary>
/// Procedimiento para guardar los datos del formulario
//</summary>
function saveUser(obj){
	event.preventDefault();
	$.post(
		$(obj).attr( "action" ),
		$(obj).serialize(),
		function( data ) { model = $.parseJSON(data);
			// Setear datos de usuario
			$( "input[name='Id']" ).val(model.Entity.Id);
			$( "input[name='Username']" ).val(model.Entity.Username);
			// Setear mensajes
			$( "#eUsername" ).text( $( "#eUsername" ).data( "msg" ) + model.eUsername );
			$( "#eResult" ).text( $( "#eResult" ).data( "msg" ) + model.eResult );
			// Setear css
			if(model.eUsernameClass != ""){
				$( "#eUsername" )
					.parent()
					.removeClass( "has-error" )
					.addClass( "has-error" );
			}
			else
				$( "#eUsername" ).parent().removeClass( "has-error" );

			$( "#eResult" )
				.parent()
				.removeClass( "has-success" )
				.removeClass( "has-error" )
				.addClass( model.eResultClass );

			$.get(
				$( "#frmUser" ).data( "success" ),
				function( data ) {
					$( ".table-responsive" ).html( data );
			});
	});
}
