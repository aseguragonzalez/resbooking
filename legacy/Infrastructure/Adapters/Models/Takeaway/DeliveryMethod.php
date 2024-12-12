<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class DeliveryMethod {

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
     * Estado lógico del registro
     * @var boolean
     */
    public bool $state = true;
}
