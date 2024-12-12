<?php

declare(strict_types=1);

/**
 * Franja horaria del servicio para un proyecto
 */
class SlotOfDelivery {

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
     * Nombre asignado a la franja horaria
     * @var string
     */
    public string $name = "";

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
     * Estado del registro
     * @var boolean
     */
    public bool $state = true;
}
