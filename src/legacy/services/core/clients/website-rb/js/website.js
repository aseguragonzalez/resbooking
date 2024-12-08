$(function(){
	// Configurar el formulario de reservas
	if($( "#frmReservas" ).length == 1){
		$( "#frmReservas" ).resbooking({
			id:7,
			cssClass:"contenedor embed-responsive-item"
		});
	}

	// Configurar aviso de cookies
	if($( "#aviso-cookies" ).length == 1){
		$( "#aviso-cookies" ).AvisoCookies( "cookies-ws" );
	}
});
