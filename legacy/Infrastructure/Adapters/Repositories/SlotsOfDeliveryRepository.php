<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz para el repositorio de turnos de reparto
 *
 * @author alfonso
 */
class SlotsOfDeliveryRepository extends \BaseRepository
    implements \ISlotsOfDeliveryRepository {

    /**
     * Referencia a la clase base
     * @var \ISlotsOfDeliveryRepository
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
     * @return \ISlotsOfDeliveryRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(SlotsOfDeliveryRepository::$_reference == null){
            SlotsOfDeliveryRepository::$_reference =
                    new \SlotsOfDeliveryRepository($project, $service);
        }
        return SlotsOfDeliveryRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \SlotsOfDeliveryAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \SlotsOfDeliveryAggregate($this->IdProject, $this->IdService);
        // Cargar las horas disponibles
        $agg->HoursOfDay = $this->Dao->GetByFilter( "HourOfDay", ["State" => 1] );
        // filtro por proyecto
        $filter = ["Project" => $this->IdProject];
        $slots = $this->Dao->GetByFilter( "SlotOfDelivery", $filter );
        foreach($slots as $slot){
            $agg->Slots[$slot->Id] = $slot;
        }
        return $agg;
    }
}
