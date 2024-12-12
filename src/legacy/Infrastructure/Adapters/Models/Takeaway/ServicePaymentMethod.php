<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class ServicePaymentMethod{

    /**
     * Identidad del registro de configuración
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad del Servicio asociado
     * @var int
     */
    public int $serviceId = 0;

    /**
     * Identidad del método|forma de pago
     * @var int
     */
    public $PaymentMethod = 0;
}
