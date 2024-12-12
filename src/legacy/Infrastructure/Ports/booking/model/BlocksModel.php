<?php

declare(strict_types=1);

/**
 * Model para la gestión de bloqueos
 *
 * @author alfonso
 */
class BlocksModel extends \ResbookingModel{

    /**
     * DTO de navegación
     * @var \WeekNavDTO
     */
    public $WeekNavDTO = null;

    /**
     * Colección de días de la semana disponibles
     * @var array
     */
    public $Days = [];

    /**
     * Colección de días de la semana disponibles
     * @var array
     */
    public array $daysOfWeek = [];

    /**
     * Colección de turnos disponibles
     * @var array
     */
    public $Turns = [];

    /**
     * Línea base de configuraciones
     * @var array
     */
    public $BaseLine = [];

    /**
     * Colección de eventos configurados
     * @var array
     */
    public $Blocks = [];

    /**
     * Constructor
     */
    public function __construct(){

        parent::__construct();

        $this->Title = "Configuración::Bloqueos";

        $this->WeekNavDTO = new \WeekNavDTO();
    }

    /**
     * Carga el modelo con la información de los bloqueos asociados
     * al anyo y semana solicitados
     * @param int $year Año solicitado
     * @param int $week Semana del año
     */
    public function GetBlocks($year = 0, $week = 0){
        $this->Turns = $this->Dao->Get("Turn");
        foreach($this->Turns as $item){
            $item->IdTurn=$item->Id;
            $item->Start = substr($item->Start,0,5);
        }
        $days = $this->Dao->Get("Day");
        $this->WeekNavDTO->SetWeekInfo($days, $year, $week);
        $this->Days = $this->WeekNavDTO->DaysOfWeek;
        $this->daysOfWeek = $this->WeekNavDTO->DaysOfWeek;
        // Cargar línea base de configuración
        $base = $this->Dao->GetByFilter("Configuration",
            ["Project" => $this->Project]);
        $this->BaseLine = json_encode($base);
        // Cargar lista de bloqueos
        $filtro = [ "Project" => $this->Project,
            "Week" => $this->WeekNavDTO->Current,
            "Year" => $this->WeekNavDTO->CurrentYear];
        $events = $this->Dao->GetByFilter("Block", $filtro);

        $this->Blocks = json_encode($events);
    }

    /**
     * Proceso de almacenamiento del estado de un bloqueo
     * @param \Block $entity Referencia a la información del bloqueo
     * @return int Código de la operación ejecutada
     */
    public function SetBlock($entity = null){
        $result = -1;
        if($entity == null){
            return $result;
        }
        // Asignar el proyecto actual
        $entity->Project = $this->Project;
        // Buscar los registros ya existentes
        $filtro = ["Project" => $entity->Project,
            "Turn" => $entity->Turn,
            "Date" => $entity->Date];

        $bloqueos = $this->Dao->GetByFilter("Block", $filtro);
        if(count($bloqueos) == 0){
            $result = $this->Dao->Create($entity);
        }
        else{
            // Eliminar los registros existentes
            foreach($bloqueos as $item){
                $this->Dao->Delete($item->Id, "Block");
            }
            $result = 0;
        }
        return $result;
    }
}
