<?php



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
