<?php

/**
 * Establece el modo depuración de la ejecución
 * @param boolean $debug Indica si se establece o no el modo
 */
function set_debug($debug = null){
    // Setear si se ha definido el parámetro
    if(!isset($debug)){
        $val = 1;
    }
    else{
        $val = ($debug) ? 1 : 0;
    }
    define( "DEBUG", $val);
}

/**
 * Establece los manejadores de error y el nivel de error
 * @param const $level Nivel de error para registar. Por defecto : E_ALL
 * @param string $errorHandler Nombre de la función para manipular los
 * errores. Por defecto : application_error_handler
 * @param string $exceptionHandler Nombre de la función para manipular las
 * excepciones. Por defecto : application_exception_handler
 */
function set_handlers( $level = E_ALL,
        $errorHandler = "application_error_handler",
        $exceptionHandler = "application_exception_handler"  ){
    // Establece el nivel de error
    error_reporting($level);
    // Establecer el manejador de errores
    set_error_handler($errorHandler);
    // Establecer el manejador de excepciones
    set_exception_handler($exceptionHandler);
}

/**
 * Establece la sesión actual
 */
function set_session(){
    session_start();
}

/**
 * Establece la zona horaria
 * @param string $zone Nombre de la zona horaria.
 * Por defecto:  Europe/Madrid
 */
function set_time($zone = 'Europe/Madrid' ){
    date_default_timezone_set( $zone );
}

/**
 * Carga todas las referencias indicadas
 * @param array $references Array con la ruta de carga de todas las
 * referencias necesarias
 * @return void
 */
function load_references($references = null){
    // Validar el parámetro pasado como argumento
    if($references == null || !is_array($references)) {
        return;
    }
    // Cargar una a una las referencias
    foreach($references as $reference){
        require_once($reference);
    }
}

/**
 * Establece la cabecera http para no almacenar en cache
 * los recursos del website
 * @param boolean $cache TRUE = guardar
 */
function set_cache($cache = FALSE){
    if($cache == FALSE){
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }
}


/**
 * Visualización del mensaje de error por pantalla
 * @param string $errno Código de error
 * @param string $errstr Mensaje del error
 * @param string $errfile Fichero donde se genera el error
 * @param string $errline Línea donde se genera el error
 * @param string $errcontext Información del contexto
 */
function print_error($errno, $errstr = null, $errfile = null, $errline = null, $errcontext = null){
    $errstr = ($errstr != null) ? $errstr : "-";
    $errfile = ($errfile != null) ? $errfile : "-";
    $errline = ($errline != null) ? $errline : "-";
    $errcontext = ($errcontext != null) ? $errcontext : "-";
    echo "<div>";
    echo "<p>Se ha producido un error.</p>";
    echo "<ul>";
    echo "<li>N. Error : ".$errno."</li>";
    echo "<li>Fichero : ".$errfile."</li>";
    echo "<li>Linea : ".$errline."</li>";
    echo "<li>Detalles : ".$errstr."</li>";
    if(is_string($errcontext)){
        echo "<li>StackTrace : ".$errcontext."</li>";
    }
    elseif(is_array($errcontext)){
        echo "<li><ul>";
        foreach($errcontext as $key => $value){
            echo "<li>$key: $value</li>";
        }
        echo "</ul></li>";
    }
    echo "</ul>";
    echo "</div>";
}

/**
 * Realiza el procesado de errores a nivel global de la aplicación.
 * @param int $errno Codigo de error
 * @param string $errstr Mensaje del error
 * @param string $errfile Fichero donde se genera el error
 * @param string $errline Linea de codigo que genera el error
 * @param string $errcontext Contexto del error
 */
function application_error_handler($errno = 0, $errstr = null,
        $errfile = null, $errline = null, $errcontext = null){

    print_error($errno, $errstr, $errfile, $errline, $errcontext);

    exit();
}

/**
 * Realiza el procesado de excepciones no capturadas a nivel
 * global de la aplicación.
 * @param \Exception $exception Excepción capturada
 */
function application_exception_handler($exception = null){

    var_dump($exception);

    exit();
}


/**
 * Establece los parámetros básicos de la url
 * @param string $startController Nombre del controlador por defecto
 */
