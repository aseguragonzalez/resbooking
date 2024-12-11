<?php

declare(strict_types=1);

/**
 * Argegado para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryAggregate extends \BaseAggregate{

    /**
     * Colección de horas disponibles en base de datos
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Referencia al Turno de reparto actual
     * @var \SlotOfDelivery
     */
    public $Slot = NULL;

    /**
     * Colección de turnos de reparto registrados
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de turnos activos
     * @var array
     */
    public $AvailableSlots = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Slot = new \SlotOfDelivery();
    }

    /**
     * Configuración de agregados
     */
    public function SetAggregate() {
        $this->AvailableSlots =
                array_filter($this->Slots, function($item){
                   return $item->State == 1;
                });
    }
}
