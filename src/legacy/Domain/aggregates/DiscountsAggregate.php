<?php

declare(strict_types=1);

/**
 * Agregado para la gestion de descuentos
 *
 * @author manager
 */
class DiscountsAggregate extends \BaseAggregate{

    /**
     * Referencia al descuento en edición
     * @var \DiscountDTO
     */
    public $Discount = NULL;

    /**
     * Colección de DTOs de descuentos activos
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de días de la semana registrados
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Colección de turnos de reparto establecidos
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Discount = new \DiscountDTO();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }

}
