<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class SlotEvent{

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
     * Identidad de la franja horaria configurada
     * @var int
     */
    public $SlotOfDelivery = 0;

    /**
     * Fecha del evento en formato yyyy-mm-dd
     * @var string
     */
    public string $date = "";

    /**
     * Tipo de evento Apertura o cierre.
     * @var boolean
     */
    public $Open = 0;
}
