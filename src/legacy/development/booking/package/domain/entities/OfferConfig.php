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
 * Configuración de dias oferta
 *
 * @author alfonso
 */
class OfferConfig{

    /**
     * Identidad del registro de configuración
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad de la oferta a la que pertenece
     * @var int
     */
    public $Offer = 0;

    /**
     *Identidad del día de la semana
     * @var int
     */
    public $Day = 0;

    /**
     * Identidad del slot asociado (franja horaria)
     * @var int
     */
    public $Slot = 0;

    /**
     * Turno asociado a la configuración
     * @var int
     */
    public $Turn = 0;
}
