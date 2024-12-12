<?php

declare(strict_types=1);

/**
 * Agregado para la gestión de eventos
 *
 * @author manager
 */
class EventsAggregate extends \BaseAggregate{

    /**
     * Colección de días de la semana
     * @var array
     */
    public array $daysOfWeek = [];

    /**
     * Referencia al evento actual
     * @var \SlotEvent
     */
    public $Event = null;

    /**
     * Colección de eventos registrados
     * @var array
     */
    public $Events = [];

    /**
     * Colección de slots configurados
     * @var array
     */
    public $BaseLine = [];

    /**
     * Colección de turnos de reparto registrados
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Colección de turnos de reparto activos
     * @var array
     */
    public $AvailableSlotsOfDelivery = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Event = new \SlotEvent();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {
        $this->AvailableSlotsOfDelivery =
                array_filter($this->SlotsOfDelivery,function($item){
                   return $item->State == 1;
                });
    }
}
