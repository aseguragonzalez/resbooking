<?php

declare(strict_types=1);

/**
 * Description of PanelModel
 *
 * @author alfonso
 */
abstract class PanelModel extends \Model{

    /**
     * Proyecto activo
     * @var \Project
     */
    public $Project = NULL;

    /**
     * Colección de proyectos del usuario
     * @var array
     */
    public $Projects = [];

    /**
     * Colección de registro de proyectos
     * @var array
     */
    public $ProjectsInfo = [];

    /**
     * Colección de servicios asociados al proyecto actual
     * @var array
     */
    public $Services = [];

    /**
     * Referencia al gestor de seguridad
     * @var \ISecurity Referencia al gestor de seguridad
     */
    protected $Security = NULL;

    /**
     * Identidad del usuario
     * @var int
     */
    public $UserId = 0;

    /**
     *  Nombre de usuario
     * @var string
     */
    public $Username = "";

    /**
     * Mensaje de error en la autenticación del usuario
     * @var string
     */
    public $eUsername = "";

    /**
     * Estilo CSS a utilizar para el error de nombre de usuario
     * @var string
     */
    public $eUsernameClass = "";

    /**
     * Mensaje de error en la autenticación de la contraseña
     * @var string
     */
    public $ePassword = "";

    /**
     *  Estilo CSS a utilizar para el error de contraseña
     * @var string
     */
    public $ePasswordClass = "";

    /**
     * Mensaje de error general en el formulario de login
     * @var string
     */
    public $eLogin = "";

    /**
     * Estilo CSS a utilizar en la etiqueta general del formulario
     * @var string
     */
    public $eLoginClass = "has-success";

    /**
     * Ticket de autenticación
     * @var string
     */
    public $Ticket = "";

    /**
     * Colección de códigos generados durante la validación
     * @var array
     */
    public $ErrorCodes = [];

    /**
     * Colección de códigos de error registrados
     * @var array
     */
    public $Codes = [];

    /**
     * Constructor
     */
    public function __construct(){
        // Constructor de la clase base
        parent::__construct();
        // Cargar el gestor de seguridad
        $this->Security = $this->Injector->Resolve( "ISecurity" );
        // Cargar nombre de usuario activo si lo hay
        $this->Username = $this->Security->GetUserName();
        // Configurar mensajes de error de autenticación
        $this->SetLoginError();
        // Cargar la lista de proyectos
        if(!empty($this->Username)){
            // Obtener la identidad del usuario
            $this->UserId = $_SESSION["userid"];
            // Establecer el ticket de autenticación
            $this->Ticket = $this->Security->GetTicket();
            // Cargar los proyectos de usuario
            $this->GetProjects();
        }
        // Establece el proyecto por defect(si sólo existe uno)
        $this->SetCurrent();
    }

    /**
     * Carga de los proyectos asociados al usuario actual
     */
    private function GetProjects(){
        // Filtro para la búsqueda de proyectos del usuario
        $filter = ["Username" => $this->Username ];
        // Filtrar la lista de proyectos
        $this->ProjectsInfo = $this->Dao->GetByFilter( "ProjectInfo" , $filter);

        foreach($this->ProjectsInfo as $project){
            if(isset($this->Projects[$project->Id])){
                continue;
            }
            $this->Projects[$project->Id] = $project;
        }
    }

    /**
     * Carga la colección de servicios asociados al proyecto actual
     */
    private function GetServices(){
        if($this->Project != NULL){
            $filter = ["Project" => $this->Project->Id, "User" => $this->UserId];
            $services = $this->Dao->GetByFilter("ServiceDTO", $filter);
            $this->Services = array_filter($services, function($item){
               return !empty($item->Platform);
            });
        }
    }

    /**
     * Establece el proyecto del modelo a partir de la información presente
     * en la sesión actual
     * @return boolean Advierte si el proyecto ha sido cargado desde la sesión
     */
    private function SetProjectFromSession(){
        $result = FALSE;
        if(isset($_SESSION["project_id"])){
            $id = intval($_SESSION["project_id"]);
            if(array_key_exists($id, $this->Projects) == TRUE){
                $this->Project = $this->Projects[$id];
                $this->GetServices();
                $result = TRUE;
            }
        }
        return $result;
    }

    /**
     * Establece el proyecto por defecto en el modelo y sesión
     * @return boolean Advierte si el proyecto ha sido cargado con éxito
     */
    private function SetDefaultProject(){
        $result = FALSE;
        if(count($this->Projects) > 0){
            $this->Project = current($this->Projects);
            $_SESSION["project_id"] = $this->Project->Id;
            $this->GetServices();
            $result = TRUE;
        }
        return $result;
    }

    /**
     * Establece el proyecto en el modelo y sesión utilizando el id de proyecto
     * @param type $id Identidad del proyecto
     * @return boolean Advierte si el proyecto ha sido cargado con éxito
     */
    private function SetProjectById($id = 0){
        $result = FALSE;
        $filters = array_filter($this->Projects, function($item) use ($id){
           return $item->Id == $id;
        });
        if(count($filters) > 0){
            $this->Project = current($filters);
            $_SESSION["project_id"] = $this->Project->Id;
            $this->GetServices();
            $result = TRUE;
        }
        return $result;
    }

    /**
     * Establece el proyecto seleccionado por su id
     * @param int $id Identidad del proyecto
     * @return boolean Resultado de la configuración de proyecto
     */
    public function SetCurrent($id = 0){
        $return = FALSE;

        if($id <= 0 && $this->SetProjectFromSession()){
            $return = TRUE;
        }
        else if($id <= 0 && $this->SetDefaultProject()){
            $return = TRUE;
        }
        else if($id > 0 && $this->SetProjectById($id)){
            $return = TRUE;
        }
        else{
            $this->Project = NULL;
            unset($_SESSION["project_id"]);
        }

        return $return;
    }

    /**
     * Configuración de los errores de autenticación
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

    /**
     * Establece los mensajes de error a partir de los codigos obtenidos
     * en la operacion anterior.
     * @return void
     */
    protected function TranslateResultCodes(){
        foreach ($this->ErrorCodes as $code){
            if(!isset($this->Codes[$code])){
                continue;
            }
            $codeInfo = $this->Codes[$code];
            $class = ($code == 0) ? "has-success" : "has-error";
            $this->{$codeInfo["name"]} = $codeInfo["msg"];
            $this->{$codeInfo["name"]."Class"} = $class;
        }
    }

    /**
     * Establece el array de "traducción" de códigos de error
     * @return void
     */
    protected abstract function SetCodes();

}
