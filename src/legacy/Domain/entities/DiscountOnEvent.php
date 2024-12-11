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
 * Entidad para registrar un evento de apertura o cierre sobre un descuento
 *
 * @author alfonso
 */
class DiscountOnEvent {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio asociado
     * @var int
     */
    public $Service = 0;

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
    public $Date = "";

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
    public $State = 0;
}
