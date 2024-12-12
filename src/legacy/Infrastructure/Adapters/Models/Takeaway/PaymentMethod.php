<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class PaymentMethod{

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
     * Estado lógico del registro
     * @var boolean
     */
    public bool $state = true;
}
