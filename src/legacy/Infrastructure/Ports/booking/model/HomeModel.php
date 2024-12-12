<?php

declare(strict_types=1);

/**
 * Model para la pantalla de inicio
 */
class HomeModel extends \ResbookingModel{

    /**
     * Opción del menú activa
     * @var string
     */
    public $Activo = "";

    /**
     * Colección de proyectos válidos para el usuario
     * @var array
     */
    public $Projects = array();

    /**
     * Constructor
     */
    public function __construct(){
        // Cargar constructor padre
        parent::__construct();
        // Título de la página
        $this->Title = "Inicio";
    }

    /**
     * Cargar todos los proyectos del usuario y establece el proyecto de
     * contexto si sólo existe uno
     * @return boolean Resultado de la carga
     */
    public function LoadProjects(){
        // filtro de busqueda
        $filter = [ "Username" => $this->Username,
            "IdService" =>  $this->Service];
        // Filtrar la lista de proyectos
        $this->Projects = $this->Dao->GetByFilter( "ProjectInfo" , $filter);
        // Comprobamos el número de proyectos actuales
        if(count($this->Projects) == 1){
            return $this->SetProject($this->Projects[0]);
        }
        // Resultado por defecto
        return false;
    }

    /**
     * Establece el proyecto seleccionado por su id
     * @param int $idProyecto Identidad del proyecto
     * @return boolean Resultado de la configuración de proyecto
     */
    public function SetCurrent($idProyecto = 0){
        // Definir el filtro de búsqueda
        $filter = [ "Username" => $this->Username,
            "IdService" => $this->Service, "Id" => $idProyecto ];
        // Filtrar el proyecto seleccionado
        $projects = $this->Dao->GetByFilter( "ProjectInfo" , $filter );
        // Comprobar si se ha encontrado algún proyecto
        if(count($projects) > 0){
            return $this->SetProject($projects[0]);
        }
        // resultado por defecto
        return false;
    }

    /**
     * Establece los datos del proyecto en sesión
     * @param \Project $project Referencia al proyecto a "setear"
     * @return boolean Resultado de la operación
     */
    private function SetProject($project = null){
        // Setear el contexto con los datos de proyecto
        if($project != null){
            $_SESSION["projectId"] = $project->Id;
            $_SESSION["projectName"] = $project->Name;
            $_SESSION["projectPath"] = $project->Path;
            return true;
        }
        // resultado por defecto
        return false;
    }

}
