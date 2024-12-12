<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class PageLog{

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Dirección IP desde donde se realiza la solicitud
     * @var string
     */
    public $IP="";

    /**
     * Url solicitada
     * @var string
     */
    public $Url="";

    /**
     * Fecha de la última actualización
     * @var string
     */
    public $Date=null;

    /**
     * Cantidad de veces que se ha realizado la misma solicitud
     * @var int
     */
    public $Count=0;
}
