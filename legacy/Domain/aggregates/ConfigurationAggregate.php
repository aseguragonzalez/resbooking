<?php

declare(strict_types=1);

/**
 * Agregado para la configuración de proyecto
 *
 * @author alfonso
 */
class ConfigurationAggregate extends \BaseAggregate{

    /**
     * Referencia a la entidad de registro de información de proyecto
     * @var \ProjectInformation
     */
    public $ProjectInfo = null;

    /**
     * Colección de formas de entrega disponibles
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de formas de pago disponibles
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Colección de códigos postales disponibles
     * @var array
     */
    public $PostCodes = [];

    /**
     * Colección de formas de entrega configurados
     * @var array
     */
    public $AvailableDeliveryMethods = [];

    /**
     * Colección de formas de pago configurados
     * @var array
     */
    public $AvailablePaymentMethods = [];

    /**
     * Colección de códigos postales configurados
     * @var array
     */
    public $AvailablePostCodes = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->ProjectInfo = new \ProjectInformation();
    }

    /**
     * Configuracion del agregado
     */
    public function SetAggregate() {

    }
}
