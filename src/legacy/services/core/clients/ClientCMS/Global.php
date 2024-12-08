<?php

	try{
		// Cargar script de arranque
		require_once( "../../../references/scripts/trycatch.mvc.router.clientcms.start.php" );
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
	catch(Exception $e){
		print catchError( "Global - Exception" , "_error.html" , $e );
	}

?>
