<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfaz para el objeto de acceso a datos
 *
 * @author alfonso
 */
interface IDataAccessObject{

    /**
     * Permite configurar los parámetros de la conexión al
     * sistema de persistencia
     * @param array $connection Array con los parámetros de conexión
     */
    public function Configure($connection = null);

    /**
     * Persiste la entidad en el sistema y la retorna actualizada
     * @param object $entity Referencia a la entidad
     */
    public function Create($entity);

    /**
     * Obtiene una entidad filtrada por su identidad utilizando el nombre
     * del tipo de entidad
     * @param object $identity Identidad de la entidad
     * @param string $entityName Nombre de la entidad
     */
    public function Read($identity, $entityName);

    /**
     * Actualiza la información de la entidad en el sistema de persistencia.
     * @param object $entity Referencia a la entidad
     */
    public function Update($entity);

    /**
     * Elimina la entidad utilizando su identidad y el nombre del
     * tipo de entidad
     * @param object $identity Identidad de la entidad
     * @param string $entityName Nombre de la entidad
     */
    public function Delete($identity, $entityName);

    /**
     * Obtiene el conjunto de entidades existentes del tipo especificado
     * @param string $entityName Nombre de la entidad
     */
    public function Get($entityName);

    /**
     * Obtiene el conjunto de entidades del tipo especificado mediante el
     * filtro especificado. El filtro debe ser un array del tipo:
     * array( "PropertyName" => $propValue, ... )
     * @param string $entityName Nombre de la entidad
     * @param array $filter filtro de búsqueda
     */
    public function GetByFilter($entityName, $filter);

    /**
     * Ejecuta la consulta pasada como parámetro
     * @param string $query Consulta sql libre
     */
    public function ExeQuery($query);

    /**
     * Valida el contenido de una entidad
     * @param object $entity Referencia a la entidad a validar
     */
    public function IsValid($entity);

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfaz para el manejador de peticiones http en contextos MVC
 *
 * @author alfonso
 */
interface IHttpHandler{

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido (existe).
     * @param string $controller Nombre del controlador a validar
     */
    public function ValidateController($controller);

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido y si existe alguna acción
     * en dicho controlador con el nombre pasado como argumento.
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function Validate($controller, $action);

    /**
     *  Establece el controlador y la acción por defecto si procede
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function SetDefault($controller, $action);

    /**
     * Procesa la url actual obteniendo el nombre del controlador, la
     * acción y los parámetros de la petición realizada. La información
     * obtenida se retornará mediante un array/diccionario donde se
     * especifique cada uno de las partes obtenidas. En el caso de que la
     * url no esté correctamente formada se lanzará una excepción.
     * @param string $urlRequest Url a procesar
     */
    public function ProcessUrl($urlRequest);

    /**
     * Debe encargarse de realizar el procesado de los parámetros de la
     * petición según las especificaciones del proyecto.
     * @param string $parameters parámetros pasados por url
     */
    public function ProcessParameters($parameters);

    /**
     * Se encarga de configurar el idioma de la petición/ ejecución en los
     * contextos donde es necesario(aplicaciones multilenguaje).
     * @param type $language
     */
    public function SetLanguage($language);

    /**
     * Obtiene el lenguaje del contexto actual si es que está definido
     * o una cadena vacía.
     */
    public function GetLanguage();

    /**
     * Ejecuta la acción del controlador definidos por la petición con los
     * parámetros fijados (si hay). Retorna la información que la petición
     * devolverá como respuesta a la petición del cliente.
     * @param string $controller Nombre del controlador a cargar
     * @param string $action Nombre de la acción a ejecutar
     * @param array $params Parámetros de la url obtenidos
     */
    public function Run($controller, $action, $params = null);

    /**
     * Almacena en el contexto la colección de rutas establecidas
     * (controlador-acción) para poder validar la acción y el controlador
     * que se desean ejecutar.
     * @param array $routes Colección de "rutas" disponibles
     */
    public function RegisterRoutes($routes);
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfaz para el modulo http que coordina la ejecución de la petición
 *
 * @author  alfonso
 */
interface IHttpModule{

    /**
     * Se encarga de realizar las tareas comunes a cualquier petición de
     * cliente como generar una traza, comprobar si existe sesión...
     */
    public function BeginRequest();

    /**
     * Se encarga de realizar el procesado de la petición. Para ello debe
     * hacer uso de las diferentes clases con las que se constituye el
     * proyecto como por ejemplo el manejador de peticiones IHttpHandler.
     */
    public function ProcessRequest();

    /**
     * Se encarga de realizar las tareas comunes previas a la finalización
     * del procesado de la petición como puede ser la generación de trazas.
     */
    public function EndRequest();

    /**
     * Es el punto de entrada de cualquier aplicación. Debe encargarse de
     * asegurar la carga de dependencias básicas y gestionar la ejecución
     * de los métodos de tratamiento de peticiones.
     */
    public static function Start();

    /**
     * Realiza el procesado de errores a nivel global de la aplicación.
     * @param int $errno Código de error
     * @param string $errstr Mensaje del error
     * @param string $errfile Fichero en el que se produce el error
     * @param string $errline Línea de ejecución que ha fallado
     * @param string $errcontext Contexto de ejecución
     */
    public static function ApplicationError($errno = 0, $errstr = null,
            $errfile = null, $errline = null, $errcontext = null);

    /**
     * Realiza el procesado de excepciones a nivel global de la aplicación.
     * @param int $errno Código de error
     * @param string $errstr Mensaje del error
     * @param string $errfile Fichero en el que se produce el error
     * @param string $errline Línea de ejecución que ha fallado
     * @param string $errcontext Contexto de ejecución
     */
    public static function ApplicationFatal($errno = 0, $errstr = null,
            $errfile = null, $errline = null, $errcontext = null);

}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfaz para el gestor de trazas
 *
 * @author alfonso
 */
interface ILogManager{

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de información a guardar
     */
    public function LogInfo($message);

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de información a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogInfoTrace($message, $e);

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de depuración a guardar
     */
    public function LogDebug($message);

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de depuración a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogDebugTrace($message, $e);

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro.
     * @param string $message Mensaje de advertencia a guardar
     */
    public function LogWarn($message);

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de advertencia a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogWarnTrace($message, $e);

    /**
     * Genera una traza tipificada como error con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error a guardar
     */
    public function LogError($message);

    /**
     * Genera una traza tipificada como error con el mensaje pasado como
     * parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogErrorTrace($message, $e);

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error FATAL a guardar
     */
    public function LogFatal($message);

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error FATAL a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogFatalTrace($message, $e);

    /**
     * Obtiene una referencia a la instancia actual del gestor de trazas.
     */
    public static function GetInstance();
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfáz de acceso al sistema de notificaciones
 *
 * @author alfonso
 */
interface INotificator{

    /**
     * Genera la notificación con los datos proporcionados
     * @param array $data Colección de parámetros para la notificación
     * @param string $content Contenido de la notificación
     */
    public function Send($data, $content);

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @param string $templateName Identidad de la plantilla
     */
    public function GetTemplate($templateName);

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfaz para el gestor de seguridad en arquitecturas MVC
 *
 * @author alfonso
 */
interface ISecurity{

    /**
     * Se encarga de realizar el proceso de autenticación del usuario que
     * accede a la aplicación mediante un ticket de acceso. En caso de ser
     * validado el ticket, se debe establecer el usuario como autenticado
     * en el contexto. Devuelve el resultado de la autenticación como un
     * valor booleano.
     * @param string $ticket ticket de sesión
     */
    public function AuthenticateTicket($ticket);

    /**
     * Se encarga de realizar el proceso de autenticación del usuario a
     * partir del nombre de usuario y el password utilizado. En el caso
     * de ser válidas las credenciales, se debe establecer el usuario
     * como autenticado en el contexto. Devuelve el resultado de la
     * autenticación como un valor booleano.
     * @param string $username Nombre de usuario
     * @param string $password Contraseña de acceso
     */
    public function Authenticate($username, $password);

    /**
     * Comprueba si la acción a ejecutar requiere que el usuario esté
     * autenticado o no
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function RequiredAuthentication($controller, $action);

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para acceder al controlador. Los criterios que
     * determinan si el usuario debe ser autorizado dependen de la
     * aplicación donde deba integrarse. Devuelve el resultado de la
     * autorización como un valor booleano.
     * @param string $controller Nombre del controlador
     */
    public function AuthorizeController($controller);

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para ejecutar la acción del controlador. Los criterios
     * que determinan si el usuario debe ser autorizado dependen de la
     * aplicación donde el componente se integra. Devuelve el resultado
     * de la autorización como un valor booleano.
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function Authorize($controller, $action);

    /**
     * Obtiene el nombre del usuario autenticado en el contexto. En caso
     * de no haber usuario autenticado, el método devolverá una
     * cadena vacía.
     */
    public function GetUserName();

    /**
     * Obtiene un array con el/los roles asociados al usuario autenticado
     * en el contexto. En caso de no estar autenticado el usuario, debe
     * retornar un array vacío.
     */
    public function GetUserRoles();

    /**
     * Obtiene un objeto con la información del usuario almacenada en el
     * contexto. En caso de no estar el usuario autenticado, se retornará
     * el valor null.
     */
    public function GetUserData();

    /**
     * Obtiene un ticket de autenticación a partir de la información del
     * usuario autenticado. En caso de no estar el usuario autenticado,
     * se retornará una cadena vacía.
     * @return string Ticket
     */
    public function GetTicket();

    /**
     * Obtiene el nombre de la vista a utilizar para la acción, el
     * controlador y el usuario autenticado. En el caso de no ser
     * necesario (no hay filtro de contenidos), retornará el nombre de
     * la vista por defecto (mismo nombre que la acción).
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function GetViewName($controller, $action);

    /**
     * Obtiene el array de controladores disponibles para el conjunto de
     * roles pasados como parámetros.
     * @param object $roles Roles del usuario
     */
    public function GetControllersByRol($roles);
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interfaz para el validador de entidades
 *
 * @author alfonso
 */
interface IValidatorClient{

    /**
     * Validación de la entidad
     * @param object $entity Referencia a la entidad a validar
     */
    public function Validate($entity = null);

    /**
     * Configuración del validador con el xml de base de datos
     * @param string $fileName Ruta al fichero de configuración
     */
    public function Configure($fileName = "");

    /**
     * Obtiene una referencia al validador de entidades
     */
    public static function GetInstance();
}

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

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión del fichero de configuración
 *
 * @author alfonso
 */
class ConfigurationManagerException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param Exception $previous Excepción previa
     */
    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Clase para el acceso al fichero de configuración
 *
 * @author alfonso
 */
class ConfigurationManager{

    /**
     * Referencia al gestor de configuraciones actual
     * @var \ConfigurationManager $_configuration Referencia al gestor
     * de configuraciones
     */
    private static $_configuration = null;

    /**
     * Referencia al nombre del fichero de configuraciones actual
     * @var string $_filename Nombre del fichero de configuración
     */
    private static $_filename = null;

    /**
     * Colección de las referencias definidas
     * @var array $References Colección de referencias a dependencias del
     * proyecto
     */
    protected $References = array();

    /**
     * Colección de las cadenas de conexión definidas
     * @var array $ConnectionStrings Colección de cadenas de conexión
     * configuradas
     */
    protected $ConnectionStrings = array();

    /**
     * Colección de pares clave-valor para la aplicación
     * @var array $Settings Colección de claves configuradas
     */
    protected $Settings = array();

    /**
     * Carga toda la información de las referencias y dependencias
     * @var object $xml Referencia al nodo xml a cargar
     */
    private function LoadRef($xml){
        if(isset($xml) && $xml != null && is_object($xml)){
            // Obtener la lista de referencias
            $nodes = $xml->references->children();
            // Array de referencias
            $references = array();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $references[(string)$attributes->name]
                        = (string)$attributes->path;
            }
            $this->References = $references;
        }
    }

    /**
     * Carga toda la información sobre cadenas de conexión
     * @var object $xml Referencia al nodo xml a cargar
     */
    private function LoadConnectionStrings($xml){
        if(isset($xml) && $xml != null && is_object($xml)){
            // Obtener la lista de configuraciones
            $nodes = $xml->connectionStrings->children();
            // Array de connectionString
            $connectionStrings = array();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $connectionStrings[(string)$attributes->name] =
                array(
                    "server" => (string)$attributes->server,
                    "user" => (string)$attributes->user,
                    "password" => (string)$attributes->password,
                    "scheme" =>  (string)$attributes->scheme,
                    "filename" => (string)$attributes->filename
                );
            }
            $this->ConnectionStrings = $connectionStrings;
        }
    }

    /**
     * Carga toda la información sobre parámetros de configuración
     * @var object $xml Referencia al nodo xml a cargar
     */
    private function LoadSettings($xml = null){
        if(isset($xml) && $xml != null && is_object($xml)){
            // Obtener la lista de configuraciones
            $nodes = $xml->settings->children();
            // Array de configuraciones
            $settings = array();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $settings[(string)$attributes->key]
                        = (string)$attributes->value;
            }
            $this->Settings = $settings;
        }
    }

    /**
     * Carga toda la información del fichero de configuración en
     * un array en memoria
     * @var string $xmlstr Ruta al fichero de configuración
     */
    private function Load($xmlstr = "config.xml"){

        $sXml = ($xmlstr == "")
                ? "config.xml": $xmlstr;

        if(file_exists ($sXml)){
            // Cargamos el contenido de la configuración desde el xml
            $configurator = simplexml_load_file($sXml);
            // Cargar los datos de configuración
            $this->LoadRef($configurator);
            $this->LoadConnectionStrings($configurator);
            $this->LoadSettings($configurator);
            return;
        }

        throw new \ConfigurationManagerException( "config file not found" );
    }

    /**
     * Constructor por defecto
     * @var string $configFile Ruta al fichero de configuración
     */
    public function __construct($configFile = ""){
        $this->Load($configFile);
    }

    /**
     * Obtiene un diccionario con los parámetros de conexión a
     * base de datos identificados con el nombre oConnName.
     * @var string $oConnName Nombre de la conexión a base de datos
     */
    public static function GetConnectionStr($oConnName){
        // Obtener una referencia
        $obj = ConfigurationManager::
                GetInstance(ConfigurationManager::$_filename);
        // retornamos la cadena seleccionada
        return $obj->ConnectionStrings[$oConnName];
    }

    /**
     * Obtiene el valor almacenado en la clave de configuración
     * identificada como keyName.
     * @var string $keyName Nombre de la clave a buscar
     * @var string $fileName Ruta del fichero de configuración
     */
    public static function GetKey($keyName, $fileName = ""){
        // Obtener una referencia
        $obj = ConfigurationManager::
                GetInstance($fileName);
        // retornamos la cadena seleccionada
        return $obj->Settings[$keyName];
    }

    /**
     * Obtiene un array con los datos de acceso a cada una de las
     * referencias que deben cargarse para la ejecución.
     * @var string $fileName Ruta del fichero de configuración
     */
    public static function GetReferences($fileName = ""){
        // Obtener una referencia
        $obj = ConfigurationManager::
                GetInstance($fileName);
        // Obtener la lista de referencias
        return $obj->References;
    }

    /**
     * Cargar todas las referencias configuradas
     * @var string $fileName Ruta del fichero de configuración
     */
    public static function LoadReferences($fileName = ""){
        // Obtener una referencia
        $obj = ConfigurationManager
                ::GetInstance($fileName);
        $references = $obj->References;
        //foreach($references as $key => $value){
        foreach($references as $value){
            // include_once($value);
            require_once($value);
        }
    }

