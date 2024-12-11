<?php

declare(strict_types=1);

/**
 * Description of SlotDTO
 *
 * @author manager
 */
class SlotDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre asignado a la franja horaria
     * @var string
     */
    public $Name = "";

    /**
     * Hora de inicio de la franja horaria
     * @var string
     */
    public $Start = "";

    /**
     * Hora de finalización de la franja horaria
     * @var string
     */
    public $End = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Estado del registro
     * @var boolean
     */
    public $State = 1;

}
