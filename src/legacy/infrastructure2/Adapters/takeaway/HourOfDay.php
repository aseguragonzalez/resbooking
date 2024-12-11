<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class HourOfDay{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Texto a visualizar para la hora, p.e. : "11:00"
     * @var string
     */
    public $Text = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;

}
