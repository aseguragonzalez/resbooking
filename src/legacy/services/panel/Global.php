<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$debug = !TRUE;

try{
    require_once("libs/debug.resbooking.lib.php");
    require_once("libs/debug.resbooking.common.php");
    // Iniciar sesión
    set_session();
    // configuración de la cache
    set_cache(FALSE);
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
catch(ProjectException $e){
    header("Location: /Home/Index");
    exit();
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
