<?php

declare(strict_types=1);

/**
 * Agregado para la gestión de solicitudes/pedidos
 */
class RequestsAggregate extends \BaseAggregate{

    /**
     * Colección de horas del día
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Colección de descuentos disponibles
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de estados del flujo de trabajo registrados
     * @var array
     */
    public $States = [];

    /**
     * Colección de las categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de los productos referenciados en la solicitud
     * @var array
     */
    public $Products = [];

    /**
     * Referencia a la solicitud cargada
     * @var \Request
     */
    public $Request = null;

    /**
     * Referencia a la información del proyecto para impresión
     * @var \ProjectInformation
     */
    public $ProjectInformation = null;

    /**
     * Colección de Productos parametrizados en la solicitud
     * @var array
     */
    public $Items = [];

    /**
     * Colección de solicitudes disponibles
     * @var array
     */
    public $Requests = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Request = new \Request();
        $this->ProjectInformation = new \ProjectInformation();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }
}
