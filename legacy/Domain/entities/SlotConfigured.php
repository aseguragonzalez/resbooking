<?php

declare(strict_types=1);

/**
 * Configuración de la franja de servicio de un proyecto
 */
class SlotConfigured{

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
     * Identidad del día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Identidad de la franja horaria configurada
     * @var int
     */
    public $SlotOfDelivery = 0;
}
