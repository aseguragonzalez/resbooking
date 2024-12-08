<?php

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
                throw new \UnAuthenticateException( $message );
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

                    throw new \UrlException($message);
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

                throw new \UnAuthorizeException( $message );
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
