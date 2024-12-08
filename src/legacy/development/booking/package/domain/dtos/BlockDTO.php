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
 * DTO para la gestión de bloqueo
 *
 * @author alfonso
 */
class BlockDTO{

    /**
     * Identidad del bloqueo
     * @var int
     */
    public $Id = 0;

    /**
     * Año del bloqueo
     * @var int
     */
    public $Year = 2014;

    /**
     * Semana del año
     * @var int
     */
    public $Week = 1;

    /**
     * Día de la semana
     * @var int
     */
    public $DayOfWeek = 1;

    /**
     * Identidad del turno
     * @var int
     */
    public $Turn = 0;

    /**
     * Fecha
     * @var string
     */
    public $Date = "";

    /**
     * Estado del bloqueo
     * @var boolean
     */
    public $Block = FALSE;

    /**
     * Constructor
     */
    public function __construct($year = 2014, $week=1,
            $day = 1, $turn = 0, $date = "", $block = FALSE, $id = 0){
        $this->Id = $id;
        $this->Year = $year;
        $this->Week = $week;
        $this->DayOfWeek = $day;
        $this->Turn = $turn;
        $this->Block = $block;
        $this->Date = $date;
    }

}
