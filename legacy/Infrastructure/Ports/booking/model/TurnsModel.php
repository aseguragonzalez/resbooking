<?php

declare(strict_types=1);

/**
 * Model para la gestión de turnos
 *
 * @author alfonso
 */
class TurnsModel extends \ResbookingModel{

    /**
     * Pestaña del menú activa
     * @var string
     */
    public $Activo = "Configuración";

    /**
     * Colección de días de la semana (para la cabecera)
     * @var array
     */
    public $DaysH = [];

    /**
     * Colección de días de la semana
     * @var array
     */
    public $Days = [];

    /**
     * Colección de turnos disponibles
     * @var array
     */
    public $Turns = [];

    /**
     * Serialización de los cupos configurados
     * @var array
     */
    public $TurnsShare = "[]";

    /**
     * Colección de configuraciones registradas
     * @var array
     */
    public $Entities = [];

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        // Título de la página
        $this->Title = "Configuración::Turnos";
    }

    /**
     * Obtiene toda la información necesaria sobre los turnos
     */
    public function GetTurns(){

        $this->GetTurnsAndDays();

        $filter = ["Project" => $this->Project];
        $configs = $this->Dao->GetByFilter("Configuration", $filter);
        if(!is_array($configs)){
            $configs = [];
        }
        $this->Entities = json_encode($configs);
    }

    /**
     * Guarda los cambios en la configuración de una configuración
     * @param \Configuration Referencia a la entidad de configuración
     * @return int Resultado de la operación
     */
    public function Save($entity = null){
        $result = -1;
        if($entity != null){
            $entity->Project = $this->Project;
            if($entity->Id == 0){
                $result = $this->Dao->Create($entity);
            }
            else{
                $this->Dao->Delete($entity->Id, "Configuration");
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Proceso para configurar el modelo de cupos
     */
    public function SetShareModel(){
        $this->GetTurnsAndDays();
        $filter = ["Project" => $this->Project];
        foreach($this->Days as $day){
            $day->ShortName = substr($day->Name, 0, 2);
            unset($day->Id);
        }
        $turnsshare = $this->Dao->GetByFilter("TurnShare", $filter);
        $this->TurnsShare = json_encode($turnsshare);
    }

    /**
     * Proceso para actualizar el cupo de un turno
     * @param \TurnShare $turnShare Referencia a la entidad
     * @return int Código de operación
     */
    public function SetShare($turnShare = null){
        if($turnShare == null){
            return -1;
        }
        // Asignar proyecto
        $turnShare->Project = $this->Project;
        // filtro de búsqueda
        $filter = ["Project" => $turnShare->Project,
            "DayOfWeek" => $turnShare->DayOfWeek,
            "Turn" => $turnShare->Turn ];
        $register = $this->Dao->GetByFilter("TurnShare", $filter);
        if(count($register) == 0){
            $this->Dao->Create($turnShare);
        }
        elseif($turnShare->Share == 0){
            $os = current($register);
            $this->Dao->Delete($os->Id, "TurnShare");
        }
        else{
            $os = current($register);
            $os->Share = $turnShare->Share;
            $this->Dao->Update($os);
        }
        return 0;
    }

    /**
     * Proceso para cargar las colecciones comunes del modelo
     */
    private function GetTurnsAndDays(){
        // Obtener los días de la semana
        $this->Days = $this->Dao->Get( "Day" );
        foreach($this->Days as $item){
            $item->IdDay = $item->Id;
        }
        // Copiar la colección de días de la semana
        $this->DaysH = $this->Days;
        // Obtener la lista de turnos
        $this->Turns = $this->Dao->Get( "Turn" );
        foreach($this->Turns as $item){
            $item->IdTurn = $item->Id;
            $item->Start = substr($item->Start,0,5);
        }
    }
}
