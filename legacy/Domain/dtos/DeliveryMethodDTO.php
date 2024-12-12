<?php

declare(strict_types=1);

/**
 * Description of DeliveryMethodDTO
 *
 * @author manager
 */
class DeliveryMethodDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Nombre del método de entrega
     * @var string
     */
    public string $name = "";

    /**
     * Descripción del método de entrega
     * @var string
     */
    public string $description = "";

    /**
     * Términos generales (opcional)
     * @var string
     */
    public $Terms = "";

    /**
     * Nombre del icono a utilizar(si procede)
     * @var type
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad del servicio actual
     * @var int
     */
    public int $serviceId = 0;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public bool $state = true;

}
