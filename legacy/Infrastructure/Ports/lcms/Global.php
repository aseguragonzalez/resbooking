<?php

	try{
		// Cargar script de arranque
		require_once( "../../references/scripts/trycatch.mvc.router.start.php" );
		// Obtener inyector de dependencias
		$injector = Injector::GetInstance();
		// Obtener implementación
		$module = $injector->Resolve( "IHttpModule" );
		// Iniciar la gestión de la petición
		$module->BeginRequest();
		// Procesado de la petición
		$module->ProcessRequest();
		// Finalizar la ejecución
		$module->EndRequest();
	}
	catch(UrlException $e){
		print catchError( "Global - UrlException" , "_notfound.html" , $e );
	}
	catch(UnAuthorizeException $e){
		print catchError( "Global - UnAuthorizeException" , "_unauthorized.html" , $e );
	}
	catch(UnAuthenticateException $e){
		print catchError( "Global - UnAuthenticateException" , "_unauthenticated.html" , $e );
	}
	catch(Exception $e){
		print catchError( "Global - Exception" , "_error.html" , $e );
	}

?>
