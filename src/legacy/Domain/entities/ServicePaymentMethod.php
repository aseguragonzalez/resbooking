<?php

declare(strict_types=1);

/**
 * Registro de configuración del método de pago para el servicio
 * y proyecto especificado
 */
class ServicePaymentMethod{

    /**
     * Identidad del registro de configuración
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del Servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Identidad del método|forma de pago
     * @var int
     */
    public $PaymentMethod = 0;
}
