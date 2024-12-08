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
 * Entidad para la gestión de cuotas por oferta
 *
 * @author alfonso
 */
class OfferShare {

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
     * Identidad de la oferta asociada
     * @var int
     */
    public $Offer = 0;

    /**
     * Identidad del día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Identidad del slot asociado
     * @var int
     */
    public $Slot = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = NULL;

    /**
     * Cuota asignada a la oferta
     * @var int
     */
    public $Share = 0;
}
