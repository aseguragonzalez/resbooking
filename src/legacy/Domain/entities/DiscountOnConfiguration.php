<?php

declare(strict_types=1);

/**
 * Configuración del día de la semana y franja horaria para el cual
 * un descuento es válido.
 */
class DiscountOnConfiguration{

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del descuento asociado
     * @var int
     */
    public $DiscountOn = 0;

    /**
     * Identidad del día de la semana en que es válido
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Identidad de la franja horaria en que es válido
     * @var int
     */
    public $SlotOfDelivery = 0;
}
