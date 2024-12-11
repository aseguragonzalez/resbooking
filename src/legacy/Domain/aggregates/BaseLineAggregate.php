<?php

declare(strict_types=1);

/**
 * Agregado para la configuración de línea base
 *
 * @author manager
 */
class BaseLineAggregate extends \BaseAggregate{

    /**
     * Referencia al Slot actual
     * @var \SlotConfigured
     */
    public $Slot = NULL;

    /**
     * Coleccion de Slots configurados
     * @var array
     */
    public $Slots = [];

    /**
     * Coleccion de turnos de reparto
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Coleccion de turnos de reparto activos
     * @var array
     */
    public $AvailableSlotsOfDelivery = [];

    /**
     * Coleccion de dias de la semana
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Slot = new \SlotConfigured();
    }

    /**
     * Configuracion del agregado
     */
    public function SetAggregate() {
        $this->AvailableSlotsOfDelivery =
                array_filter($this->SlotsOfDelivery,function($item){
                   return $item->State == 1;
                });
    }
}
