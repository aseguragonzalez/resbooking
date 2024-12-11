<?php

/*
 * Copyright (C) 2015 manager
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
 * Día de la semana
 */
class DayOfWeek{

    /**
     * Identidad del resgitro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del día
     * @var type
     */
    public $Name = "";

    /**
     * Abreviatura del día
     * @var type
     */
    public $ShortName = "";

    /**
     * Nombre del icono utilizado (si es necesario)
     * @var string
     */
    public $IcoName = "";

    /**
     * Número de día de la semana [1 - 7]
     * @var int
     */
    public $DayOfWeek = 0;
}
