<?php

declare(strict_types=1);

/**
 * Hora del día
 */
class HourOfDay{

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Texto a visualizar para la hora, p.e. : "11:00"
     * @var string
     */
    public $Text = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public bool $state = true;

}
