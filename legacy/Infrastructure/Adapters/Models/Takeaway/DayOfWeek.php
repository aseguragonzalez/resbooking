<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\Takeaway;

class DayOfWeek{

    /**
     * Identidad del resgitro
     * @var int
     */
    public int $id = 0;

    /**
     * Nombre del día
     * @var type
     */
    public string $name = "";

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
     * Número de día de la semana [0 - 7]
     * @var int
     */
    public $NumberOfDay = 0;
}
