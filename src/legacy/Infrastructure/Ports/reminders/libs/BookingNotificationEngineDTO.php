<?php

declare(strict_types=1);

/**
 * Description of BookingNotificationDTO
 *
 * @author alfonso
 */
class BookingNotificationEngineDTO {

    /**
     * Identidad de la reserva
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public $Project= 0;

    /**
     * Hora de la reserva
     * @var string
     */
    public $Start = "";

    /**
     * Fecha de la reserva
     * @var string
     */
    public $Date = "";

    /**
     * Fecha de creación de la reserva
     * @var string
     */
    public $CreateDate = "";

    /**
     * Número de comensales
     * @var int
     */
    public $Diners = 0;

    /**
     * Nombre del cliente
     * @var string
     */
    public $Name = "";

    /**
     * Email del cliente
     * @var string
     */
    public $Email = "";

    /**
     * Teléfono del cliente
     * @var string
     */
    public $Phone = "";

    /**
     * Estado de la reserva
     * @var int
     */
    public $State = NULL;

    /**
     * Lugar de la reserva
     * @var string
     */
    public $Place = "";

    /**
     * Título de la oferta
     * @var string
     */
    public $Title = "";

    /**
     * Descripción de la oferta
     * @var string
     */
    public $Description = "";

    /**
     * Términos y condiciones de la oferta
     * @var string
     */
    public $Terms = "";

    /**
     * Comentarios del cliente
     * @var string
     */
    public $Comment = "";

    /**
     * Notas de la reserva
     * @var string
     */
    public $Notes = "";

    /**
     * Información del pre-pedido
     * @var string
     */
    public $PreOrders = "";
}
