<?php

declare(strict_types=1);

/**
 * Agregado para la gestión de solicitudes
 * @author manager
 */
class OrderAggregate extends \BaseAggregate{

    /**
     * Colección de categorías disponibles
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de productos disponibles
     * @var array
     */
    public $Products = [];

    /**
     * Colección de Slots
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de eventos registrados
     * @var array
     */
    public $Events = [];

    /**
     * Colección de horas disponibles
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Métodos de pago disponibles
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Métodos de entrega disponibles
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de turnos de reparto existentes
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Colección de códigos postales asociados
     * @var array
     */
    public $PostCodes = [];

    /**
     * Colección de descuentos configurados
     * @var array
     */
    public $Discounts = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }

}
