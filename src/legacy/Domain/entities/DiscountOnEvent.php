<?php

declare(strict_types=1);

/**
 * Entidad para registrar un evento de apertura o cierre sobre un descuento
 *
 * @author alfonso
 */
class DiscountOnEvent {

    /**
     * Identidad del registro
     * @var int
     */
    public int $id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public int $projectId = 0;

    /**
     * Identidad del servicio asociado
     * @var int
     */
    public int $serviceId = 0;

    /**
     * Identidad del descuento asociado
     * @var int
     */
    public $DiscountOn = 0;

    /**
     * Identidad de la franja de reparto asociada
     * @var int
     */
    public $SlotOfDelivery = 0;

    /**
     * Fecha del evento
     * @var string
     */
    public string $date = "";

    /**
     * Anyo del evento
     * @var int
     */
    public $Year = 0;

    /**
     * Semana del anyo
     * @var int
     */
    public $Week = 0;

    /**
     * Día de la semana asociado
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Estado del descuento: Abierto o cerrado
     * @var int
     */
    public bool $state = false;
}
