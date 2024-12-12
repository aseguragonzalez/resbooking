<?php

declare(strict_types=1);

/**
 * Description of EventsRepository
 *
 * @author alfonso
 */
class EventsRepository extends \BaseRepository implements \IEventsRepository{

    /**
     * Referencia a la clase base
     * @var \IEventsRepository
     */
    private static $_reference = null;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IEventsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(EventsRepository::$_reference == null){
            EventsRepository::$_reference =
                    new \EventsRepository($project, $service);
        }
        return EventsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \EventsAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \EventsAggregate($this->IdProject, $this->IdService);
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject];
        // Cargar los días de la semana
        $agg->Events = $this->Dao->GetByFilter("SlotEvent", $filter);
        // Cargar los turnos de reparto registrados
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter("SlotOfDelivery", $filter);
        // Cargar turnos configurados
        $agg->BaseLine = $this->Dao->GetByFilter("SlotConfigured", $filter);
        // Cargar los días de la semana disponibles
        $agg->DaysOfWeek = $this->Dao->Get("DayOfWeek");

        return $agg;
    }
}
