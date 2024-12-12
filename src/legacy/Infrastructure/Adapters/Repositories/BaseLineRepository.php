<?php

declare(strict_types=1);

/**
 * Implementación del repositorio para la gestión de línea base
 *
 * @author alfonso
 */
class BaseLineRepository extends \BaseRepository implements \IBaseLineRepository{

    /**
     * Referencia a la clase base
     * @var \IBaseLineRepository
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
     * @return \IBaseLineRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(BaseLineRepository::$_reference == null){
            BaseLineRepository::$_reference =
                    new \BaseLineRepository($project, $service);
        }
        return BaseLineRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseLineAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \BaseLineAggregate($this->IdProject, $this->IdService);
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject];
        // Cargar los días de la semana
        $agg->DaysOfWeek = $this->Dao->Get("DayOfWeek");
        // Cargar los turnos de reparto registrados
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter("SlotOfDelivery", $filter);
        // Obtener turnos configurados
        $slots = $this->Dao->GetByFilter("SlotConfigured", $filter);
        // Cargar los turnos de reparto configurados
        foreach($slots as $item){
            $agg->Slots[$item->Id] = $item;
        }
        return $agg;
    }

}
