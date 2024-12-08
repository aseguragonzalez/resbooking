<?php

/**
 * Clase base para los model de la aplicación
 */
class CoreModel extends \SaasModel{

    /**
     * Título del formulario
     * @var String
     */
    public $Title = "{try-catch} Core";

    /**
     * Parámetro para activar el menú
     * @var String
     */
    public $MenuActivo = "Core";

    /**
     * @ignore
     * Constructor de la clase
     */
    public function __construct(){
        parent::__construct();
        $this->LoadProject();
    }

    /**
     * Carga la información del proyecto seleccionado
     * @return void
     * @throws Exception
     */
    private function LoadProject(){
        $projects =
                $this->Dao->GetByFilter( "Project", ["Name" => "Core" ]);
        if(!empty($projects) && count($projects) > 0){
            $project = $projects[0];
            $this->Project = $project->Id;
            $this->ProjectName = $project->Name;
            $this->ProjectPath = $project->Path;
            return;
        }
        throw new Exception("Proyecto Core no localizado");
    }
}
