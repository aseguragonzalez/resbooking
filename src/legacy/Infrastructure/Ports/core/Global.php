<?php

    try{
        $debug = TRUE;
        require_once("lib/core.domain.min.php");
        require_once("lib/debug.trycatch.lib.min.php");
        require_once("lib/debug.resbooking.lib.min.php");
        // Iniciar sesión
	set_session();
        // configuración de la cache
        set_cache(FALSE);
	// Establecer los manejadores de errores
        set_handlers(E_ALL);
	// Establecer localización para las fechas
	setlocale(LC_ALL,"es_ES.UTF-8");
	// Establecer el formato de fecha-hora
	set_time();
	// Establecer parámetros de la url
	setUrl( "Home" );
	// Establecer el modo depuración
	set_debug($debug);
	// Cargar dependencias
	ConfigurationManager::LoadReferences("config.xml");
        // Obtener inyector de dependencias
        $injector = Injector::GetInstance();
        // Obtener implementación
        $module = $injector->Resolve( "IHttpModule" );
        // Validación del module
        if($module != NULL){
            // Iniciar la gestión de la petición
            $module->BeginRequest();
            // Procesado de la petición
            $module->ProcessRequest();
            // Finalizar la ejecución
            $module->EndRequest();
        }
        else{
            throw new Exception("Error: no module");
        }
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
    catch(ajaxException $e){
        print catchError( "Global - ajaxException" , "_ajaxError.html" , $e );
    }
    catch(Exception $e){

        var_dump($e); exit();

        print catchError( "Global - Exception" , "_error.html" , $e );
    }
