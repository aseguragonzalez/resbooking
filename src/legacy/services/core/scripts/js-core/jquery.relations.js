function onProjectChange(obj){
	window.location = $(obj).data( "url" ) + "?p=" + $(obj).val();
}

function setRoles(){
$( "input[type='checkbox']" )
		.removeAttr( "checked" )
		.attr("disabled","disabled");
	if($( "#relations" ).val() != ""
		&& $( "#relations" ).val() != undefined){
		var relations = $.parseJSON($( "#relations" ).val());
		$.each(relations, function(index, item){
			var inputs =	$( ".services[data-service='" + item.IdService + "']" ).find( "input" );
			$.each(inputs, function(ind, obj){
				var roles = item.roles;
				$.each(roles, function(i, o){
					if($(obj).data( "role" ) == o.IdRole){
						$(obj).removeAttr( "disabled" );
						if(o.Checked == true)
							$(obj).attr( "checked", "checked" );
					}
				});
			});
		});
	}
}

function setRelation(obj){
	var o = {
		IdUser:$( "#user" ).val(),
		IdProject:$( "#slcProjects" ).val(),
		IdRole:$(obj).data( "role" ),
		IdService: $(obj).data( "service" )
	};
	$.post( $( "#relation-url" ).val(), o);
}


$(function(){
	// Configurar la tabla
	if($( "#roles" ).val() != undefined
			&& $( "#roles" ).val() != "" ){
		// obtener número de columnas para agregar
		var roles = $.parseJSON($( "#roles" ).val());
		// Construir tabla
		$.each($( ".services" ), function(index, $item){
			$.each($(roles), function(i, obj){
				var input = $( "<input />" )
					.attr( "type", "checkbox" )
					.attr( "disabled", "disabled" )
					.attr( "onclick", "setRelation(this)" )
					.data( "service" , $($item).data( "service" ) )
					.data( "role" , obj.Id );
				var td = $( "<td />" )
					.data("service", $($item).data( "service" ))
					.data("role", obj.Id )
					.append(input);
				$($item).append(td);
			});
		});
	}

	setRoles();
});