function setUrl($startController = ""){
    // Obtener parámetro url si corresponde
    $url = (isset($_REQUEST["url"]))
            ? $url = $_REQUEST["url"] : "";
    // Setear la url solicitada
    if($url != ""){
        $_SERVER['REQUEST_URI'] = $url;
    }
    // Establecer Controlador por defecto
    if($_SERVER['REQUEST_URI'] == "/"){
        $_SERVER['REQUEST_URI'] = "/".$startController;
    }
}

/**
 * Gestión de excepciones capturadas en arquitecturas mvc.
 * Dependencias : Injector, ILogManager, ConfigurationManager
 * @param string $message Mensaje de error
 * @param string $fileName Nombre del fichero
 * @param Exception $e Referencia a la excepcion
 * @return string
 */
function catchError($message = "", $fileName = "" , $e = NULL ){
    // Obtener inyector de dependencias
    $injector = Injector::GetInstance();
    // Obtener implementación
    $log = $injector->Resolve( "ILogManager" );
    // Generar log
    $log->LogErrorTrace( $message , $e);
    // Obtener path
    $sPath = ConfigurationManager::GetKey( "path" );
    // Reemplazar el path en la plantilla
    $sView = str_replace("{Path}" , $sPath,
            file_get_contents( "view/shared/".$fileName ));
    // Obtener ruta de recursos
    $path = ConfigurationManager::GetKey( "resources" );
    // Reemplazar la ruta de recursos
    $view = str_replace( "{Resources}" , $path, $sView);
    // Procesado del error de login si corresponde
    return processLoginError($view);
}

/**
 * Reemplazo de los tags eLogin y eLoginClass
 * @param string $view Vista a renderizar
 */
function replaceLoginError($view = ""){
    if(isset($_SESSION["eLogin"])){
        $sView = str_replace( "{eLogin}", $_SESSION["eLogin"], $view );
        $finalView = str_replace( "{eLoginClass}", "has-error", $sView );
        unset($_SESSION["eLogin"]);
    }
    else{
        $sView = str_replace( "{eLogin}", "", $view );
        $finalView = str_replace( "{eLoginClass}", "", $sView );
    }
    return $finalView;
}

/**
 * Reemplaza el error de nombre de usuario
 * @param type $view
 * @return type
 */
function replaceUsernameError($view = ""){
    if(isset($_SESSION["eUsername"])){
        $sView = str_replace("{eUsername}",$_SESSION["eUsername"],$view);
        $finalView = str_replace("{eUsernameClass}","has-error",$sView);
        unset($_SESSION["eUsername"]);
    }
    else{
        $sView = str_replace( "{eUsername}", "", $view);
        $finalView = str_replace( "{eUsernameClass}","",$sView);
    }
    return $finalView;
}

/**
 * Reemplaza el error de contraseña
 * @param string $view Contenido a renderizar
 * @return string
 */
function replacePasswordError($view = ""){
    if(isset($_SESSION["ePassword"])){
        $sView = str_replace("{ePassword}",$_SESSION["ePassword"],$view);
        $finalView = str_replace("{ePasswordClass}","has-error",$sView);
        unset($_SESSION["ePassword"]);
    }
    else{
        $sView = str_replace("{ePassword}","", $view);
        $finalView = str_replace("{ePasswordClass}","",$sView);
    }
    return $finalView;
}

/**
 * Procesa el mensaje de error en login
 * @param string $view Nombre de la vista
 * @return string
 */
function processLoginError($view = ""){
    // Comprobar si hay vista
    if($view != "" ){
        // Procesar el error del login
        $lView = replaceLoginError($view);
        // Procesar el error del nombre de usuario
        $uView = replaceUsernameError($lView);
        // Procesar el error de la contraseña
        $pView = replacePasswordError($uView);
        // Obtener el nombre de usuario (si es que está enviado)
        $username = (isset($_REQUEST["username"])) ? $_REQUEST["username"] : "";
        // proceso final de la vista
        $view = str_replace( "{username}", $username, $pView );
    }
    return $view;
}

/**
 * Envío de mensajes de soporte para errores
 * @param string $message Mensaje de error a enviar
 * @param Exception $e Referencia a la excepción generada
 */
function sendError($message = "", $e = NULL){

}
