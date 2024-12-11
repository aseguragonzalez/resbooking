<?php

declare(strict_types=1);

/**
 * Día de la semana
 */
class DayOfWeek{

    /**
     * Identidad del resgitro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del día
     * @var type
     */
    public $Name = "";

    /**
     * Abreviatura del día
     * @var type
     */
    public $ShortName = "";

    /**
     * Nombre del icono utilizado (si es necesario)
     * @var string
     */
    public $IcoName = "";

    /**
     * Número de día de la semana [1 - 7]
     * @var int
     */
    public $DayOfWeek = 0;
}
