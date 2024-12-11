<?php

declare(strict_types=1);

/**
 * DTO para las notificaciones de pedidos
 *
 * @author alfonso
 */
class RequestNotificationDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del cliente
     * @var string
     */
    public $Name = "";

    /**
     * Dirección del cliente
     * @var string
     */
    public $Address = "";

    /**
     * Correo electrónico del cliente
     * @var string
     */
    public $Email = "";

    /**
     * Teléfono de contacto
     * @var string
     */
    public $Phone = "";

    /**
     * Ticket de la solicitud
     * @var string
     */
    public $Ticket = "";

    /**
     * Importe sin descuento
     * @var float
     */
    public $Amount = "";

    /**
     * Importe con descuento
     * @var float
     */
    public $Total = "";

    /**
     * Descuento asociado al pedido
     * @var string
     */
    public $Discount = "";

    /**
     * Método de pago seleccionado
     * @var string
     */
    public $PaymentMethod = "";

    /**
     * Método de entrega seleccionado
     * @var string
     */
    public $DeliveryMethod = "";

    /**
     * Hora de entrega seleccionada
     * @var string
     */
    public $DeliveryTime = "";

    /**
     * Fecha de entrega seleccionada
     * @var string
     */
    public $DeliveryDate = "";

    /**
     * Colección de productos del pedido
     * @var array
     */
    public $Items = [];
}
