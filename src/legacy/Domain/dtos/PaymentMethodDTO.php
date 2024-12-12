<?php

declare(strict_types=1);

/**
 * Description of PaymentMethodDTO
 *
 * @author manager
 */
class PaymentMethodDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Nombre asignado al medio de pago
     * @var string
     */
    public string $name = "";

    /**
     * Descripción del medio de mago
     * @var string
     */
    public string $description = "";

    /**
     * Abreviatura del nombre asignado
     * @var string
     */
    public $ShortName = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
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
