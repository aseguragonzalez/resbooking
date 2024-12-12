<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class ServiceDeliveryMethod{

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    public int $serviceId = 0;

    /**
     * Identidad del método de entrega|recogida
     * @var int
     */
    public int $deliveryMethod = 0;
}
