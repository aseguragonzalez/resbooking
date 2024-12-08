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
 * DTO para la gestión de cupos por turno
 *
 * @author alfonso
 */
class TurnShareDTO {

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = NULL;

    /**
     * Fecha de registro
     * @var string
     */
    public $Date = "";

    /**
     * Cuota configurada
     * @var int
     */
    public $Share = 0;

    /**
     * Reservas actuales
     * @var int
     */
    public $BookingsTotal = 0;

    /**
     * Reservas pendientes
     * @var int
     */
    public $BookingsFree = 0;

    /**
     * Comensales actuales
     * @var int
     */
    public $DinersTotal = 0;

    /**
     * Comensales pendientes
     * @var int
     */
    public $DinersFree = 0;

}
