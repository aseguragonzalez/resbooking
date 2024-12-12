<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class Log{

    /**
     * Identidad del log en base de datos
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad de visitas recibidas
     * @var int
     */
    public $Count=0;

}
