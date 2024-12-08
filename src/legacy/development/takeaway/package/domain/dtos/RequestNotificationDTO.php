<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
