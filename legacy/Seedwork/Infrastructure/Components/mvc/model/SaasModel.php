<?php



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
    public int $projectId = 0;

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
    public int $serviceId = 0;

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
