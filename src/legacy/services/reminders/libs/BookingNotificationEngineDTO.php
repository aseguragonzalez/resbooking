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
