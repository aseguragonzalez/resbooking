<?php

declare(strict_types=1);

/**
 * Evento en la franja horaria de servicio. Permite "abrir" una franja
 * de servicio no configurada en una fecha específica o cerrar una
 * franja configurada en una fecha dada.
 */
class SlotEvent{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad de la franja horaria configurada
     * @var int
     */
    public $SlotOfDelivery = 0;

    /**
     * Fecha del evento en formato yyyy-mm-dd
     * @var string
     */
    public $Date = "";

    /**
     * Tipo de evento Apertura o cierre.
     * @var boolean
     */
    public $Open = 0;
}