    /**
     * Obtiene la instancia actual de ConfigurationManager
     * @param string $configFile Ruta del fichero de configuración
     * @return \ConfigurationManager
     */
    public static function GetInstance($configFile = ""){
        // Comprobar si hay una referencia actual al
        // gestor de configuraciones
        if(ConfigurationManager::$_configuration == null){
            ConfigurationManager::$_configuration
                    = new \ConfigurationManager( $configFile );
        }
        // Asignar el nombre del fichero si no está definido
        if(ConfigurationManager::$_filename == null
                && $configFile != "" && $configFile != null){
                ConfigurationManager::$_filename = $configFile;
        }
        // Retornar referencia al fichero actual
        return ConfigurationManager::$_configuration;
    }

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción en la gestión de errores de la factoría de contraseñas
 *
 * @author alfonso
 */
class PasswordFactoryException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de excepción
     * @param int $code Código de excepción
     * @param Exception $previous Referencia a la excepción original
     */
    public function __construct($message, $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación para la generación de password aleatorio
 * y cálculo de funciones Hash
 *
 * @author alfonso
 */
class PasswordFactory{

    /**
     * Referencia al generador actual
     * @var \PasswordFactory Referencia a la factoría actual
     */
    protected static $Factory = null;

    /**
     * Cadena de texto con el conjunto de caracteres válidos
     * @var string Alfabeto disponible
     */
    protected $Alphabet = "";

    /**
     * Longitud mínima configurada para el password
     * @var int Longitud mínima de contraseña
     */
    protected $MinLength = 8;

    /**
     * Longitud máxima configurada para el password
     * @var int Longitud máxima de contraseña
     */
    protected $MaxLength = 20;

    /**
     * Longitud por defecto para el password
     * @var int Longitud de contraseña
     */
    protected $Length = 12;

    /**
     * Constructor privado
     * @var string $sfile Nombre del fichero de configuración
     */
    private function __construct($sfile = ""){
        // asignar la ruta de fichero
        $file = ($sfile == "") ? "config.xml": $sfile;

        if(file_exists ($file)){
            // Cargamos el contenido de la configuración desde el xml
            $xml = simplexml_load_file($file);
            $attr1 = $xml->passwordfactory->alphabet->attributes();
            // Configuramos el alfabeto de generación
            $this->Alphabet = (string)$attr1["value"];
            $attr2 = $xml->passwordfactory->minlength->attributes();
            // Configuramos la longitud mínima para el password
            $this->MinLength = (string)$attr2["value"];
            $attr3 = $xml->passwordfactory->maxlength->attributes();
            // Configuramos la longitud máxima para el password
            $this->MaxLength = (string)$attr3["value"];
            $attr4 = $xml->passwordfactory->default->attributes();
            // Configuramos la longitud por defecto
            $this->Length = (string)$attr4["value"];
            // Cargar los datos de configuración
            return;
        }

        throw new \PasswordFactoryException( "config file not found" );
    }

    /**
     * Genera un password aleatorio la longitud indicada
     * @var int $length Longitud del password a generar
     */
    public function GetPassword($length = 12){
        // Validar la longitud de cadena
        if(!is_numeric($length)){
            $length = $this->Length;
        }
        else if($length < $this->MinLength){
            $length = $this->MinLength;
        }
        else if($length > $this->MaxLength){
            $length = $this->MaxLength;
        }
        // Se define una cadena de caractares. Te recomiendo que uses esta.
        $cadena = $this->Alphabet;
        //Obtenemos la longitud de la cadena de caracteres
        $longitudCadena = strlen($cadena);
        //Se define la variable que va a contener la contraseña
        $pass = "";
        //Se define la longitud de la contraseña, en mi caso 10, pero
        //puedes poner la longitud que quieras
        $longitudPass = $length;
        //Creamos la contraseña
        for($i=1 ; $i <= $longitudPass ; $i++){
            // Definimos numero aleatorio entre 0 y la longitud de la
            // cadena de caracteres-1
            $pos=rand(0,$longitudCadena-1);
            // Agregar un caracter al password
            $pass .= substr($cadena,$pos,1);
        }
        return $pass;
    }

    /**
     * Obtiene la transformación md5 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetMD5($text = ""){
        return hash( "md5", $text );
    }

    /**
     * Obtiene la transformación sha1 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetSHA1($text = ""){
        return hash( "sha1", $text );
    }

    /**
     * Obtiene la transformación sha256 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetSHA256($text = ""){
        return hash( "sha256", $text );
    }

    /**
     * Obtiene la transformación sha512 del texto referenciado
     * @var string $text cadena de la que obtener el hash
     */
    public function GetSHA512($text = ""){
        return hash( "sha512", $text );
    }

    /**
     * Obtiene la instancia actual del generador de password
     * @param type $file
     * @return type
     */
    public static function GetInstance($file = ""){
        if(PasswordFactory::$Factory == null){
            PasswordFactory::$Factory = new \PasswordFactory($file);
        }
        return PasswordFactory::$Factory;
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación de la excepción tipo para el inyector
 *
 * @author alfonso
 */
class InjectorException extends \BaseException {

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param \Exception $previous Excepción previa
     */
    public function InjectorException($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Clase para la inyección de dependencias. Requiere que exista
 * una configuración previa de las dependencias en un fichero
 * de configuraciones (xml)
 *
 * @author alfonso
 */
class Injector{

    /**
     * Referencia al inyector de dependencias cargado
     */
    private static $_injector = null;

    /**
     * Referencia al repositorio de interfaces
     */
    private $_repository = null;

    /**
     * Constructor privado
     */
    private function __construct($fileName = ""){
        // Validación del nombre de fichero de configuración
        // pasado por parámetro
        $sFileName = ($fileName == "" || $fileName == null)
                ? "config.xml" : $fileName;
        // Cargamos el contenido de la configuración desde el xml
        $configurator = simplexml_load_file($sFileName);
        // Iniciar la referencia al repositorio de traducciones
        $this->_repository = array();
        // Leer XML
        $this->Load($configurator);
    }

    /**
     * Carga la información del fichero de configuración
     * @var object $xml Nodo xml a cargar
     */
    private function Load($xml = null){
        if(isset($xml) && is_object($xml)){
            // Obtener la lista de configuraciones
            $nodes = $xml->interfaces->children();
            // Almacenar cada uno de los nodos
            foreach($nodes as $node){
                // Obtener los atributos del nodo
                $attributes = $node->attributes();
                // guardarlos en el array
                $this->_repository[(string)$attributes->name] =
                        array(
                            "mapTo" => (string)$attributes->mapTo,
                            "src" => (string)$attributes->src
                        );
            }
        }
    }

    /**
     * Se encarga de obtener una nueva instancia que implemente la
     * interfaz identificada con el nombre interfaceName.
     * @param string $interfaceName Nombre de la interfaz a resolver
     * @param boolean $include Indica si hay que cargar la referencia
     * @return object
     * @throws InjectorException
     */
    public function Resolve($interfaceName = "", $include = FALSE){
        // Buscar la referencia en el repositorio
        if(!array_key_exists($interfaceName, $this->_repository)){
            return null;
        }
        // Recuperar los datos de la interfaz a resolver
        $data = $this->_repository[$interfaceName];
        // Comprobar los datos
        if($data["mapTo"] == "" || $data["src"] == ""){
            return NULL;
        }
        // Cargar dependencias
        if($include == TRUE){
            require_once($data["src"]);
        }
        // Crear instancia del objeto
        $object = new $data["mapTo"]();
        // Comprobar que la instancia cumple con la interfaz definida
        if (!$object instanceof $interfaceName){
            throw new \InjectorException($interfaceName);
        }
        // Devolver la instancia
        return $object;
    }

    /**
     * Obtiene una referencia al gestor de dependencias.
     * @param string $fileName Ruta del fichero de configuración
     * @return \Injector Referencia al injector actual
     */
    public static function GetInstance($fileName = ""){
        // Comprobación si ya existe una referencia
        // al gestor de dependencias
        if(Injector::$_injector == null){
            Injector::$_injector = new \Injector( $fileName );
        }
        // Retornar referencia al gestor actual
        return Injector::$_injector;
    }
}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación de la interfaz ILogManager
 *
 * @author alfonso
 */
class LogManager implements \ILogManager{

    /**
     * Referencia al gestor de trazas actual
     * @var \ILogManager $_logmanager Referencia al gestor de trazas actual
     */
    private static $_logmanager = null;

    /**
     * Nombre del fichero destino
     * @var string $FileName Ruta de acceso al fichero de log
     */
    protected $FileName = null;

    /**
     * Constructor
     * @var string $fileName Ruta al fichero de log
     */
    public function __construct($fileName = ""){
        if(LogManager::$_logmanager == null){
            // Apuntar el nombre del fichero destino
            $this->FileName = $fileName;
            // Almacenar la referencia al gestor de trazas
            LogManager::$_logmanager = $this;
        }
    }

    /**
     * Se encarga de realizar la escritura de la traza en el fichero
     * @var object $type Tipología del mensaje a guardar
     * @var string $data Mensaje a guardar
     */
    private function WriteLog($type, $data){
        $date = new DateTime( "NOW" );
        // Preparar la traza a serializar
        $trace = array( "date" => $date->format("Ynj - h:i:s A"),
            "level" => $type, "details" => $data );
        // Serializar a json
        $trace = json_encode($trace).",\n";
        // Fijar el nombre del fichero utilizando la fecha (dia, año y mes)
        $fileName = ($this->FileName == "" || $this->FileName == null)
                ? "logs/data-".date("Ynj").".log"
                : $this->FileName.date("Ynj").".log";
        // Abrir el flujo al fichero en modo agregar
        $fp = fopen($fileName, 'a');
        // Escribir el mensaje
        fwrite($fp, $trace);
        // Cerrar el flujo de escritura
        fclose($fp);
    }

    /**
     * Se encarga de realizar la escritura de la traza de
     * error en el fichero
     * @var object $type Tipología del mensaje
     * @var string $data Mensaje que se desea guardar
     * @var Exception $e Referencia a la excepción que se va a trazar
     */
    private function WriteErrorLog($type, $data, $e){
        $date = new DateTime( "NOW" );
        // Preparar la traza a serializar
        $trace = array(
            "date" => $date->format("Ynj - h:i:s A"),
            "level" => $type, "details" => $data,
            "exception" => $e->getMessage());
        // Serializar a json
        $trace = json_encode($trace).",\n";
        // Fijar el nombre del fichero utilizando la fecha (dia, año y mes)
        $fileName = ($this->FileName == "" || $this->FileName == null)
                ? "logs/data-".date("Ynj").".log"
                : $this->FileName.date("Ynj").".log";
        // Abrir el flujo al fichero en modo agregar
        $fp = fopen($fileName, 'a');
        // Escribir el mensaje
        fwrite($fp, $trace);
        // Cerrar el flujo de escritura
        fclose($fp);
    }

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de información a guardar
     */
    public function LogInfo($message){
        $this->WriteLog( "Info" , $message);
    }

    /**
     * Genera una traza tipificada como de información genérica con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de información a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogInfoTrace($message, $e){
        $this->WriteErrorLog( "Info" , $message, $e);
    }

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro.
     * @param string $message Mensaje de depuración a guardar
     */
    public function LogDebug($message){
        $this->WriteLog( "Debug" , $message);
    }

    /**
     * Genera una traza tipificada como información de depuración con el
     * mensaje pasado como parámetro y la información de la excepción
     * capturada.
     * @param string $message Mensaje de depuración a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogDebugTrace($message, $e){
        $this->WriteErrorLog( "Debug" , $message, $e);
    }

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro.
     * @param string $message Mensaje de advertencia a guardar
     */
    public function LogWarn($message){
        $this->WriteLog( "Warn" , $message);
    }

    /**
     * Genera una traza tipificada como de advertencia con el mensaje
     * pasado como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de advertencia a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogWarnTrace($message, $e){
        $this->WriteErrorLog( "Warn" , $message, $e);
    }

    /**
     * Genera una traza tipificada como error con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error a guardar
     */
    public function LogError($message){
        $this->WriteLog( "Error" , $message);
    }

    /**
     * Genera una traza tipificada como error con el mensaje pasado como
     * parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogErrorTrace($message, $e){
        $this->WriteErrorLog( "Error" , $message, $e);
    }

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro.
     * @param string $message Mensaje de error FATAL a guardar
     */
    public function LogFatal($message){
        $this->WriteLog( "Fatal" , $message);
    }

    /**
     * Genera una traza tipificada como error fatal con el mensaje pasado
     * como parámetro y la información de la excepción capturada.
     * @param string $message Mensaje de error FATAL a guardar
     * @param Exception $e Referencia a la excepción generada
     */
    public function LogFatalTrace($message, $e){
        $this->WriteErrorLog( "Fatal" , $message, $e);
    }

    /**
     * Obtiene una referencia a la instancia actual del gestor de trazas.
     * @var string $fileName Ruta para el fichero de log
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya existe una referencia
        if(LogManager::$_logmanager == null){
            LogManager::$_logmanager = new \LogManager( $fileName );
        }
        // Retornar la referencia al gestor actual
        return LogManager::$_logmanager;
    }

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Clase base para las excepciones
 *
 * @author alfonso
 */
class BaseException extends \Exception {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param \Exception $previous Excepción original
     */
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {

        if(($code != 0)&&($previous != null)){
            parent::__construct($message, $code, $previous);
        }

        if(($code == 0)&&($previous != null)){
            parent::__construct($message, $code, $previous);
        }

        if(($code != 0)&&($previous == null)){
            parent::__construct($message, $code);
        }

        if(($code == 0)&&($previous == null)){
            parent::__construct($message);
        }
    }

    /**
     * Representación de cadena personalizada del objeto
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para gestión de errores en comunicaciones Asíncronas (AJAX)
 *
 * @author alfonso
 */
class AjaxException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión en el paso de argumentos a [funciones|métodos]
 *
 * @author alfonso
 */
class ArgumentException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores por la no implementación de
 * [métodos|funciones]
 *
 * @author alfonso
 */
class NotImplementedException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para el control de selección de proyecto. Se utiliza para identificar
 * cuando se accede a un recurso sin que exista un proyecto activo.
 *
 * @author manager
 */
class ProjectException extends \Exception{

    public function __construct($message, $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores producidos al no
 * encontrar un recurso
 */
class ResourceNotFoundException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="",
            $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores en comunicaciones con base de datos
 *
 * @author alfonso
 */
class SqlConnectionException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepcion para la gestion de errores debidas a consultas sql
 */
class SqlException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores debidos a procesos de autenticación
 *
 * @author alfonso
 */
class UnAuthenticateException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores debida a procesos de autorización
 *
 * @author alfonso
 */
class UnAuthorizeException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepcion para la gestion de errores relacionados con la url
 * de la solicitud actual
 *
 * @author alfonso
 */
class UrlException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores en la clase Uploader
 *
 * @author alfonso
 */
class FileManagerException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param Exception $previous Excepción previa
     */
    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Gestión de ficheros y directorios
 *
 * @author alfonso
 */
class FileManager{

    /**
     * Obtiene la colección de estructuras [ Path, Name ] que identifican
     * a cada uno de los ficheros del path pasado como parámetro
     * @param string $path Ruta a listar
     * @return array
     */
    public static function GetFiles($path = ""){
        // Iniciar array a devolver
        $array = array();
        // Validar path
        if($path == "") {
            return $array;
        }
        // Abrir descriptor del path
        if(($fileManager = opendir($path))== true) {
            while (false !== ($file = readdir($fileManager))){
                // Crear estructura de datos a guardar
                $item =
                    array( "Path" =>  $path."/".$file ,"Name" => $file );
                // Agregar estructura al array final
                array_push($array, $item);
            }
                    // Cerrar descriptor
            closedir($fileManager);
        }
        // retornar array de estructuras
        return $array;
    }

    /**
     * Obtiene la colección de estructuras [ Path, Name ] que identifican
     * a cada uno de los ficheros del path pasado como parámetro filtrados
     * por la extensión
     * @param string $path Ruta de acceso
     * @param string $extension Extensiones a filtrar
     * @return array
     */
    public static function GetFilterFiles($path = "", $extension = ""){
        // Iniciar array a retornar
        $array = array();
        // Validar el path a inspeccionar
        if($path == ""){
            return $array;
        }

        // Validar la extensión para filtrar
        if($extension == ""){
            return FileManager::GetFiles($path);
        }
            // Abrir el descriptor
        if(($fileManager = opendir($path))==true){
            while(false !== ($file = readdir($fileManager))) {
                // Determinar la posición de la extensión del fichero
                $posicion = strrpos($file, "." );
                // Validar la posición
                if(!is_numeric($posicion)){
                    continue;
                }
                // Extraer la extensión del fichero
                $ext = substr( $file, $posicion + 1);
                // Validar la extensión
                if($ext != "" && stristr($extension, $ext)){
                    // Iniciar estructura a agregar
                    $item = array( "Path" =>
                        $path."/".$file ,"Name" => $file );
                    // Agregar estructura al array a devolver
                    array_push($array, $item);
                }
            }
            // Cerrar el descriptor
            closedir($fileManager);
        }
        // Retornar la colección
        return $array;
    }

    /**
     * Realiza una copia recursiva de un path origen en otro destino
     * @param string $source Ruta origen
     * @param string $destination Ruta destino
     */
    public static function CopyDirectory( $source, $destination ) {
        if ( is_dir( $source ) ) {
            // Crear directorio destino
            @mkdir( $destination, 0777 );
            // Asignar permisos del directorio destino
            chmod($destination, 0777);
            // Establecer referencia al directorio origen
            $directory = dir( $source );
            // Recorrer el descriptor
            while ( FALSE !== ( $readdirectory = $directory->read())){
                // Comprobar que no se apunta al
                // directorio actual o al padre
                if ( $readdirectory == '.' || $readdirectory == '..' ){
                    continue;
                }
                // definir el path a copiar
                $PathDir = $source . '/' . $readdirectory;
                // validar si el path corresponde a un fichero
                if(is_dir( $PathDir )){
                    // Realizar copia recursiva
                    FileManager::CopyDirectory( $PathDir,
                            $destination . '/' . $readdirectory );
                    continue;
                }
                // Copiar el fichero si se trata de fichero
                copy( $PathDir, $destination . '/' . $readdirectory );
            }
            // Cerrar descriptor
            $directory->close();
        }else {
            // Copiar el fichero origen en el destino
            copy( $source, $destination );
        }
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepción para la gestión de errores en la clase Uploader
 *
 * @author alfonso
 */
class UploaderException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param \Exception $previous Excepción previa
     */
    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Clase para la gestión de subidas de ficheros en peticiones post
 *
 * @author alfonso
 */
class Uploader{

    /**
     * Path de Almacenamiento
     * @var string $Path Ruta de almacenamiento del fichero
     */
    public $Path;

    /**
     * Extensiones permitidas
     * @var array $AllowedExts Colección de extensiones permitidas
     */
    public $AllowedExts;

    /**
     * Nombre de valirable $_FILE
     * @var string $Name Nombre de valirable $_FILE
     */
    public $Name;

    /**
     * Constructor
     */
    public function __construct(){
        $this->Path = "";
        $this->AllowedExts = array("php", "PHP");
        $this->Name = "file";
    }

    /**
     * Método para subir un fichero con la configuración del objeto
     * @return void
     * @throws UploaderException
     */
    public function Upload(){
        // Obtener la variable File
        $file =  $_FILES[$this->Name];
        // Extensión del fichero a guardar
        $extension = end(explode(".", $file["name"]));
        // Comprobar la extensión
        if (in_array($extension, $this->AllowedExts)){
            if ($file["error"] > 0){
                throw new \UploaderException("Return Code: "
                        . $file["error"]);
            }

            if (file_exists($this->Path. $file["name"])){
                throw new \UploaderException($file["name"]
                        ." already exists.");
            }

            move_uploaded_file($file["tmp_name"],
                    $this->Path.$file["name"]);

            return;
        }

        throw new \UploaderException("Invalid file");
    }

    /**
     * Método estático para la subida de ficheros.
     * @param string $sFile Nombre de la variable a recoger en $_FILE
     * @param string $path Ruta Relativa donde almacenar el fichero
     * @param array $extension Conjunto de extensiones válidas
     * @param boolean $overRide Indica si se sobreescribe
     * @return void
     * @throws UploaderException
     */
    public static function UploadFile($sFile="",
            $path="", $extension="", $overRide = false){
        // Obtener la variable File
        $file =  $_FILES[$sFile];
        // extraer nombre
        $fileName = $file[ "name" ];
        // Obtener extensión
        $sExtension = ($extension=="")
                ? explode(".", $fileName)
                : $extension;
        // Extensión del fichero a guardar
        $ext = end($sExtension);
        // Comprobar la extensión
        if (in_array($ext, $extension)){
            if ($file["error"] > 0){
                throw new \UploaderException("Return Code: "
                        . $file["error"]);
            }

            $exist = file_exists($path. $file["name"]);

            if($exist && !$overRide){
                throw new \UploaderException($file["name"]." "
                        . "already exists.");
            }
            elseif($exist && $overRide){
                unlink($path. $file["name"]);
            }

            move_uploaded_file($file["tmp_name"], $path.$file["name"]);

            return;
        }

        throw new \UploaderException("Invalid file");
    }
}

    /*
        Dependencias :
        - Interfaz IHttpModule
        - Clase Injector para la inyección de componentes
        - Componentes definidos : [ IHttpHandler, ILogManager, ISecurity ]
        - Clase ConfigurationManager para el acceso al config.xml
        - Claves de config.xml : [ path, resources ]
    */

    /**
     * Implementación de la interfaz IHttpModule
     */
    class HttpModule implements IHttpModule{

        /**
         * Referencia al objeto inyector de dependencias
         * @var \Injector
         */
        public $Injector;

        /**
         * Referencia a la instancia para la manipulación de la petición
         * @var \IHttpHandler
         */
        public $HttpHandler;

        /**
         * Referencia a la instancia para la gestión de trazas
         * @var \ILogManager
         */
        public $LogManager;

        /**
         * Referencia a la instancia para la gestión de seguridad
         * @var \ISecurity
         */
        public $Security;

        /**
         * Contiene la información a enviar al cliente
         * @var string
         */
        public $Render;

        /**
         * Proceso de autenticación del usuario
         * @throws UnAuthenticateException
         */
        protected function Authentication(){
            // Comprobar si el usuario ya está autenticado
            if($this->Security->GetUserName() == ""){
                if(!isset($_POST["password"])
                        || !isset($_POST["username"])){
                    // Establecer el mensaje de error
                    $message = get_class()
                            ." - Authentication - no parameters";
                    // Lanzar excepción
                    throw new UnAuthenticateException( $message );
                }

                if(!$this->Security->Authenticate(
                            $_POST["username"],
                            $_POST["password"]
                        )){
                    $message = "Authentication - user: "
                            .$_POST["username"]." , pass: "
                            .$_POST["password"];
                    // Lanzar excepción
                    throw new UnAuthenticateException( $message );
                }
            }
        }

        /**
         * Validación del controlador y la acción solicitados
         * @param array $urlData
         * @return array
         * @throws UrlException
         */
        protected function ValidateUrlData( $urlData = null){
            if(is_array($urlData)){
                // Validar controlador y acción
                if(!$this->HttpHandler->Validate(
                            $urlData["Controller"],
                            $urlData["Action"]
                        )){

                    if($urlData["Action"] == ""){
                        $result = $this->HttpHandler->SetDefault(
                                    $urlData["Controller"],
                                    $urlData["Action"]
                                );
                        $urlData["Controller"] = $result["Controller"];
                        $urlData["Action"] = $result["Action"];
                    }
                    else{
                        $message = "ValidateUrlData - Validate "
                                    .$_SERVER['REQUEST_URI'];

                        throw new UrlException($message);
                    }
                }
            }
            return $urlData;
        }

        /**
         * Validación de la autenticación y la autorización para
         * la url solicitada
         * @param array $urlData
         * @return array
         * @throws UnAuthorizeException
         */
        protected function ValidateSecurity( $urlData ){
            // Obtener requisitos de validación
            $required = $this->Security->RequiredAuthentication(
                        $urlData["Controller"],
                        $urlData["Action"]
                    );
            // Validar permisos de seguridad
            if($required){
                // Proceso de autenticación del usuario
                $this->Authentication();
                // Proceso de autorización
                if(!$this->Security->Authorize(
                            $urlData["Controller"],
                            $urlData["Action"]
                        )){
                    // Establecer el mensaje de error
                    $message = "ValidateSecurity - Authorize -"
                            .$_SERVER['REQUEST_URI'];

                    throw new UnAuthorizeException( $message );
                }
            }
            return $urlData;
        }

        /**
         * Constructor
         */
        public function __construct(){
            // Procesos de inicio
            HttpModule::Start();
            // Cargar las referencias
            $this->Injector = Injector::GetInstance();
            // Cargar el manejador de peticiones
            $this->HttpHandler = $this->Injector->Resolve( "IHttpHandler" );
            // Cargar el gestor de trazas
            $this->LogManager = $this->Injector->Resolve( "ILogManager" );
            // Cargar dependencias de seguridad
            $this->Security = $this->Injector->Resolve( "ISecurity" );
        }

        /**
         * Se encarga de realizar las tareas comunes a cualquier petición
         * de cliente como generar una traza, comprobar si existe sesión...
         */
        public function BeginRequest(){

        }

        /**
         * Se encarga de realizar el procesado de la petición. Para ello
         * debe hacer uso de las diferentes clases con las que se
         * constituye el proyecto como por ejemplo el manejador de
         * peticiones IHttpHandler.
         */
        public function ProcessRequest(){
            // Obtener los datos de la petición
            $urlData =
                    $this->HttpHandler->ProcessUrl($_SERVER['REQUEST_URI']);
            // Validar el control y la acción solicitados
            $urlData = $this->ValidateUrlData( $urlData );
            // Obtener requisitos de validación
            $urlData = $this->ValidateSecurity( $urlData );
            // Ejecutar controlador y acción
            $this->Render .= $this->HttpHandler->Run(
                        $urlData["Controller"],
                        $urlData["Action"],
                        $urlData["Params"]
                    );
        }

        /**
         * Se encarga de realizar las tareas comunes previas a la
         * finalización del procesado de la petición como puede ser la
         * generación de trazas.
         */
        public function EndRequest(){
            print $this->Render;
        }

        /**
         * Es el punto de entrada de cualquier aplicación. Debe encargarse
         * de asegurar la carga de dependencias básicas y gestionar la
         * ejecución de los métodos de tratamiento de peticiones.
         */
        public static function Start(){

        }

        /**
         * Realiza el procesado de errores a nivel global de la aplicación.
         * @param integer $errno Código de error
         * @param string $errstr Mensaje de error
         * @param string $errfile Fichero que genera el error
         * @param string $errline Línea donde se genera el error
         * @param string $errcontext Descripción del contexto de error
         */
        public static function ApplicationError($errno = 0, $errstr = null,
                $errfile = null, $errline = null, $errcontext = null){

        }

        /**
         * Realiza el procesado de excepciones a nivel global de la aplicación.
         * @param integer $errno Código de error
         * @param string $errstr Mensaje de error
         * @param string $errfile Fichero que genera el error
         * @param string $errline Línea donde se genera el error
         * @param string $errcontext Descripción del contexto de error
         */
        public static function ApplicationFatal($errno = 0, $errstr = null,
                $errfile = null, $errline = null, $errcontext = null){

        }

    }




/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
    Dependencias :
    - Clase base HttpModule y todas sus dependencias
    - Interfaz IHttpModule
    - Componentes definidos : [ IDataAccessObject ]
    - Claves de config.xml : [ connectionString ]
    - Entidad de base de datos : Service
*/

/**
 * Implementación de la interfaz IHttpModule para aplicaciones Saas
 *
 * @author alfonso
 */
class SaasHttpModule extends \HttpModule implements \IHttpModule{

    /**
     * Obtiene el nombre del servicio actual a partir de la ruta
     * actual de ejecución
     */
    protected function GetServiceName(){
        // Obtener ruta actual
        $path = getcwd();
        // Buscamos la posición del último directorio
        $pos = strrpos ( $path , "/" );
        // Si no encontramos el caracter error
        if( $pos === false ){
            throw new UrlException( "GetServiceName - ".$path );
        }
        // Extraemos el último directorio
        $path = substr( $path, $pos);
        // Eliminar el caracter /
        $name = str_replace( "/" , "" , $path);

        return $name;
    }

    /**
     * Configurar los datos del servicio actual a partir del nombre
     * @param string Nombre del servicio
     * @throws UrlException
     */
    protected function SetServiceData( $name = "" ){
        // Buscar datos del servicio
        $services = $this->Dao->GetByFilter( "Service",
                array ( "Name" => $name ));
        // Comprobar si hay resultados
        if(count($services) == 0){
            throw new UrlException( "BeginRequest - ".$name );
        }
        // Almacenar en sesión los datos del primer servicio obtenido
        $_SESSION["serviceId"] = $services[0]->Id;
        $_SESSION["serviceName"] = $services[0]->Name;
    }

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar el constructor padre
        parent::__construct();
        // Cargar el objeto de acceso a datos
        $this->Dao = $this->Injector->Resolve( "IDataAccessObject" );
        // Obtener la clave de cadena de conexión
        $connectionString =
                ConfigurationManager::GetKey( "connectionString" );
        // Obtener los parámetros de conexión a bbdd
        $oConnString =
                ConfigurationManager::GetConnectionStr($connectionString);
        // Configurar Objeto de acceso a datos
        $this->Dao->Configure($oConnString);
    }

    /**
     * Se encarga de realizar las tareas comunes a cualquier petición de
     * cliente como generar una traza, comprobar si existe sesión...
     */
    public function BeginRequest(){
        // Obtener el nombre del servicio actual
        $name = $this->GetServiceName();
        // Setear los datos del servicio activo
        $this->SetServiceData( $name );
        // Cargar dependencias de seguridad
        $this->Security = $this->Injector->Resolve( "ISecurity" );
    }

    /**
     * Proceso de autenticación del usuario
     * @throws UnAuthenticateException
     */
    protected function Authentication(){
        if($this->Security->GetUserName() != ""){
            return;
        }
        // Comprobación si se utiliza un ticket de autenticación
        $ticket = filter_input(INPUT_GET, "ticket");
        // Proceso de validación del ticket
        if($ticket != FALSE && $ticket != NULL){
            if(!$this->Security->AuthenticateTicket($ticket)){
                // Establecer el mensaje de error
                $message = get_class()." - Authentication - ticket no validado";
                // Lanzar excepción
                throw new UnAuthenticateException( $message );
            }
        }
        parent::Authentication();
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación de la interfaz IHttpHandler
 *
 * @author alfonso
 */
class HttpHandler implements \IHttpHandler{

    /**
     * Array con los datos de los controladores asociados
     * @var array $Controllers Colección de controladores registrados
     */
    private $Controllers = array();

    /**
     * Nombre del controlador a utilizar
     * @var string $Controller Nombre del controlador seleccionado
     */
    private $Controller="";

    /**
     * Nombre de la acción que se desea ejecutar
     * @var string $Action Nombre de la acción que se debe ejecutar
     */
    private $Action="";

    /**
     * Primer parámetros de la petición : /controller/action/First?asd=asd
     * @var object $First Argumento pasado por url
     */
    private $First=null;

    /**
     * Parámetros del QueryString
     * @var array $Params Parametros pasados por QueryString
     */
    private $Params = array();

    /**
     * Booleano que indica si la url contiene/o puede contener el lenguaje
     * @var boolean $GetLan Establece si se ha especificado un lenguaje
     */
    private $GetLan = FALSE;

    /**
     * Idioma seleccionado (si procede)
     * @var string $Language Idioma asociado
     */
    private $Language = "";

    /**
     * Cargar los datos de controladores del fichero de configuración
     * @var object $xml Nodo xml con la configuracion de controladores
     */
    protected function LoadControllers($xml=null){
        // Obtener la lista de Controladores
        $nodes = $xml->controllers->children();
        // Array de connectionString
        $this->Controllers = array();
        // Almacenar cada uno de los controladores
        foreach($nodes as $node){
            // Obtener cada acción del controlador
            $actions = $node->actions->children();
            // Array de acciones
            $actiones = array();
            // Agregar cada acción al array
            foreach($actions as $action){
                // Obtener los atributos del nodo
                $attr = $action->attributes();
                // Agregar la acción con sus roles asociados
                $actiones[(string)$attr->name] = array(
                    "roles" => (string)$attr->roles,
                    "params" =>  (string)$attr->params);
            }
            // Obtener los atributos del nodo
            $attributes = $node->attributes();
            // guardarlos en el array
            $this->Controllers[(string)$attributes->name] = array(
                            "actions" => $actiones,
                            "action" => (string)$attributes->action,
                            "roles" => (string)$attributes->roles
                    );
        }
    }

    /**
     * Establece los parametros pasados por url cuando se ha especificado
     * idioma
     * @param array $parts Partes de la url
     */
    private function SetUrlParts($parts = null){
        // Obtener el número de partes de la url
        $count = count($parts);
        // aplicar filtros
        switch($count){
            case 2:
                $this->Controller = $parts[1];
                $this->Action = "";
                break;

            case 3:
                $this->Controller = $parts[1];
                $this->Action = $parts[2];
                break;

            case 4:
                $this->Controller = $parts[1];
                $this->Action = $parts[2];
                $this->First = $parts[3];
                break;

            default:
                $this->Controller = "";
                $this->Action = "";
                break;
        }
    }

    /**
     * Establece los parametros pasados por url cuando se ha especificado
     * idioma
     * @param array $parts Partes de la url
     */
    private function SetUrlLanguageParts($parts = null){
        // Obtener el número de partes de la url
        $count = count($parts);
        // Establecer partes de la url
        if($count == 2){
            $this->SetLanguage($parts[1]);
            $this->Controller = "";
            $this->Action = "";
        }
        elseif($count == 3){
            $this->SetLanguage($parts[1]);
            $this->Controller = $parts[2];
            $this->Action = "";
        }
        elseif($count == 4){
            $this->SetLanguage($parts[1]);
            $this->Controller = $parts[2];
            $this->Action = $parts[3];
        }
        elseif($count == 5){
            $this->SetLanguage($parts[1]);
            $this->Controller = $parts[2];
            $this->Action = $parts[3];
            $this->First = $parts[4];
        }
    }


    /**
     * Procesa las partes de la url : /lan-lan/controller/action
     * @var array $parts Coleccion de partes de una url
     */
    protected function GetUrlParts($parts = null){
        if($this->GetLan){
            // Establece las secciones
            $this->SetUrlLanguageParts($parts);
        }
        else{
            $this->SetUrlParts($parts);
        }
    }

    /**
     * Constructor
     */
    public function __construct(){
        // Ruta de acceso al fichero de configuración
        $xmlstr = ConfigurationManager::GetKey( "controllers" );
        // Cargamos el contenido de la configuración desde el xml
        $configurator = simplexml_load_file($xmlstr);
        // Cargar los datos de configuración
        $this->LoadControllers($configurator);
        // Comprobar si está definido el parámetro multidioma
        $this->GetLan = isset($configurator->language);
        // Establecer el idioma por defecto si procede
        if($this->GetLan){
            $this->Language =
                    (string)$configurator->language->attribute["default"];
        }
    }

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido (existe).
     * @var string $controller Nombre del controlador solicitado
     * @return boolean Estado de la validacion
     */
    public function ValidateController($controller){
        return (array_key_exists ($controller , $this->Controllers ));
    }

    /**
     * Determina si el nombre del controlador pasado como argumento
     * se corresponde con un controlador válido y si existe alguna acción
     * en dicho controlador con el nombre pasado como argumento.
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la accion
     * @return boolean Estado de la validacion
     */
    public function Validate($controller, $action){
        return (array_key_exists ($controller , $this->Controllers ))
                    && (array_key_exists ($action ,
                            $this->Controllers[$controller]["actions"]));
    }

    /**
     * Establece el controlador y la acción por defecto si procede
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la accion
     * @return array Array con los parametros de la solicitud
     */
    public function SetDefault($controller, $action){
        if(array_key_exists ($controller , $this->Controllers)){
            return array("Controller" => $controller,
                "Action" => $this->Controllers[$controller]["action"]);
        }
        else{
            // Establecer el mensaje de error
            $message = "HttpHandler - SetDefault - Controller : "
                    .$controller.", Action : ".$action;
            // Lanzar excepción
            throw new UrlException( $message );
        }
    }

    /**
     * Procesa la url actual obteniendo el nombre del controlador,
     * la acción y los parámetros de la petición realizada. La información
     * obtenida se retornará mediante un array/diccionario donde se
     * especifique cada uno de las partes obtenidas. En el caso de que
     * la url no esté correctamente formada se lanzará una excepción.
     * @var string $urlRequest Url de la solicitud
     * @return array Array con los parametros para procesar la solicitud
     */
    public function ProcessUrl($urlRequest){
        // Extraer parámetros de la url ( si existen )
        $urlParts = explode("?", $urlRequest);
        // Establecer las partes de la url
        $urlPartsCount = count($urlParts);
        // Comprobar si había parámetros
        if($urlPartsCount == 1){
            // Extraemos la parte sin parámetros de la petición
            $url = $urlParts[0];
            // Fraccionamos la url para obtener controlador, acción e idioma(si procede)
            $parts = explode("/", $url);
            // Asignar cada parte de la url
            $this->GetUrlParts($parts);
        }
        else if($urlPartsCount > 1){
            // Extraemos la parte sin parámetros de la petición
            $url = $urlParts[0];
            // Fraccionamos la url para obtener controlador,
            // acción e idioma(si procede)
            $parts = explode("/", $url);
            // Asignar cada parte de la url
            $this->GetUrlParts($parts);
            // Asignar los parñametros dle querystring
            $this->Params = $urlParts[1];
        }
        else{
            throw new \UrlException("HttpHandler - ProcessUrl - "
                    . $urlRequest);
        }
        // Establecer los parámetros obtenidos
        $this->ProcessParameters($this->Params);
        // Establecer el resultado del análisis
        $result = array(
            "Language" => $this->Language,
            "Controller" => $this->Controller,
            "Action" => $this->Action,
            "First" => $this->First,
            "Params" => $this->Params
        );
        // Retornar la estructura de datos
        return $result;
    }

    /**
     * Debe encargarse de realizar el procesado de los parámetros de la
     * petición (get / post) según las especificaciones del proyecto.
     * Por lo general, se encargará de buscar estructuras de posibles
     * ataques de inyección.
     * @var array $parameters Array de parametros para la solicitud
     */
    public function ProcessParameters($parameters){
        // Estabelcer el array a devolver
        $params = array();
        // Establecer el parámetro pasado como parte de la acción
        if(isset($this->First)){
            $params["id"] = $this->First;
        }
        // Validación del argumento
        if(is_string($parameters)) {
            // Fraccionar la lista de parámetros
            $parameters = explode("&", $parameters);
            // Recorrer los parámetros
            //foreach($parameters as $key => $value){
            foreach($parameters as $value){
                $parts = explode("=", $value);
                if(count($parts) == 2){
                    $params[$parts[0]] = $parts[1];
                }
            }
        }
        // Almacenar los parámetros obtenidos
        $this->Params = $params;
    }

    /**
     * Se encarga de configurar el idioma de la petición/ ejecución en
     * los contextos donde es necesario( aplicaciones multilenguaje).
     * @var string $language Idioma seleccionado
     */
    public function SetLanguage($language){
        // Establecer el lenguaje como parte del handler
        $this->Language = $language;
        // Almacenar el valor de lenguaje en la sesión
        $_SESSION["language"] = $language;
    }

    /**
     * Obtiene el lenguaje del contexto actual si es que está
     * definido o una cadena vacía.
     */
    public function GetLanguage(){
        return (isset($_SESSION["language"]))
            ? $_SESSION["language"] : "";
    }

    /**
     * Ejecuta la acción del controlador definidos por la petición con
     * los parámetros fijados (si hay). Retorna la información que la
     * petición devolverá como respuesta a la petición del cliente.
     * @var string $sController Nombre del controlador
     * @var string $action Nombre de la accion
     * @var array $params Coleccion de parametros de la llamada
     */
    public function Run($sController, $action, $params = null){
        $name = $sController."Controller";
        require_once("controller/$name.php");
        $controller = new $name();
        return call_user_func_array(array($controller, $action), $params);
    }

    /**
     * Almacena en el contexto la colección de rutas establecidas
     * (controlador-acción) para poder validar la acción y el controlador
     * que se desean ejecutar.
     */
    public function RegisterRoutes($routes){
        throw new \NotImplementedException( "HttpHandler" );
    }

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * DTO para visualizar errores
 */
class ItemError{

    /**
     * Texto a visualizar
     * @var string
     */
    public $Text = "";

    /**
     * Constructor
     * @param string $text texto a visualizar
     */
    public function __construct($text = "") {
        $this->Text = $text;
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Clase para los controles option de un control Select
 *
 * @author alfonso
 */
class SelectControlItem{

    /**
     * Valor del control Option
     * @var string
     */
    public $Value = "";

    /**
     * Texto del control Option
     * @var string
     */
    public $Text = "";

    /**
     * Constructor
     * @param string $text Texto a utilizar
     * @param string $value Valor asociado
     */
    public function __construct($text="", $value = ""){
        $this->Text = $text;
        $this->Value = $value;
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * DTO para retornar resultado de operaciones en JSON
 *
 * @author alfonso
 */
class JsonResultDTO {
    public $Result = FALSE;
    public $Error = "";
    public $Code = 200;
    public $Exception = NULL;
}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Clase para el uso de excepciones genéricas en la clase de
 * acceso a datos StmtClient
 *
 * @author alfonso
 */
class StmtClientException extends \BaseException{

    /**
     * Redefinir la excepción, por lo que el mensaje no es opcional
     * @param string $message Mensaje de error
     * @param int $code Código de excepción
     * @param Exception $previous Excepción previa
     */
    public function __construct($message = "" , $code = 0,
            \Exception $previous = null) {
       parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepciones específica en la ejecución de INSERT, UPDATE, DELETE...
 *
 * @author alfonso
 */
class StmtClientExecuteException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param Exception $previous Excepción previa
     */
    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación de la interfaz IValidatorClient
 *
 * @author alfonso
 */
class ValidatorClient implements \IValidatorClient{

    /**
     * Validación de la entidad
     * @var \IValidatorClient $_singleton
     * Referencia al cliente de validación
     */
    private static $_singleton = null;

    /**
     * Validación de la entidad
     * @var boolean $_isConfigure Estado de configuración
     */
    private $_isConfigure = false;

    /**
     * Constructor por defecto
     */
    private function __construct(){

    }

    /**
     * Validación de la entidad
     * @var object $entity Referencia a la entidad a validar
     */
    public function Validate($entity = null){
        return array();
    }

    /**
     * Configuración del validador con el xml de base de datos
     * @var string $fileName Nombre del fichero de configuración
     */
    public function Configure($fileName = ""){
        $this->_isConfigure = true;
    }

    /**
     * Obtiene una referencia al validador de entidades
     * @var string $fileName Nombre del fichero de configuración
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya hay una instancia
        if(ValidatorClient::$_singleton == null){
            ValidatorClient::$_singleton = new \ValidatorClient();
        }

        if($fileName != ""){
            ValidatorClient::$_singleton->Configure($fileName);
        }

        return ValidatorClient::$_singleton;
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Clase de acceso a datos utilizando la clase de mysqli Stmt
 * ref : http://php.net/manual/es/class.mysqli-stmt.php
 *
 * @author alfonso
 */
class StmtClient {

    /**
     * Referencia a la instancia existente
     * @var \StmtClient Referencia al cliente StmtClient actual
     */
    private static $_singleton = null;

    /**
     * Nombre del fichero de configuración y mapeado de la bbdd
     * @var string $filename Nombre del fichero de configuración
     */
    private $_filename;

    /**
     * Array con los parámetros de conexión :
     * array ( "server" => "server", "user" => "user",
     * "password" => "password", "scheme" => "scheme");
     * @var array $_oConnData array de datos de conexión
     */
    private $_oConnData = null;

    /**
     * Nombre de la base de datos
     * @var string Nombre de la base de datos
     */
    private $_dbName;

    /**
     * Colección de tablas y vistas definidas
     * @var array $_dbObjects Array de objetos mapeados
     */
    private $_dbObjects = array();

    /**
     * Referencia a la instancia de conexión mysqli
     * @var object $_oConn Referencia al objeto de conexión
     */
    private $_oConn = null;

    /**
     * Cadena con los tipos de parámetros declarados
     * @var string Cadena de tipología de datos
     */
    private $_strTypes = "";

    /**
     * Array de parámetros a utilizar en la consulta
     * @var array $_parameters Array de parámetros a utilizar
     */
    private $_parameters = array();

    /**
     * Consulta SQL a ejecutar
     * @var string
     */
    private $_query = "";

    /**
     * Nombre del campo PK que se debe utilizar
     * @var string
     */
    private $_pkname = "";

    /**
     * Clausula WHERE de la consulta sql
     * @var string
     */
    private $_where = "";

    /**
     * Nombre de la tabla sobre la que se opera
     * @var string
     */
    private $_tablename = "";

    /**
     * Obtiene el tipo de dato del parámetro
     * @var string $type Tipo de dato
     */
    private function GetPropertyType($type = ""){
        if($type == "string" || $type == "date"){
            return "s";
        }
        else if ($type == "int" || $type == "bool"){
            return "i";
        }
        else if ($type == "double" || $type == "float"){
            return "d";
        }
        else{
            return "s";
        }
    }

    /**
     * Obtiene el tipo de dato del parámetro
     * @var string $type tipo de dato
     * @var object $val valor de dato
     */
    private function GetPropertyValue($type = "", $val= null){
        $value = $val;

        if($type == "bool"){
            $value = ($val) ? 1: 0;
        }

        return $value;
    }

    /**
     * Validar si la entidad especificada está mapeada en el
     * archivo de configuración
     * @var string $entityName Nombre de la entidad
     */
    private function IsMapped($entityName = "" ){
        // Comprobar que se ha pasado un nombre de entidad
        if($entityName == ""){
            throw new \StmtClientException("EntityName : is Empty");
        }
        // Comprobar que la entidad está definida en la lista
        // de objetos mapeados
        if(!array_key_exists( $entityName, $this->_dbObjects )){
            throw new \StmtClientException("EntityName : ".$entityName);
        }
        // Retornar datos del objeto mapeado
        return $this->_dbObjects[$entityName];
    }

    /**
     * Iniciar los atributos utilizados al construir las querys
     */
    private function InitQueryParameters(){
        // Iniciar la lista de parámetros
        $this->_parameters = array();
        // Iniciar la lista de tipos
        $this->_strTypes = "";
        // Iniciar variables para las consultas
        $this->_query = "";
        $this->_pkname = "";
        $this->_where = "";
        $this->_tablename = "";
    }

    /**
     * Obtiene los atributos del nodo xml y los devuelve en un array
     * @param object $attrs Referencia al nodo xml
     * @return array
     */
    private static function ReadAttributes($attrs = null){
        $atributos = array();
        // Recorrer la colección de columnas del xml generando el array de columnas
        foreach($attrs as $attr){
            // Obtener los atributos del nodo
            $attributes = $attr->attributes();
            // Guardar los atributos en el array
            $atributos[(string)$attributes->property] = array(
                "Name" => (string) $attributes->name,
                "Property" => (string) $attributes->property,
                "DataType" => (string) $attributes->dataType,
                "ColumnType" => (string) $attributes->columnType,
                "Required" => (string) $attributes->required,
                "MaxLength" => (isset($attributes->maxLength))
                    ? (string) $attributes->maxLength : "-",
                "Min" => (isset($attributes->min))
                ? (string) $attributes->min : "-",
                "Max" => (isset($attributes->max))
                ? (string) $attributes->max : "-"
            );
        }
        return $atributos;
    }

    /**
     * Carga la información de los atributos de un objeto de base de datos
     * @var object $object Referencia al nodo xml
     */
    private static function GetAttributes($object = null){
        // colección de columnas
        $atributos = array();
        // validar el nodo xml
        if($object == null){
            return $atributos;
        }
        // Obtener la colección de columnas de la tabla
        $attrs = $object->children();
        // Obtener los atributos
        return StmtClient::ReadAttributes($attrs);
    }

    /**
     * Carga la información de los objetos de base de datos
     * @var object $objects parámetros de carga
     */
    private function Load($objects = null){
        // Validación del parámetros
        if( $objects == null ){
            return;
        }
        // Recorremos la colección de tablas cargando los
        // datos de cada nodo xml
        foreach($objects as $object){
            // Obtener las columnas de la tabla
            $attrs = StmtClient::GetAttributes($object);

            $attributes = $object->attributes();
            // Agregar cada objeto de base de datos incluido en el xml
            $this->_dbObjects[(string)$attributes->entity] = array(
                "Type" =>(string)$attributes->type,
                "Name" => (string)$attributes->name,
                "Entity" => (string)$attributes->entity,
                "Properties" =>$attrs
            );
        }
    }

    /**
     * Obtener los datos de configuración de la conexión desde el
     * xml de descripción de la base de datos
     * @var array $databaseNode Nodo xml sobre la base de datos
     */
    private function GetDataConnection($databaseNode = null){
        // Validar el nodo pasado como argumento
        if($databaseNode == null) {
            return array();
        }
        // Extraer info de los atributos
        $attrs = $databaseNode->attributes();
        // Retornar array con los datos de conexión
        return array(
                "server" => (string)$attrs->server,
                "user" => (string)$attrs->user,
                "password" => (string)$attrs->password,
                "scheme" => (string)$attrs->scheme
         );
    }

    /**
     * Carga la información del fichero de configuración
     */
    private function LoadDataBase(){
        // Validar el fichero de descripción de base de datos
        if(!file_exists( $this->_filename )){
            throw new \Exception( "FileNotFound :".$this->_filename);
        }
        // Leer el xml con la descripción de la base de datos
        $configurator = simplexml_load_file($this->_filename);
        // Setear los datos de configuración de la conexión a base de datos
        $this->_oConnData =
                $this->GetDataConnection($configurator->database);
        // Extraer la colección de objetos de la base de datos
        $objects = $configurator->objects->children();
        // Cargar la colección de objetos
        $this->Load($objects);
    }

    /**
     * Constructor privado de la clase
     * @var string $fileName Nombre del fichero
     */
    private function __construct($fileName = ""){
        // Setear el fichero de configuración
        $this->_filename = ($fileName != "")
                ? $fileName : "database.xml";
        // Cargar la información de base de datos
        $this->LoadDataBase();
    }

    /**
     * Destructor de la clase
     */
    public function __destruct(){
        $this->Close();
    }

    /**
     * Adapta los datos obtenidos contra una entidad
     * @var string $entityName Nombre de la entidad
     * @var array $arrayData datos
     */
    public function SetEntity($entityName, $arrayData){
        // Instanciar entidad
        $entity = new $entityName();
        // Instanciar reflector
        $reflector = new \ReflectionClass($entityName);
        // Obtener las propiedades del objeto
        $properties = $reflector->getProperties();
        // Recorrer las propiedades asignando el valor correspondiente
        foreach($properties as $property){
            // Validar la propiedad con el array
            if(!array_key_exists($property->getName(), $arrayData)){
                continue;
            }
            // Setear el valor de la propiedad
            $entity->{ $property->getName() } =
                    $arrayData[$property->getName()];
        }
        // Retornar instancia de la entidad
        return $entity;
    }

    /**
     * Obtener una instancia de entidad con el Id seteado
     * @var string $entityName Nombre de la entidad
     * @var object $identity Identidad de la entidad
     */
    public function GetEntity($entityName, $identity){
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener el nombre del ojeto
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        // Instanciar entidad
        $entity = new $entityName();
        // Asignar el valor de la identidad
        // foreach($columns as $key => $value){
        foreach($columns as $value){
            if($value["ColumnType"] == "pk"
                    || $value["ColumnType"] == "pk-auto"){
                $entity-> { $value["Property"] } = $identity;
            }
        }
        // Retornar referencia a la entidad
        return $entity;
    }

    /**
     * Agrega el tipo de dato de la propiedad filtrando si no es
     * un "filter"
     * @param array $data Información de la propiedad
     */
    private function SetPropertyType($data = ""){
        $pos = strpos($data["Property"], "-filter");
        // Parámetros por filtro
        if(!($pos === false)){
            $this->_strTypes .= $this->GetPropertyType( $data["DataType"] );
        }
    }

    /**
     * Establece los parámetros de consulta
     * @var object $entity Referencia a la entidad
     */
    public function SetParameters($entity = null){
        // Validar parámetro
        if($entity != null){
            if(!is_array($entity)){
                settype( $entity, "array" );
            }
            foreach($this->_parameters as $key => $value){

                if(array_key_exists($value["Property"], $entity)) {
                    //$propName = $value["Property"];
                    $this->_parameters[$key]["Value"] =
                            $this->GetPropertyValue($value["DataType"],
                                    $entity[$value["Property"]]);
                    $this->_strTypes .=
                            $this->GetPropertyType($value["DataType"]);
                    continue;
                }

                $this->SetPropertyType($value);
                /*
                $pos = strpos($value["Property"], "-filter");
                    // Parámetros por filtro
                if(!($pos === false)){
                    $this->_strTypes .=
                            $this->GetPropertyType( $value["DataType"] );
                }
                */
            }
        }
    }

    /**
     * Ejecuta una consulta sin evaluar el resultado
     * @var string $query Consulta Sql a ejecutar
     */
    public function Execute($query){
        $stmt = $this->_oConn->prepare($query);
        $parameters = array();
        $parameters[] = $this->_strTypes;
        foreach($this->_parameters as $key => $value){
            $parameters[] = &$this->_parameters[$key]["Value"];
        }

        call_user_func_array(array($stmt, 'bind_param'), $parameters);

        if($stmt){
            if($stmt->execute()) {
                $id = $stmt->insert_id;
                $stmt->close();
                return $id;
            }
            throw new \StmtClientExecuteException(
                "Execute execute fail : ".$stmt->error);
        }
        throw new \StmtClientExecuteException(
            "Execute prepare fail : ".$this->_oConn->error);
    }

    /**
     * Ejecuta una consulta de lectura de datos
     * @var string $query Consulta Sql a ejecutar
     * @var string $entityName Nombre de la entidad
     */
    public function ExecuteQuery($query, $entityName){
        $result = array();
        $temp = new $entityName();
        settype( $temp, "array" );

        if (true == $stmt = $this->_oConn->prepare($query)) {
            $count = count($this->_parameters);
            if( $count > 0){
                $parameters[] = $this->_strTypes;
                foreach($this->_parameters as $key => $value){
                    $parameters[] = &$this->_parameters[$key]["Value"];
                }

                call_user_func_array(array($stmt, 'bind_param'),
                        $parameters);
            }

            if($stmt->execute()){
                $parameters = array();
                foreach($temp as $key => $value){
                    if(is_array($value)) {
                        continue;
                    }
                    $parameters[$key] = &$temp[$key];
                }

                call_user_func_array(array($stmt, 'bind_result'),
                        $parameters);

                while ($stmt->fetch()) {
                    $item = $this->SetEntity($entityName, $temp);
                    array_push($result, $item);
                }
                $stmt->close();
            }
            else{
                throw new \StmtClientExecuteException(
                        "ExecuteQuery execute fail : ".$stmt->error);
            }
        }
        else{
            throw new \StmtClientExecuteException(
                    "ExecuteQuery prepare fail : ".$this->_oConn->error);
        }

        return $result;
    }

    /**
     * Genera el string de consulta para la obtener/leer una
     * entidad filtrada por su id
     * @var string $entityName Nombre de la entidad
     */
    public function GetReadQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        // foreach($columns as $key => $value){
        foreach($columns as $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
            if($value["ColumnType"] == "pk"
                    || $value["ColumnType"] == "pk-auto"){
                $this->_pkname = $value["Name"];
                array_push($this->_parameters, array (
                    "Name" => $value["Name"],
                    "Property" => $value["Property"],
                    "DataType" => $value["DataType"],
                    "Value" => ""
                ));
            }
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM "
                .$this->_tablename." WHERE ".$this->_pkname." = ? ;";
    }

    /**
     * Obtiene la cadena de columnas y parámetros para una consulta
     * Insert
     * @param array $columns Colección de columnas que intervienen
     * @return array
     */
    private function GetParamsAndNamesInsert($columns = null){

        $result = array( "Names" => "", "Params" => "" );

        if(isset($columns) && is_array($columns)){
            $sParams = "";
            $sNames = "";
            // foreach($columns as $key => $value){
            foreach($columns as $value){
                if($value["ColumnType"] != "pk-auto"){
                    $sNames .= ", ".$value["Name"];
                    $sParams .= ", ?";
                    array_push($this->_parameters, array (
                                "Name" => $value["Name"],
                                "Property" => $value["Property"],
                                "DataType" => $value["DataType"],
                                "Value" => ""
                        ));
                }
            }
            $result["Names"] = substr($sNames, 1);
            $result["Params"] = substr($sParams, 1);
        }
        return $result;
    }

    /**
     * Genera el string de consulta para la creación una entidad
     * @var string $entityName Nombre de la entidad
     */
    public function GetCreateQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        // obtiene las subcadenas de la consulta
        $res = $this->GetParamsAndNamesInsert($columns);
        // Retornar la consulta
        return "INSERT INTO ".$this->_tablename
            ." (".$res["Names"].") VALUES (".$res["Params"].");";
    }

    /**
     * Genera el string de consulta para la actualización una entidad
     * @var string $entityName Nombre de la entidad
     */
    public function GetUpdateQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];

        $pkName = "";
        $pkProperty = "";
        $pkDataType = "";

        foreach($columns as $key => $value){
            // Si es clave, guardamos los datos para
            // incluirlos en el filtro where
            if($value["ColumnType"] == "pk-auto"
                    || $value["ColumnType"] == "pk"){
                $pkName = $value["Name"];
                $pkProperty = $value["Property"];
                $pkDataType = $value["DataType"];
                continue;
            }

            $this->_query .= ", ".$value["Name"]." = ?";

            array_push($this->_parameters, array (
                                "Name" => $value["Name"],
                                "Property" => $value["Property"],
                                "DataType" => $value["DataType"],
                                "Value" => ""
                            ));
        }

        array_push($this->_parameters, array (
                            "Name" => $pkName,
                            "Property" => $pkProperty,
                            "DataType" => $pkDataType,
                            "Value" => ""
                        ));

        $this->_query = substr($this->_query, 1);

        return "UPDATE ".$this->_tablename." SET "
                .$this->_query." WHERE ".$pkName." = ?;";
    }

    /**
     * Genera el string de consulta para eliminar una entidad por su id
     * @var string $entityName Nombre de la entidad
     */
    public function GetDeleteQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener el nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];

        foreach($columns as $key => $value){
            if($value["ColumnType"] == "pk"
                    || $value["ColumnType"] == "pk-auto"){
                $this->pkname = $value["Name"];
                array_push($this->_parameters, array (
                    "Name" => $value["Name"],
                    "Property" => $value["Property"],
                    "DataType" => $value["DataType"],
                    "Value" => ""
                ));
            }
        }
        return "DELETE FROM ".$this->_tablename
                ." WHERE ".$this->pkname." = ?;";
    }

    /**
     * Genera el string de consulta para obtener una lista
     * de entidades ordenadas
     * @var string $entityName Nombre de la entidad
     * @var object $order
     */
    public function GetOrderByQuery($entityName = "", $order = null){
        // Comprobar el mapeado
        $object = $this->IsMapped($entityName);
        // Obtener las "keys" del array asociativo para crear la query
        $keys = array_keys ($order);
        // Obtener la consulta de filtro
        $sSqlQuery = $this->GetListQuery($entityName);
        // Eliminar fin de consulta
        $sqlQuery = str_replace( ";", "", $sSqlQuery );
        // Obtener los parámetros de la clausula
        $columnas = $object["Properties"];
        // Nombre de la columna
        $columna = $columnas[$keys[0]]["Name"];
        // Tipo de orden : ASC | DESC
        $tipo = $order[$keys[0]];
        // Agregar clausula order by
        $sqlQuery .= " ORDER BY ".$columna." ".$tipo;
        // retornar la consulta generada
        return $sqlQuery;
    }

    /**
     * Genera el string de consulta para obtener una lista de
     * entidades filtradas y ordenadas
     * @var string $entityName Nombre de la entidad
     * @var array $filter filtro de búsqueda
     * @var object $order
     */
    public function GetOrderByFilterQuery($entityName = "",
            $filter = null, $order = null){
        // Comprobar el mapeado
        $object = $this->IsMapped($entityName);
        // Obtener las "keys" del array asociativo para crear la query
        $keys = array_keys ($order);
        // Obtener la consulta de filtro
        $sSqlQuery = $this->GetFilterQuery($entityName, $filter);
        // Eliminar fin de consulta
        $sqlQuery = str_replace( ";", "", $sSqlQuery );
        // Obtener los parámetros de la clausula
        $columnas = $object["Properties"];
        // Nombre de la columna
        $columna = $columnas[$keys[0]]["Name"];
        // Tipo de orden : ASC | DESC
        $tipo = $order[$keys[0]];
        // Agregar clausula order by
        $sqlQuery .= " ORDER BY ".$columna." ".$tipo;
        // retornar la consulta generada
        return $sqlQuery;
    }

    /**
     * Genera el string de consulta para obtener una lista de
     * entidades filtradas
     * @var string $entityName Nombre de la entidad
     * @var array $filter Filtro de parámetros
     */
    public function GetFilterQuery($entityName = "", $filter = null){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener el nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];

        foreach($columns as $key => $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
        }

        if(is_array($filter)){

            $nFilter = array();
            // traducir los nombres de columnas
            foreach($columns as $key => $value){
                if(array_key_exists( $value["Property"], $filter)){
                    $value["Value"] = $filter[$value["Property"]];
                    $nFilter[$value["Name"]] = $value;
                }
            }

            foreach($nFilter as $key => $value){

                if($value["Value"] === NULL){
                    $this->_where .= " AND ".$key." is null";
                    continue;
                }

                $this->_where .= ($value["DataType"]=="string"
                        || $value["DataType"]=="date")
                        ? " AND ".$key." LIKE ?" : " AND ".$key." = ?";

                array_push($this->_parameters, array (
                        "Name" => $key,
                        "Property" => $value["Property"]."-filter",
                        "DataType" => $value["DataType"],
                        "Value" => $value["Value"]
                    ));
            }

            if(strlen($this->_where) > 0){
                $this->_where = " WHERE ".substr($this->_where, 4);
            }
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM "
                .$this->_tablename." ".$this->_where.";";
    }

    /**
     * Genera el string de consulta para obtener una lista de
     * entidades filtradas
     * @var string $entityName Nombre de la entidad
     * @var array $filter filtro para la busqueda
     */
    public function GetStringFilterQuery($entityName = "", $filter = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns =$object["Properties"];
        //foreach($columns as $key => $value){
        foreach($columns as $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
        }

        if(strlen($filter) > 0){
            $this->_where = " WHERE ".$filter;
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM "
                .$this->_tablename." ".$this->_where.";";
    }

    /**
     * Genera el string de consulta para obtener una lista de entidades
     * @var string $entityName Nombre de la entidad
     */
    public function GetListQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        //foreach($columns as $key => $value){
        foreach($columns as $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM ".$this->_tablename.";";
    }

    /**
     * Establece los parámetros de conexión con la bbdd :
     * array ( "server" => "server", "user" => "user",
     * "password" => "password", "scheme" => "scheme");
     * @var array $data Datos de la conexión
     */
    public function SetDataConnection($data = null){
        // Validación del parámetro de conexión
        if($data == null){
            return;
        }
        // Setear los datos
        $this->_oConnData = $data;
    }

    /**
     * Abre una conexión a base de datos
     */
    public function Open(){
        // Validar que se han seteado los parámetros de conexión
        if($this->_oConnData == null){
            throw new \StmtClientException('No data connection');
        }
        // Referencia a los datos de conexión
        $data = $this->_oConnData;
        // Instanciar referencia a mysqli
        $this->_oConn = new \mysqli($data["server"], $data["user"],
                $data["password"],$data["scheme"]);
        // Comprobar que no hay errores de conexión y finalizar
        if (is_null(mysqli_connect_error())){
            return;
        }
        // Se ha producido un error: Eliminar la conexión
        // die("");
        // Lanzar una excepción con los datos del error
        throw new \StmtClientException('Fail connection.. :'
                . mysqli_connect_error());
    }

    /**
     * Cierra la conexión actual si está abierta
     */
    public function Close(){

    }

    /**
     * Obtiene una instancia del objeto de acceso a base de datos
     * @var string $fileName Nombre del fichero de configuración
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya está instanciada la referencia
        if(StmtClient::$_singleton == null){
            // Crear una nueva instancia
            StmtClient::$_singleton = new \StmtClient($fileName);
        }
        // Retornar referencia a la instancia
        return StmtClient::$_singleton;
    }
}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Implementación de la interfaz DAO basado en la clase StmtClient
 * para el acceso a la base de datos
 *
 * @author alfonso
 */
class StmtBaseDAO implements \IDataAccessObject{

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager Referencia al gestor de trazas
     */
    private $_log = null;

    /**
     * Atributo para generar trazas por cada consulta ejecutada
     * @var boolean Indica si está en modo debug
     */
    private $_isDebug = FALSE;

    /**
     * Referencia al cliente Stmt para el acceso a base de datos
     * @var \SmtpClient Referencia al cliente Stmt mysqli
     */
    protected $StmtClient = null;

    /**
     * Referencia al cliente validador de entidades
     * @var IValidatorClient Referencia al cliente de validación
     */
    protected $ValidatorClient = null;

    /**
     * Establece si el modo de trabajo es en depuración
     * (Generando trazas de consultas)
     */
    private function SetDebug(){
        // Evaluar si está definido el modo depuración
        $this->_isDebug = (DEBUG == 1);
    }

    /**
     * Configuración de las dependencias
     */
    private function SetReferences(){
        // Obtener referencia al inyector de dependencias
        $injector = Injector::GetInstance();
        // Obtener referencia al gestor de trazas
        $this->_log = $injector->Resolve( "ILogManager" );
    }

    /**
     * Generación de trazas de las consultas a ejecutar
     * @var string $method Método que ejecuta la consulta
     * @var string $entity Nombre de la entidad relacionada
     * @var string $query Cunslta sql a ejecutar
     */
    protected function LogQuery( $method = "", $entity = "" ,$query = "" ){
        // Comprobación del modo de trabajo
        if(!$this->_isDebug) {
            return;
        }
        // Comprobación de la referencia al gestor de trazas
        if($this->_log == null) {
            return;
        }
        // Creación del mensaje
        $message = $method . " - ".$entity." - ".$query;
        // Generar traza en modo info
        $this->_log->LogInfo( $message );
    }

    /**
     * Constructor por defecto de la clase
     */
    public function __construct(){
        // Establecer el modo de trabajo
        $this->SetDebug();
        // Establecer las referencias de los atributos
        $this->SetReferences();
    }

    /**
     * Permite configurar los parámetros de la conexión al
     * sistema de persistencia.
     * @var array $connection Datos de la conexión
     */
    public function Configure($connection = null){
        // Validación del parámetro
        if ($connection == null){
            return;
        }
        // Obtener Instancia del dao
        $this->StmtClient =
                StmtClient::GetInstance(null, $connection["filename"]);
        // Obtener referencia al validador de entidades
        $this->ValidatorClient =
                ValidatorClient::GetInstance($connection["filename"]);
    }

    /**
     * Persiste la entidad en el sistema y la retorna actualizada
     * @var object $entity Referencia a la entidad
     */
    public function Create($entity){
        // Obtener el nombre de la entidad
        $entityName = get_class($entity);
        // Obtener la consulta
        $select = $this->StmtClient->GetCreateQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Create", $entityName, $select);
        // Configurar los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $id = $this->StmtClient->Execute($select);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar Id generado
        return $id;
    }

    /**
     * Obtiene una entidad filtrada por su identidad utilizando
     * el nombre del tipo de entidad
     * @var object $identity Identidad de la entidad
     * @var string $entityName Nombre de la entidad
     */
    public function Read($identity, $entityName){
        // Obtener la consulta
        $select = $this->StmtClient->GetReadQuery( $entityName );
        // Generar la traza de la consulta
        $this->LogQuery( "Read", $entityName, $select);
        // Obtener instancia de la entidad
        $entity = $this->StmtClient->GetEntity( $entityName, $identity );
        // Configurar los parámetros
        $this->StmtClient->SetParameters( $entity );
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $result =	$this->StmtClient->ExecuteQuery( $select, $entityName );
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar la referencia (si existe)
        return (count($result) >0)? $result[0] : null;
    }

    /**
     * Actualiza la información de la entidad en el sistema de persistencia.
     * @var object $entity Referencia a la entidad
     */
    public function Update($entity){
        // Obtener el nombre de la [entidad | clase]
        $entityName = get_class($entity);
        // Obtener la consulta
        $select = $this->StmtClient->GetUpdateQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Update", $entityName, $select);
        // Configurar los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $data = $this->StmtClient->Execute($select);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar el resultado de la consulta
        return $data;
    }

    /**
     * Elimina la entidad utilizando su identidad y el nombre del
     * tipo de entidad
     * @var object $identity Identidad de la entidad
     * @var string $entityName Nombre de la entidad
     */
    public function Delete($identity, $entityName){
        // Obtener la consulta
        $select = $this->StmtClient->GetDeleteQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Delete", $entityName, $select);
        // Obtener instancia de la entidad
        $entity = $this->StmtClient->GetEntity( $entityName, $identity );
        // Configurar los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $data = $this->StmtClient->Execute($select);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar el resultado de la operación
        return $data;
    }

    /**
     * Obtiene el conjunto de entidades existentes del tipo especificado
     * @var string $entityName Nombre de la entidad
     */
    public function Get($entityName){
        // Obtener la consulta
        $select = $this->StmtClient->GetListQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Get", $entityName, $select);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $result = $this->StmtClient->ExecuteQuery($select, $entityName);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar el resultado
        return $result;
    }

    /**
     * Obtiene el conjunto de entidades del tipo especificado mediante
     * el filtro especificado. El filtro debe ser un array:
     * array( "Campo1" => valor1, "Campo2" => valor2... )
     * @var string $entityName Nombre de la entidad
     * @var array $filter Filtro de búsqueda
     */
    public function GetByFilter($entityName, $filter){
        // Obtener la consulta
        $select = $this->StmtClient->GetFilterQuery($entityName, $filter);
        // Generar traza de la consulta
        $this->LogQuery( "GetByFilter", $entityName, $select);
        // Instanciar entidad
        $entity = $this->StmtClient->SetEntity( $entityName, $filter);
        // Setear los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();

        // Ejecutar la consulta
        $result = $this->StmtClient->ExecuteQuery($select, $entityName);
        // Cerrar la conexión
        $this->StmtClient->Close();
        // Retornar el resultado
        return $result;
    }

    /**
     * Ejecuta la consulta pasada como parámetro
     * @var string $query Consulta sql a ejecutar
     */
    public function ExeQuery($query){
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $this->StmtClient->ExecuteQuery($query);
        // Cerrar conexión
        $this->StmtClient->Close();
    }

    /**
     * Valida el contenido de una entidad
     * @var object $entity Referencia a la entidad a validar
     */
    public function IsValid($entity){
        // Comprobar la referencia
        if( $this->ValidatorClient == null) {
            return array();
        }
        // validación de la entidad
        return $this->ValidatorClient->Validate($entity);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
 *  Dependencias:
 *
 *	- ConfigurationManager
 *	- Injector
 *	- PasswordFactory
 *	- IDataAccessObject y una implementación
 *	- Notification (entidad bbdd)
 */

/**
 * DTO para el envío de notificaciones de passwords
 *
 * @author alfonso
 */
class UserDTOUtils{

    /**
     * Email del usuario
     * @var string
     */
    public $Email = "";

    /**
     * Nueva contraseña generada
     * @var string
     */
    public $Password = "";

    /**
     * Fecha en la que se genera la contraseña
     * @var string
     */
    public $Date = "";

}

/**
 * Utilidades comunes para los usuarios
 *
 * @author alfonso
 */
class UserUtilities{

    /**
     * Obtiene una instancia de acceso a datos
     * @return \IDataAccessObject
     */
    private static function GetDao(){
        // Obtener referencia al inyector
        $injector = Injector::GetInstance();
        // Obtener referencia al dao
        $dao = $injector->Resolve( "IDataAccessObject" );
        // Obtener nombre de la cadena de conexión
        $connectionString =
                ConfigurationManager::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString =
                ConfigurationManager::GetConnectionStr($connectionString);
        // Configurar el objeto de conexión a datos
        $dao->Configure($oConnString);
        // Retornar referencia
        return $dao;
    }

    /**
     * Registrar los datos de usuario en la base de datos
     * @param \PasswordFactory $factory referencia al generador de passwords
     * @param \User $user Referencia a los datos de usuario
     * @return string
     */
    private static function Create($factory = null, $user = null){
        if($factory != null && $user != null){
            // Obtener referencia al dao
            $dao = UserUtilities::GetDao();
            // Generar password nueva
            $pass = $factory->GetPassword();
            // Generar Hash
            $hash = $factory->GetSHA512( $pass );
            // asignar nueva pass
            $user->Password = $hash;
            // guardar los datos
            $dao->Create( $user );
        }
        return $pass;
    }

    /**
     * Resetea el password del usuario
     * @param \PasswordFactory $factory Referencia al generador de passwords
     * @param \User $user Referencia al usuario
     * @return string
     */
    private static function Update($factory = null, $user = null){
        if($factory != null && $user != null){
            // Obtener referencia al dao
            $dao = UserUtilities::GetDao();
            // Generar password nueva
            $pass = $factory->GetPassword();
            // Generar Hash
            $hash = $factory->GetSHA512( $pass );
            // asignar nueva pass
            $user->Password = $hash;
            // guardar los datos
            $dao->Update( $user );
        }
        return $pass;
    }

    /**
     * Obtiene un dto de usuario para la notificación
     * @param \User $user
     * @param string $pass
     * @return \UserDTOUtils
     */
    private static function GetUserDto($user = null, $pass = ""){
        // Instanciar datetime
        $date = new \DateTime( "NOW" );
        // Crear dto con la información a enviar
        $userDto = new \UserDTOUtils();
        $userDto->Email = $user->Username;
        $userDto->Password = $pass;
        $userDto->Date = $date->format( "d-m-Y" );
        return $userDto;
    }

    /**
     * Genera la notificación de nuevo usuario
     * @param array $data Array con la información del contexto
     * @param \User Referencia al usuario
     * @param \UserDTOUtils Referencia al dto de usuario
     */
    private static function CreateNotification($data = null,
            $user = null, $userDto = null){
        // Instanciar datetime
        $date = new \DateTime( "NOW" );
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        // Crear instancia de notificación e iniciar valores
        $dto = new \Notification();
        $dto->Project = $data[ "project" ];
        $dto->Service = $data[ "service" ];
        $dto->To = $user->Username;
        $dto->Subject =  "create-user";
        $dto->Content = json_encode($userDto);
        $dto->Date = $date->format( "Y-m-d" );
        // Crear registro
        $dao->Create( $dto );
    }

    /**
     * Genera la notificación de nuevo usuario
     * @param array $data Array con la información del contexto
     * @param \User Referencia al usuario
     * @param \UserDTOUtils Referencia al dto de usuario
     */
    private static function ResetNotification($data = null,
            $user = null, $userDto = null){
        // Instanciar datetime
        $date = new \DateTime( "NOW" );
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        // Crear instancia de notificación e iniciar valores
        $dto = new \Notification();
        $dto->Project = $data[ "project" ];
        $dto->Service = $data[ "service" ];
        $dto->To = $user->Username;
        $dto->Subject =  "create-user";
        $dto->Content = json_encode($userDto);
        $dto->Date = $date->format( "Y-m-d" );
        // Crear registro
        $dao->Create( $dto );
    }

    /**
     * Carga la colección de usuarios que contienen el e-mail indicado
     * @param string $email
     * @return array
     */
    private static function GetUserByEmail($email = ""){
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        // buscar usuarios por email
        return $dao->GetByFilter( "User" , array( "Username" => $email ));
    }

    /**
     * Crea un nuevo usuario con los datos pasados como argumento
     * @param array $data data( "user" => object , "service" => "",
     * "project" => "")
     * @return boolean
     */
    public static function CreateUser($data = null){
        // Resultado por defecto
        $result = false;
        // Obtener referencia al gestor de passwords
        $factory = PasswordFactory::GetInstance();
        // Validar datos
        if(isset($data)
            && $data != null
                && is_array($data)
                    && isset($data["user"])
                        && is_object($data["user"])){
            // Obtener referencia a los datos de usuario
            $user = $data["user"];
            // Crear usuario en bbdd
            $pass = UserUtilities::Create($factory, $user);
            // Obtener dto para la notificación
            $userDto = UserUtilities::GetUserDto($user, $pass);
            // Crear notificacion
            UserUtilities::CreateNotification($data, $userDto);
            // Asignar el resultado de la operación
            $result = true;
        }
        return $result;
    }

    /**
     * Resetear la contraseña de acceso del usuario
     * @param array $data data( "email" => "" , "service" => "", "project" => "")
     * @return boolean
     */
    public static function ResetPassword($data = null){
        // Resultado por defecto
        $result = false;
        // Obtener referencia al gestor de passwords
        $factory = PasswordFactory::GetInstance();
        // buscar usuarios por email
        $emails = UserUtilities::GetUserByEmail($data["email"]);
        // Validar datos
        if(isset($emails) && $emails != null && count($emails) > 0){
            // Obtener referencia inicial
            $user = $emails[0];
            // Actualización del password de usuario
            $pass = UserUtilities::Update($factory, $user);
            // Obtener dto para la notificación
            $userDto = UserUtilities::GetUserDto($user, $pass);
            // Generar notificación
            UserUtilities::ResetNotification($data, $user, $userDto);
            // Asignar el resultado de la operación
            $result = true;
        }

        return $result;
    }

    /**
     * Modificar la contraseña acceso
     * @param array $data aray( "email" => "" , "pass" => "" , "newpass" => "")
     * @return boolean
     */
    public static function ChangePassword($data = null){
        // Resultado por defecto
        $result = false;
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        //$dao = $injector->Resolve( "IDataAccessObject" );
        // Filtro de búsqueda
        $filter = array( "Username" => $data[ "email" ],
            "Password" => $data[ "pass" ] );
        // buscar usuarios por email
        $emails = $dao->GetByFilter( "User" , $filter);
        // Validar datos
        if(isset($emails) && $emails != null && count($emails) > 0){
            // Obtener referencia inicial
            $user = $emails[0];
            // Generar password nueva
            $hash = $data[ "newpass" ];
            // asignar nueva pass
            $user->Password = $hash;
            // guardar los datos
            $dao->Update( $user );
            // Asignar el resultado de la operación
            $result = true;
        }
        return $result;
    }

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepciones específica en el envío de emails de la implementacion
 * MailNotificator
 *
 * @author alfonso
 */
class MailNotificatorException extends \BaseException{
    // Redefinir la excepción, por lo que el mensaje no es opcional
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación de la interfaz de notificaciones
 *
 * @author alfonso
 */
class MailNotificator implements \INotificator{

    /**
     * Genera la notificación con los datos proporcionados
     * @var array $data Datos para la notificación
     * [ To : "...", From : "..." , Subject : "..." , IsHtml : true|false ]
     * @var string $content Contenido de la notificación
     */
    public function Send($data, $content){

        if(!is_array($data)){
            throw new \MailNotificatorException( "data - is not array" );
        }

        if (!array_key_exists( "To" , $data)){
            throw new \MailNotificatorException( "To - is not defined" );
        }

        if (!array_key_exists( "From" , $data)){
            throw new \MailNotificatorException( "From - is not defined" );
        }

        if (!array_key_exists( "Subject" , $data)){
            throw new \MailNotificatorException( "Subject - is not defined" );
        }

        if ( $content == "" ){
            throw new \MailNotificatorException( "Content - is empty" );
        }

        $contentType = "Content-type: text/html; charset=UTF-8\r\n";
        // Construir la cabecera del mensaje
        $headers = str_replace( "{FROM}", $data["From"], "From: {FROM}\r\n " );
        // Realizar envío
        mail($data["To"], $data["Subject"], $content, $contentType.$headers);
    }

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @var string $templateName nombre de la plantilla
     */
    public function GetTemplate($templateName){
        // Obtener la ruta de la plantilla
        $path = ConfigurationManager::GetKey( $templateName );
        // Obtener el contenido de la plantilla
        $result = file_exists( $path ) ? file_get_contents( $path ) : "";
        // retornar el contenido
        return $result;
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepciones específica en el envío de emails
 *
 * @author alfonso
 */
class NotificatorException extends \BaseException{

    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Generador de notificaciones vía email
 * $data : [ To : "...", From : "..." , Subject : "..." , Info : object(...) ]
 * Formato de los parámetros en la plantilla: {paramName}
 *
 * @author alfonso
 */
class Notificator {

    /**
     * Content type de la notificación
     * @var string
     */
    public $ContentType = "Content-type: text/html; charset=UTF-8\r\n ";

    /**
     * Cabecera
     * @var string
     */
    public $Header = "From: {FROM}\r\n ";

    /**
     * Enviar notificación
     * @param array $data Array de información para la notificación
     * @param string $templateName Nombre de la plantilla a utilizar
     */
    public function Send( $data = null, $templateName = ""){
        if( $data == null || $templateName == "" ){
            return;
        }
        // Obtener el contenido del template
        $sContent = $this->GetTemplate( $templateName );
        // Validar datos
        $this->Validate( $data, $sContent );
        // Obtener info
        $object = (isset($data)) ? $data["Info"] : array();
        // Procesar el contenido de la notificación
        $content = $this->GetContent( $object, $content );
        // Enviar la notificación
        $this->SendMail($data, $content);
    }

    /**
     * Validación de todos los parámetros de la notificación
     * @var array $data Colección de parámetros
     * @var string $content Contenido de la notificación
     */
    private function Validate($data, $content){

        if(!is_array($data)){
            throw new \NotificatorException( "data - is not array" );
        }

        if (!array_key_exists( "To" , $data)){
            throw new \NotificatorException( "To - is not defined" );
        }

        if ( $data[ "To" ] == ""){
            throw new \NotificatorException( "To - is empty" );
        }

        if (!array_key_exists( "From" , $data)){
            throw new \NotificatorException( "From - is not defined" );
        }

        if ($data[ "From" ] == ""){
            throw new \NotificatorException( "From - is empty" );
        }

        if (!array_key_exists( "Subject" , $data)){
            throw new \NotificatorException( "Subject - is not defined" );
        }

        if ( $data[ "Subject" ] == "" ){
            throw new \NotificatorException( "Subject - is empty" );
        }

        if ( $content == "" ){
            throw new \NotificatorException( "Content - is empty" );
        }
    }

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @param string $templateName Nombre de la plantilla
     */
    private function GetTemplate($templateName){
        // Obtener la ruta de la plantilla
        $path = ConfigurationManager::GetKey( $templateName );
        // Obtener el contenido de la plantilla
        $result = file_exists( $path ) ? file_get_contents( $path ) : "";
        // retornar el contenido
        return $result;
    }

    /**
     * Procesado del contenido con el objeto de datos ( o array de datos)
     * @param object $object Referencia al objeto de información
     * @param string $content Contenido de la notificación
     */
    private function GetContent($object = null, $content = ""){
        // Validación de datos
        if( $object == null || $content == "" ){
            return $content;
        }
        // Convertir en array el objeto
        if(!is_array($object)){
            settype( $object, "array" );
        }
        // reemplazar todos los tags
        foreach($object as $key => $value){
            $content = str_replace( "{".$key."}", $value, $content);
        }
        return $content;
    }

    /**
     * Genera la notificación con los datos proporcionados
     * @var array $data Array con los parámetros de envío
     * [ To : "...", From : "..." , Subject : "..." , IsHtml : true|false ]
     * @var string $sContent Contenido de la notificación
     */
    private function SendMail($data, $sContent){
        // Validar los parámetros
        $this->Validate($data, $sContent);
        // Procesar contenido
        $content = $this->GetContent( $data[ "Info" ], $content);
        // Construir la cabecera del mensaje
        $headers = str_replace( "{FROM}", $data["From"],  $this->Header );
        // Realizar envío
        mail($data["To"], $data["Subject"], $content, $this->ContentType.$headers);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Excepciones específica en el envío de emails
 *
 * @author alfonso
 */
class NotificatorDBException extends \BaseException{

    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Generador de notificaciones vía email
 * $data : [ To : "...", From : "..." , Subject : "..." , Info : object(...) ]
 * Formato de los parámetros en la plantilla: {paramName}
 *
 * @author alfonso
 */
class NotificatorDB {

    /**
     * Cabecera tipo de contenido de la notificación
     * @var string
     */
    public $ContentType = "Content-type: text/html; charset=UTF-8\r\n ";

    /**
     * Cabecera dirigido "a"
     * @var string
     */
    public $Header = "From: {FROM}\r\n ";

    /**
     * Enviar notificación
     * @param array $data Parámetros de envío
     * @param string $templateName nombre de la plantilla
     * @return void
     */
    public function Send( $data = null, $templateName = ""){
        // Validación de datos
        if( $data == null || $templateName == "" ){
            return;
        }
        // Obtener el contenido del template
        $sContent = $this->GetTemplate( $templateName );
        // Obtener info
        $object = (isset($data)) ? $data["Info"] : array();
        // Procesar el contenido de la notificación
        $content = $this->GetContent( $object, $sContent );
        // Registrar el nombre de la plantilla para la notificación
        $data["template"] = $templateName;
        // Enviar la notificación
        $this->SendMail($data, $content);
    }

    /**
     * Validación de todos los parámetros de la notificación
     * @param array $data Parámetros de envío
     * @param string $content Contenido de la plantilla
     * @throws NotificatorDBException
     */
    private function Validate( $data, $content){
        if(!is_array($data)){
            throw new \NotificatorDBException( "data - is not array" );
        }

        if (!array_key_exists( "To" , $data)){
            throw new \NotificatorDBException( "To - is not defined" );
        }

        if ( $data[ "To" ] == ""){
            throw new \NotificatorDBException( "To - is empty" );
        }

        if (!array_key_exists( "From" , $data)){
            throw new \NotificatorDBException( "From - is not defined" );
        }

        if ($data[ "From" ] == ""){
            throw new \NotificatorDBException( "From - is empty" );
        }

        if (!array_key_exists( "Subject" , $data)){
            throw new \NotificatorDBException( "Subject - is not defined" );
        }

        if ( $data[ "Subject" ] == "" ){
            throw new \NotificatorDBException( "Subject - is empty" );
        }

        if ( $content == "" ){
            throw new \NotificatorDBException( "Content - is empty" );
        }
    }

    /**
     * Obtiene el contenido de la plantilla de notificación
     * @param string Nombre de la plantilla
     * @return string Plantilla
     */
    private function GetTemplate($templateName){
        // Obtener la ruta de la plantilla
        $path = ConfigurationManager::GetKey( $templateName );
        // Obtener el contenido de la plantilla
        $result = file_exists( $path ) ? file_get_contents( $path ) : "";
        // retornar el contenido
        return $result;
    }

    /**
     * Procesado del contenido con el objeto de datos ( o array de datos)
     * @param object $object Objeto a procesar
     * @param string $content Contenido de la notificación
     * @return string Contenido procesado
     */
    private function GetContent($object = null, $content = ""){
        // Validación de datos
        if( $object == null || $content == "" ){
            return $content;
        }
        // Convertir en array el objeto
        if(!is_array($object)){
            settype( $object, "array" );
        }
        // reemplazar todos los tags
        foreach($object as $key => $value){
            $content = str_replace( "{".$key."}", $value, $content);
        }

        return $content;
    }

    /**
     * Genera la notificación con los datos proporcionados
     * @param array $data Parámetros de la notificación. $data :
     * [ To : "...", From : "..." , Subject : "..." , IsHtml : true|false ]
     * @param string $sContent Contenido de la notificación
     */
    private function SendMail($data, $sContent){
        // Procesar contenido
        $content = $this->GetContent( $data[ "Info" ], $sContent);
        // Validar los parámetros
        $this->Validate($data, $content);
        // Construir la cabecera del mensaje
        $headers = str_replace( "{FROM}", $data["From"],  $this->Header );
        // Instanciar dto de la notificación
        $dto = new Notification();
        $dto->Project = $data["project"];
        $dto->Service = $data["service"];
        $dto->To = $data["To"];
        $dto->From = $data["From"];
        $dto->Subject = $data["Subject"];
        $dto->Header = $this->ContentType.$headers;
        $dto->Content = $content;
        $dto->Template = $data["template"];
        $date = new DateTime( "NOW" );
        $dto->Date = $date->format( "y-m-d" );
        // Obtener referencia al DAO
        $dao = Injector::GetInstance();
        // Crear registro
        $dao->Create( $dto );
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
    Dependencias :
    - Clase Injector para la inyección de componentes
    - Componentes definidos : [ ILogManager ]
    - Clase ConfigurationManager para el acceso al config.xml
    - Claves de config.xm : [ path ]
*/

/**
 * Clase base para los controladores
 *
 * @author alfonso
 */
class Controller{

    /**
     * Array de métodos privados para filtrar al obtener el ActionName
     * @var array
     */
    protected $_PrivateMethods = array( "PartialView", "GetActionName" );

    /**
     * Expresión patrón para la búsqueda de subcadenas
     * @var string $Pattern Patron para buscar reemplazos en la vista
     */
    protected $Pattern = "<!--NAME-->";

    /**
     * Nombre de la clase
     * @var string $ClassName Nombre del controlador
     */
    protected $ClassName = "Controller";

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager Gestor de trazas
     */
    protected $Log = null;

    /**
     * Referencia al gestor de inyecciones
     * @var Injector Referencia al gestor de dependencias
     */
    protected $Injector = null;

    /**
     * Constructor de la clase base
     */
    public function __construct(){
        // Obtener referencia al objeto inyector de dependencias
        $this->Injector = Injector::GetInstance();
        // Obtener referencia al gestor de trazas
        $this->Log = $this->Injector->Resolve( "ILogManager" );
        // Asignar nombre actual de la clase
        $this->ClassName = get_class($this);
    }

    /**
     * Buscar la subcadena patrón
     * @param string $sName Nombre de la propiedad que se desea buscar
     * @param string $content Contenido por el que se reemplaza
     * @return string
     */
    private function FindPattern($sName = "", $content = ""){
        $result = "";
        // Obtener la expresión a buscar
        $name = str_replace("NAME", $sName, $this->Pattern);
        // Buscamos la primera aparición de la subcadena $name en $content
        $start = strpos( $content , $name );
        // Comprobar Si se ha encontrado la posición inicial
        if($start === FALSE){
            return $result;
        }
        // Buscamos si hay una segunda aparición
        $end = strpos( $content , $name , ($start + 1));
        // Comprobar Si se ha encontrado la posición final
        if($end === FALSE){
            return $result;
        }
        // Extraer la subcadena del patrón
        return substr( $content , $start , ($end - $start));
    }

    /**
     * Obtiene el nombre de la acción que se está ejecutando
     */
    private function GetActionName(){
        // Obtener stacktrace
        $trace = debug_backtrace();
        // Buscar el nombre de la función actual
        foreach($trace as $method){
            // Obtener el nombre de la acción actual
            $function = $method["function"];
            // Obtener el nombre de la clase actual
            $class = $method["class"] == $this->ClassName;
            // Evaluar si la acción | método en la pila es una acción
            //  o un método privado
            $action = !in_array($function, $this->_PrivateMethods);
            // Validación de los datos
            if($class && $action) {
                return $function;
            }
        }
        return "";
    }

    /**
     * Elimina los tags de reemplazos de la plantilla
     * @param string $propertyName Nombre de la propiedad
     * @param string $item Patrón
     * @return string contenido procesado
     */
    private function ClearPatternSubrArray($propertyName="", $item=""){
        $pattern = $this->FindPattern($propertyName, $item);
        $sItem = str_replace($pattern, "", $item);
        return str_replace("<!--$propertyName-->", "", $sItem);
    }

    /**
     * Reemplazo de un array contenido en otro
     * @param string $view Patrón de reemplazo
     * @param string $name Nombre de la propiedad
     * @param object $array Referencia a los datos de reemplazo
     * @return string Texto reemplazado
     */
    private function ReplaceSubArray($view="", $name="", $array = null){
        if(is_object($array)){
            settype($array, "array");
        }
        $temp = ""; $sView = "";
        // Obtener subpatron
        $pattern = $this->FindPattern($name, $view);

        foreach($array as $items){
            $temp = $pattern;

            if(!is_array($items)){
                settype($items, "array");
            }

            foreach($items as $key => $value){
                $val = str_replace("{item.$name.$key}",$value, $temp);
                $temp = ($val != $pattern ) ? $val : "";
            }
            $sView .= $temp;
        }

        // Obtener la expresión a buscar
        $tag = str_replace("NAME", $name, $this->Pattern);

        return str_replace($tag, "", $sView);
    }

    /**
     * Genera el remplazo sobre un patrón
     * @param string $item Patrón de reemplazo
     * @param string $propertyName Nombre de la propiedad
     * @param object $propertyValue Referencia a la info de reemplazo
     * @return string Contenido reemplazado
     */
    private function ReplaceItem($item="", $propertyName="",
            $propertyValue = NULL){
        if(is_array($propertyValue)|| is_object($propertyValue)){
            $item .= $this->ReplaceSubArray($item,
                    $propertyName, $propertyValue );
            $item = $this->ClearPatternSubrArray(
                    $propertyName, $item);
        }
        else{
            $item = str_replace("{item.$propertyName}",
                    $propertyValue, $item);
        }
        return $item;
    }

    /**
     * Genera el remplazo de elementos contenidos en un tipo array
     * @param string $sView Nombre de la vista
     * @param string $name Nombre de la propiedad
     * @param array $array de Items a reemplazar
     * @return string Vista renderizada
     */
    protected function ReplaceArray($sView="", $name="", $array=NULL){
        // Buscar el patrón
        $match = $this->FindPattern($name, $sView);
        // Contenido a reemplazar por el patrón
        $toReplace = "";
        // Generación de la vista a reemplazar
        foreach($array as $object){
            settype($object, "array");
            $item = $match;
            foreach($object as $propertyName => $propertyValue){
                $item = $this->ReplaceItem($item,
                        $propertyName, $propertyValue);
            }
            $toReplace .= $item;
        }
        // Reemplazar contenido en la vista
        $view = str_replace($match, $toReplace, $sView);
        // Reemplazar tags de expresión regular
        return str_replace("<!--$name-->", "", $view);
    }

    /**
     * Genera el remplazo de elementos contenidos en un objeto
     * @param string $view Nombre de la vista
     * @param string $name Nombre de la propiedad
     * @param object $object Referencia al objeto a reemplazar
     * @return string Vista renderizada
     */
    protected function ReplaceObject($view="",
            $name="", $object=null){
        // Convertir el objeto a array
        if(!is_array($object)){
            settype($object, "array");
        }
        // Recorrer las propiedades del objeto para ser reemplazadas
        foreach($object as $key => $value){
            // Si la propiedad es array u objeto, pasamos a la siguiente,
            // se ingnora
            if(is_array($value) || is_object($value)){
                continue;
            }
            // Reemplazar en el contenido a generar la etiqueta
            // correspondiente por el valor de la propiedad
            $view = str_replace("{".$name.".".$key."}", $value, $view);
        }
        // Retornar la vista generada
        return $view;
    }

    /**
     * Obtiene la vista parcial más el layout si necesita
     * @var string $filepath Ruta a la vista
     * @return string Vista renderizada
     */
    protected function GetViewContent($filepath = ""){
        // Obtiene el contenido del fichero de vista
        $fileContent = file_get_contents($filepath);
        // Obtener layout si es necesario
        $start = strpos($fileContent, "<!--Layout={");
        $last = strpos( $fileContent, "}-->");
        if($start !== false && $last !== false){
            $start = $start + 12;
            $length = $last - $start;
            $layout = substr($fileContent, $start, $length);
            if($layout != ""){
                $layout = "view/shared/".$layout;
                $fileContent = str_replace("{BODY}", $fileContent,
                        file_get_contents($layout));
            }
        }
        return $fileContent;
    }

    /**
     * Genera la vista sin objeto modelo
     * @var string $view Nombre de la vista
     * @var \Model $model Referencia al modelo de datos
     * @return string Vista renderizada
     */
    protected function ProcessView($view="", $model=null){
        // Convertir el objeto modelo a arrya
        if(is_object($model)){
            settype($model, "array");
        }
        // Recorrer las propiedades del modelo para generar los reemplazos
        foreach($model as $propertyName => $propertyValue){
            if(is_array($propertyValue)){
                $view = $this->ReplaceArray($view,
                        $propertyName, $propertyValue);
            }
            else if(is_object($propertyValue)){
                $view = $this->ReplaceObject($view,
                        $propertyName, $propertyValue);
            }
            else{
                $view = str_replace("{".$propertyName."}",
                        $propertyValue, $view);
            }
        }
        // Retornar la vista
        return $view;
    }

    /**
     * Procesa el contenido de la vista sin objeto modelo
     * @var string $filepath Ruta física a la vista
     * @return string Vista renderizada
     * @throws ResourceNotFount
     */
    protected function Render($filepath = ""){
        if($filepath == "" || !file_exists ($filepath)){
            throw new ResourceNotFoundException("file name :".$filepath);
        }
        return $this->GetViewContent($filepath);
    }

    /**
     * Procesa el contenido de la vista con objeto modelo
     * @var string $filepath Ruta física a la vista
     * @var \Model $model Referencia al modelo a renderizar
     * @return string Vista renderizada
     * @throws ResourceNotFount
     */
    protected function RenderView($filepath="", $model=null){
        if($filepath == "" || !file_exists ($filepath)){
            throw new ResourceNotFoundException("file name :".$filepath);
        }

        $view = $this->GetViewContent($filepath);

        return $this->ProcessView($view, $model);
    }

    /**
     * Procesar la vista con o sin modelo
     * @var \Model $model Referencia al modelo a renderizar
     * @return string Vista renderizada
     */
    public function PartialView($model = null){
        // Obtener el nombre de la acción actual
        $actionName = $this->GetActionName().".html";
        // Obtener el nombre del controlador actual
        $className = str_replace("Controller", "", get_class($this));
        // Construir el path para la vista
        $filePath = "view/".$className."/".$actionName;
        // Validar la referencia al modelo
        return ($model == null)
            ? $this->Render($filePath)
            : $this->RenderView($filePath, $model);
    }

    /**
     * Procesar la vista parametrizando el nombre de la vista
     * @var string $viewName Nombre de la vista
     * @var \Model $model Referencia al modelo a renderizar
     * @return string Vista renderizada
     */
    public function Partial($viewName = "", $model = null){
        // Construir el nombre de la vista
        $actionName = $viewName.".html";
        // Obtener el nombre del controlador
        $className = str_replace("Controller", "", get_class($this));
        // Construir el path de acceso a la vista
        $filePath = "view/".$className."/".$actionName;
        // Validar la referencia al modelo
        return ($model == null)
            ? $this->Render($filePath)
            : $this->RenderView($filePath, $model);
    }

    /**
     * Completa la referencia a la entidad con los parámetros de la
     * solicitud http
     * @param object $entity Referencia a la entidad
     * @param array $array Mapeado de campos
     * @return object Entidad completada
     */
    private function ReadEntityFromRequest($entity = null,
            $array = null){
        if($entity != null && $array != null){
            // los valores en los parámetros de la llamada
            foreach( $array as $key => $value){
                if(isset($_REQUEST[$key])){

                    $item = $_REQUEST[$key];
                    // Eliminamos posibles tags html y php.
                    $value = strip_tags($item);
                    // Asignar parámetro
                    $entity->{ $key } = $value;
                }
            }
        }
        return $entity;
    }

    /**
     * Obtener una entidad con los parámetros de la petición http
     * @param string $entityName Nombre de la entidad
     * @return object Entidad obtenida
     */
    public function GetEntity($entityName = ""){
        // Validar el nombre de la entidad
        if($entityName == "" ){
            return null;
        }
        // Instanciar objeto temporal para la lectura
        $temp = new $entityName();
        // Instanciar objeto a devolver
        $entity = new $entityName();
        // Convertir el temporar el array para
        // recorrer sus propiedades
        settype( $temp , "array" );
        // Completar los datos de entidad
        return $this->ReadEntityFromRequest($entity, $temp);
    }

    /**
     * Procesar la redirección de la llamada
     * @param string $action Acción solicitada
     * @param string $controller Controlador a ejecutar
     * @param array $args Argumentos de la llamada
     * @return string
     */
    public function RedirectTo($action = "",
            $controller = "", $args = null){
        // Obtener el path de ejecución
        $path = ConfigurationManager::GetKey( "path" );
        // Construir la url
        $url = $path."/".$controller."/".$action;
        $params = "";
        if(is_array($args)){
            foreach($args as $key => $value){
                $params .= "&".$key."=".$value;
            }
            if(count($args) > 0){
                $params = substr($params, 1);
            }
        }
        // Url
        $url = (strlen($params) > 0) ? $url."?".$params : $url;
        // Contenido a renderizar
        return "<script type='text/javascript'>"
            . "window.location=\"".$url."\"</script>";
    }

    /**
     * Configura la peticion http actual y serializa el objeto para
     * generar un response de tipo json
     * @param object $obj Referencia al objeto a serializar
     * @return string
     */
    public function ReturnJSON($obj = NULL){
        $returnValue = "[]";
        header('Content-Type: application/json');
        if($obj != NULL){
            $returnValue = json_encode($obj);
        }
        return $returnValue;
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
    Dependencias :
    - Clase Controller (MVC) y sus dependencias.
    - Componentes definidos : [ ISecurity ]
*/

/**
 * Clase base para los controladores de aplicaciones saas
 *
 * @author alfonso
 */
class SaasController extends \Controller{

    /**
     * Referencia al gestor de seguridad
     * @var \ISecurity Referencia al gestor de seguridad
     */
    protected $Security = null;

    /**
     * Id del Proyecto en ejecución
     * @var int Identidad del proyecto en ejecución
     */
    public $Project = 0;

    /**
     * Nombre del proyecto actual
     * @var string Nombre del proyecto actual
     */
    public $ProjectName = "";

    /**
     * Path del proyecto actual
     * @var string Ruta del proyecto actual
     */
    public $ProjectPath = "";

    /**
     * Referencia al servicio actual
     * @var int Identidad del servicio en ejecución
     */
    public $Service = 0;

    /**
     * Constructor de la clase base
     */
    public function __construct(){
        // Llamada al constructor padre
        parent::__construct();
        // Obtener referencia al gestor de seguridad
        $this->Security = $this->Injector->Resolve( "ISecurity" );
        // Establecer parámetros del contexto
        $this->SetContext();
    }

    /**
     * Establece las propiedades del controlador que dependen
     * del contexto (proyecto y servicio)
     */
    protected function SetContext(){
        // Cargar la identidad del proyecto actual
        $this->Project = (isset($_SESSION["projectId"]))
                ? $_SESSION["projectId"] : 0;
        // Cargar el nombre del proyecto actual
        $this->ProjectName = (isset($_SESSION["projectName"]))
                ? $_SESSION["projectName"] : "";
        // Cargar la ruta del proyecto actual
        $this->ProjectPath = (isset($_SESSION["projectPath"]))
                ? $_SESSION["projectPath"] : "";
        // Establecer el id de servicio
        $this->Service = (isset($_SESSION["serviceId"]))
                ? $_SESSION["serviceId"] : 0;
    }

    /**
     * Proceso para el registro de errores
     * @param string $method Método que genera el error
     * @param \Exception $e Referencia a la excepción actual
     */
    protected function LogErrorTrace($method = "", $e = null){

        $error = (isset($e) && $e != null) ? $e->getMessage() : "";

        $msg = "Method: ".$method." - Info: ".$error;

        $this->LogError($msg);
    }

}


/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
Dependencias :
- Clase Injector para la inyección de componentes
- Componentes definidos : [ IDataAccessObject ]
- Clase ConfigurationManager para el acceso al config.xml
- Claves de config.xml : [ path, resources, connectionString ]
*/

/**
 * Clase base para los model
 */
class Model{

    /**
     * Referencia al inyector de dependencias
     * @var \Injector $Injector Gestor de inyección de dependencias
     */
    protected $Injector = null;

    /**
     * Referencia al objeto de acceso a datos
     * @var \IDataAccessObject Referencia al objeto de acceso a datos
     */
    protected $Dao = null;

    /**
     * Ruta base para enlaces
     * @var string $Path Ruta base para la navegación
     */
    public $Path = "";

    /**
     * Ruta base para recursos locales
     * @var string $Resources Ruta base par alos recursos
     */
    public $Resources = "";

    /**
     * Título de la página a renderizar
     * @var string $Title Cabecera del formulario
     */
    public $Title = "";

    /**
     * Array de opciones menú
     * @var array $Menu Colección de items para el menú de navegación
     */
    public $Menu = array();

    /**
     * Array de errores
     * @var array $ErrorList Colección de errores detectados
     */
    public $ErrorList = array();

    /**
     * Constructor
     */
    public function __construct(){
        // Obtener path
        $this->Path = ConfigurationManager
                ::GetKey( "path" );
        // Obtener ruta de recursos
        $this->Resources = ConfigurationManager
                ::GetKey( "resources" );
        // Obtener nombre de la cadena de conexión
        $connectionString = ConfigurationManager
                ::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString = ConfigurationManager
                ::GetConnectionStr($connectionString);
        // Cargar las referencias
        $this->Injector = Injector::GetInstance();
        // Cargar el objeto de acceso a datos
        $this->Dao = $this->Injector->Resolve( "IDataAccessObject" );
        // Configurar el objeto de conexión a datos
        $this->Dao->Configure($oConnString);
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*
    Dependencias :
    - Clase Model (MVC) y sus dependencias.
    - Componentes definidos : [ ISecurity ]
*/

/**
 * Clase base para los modelos de aplicaciones saas
 *
 * @author alfonso
 */
class SaasModel extends \Model{

    /**
     * Referencia al gestor de seguridad
     * @var \ISecurity Referencia al gestor de seguridad
     */
    protected $Security = null;

    /**
     * Id del Proyecto en ejecución
     * @var int Identidad del proyecto
     */
    public $Project = 0;

    /**
     * Nombre del proyecto actual
     * @var string Nombre del proyecto
     */
    public $ProjectName = "";

    /**
     * Path del proyecto actual
     * @var string Ruta del proyecto
     */
    public $ProjectPath = "";

    /**
     * Referencia al servicio actual
     * @var int Identidad del servicio
     */
    public $Service = 0;

    /**
     * Nombre del usuario en ejecución
     * @var string Nombre de usuario
     */
    public $Username = "";

    /**
     * Texto del mensaje de error en el nombre de usuario
     * @var string Mensaje de error en la autenticación del usuario
     */
    public $eUsername = "";

    /**
     * Estilo CSS a utilizar en la etiqueta de nombre de usuario
     * @var string Estilo CSS a utilizar para el error de nombre de usuario
     */
    public $eUsernameClass = "";

    /**
     * Texto del mensaje de error en el parámetro contraseña
     * @var string Mensaje de error en la autenticación de la contraseña
     */
    public $ePassword = "";

    /**
     * Estilo CSS a utilizar en la etiqueta de password
     * @var string Estilo CSS a utilizar para el error de contraseña
     */
    public $ePasswordClass = "";

    /**
     * Texto del mensaje general de error en el formulario de login
     * @var string Mensaje de error general en el formulario de login
     */
    public $eLogin = "";

    /**
     * Estilo CSS a utilizar en la etiqueta general del formulario
     * @var string Estilo CSS a utilizar para el error general
     */
    public $eLoginClass = "has-success";

    /**
     * Constructor de la clase
     */
    public function __construct(){
        // Constructor de la clase base
        parent::__construct();
        // Cargar el gestor de seguridad
        $this->Security = $this->Injector->Resolve( "ISecurity" );
        // Cargar el array menú
        $this->Menu = $this->Security->GetControllersByRol(
                $this->Security->GetUserRoles());
        // Cargar nombre de usuario activo si lo hay
        $this->Username = $this->Security->GetUserName();
        // Establecer los parámetros del contexto
        $this->SetDataContext();

        $this->SetLoginError();
    }

    /**
     * Configuración de los datos de contexto
     */
    private function SetDataContext(){
        // Configurar Identidad del proyecto
        $this->Project = (isset($_SESSION["projectId"]))
                ? $_SESSION["projectId"] : 0;
        // Configurar el nombre de proyecto
        $this->ProjectName = (isset($_SESSION["projectName"]))
                ? $_SESSION["projectName"] : "";
        // Configurar la ruta de proyecto
        $this->ProjectPath = (isset($_SESSION["projectPath"]))
                ? $_SESSION["projectPath"] : "";
        // Establecer la identidad del servicio
        $this->Service = (isset($_SESSION["serviceId"]))
                ? $_SESSION["serviceId"] : 0;
    }

    /**
     * Configurar errores de login
     */
    private function SetLoginError(){

        if(isset($_SESSION["eUsername"])){
            $this->eUsername = $_SESSION["eUsername"];
            $this->eUsernameClass = "has-error";
            unset($_SESSION["eUsername"]);
        }

        if(isset($_SESSION["ePassword"])){
            $this->ePassword = $_SESSION["ePassword"];
            $this->ePasswordClass = "has-error";
            unset($_SESSION["ePassword"]);
        }

        if(isset($_SESSION["eLogin"])){
            $this->eLogin = $_SESSION["eLogin"];
            $this->eLoginClass = "has-error";
            unset($_SESSION["eLogin"]);
        }
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Implementación de la interfáz de seguridad basado en un repositorio
 *
 * @author alfonso
 */
class Security implements \ISecurity{

    /**
     * Array con los datos de los controladores asociados
     * @var array $Controller Array de controladores
     */
    protected $Controllers = NULL;

    /**
     * Obtiene la información de un nodo action en un array
     * @param object $action Referencia al XmlNode con la info
     * @return mixed Array con la información de la acción o FALSE
     */
    private function ReadChildren($action = NULL){
        if($action != NULL){
            $attr = $action->attributes();
            $visible = (isset($attr->visible)
                && (strtolower((string)$attr->visible) == "true" ));
            return [
                "name" => (string)$attr->name,
                "action"=> (string)$attr->action,
                "roles" => (string)$attr->roles,
                "param" => (string)$attr->param,
                "controller" => (string)$attr->controller,
                "title" =>  (string)$attr->title,
                "text" =>  (string)$attr->text,
                "visible" =>  $visible
                    ];
        }
        return FALSE;
    }

    /**
     * Obtiene el array de acciones de segundo nivel
     * @param array $actions Array de acciones
     * @return array Array de acciones actualizado
     */
    private function GetChildrens($actions = NULL){
        $acciones = [];
        if($actions != NULL && is_object($actions)){
            foreach($actions as $action){
                $item = $this->ReadChildren($action);
                if($item != FALSE && $item["visible"] === TRUE){
                    $acciones[$item["name"]] = $item;
                }
            }
        }
        return $acciones;
    }

    /**
     * Obtiene el array de acciones a partir de la colección de nodos XML
     * @param object $actions Referencia a los nodos con las acciones
     * @return array Colección de arrays con la infor de las acciones
     */
    private function GetActions($actions = NULL){
        $acciones = [];
        if($actions != NULL && is_object($actions)){
            foreach($actions as $action){
                $attr = $action->attributes();
                $acciones[(string)$attr->name] = (string)$attr->roles;
            }
        }
        return $acciones;
    }

    /**
     * Procesar la información de cada controlador configurado
     * @param type $node
     */
    private function ProcessControllerEntry($node = NULL){
        $actions = $node->actions->children();
        $acciones = $this->GetActions($actions);
        $childrens = $this->GetChildrens($actions);
        $attributes = $node->attributes();
        $name = (string)$attributes->name;

        $visible = (isset($attributes->visible)
                && (strtolower((string)$attributes->visible) == "true" ));
        $this->Controllers[$name] =
                [
                    "name" => $name,
                    "actions" => $acciones,
                    "childrens" => $childrens,
                    "action" => (string)$attributes->action,
                    "roles" => (string)$attributes->roles,
                    "title" => (string)$attributes->title,
                    "text" => (string)$attributes->text,
                    "visible" => $visible
                ];
    }

    /**
     * Cargar los datos de controladores del fichero de configuración
     * @var object $xml Nodo xml de configuración
     */
    protected function LoadControllers($xml){
        // Obtener la lista de Controladores
        $nodes = $xml->controllers->children();
        // Array de connectionString
        $this->Controllers = [];
        // Almacenar cada uno de los controladores
        foreach($nodes as $node){
            $this->ProcessControllerEntry($node);
        }
    }

    /**
     * Se encarga de realizar el proceso de autenticación del usuario
     * que accede a la aplicación mediante un ticket de acceso. En caso
     * de ser validado el ticket, se debe establecer el usuario como
     * autenticado en el contexto. Devuelve el resultado de la
     * autenticación como un valor booleano.
     * @var string $ticket ticket de autenticación de usuario
     * @return boolean Estado de la validación
     */
    public function AuthenticateTicket($ticket){
        throw new \NotImplementedException( "AuthenticateTicket" );
    }

    /**
     * Valida los datos de usuario contra el repositorio actual
     * (xml, bbdd, webservice..) y establece en la sesión los datos
     * de usuario [ $_SESSION["user"], $_SESSION["userid"] ]
     * @var string $username Nombre de usuario
     * @var string $password Contraseña de usuario
     * @return boolean Estado de la validación
     */
    protected function ValidateUser($username, $password){
        throw new \NotImplementedException( "ValidateUser" );
    }

    /**
     * Establece los parámetros de contexto cuando no se verifica
     * el proceso de autenticación
     */
    protected function SetAuthenticateFail(){
        $count = (isset($_SESSION["auth_count"]))
                ? intval($_SESSION["auth_count"]) : 0;
        $count++;
        $_SESSION["auth_count"] = $count;
    }

    /**
     * Comprobación si el usuario ya está autenticado
     * @return boolean Estado de la autenticación
     */
    protected function IsAuthenticate(){
        // Comprobamos si ya hay una sessión activa
        return (isset($_SESSION["user"]) && ($_SESSION["user"] != ""));
    }

    /**
     * Comprobación si el acceso está bloqueado por exceso de
     * intentos de autenticación
     * @return boolean Estado del bloqueo
     */
    protected function IsBlocked(){
        // Comprobamos si se ha definido el contador de intentos
        if(isset($_SESSION["auth_count"])){
            // Obtenemos el valor del contador de bloqueos
            $count = intval($_SESSION["auth_count"]);
            // validar si se se han superado el máximo de intentos
            return $count >= 5;
        }
        return FALSE;
    }

    /**
     * Constructor
     */
    public function __construct(){
        // Ruta de acceso al fichero de configuración
        $xmlstr = ConfigurationManager::GetKey( "controllers" );
        // Cargamos el contenido de la configuración desde el xml
        $configurator = simplexml_load_file($xmlstr);
        // Cargar los datos de configuración
        $this->LoadControllers($configurator);
    }

    /**
     * Se encarga de realizar el proceso de autenticación del usuario
     * a partir del nombre de usuario y el password utilizado.
     * En el caso de ser válidas las credenciales, se debe establecer
     * el usuario como autenticado en el contexto.
     * Devuelve el resultado de la autenticación como un valor booleano.
     * @var string $username Nombre de usuario
     * @var string $password Contraseña de usuario
     * @return boolean Estado de la validación
     */
    public function Authenticate($username, $password){
        // Validar que no está bloqueado el acceso
        if($this->IsBlocked()){
            return FALSE;
        }
        // Validación de la sesión actual
        if($this->IsAuthenticate()){
            return TRUE;
        }
        // Validar la información de usuario
        $auth = $this->ValidateUser($username, $password);
        // Comprobar el proceso de validación
        if(!$auth){
            $this->SetAuthenticateFail();
        }
        // retornar el resultado de la validación
        return $auth;
    }

    /**
     * Comprueba si la acción a ejecutar requiere que el usuario
     * esté autenticado o no
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la acción
     * @return boolean Indica si requiere autenticación
     */
    public function RequiredAuthentication($controller, $action){
        $required = FALSE;
        if(array_key_exists ($controller, $this->Controllers)){
            $item = $this->Controllers[$controller];
            if(array_key_exists($action, $item["actions"])){
                $required = ($item["actions"][$action] != "");
            }
        }
        return $required;
    }

    /**
     * Obtener array de roles
     * @param string $strRoles Lista de roles separados por ','
     * @return array Colección de roles
     */
    private function GetArrayRoles($strRoles = ""){
        $roles = [];
        if($strRoles != ""){
            // Obtener vector de roles
            $roles = explode(",", $strRoles);
            // Liminar caracter espacio al principio y fin de cada role
            foreach($roles as $key => $role){
                $roles[$key] = trim($role);
            }
        }
        return $roles;
    }

    /**
     * Comprueba si el usuario tiene alguno de los roles
     * establecidos para la ejecución de la acción
     * @param array $roles Colección de roles de la acción
     * @return boolean Estado de la validación
     */
    private function ValidateUserRole($roles = NULL){
        // Error en los parámetros
        if(!is_array($roles)){
            return FALSE;
        }
        // No require roles
        if(count($roles) == 0){
            return TRUE;
        }

        // Obtener los roles del usuario
        $userRoles = $this->GetUserRoles();

        if(!is_array($userRoles)){
            return FALSE;
        }

        $authorize = FALSE;
        // Comprobar los roles del usuario
        foreach($userRoles as $rol){
            if(in_array($rol, $roles)){
                $authorize = TRUE;
            }
        }

        return $authorize;
    }

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para ejecutar la acción del controlador.
     * Los criterios que determinan si el usuario debe ser autorizado
     * dependen de la aplicación donde el componente se integra.
     * Devuelve el resultado de la autorización como un valor booleano.
     * @var string $controller Nombre del controlador
     * @var string $action Nombre de la acción
     * @return boolean Estado de la autorización
     */
    public function Authorize($controller, $action){
        // Validar la existencia del control solicitado
        if(array_key_exists($controller, $this->Controllers)){

            $item = $this->Controllers[$controller];

            if(!array_key_exists($action, $item["actions"])){
                throw new \ResourceNotFoundException("Action not found: ".$action);
            }

            $sRoles = $item["actions"][$action];

            $roles = $this->GetArrayRoles($sRoles);

            return $this->ValidateUserRole($roles);
        }
        return TRUE;
    }

    /**
     * Procesa las subcadenas de los roles
     * @param object $roles Colección de roles
     * @return object c
     */
    private function ProcessRoles($roles){
        // Validar el parámetro pasado
        if(!is_array($roles)){
            $roles = explode(",", $roles);
        }
        // Eliminar espacios en blanco de los nombres de role
        foreach($roles as $key => $role){
            $roles[$key] = trim($role);
        }
        return $roles;
    }

    /**
     * Filtro de acciones por roles de usuario
     * @param array $actions Colección de acciones disponibles
     * @param array $roles Colección de roles de usuario
     * @return array Colección de arrays accesibles
     */
    private function FilterActionsByRole($actions = NULL, $roles = NULL){

        $childrens = [];

        if($roles == NULL || $actions == NULL){
            return $childrens;
        }
        foreach($actions as $item){
            if($item["roles"] == ""
                    && $item["visible"] == TRUE){
                $childrens[$item["name"]] = $item;
                continue;
            }
            $sRoles = explode(",", $item["roles"]);
            foreach($sRoles as $role){
                if(in_array(trim($role), $roles)
                        && $item["visible"] == TRUE){
                    $childrens[$item["name"]] = $item;
                }
            }
        }
        return $childrens;
    }

    /**
     * Validación del controlador por roles de usuario
     * @param object $item Referencia a la información del controlador
     * @param array $roles Colección de roles disponibles
     * @return mixed Referencia al controlador filtrado o FALSE
     */
    private function FilterControllerByRole($item = NULL, $roles = NULL){
        if($item != NULL){
            $childrens = $this->FilterActionsByRole($item["childrens"], $roles);
            settype($childrens, "array");
            $item["childrens"] = json_encode($childrens);

            if($item["roles"] == "" && $item["visible"] == TRUE){
                return $item;
            }

            $sRoles = $this->ProcessRoles($item["roles"]);

            foreach($sRoles as $role){
                if(in_array($role, $roles) && $item["visible"] == TRUE){
                    return $item;
                }
            }
        }
        return FALSE;
    }

    /**
     * Obtiene el array de controladores disponibles para el conjunto
     * de roles pasados como parámetros.
     * @var array $sRoles Colección de controladores
     */
    public function GetControllersByRol($sRoles){
        // Definir array de controladores disponibles
        $controllers = [];
        // Procesar roles
        $roles = $this->ProcessRoles($sRoles);
        // Filtrar la información de los controladores según el role
        foreach($this->Controllers as $value){
            if(($controller =
                    $this->FilterControllerByRole($value, $roles)) == TRUE){
                $controllers[] = $controller;
            }
        }
        return $controllers;
    }

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para acceder al controlador.
     * Los criterios que determinan si el usuario debe ser autorizado
     * dependen de la aplicación donde deba integrarse.
     * Devuelve el resultado de la autorización como un valor booleano.
     */
    public function AuthorizeController($controller){
        throw new \NotImplementedException( "AuthorizeController" );
    }

    /**
     * Obtiene el nombre del usuario autenticado en el contexto. En caso
     * de no haber usuario autenticado, el método devolverá una
     * cadena vacía.
     */
    public function GetUserName(){
        return (isset($_SESSION["user"])) ? $_SESSION["user"] : "";
    }

    /**
     * Obtiene un array con el/los roles asociados al usuario autenticado
     * en el contexto. En caso de no estar autenticado el usuario, debe
     * retornar un array vacío.
     */
    public function GetUserRoles(){
        throw new \NotImplementedException( "GetUserRoles" );
    }

    /**
     * Obtiene un objeto con la información del usuario almacenada
     * en el contexto. En caso de no estar el usuario autenticado,
     * se retornará el valor NULL.
     */
    public function GetUserData(){
        throw new \NotImplementedException( "GetUserData" );
    }

    /**
     * Obtiene un ticket de autenticación a partir de la información del
     * usuario autenticado. En caso de no estar el usuario autenticado,
     * se retornará una cadena vacía.
     */
    public function GetTicket(){
        throw new \NotImplementedException( "GetTicket" );
    }

    /**
     * Obtiene el nombre de la vista a utilizar para la acción, el
     * controlador y el usuario autenticado.
     * En el caso de no ser necesario (no hay filtro de contenidos),
     * retornará el nombre de la vista por defecto (mismo nombre
     * que la acción).
     */
    public function GetViewName($controller, $action){
        throw new \NotImplementedException( "GetViewName" );
    }
}

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


/*
    Dependencias :
    - Clase base Security y todas sus dependencias
    - Interfaz ISecurity
    - Componentes definidos : [ IDataAccessObject ]
    - Claves de config.xml : [ connectionString, urlReferer ]
    - Entidad de base de datos : Service, Project, AuthEntity
    - Variables de sessión (Opcional) : projectId, serviceId
*/

/**
 * Implementación de la interfáz de seguridad
 */
class SaasSecurity extends \Security implements \ISecurity{

    /**
     * Referencia al Objeto de acceso a datos
     * @var \IDataAccessObject
     */
    protected $Dao = NULL;

    /**
     * ID del servicio en ejecución
     * @var int
     */
    protected $Service = 0;

    /**
     * ID del proyecto actual
     * @var int
     */
    protected $Project = 0;

    /**
     * Obtiene el ID de servicio del contexto
     */
    protected function GetServiceID(){
        return (isset($_SESSION["serviceId"]))
            ? $_SESSION["serviceId"] : 0;
    }

    /**
     * Obtiene el ID de proyecto del contexto
     */
    protected function GetProjectID(){
        return (isset($_SESSION["projectId"]))
            ? $_SESSION["projectId"] : 0;
    }

    /**
     * Constructor por defecto
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
        // Asignar ID del servicio actual
        $this->Service = $this->GetServiceID();
        // Asignar el ID del proyecto (si hay)
        $this->Project = $this->GetProjectID();
        // Obtener una referencia al Inyector de dependencias
        $Ioc = Injector::GetInstance();
        // Obtener el nombre de la cadena de conexión a utilizar
        $strConn = ConfigurationManager::GetKey( "connectionString" );
        // Obtener datos de conexión a bbdd
        $oConnData = ConfigurationManager::GetConnectionStr( $strConn );
        // Inyectar referencia
        $this->Dao = $Ioc->Resolve( "IDataAccessObject" );
        // Configurar DAO
        $this->Dao->Configure($oConnData);
    }

    /**
     * Establece los errores básicos en el proceso de autenticación
     * @param string Nombre de usuario
     * @param string Contraseña de usuario
     */
    private function SetError($username = "", $password = ""){
        if(empty($username)){
            $_SESSION["eUsername"] = "El campo nombre de usuario es obligatorio.";
        }

        if(empty($password)){
            $_SESSION["ePassword"] = "El campo password es obligatorio.";
        }
    }

    /**
     * Procesa la constraseña enviada calculando su función hash si se ha
     * especificado en la solicitud http mediante el parámetro "hash".
     * Si el parámetro no es especificado, se entiende que no hay que
     * tratar la contraseña. En caso contrario, se aplicará la función hash
     * definida en el parámetro o md5 por defecto.
     * @param string $pass Contraseña enviada en la solicitud http
     * @return string Contraseña tratada
     */
    private function ProcessPassword($pass = ""){

        if(empty($pass)){
            return $pass;
        }

        $hashFunction = filter_input(INPUT_POST, "hash", FILTER_SANITIZE_STRING);

        if($hashFunction!== FALSE && $hashFunction!== NULL){
            $hashFunction = strtoupper(trim($hashFunction));
            $algo = "md5";
            if($hashFunction == "SHA1"){
                $algo = "sha1";
            }
            else if($hashFunction == "SHA256"){
                $algo = "sha256";
            }
            else if($hashFunction == "SHA512"){
                $algo = "sha512";
            }
            return hash($algo, $pass);
        }
        return $pass;
    }

    /**
     * Valida los datos de usuario contra el repositorio actual
     * y establece en la sesión los datos de usuario
     * [ $_SESSION["user"], $_SESSION["userid"] ]
     * @param string Nombre de usuario
     * @param string Contraseña de usuario
     * @return boolean
     */
    protected function ValidateUser($username = "", $spassword = ""){
        // Establecer los errores de login
        $this->SetError($username, $spassword);

        $password = $this->ProcessPassword($spassword);

        // Definir el filtro de búsqueda
        $filter = ($this->Project > 0)
            ? [ "Username" => $username, "Password" => $password,
                "IdService" => $this->Service,
                "IdProject" => $this->Project ]
            : [ "Username" => $username, "Password" => $password,
                "IdService" => $this->Service ];

        // Obtener el usuario de base de datos
        $users = $this->Dao->GetByFilter( "AuthEntity" , $filter);
        // Comprobar que hay resultados válidos: nos quedamos con el primero
        if(count($users) > 0 ){
            // almacenar en sesión datos de usuario
            $_SESSION[ "user" ] = $username;
            $_SESSION[ "userid" ] = $users[0]->IdUser;
            return TRUE;
        }
        // Mensaje de error para el login
        $_SESSION[ "eLogin" ]
                = "Las credenciales de usuario no han sido validadas.";
        return FALSE;
    }

    /**
     * Obtiene un array con el/los roles asociados al usuario autenticado
     * en el contexto. En caso de no estar autenticado el usuario, debe
     * retornar un array vacío.
     */
    public function GetUserRoles(){
        // Colección de roles de usuairo
        $roles = [];
        // Comporbar que el usuario está autenticado
        if(!$this->IsAuthenticate()) {
            return $roles;
        }
        // Obtener usuario del contexto
        $username = $this->GetUserName();
        // Definir el filtro
        $filter = ($this->Project > 0)
                ?["Username" => $username,
                    "IdService" => $this->Service,
                    "IdProject" => $this->Project ]
                :[ "Username" => $username,
                    "IdService" => $this->Service ];
        // Obtener el usuario de base de datos
        $result = $this->Dao->GetByFilter( "AuthEntity" , $filter);
        // Agregar los roles obtenidos al array
        foreach($result as $entity){
            $roles[] = trim($entity->Role);
        }
        // Retornar array de roles
        return $roles;
    }

    /**
     * Se encarga de realizar el proceso de autenticación del usuario
     * a partir del nombre de usuario y el password utilizado.
     * En el caso de ser válidas las credenciales, se debe establecer
     * el usuario como autenticado en el contexto.
     * Devuelve el resultado de la autenticación como un valor booleano.
     * @var string $username Nombre de usuario
     * @var string $password Contraseña de usuario
     * @return boolean Estado de la validación
     */
    public function Authenticate($username, $password){
        // Comprobación si se utiliza un ticket de autenticación
        $ticket = filter_input(INPUT_GET, "ticket");
        // Proceso de validación del ticket
        if($ticket != FALSE && $ticket != NULL){
            return $this->AuthenticateTicket($ticket);
        }
        // ejecutar el proceso de autenticación básico
        return parent::Authenticate($username, $password);
    }

    /**
     * Se encarga de realizar el proceso de autenticación del usuario
     * que accede a la aplicación mediante un ticket de acceso. En caso
     * de ser validado el ticket, se debe establecer el usuario como
     * autenticado en el contexto. Devuelve el resultado de la
     * autenticación como un valor booleano.
     * @var string $ticket ticket de autenticación de usuario
     * @return boolean Estado de la validación
     */
    public function AuthenticateTicket($ticket){
        $arr = $this->ValidateTicket($ticket);
        if(is_array($arr)){
            // Validar origen del ticket
            if($arr["source"] != $_SERVER["REMOTE_ADDR"]){
                return FALSE;
            }
            // Validar fecha ticket
            $date = new \DateTime($arr["date"]);
            $now = new \DateTime("NOW");
            if(intval($date->format("d")) < intval($now->format("d"))){
                return FALSE;
            }
            // Establecer los datos de sesión
            $_SESSION["user"] = $arr["user"];
            $_SESSION["userid"] = $arr["userid"];
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Proceso para la validación del formato/estructura del ticket
     * @param string $ticket ticket de autenticación
     * @return mixed Retorna FALSE si falla el proceso o referencia al array con
     * la información del ticket
     */
    private function ValidateTicket($ticket = ""){
        // Validar tipología del parámetro
        if(!is_string($ticket)){
            return FALSE;
        }
        $json = base64_decode($ticket);
        // verificar la decodificación
        if($json == FALSE){
            return FALSE;
        }
        $arr = json_decode($json);
        // verificar la decodificación json
        if($arr == NULL || !is_object($arr)){
            return FALSE;
        }

        settype($arr, "array");

        if(empty($arr["user"])||empty($arr["userid"])
                ||empty($arr["source"])||empty($arr["date"])){
            return FALSE;
        }
        return $arr;
    }

    /**
     * Obtiene un ticket de autenticación a partir de la información del
     * usuario autenticado. En caso de no estar el usuario autenticado,
     * se retornará una cadena vacía.
     */
    public function GetTicket(){
        $ticket = "";
        if($this->IsAuthenticate()){
            $date = new \DateTime("NOW");
            $obj = [
                "user" => $_SESSION[ "user" ],
                "userid" => $_SESSION["userid"],
                "source" => $_SERVER["REMOTE_ADDR"],
                "date" => $date->format("Y-m-d H:i:s")
            ];
            $sObj = json_encode($obj);
            $ticket = base64_encode($sObj);
        }
        return $ticket;
    }
} ?>
