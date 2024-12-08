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
 * DTO para un registro de turno configurado. Permite obtener la
 * información de un turno junto a los parámetros de configuración
 * del proyecto
 *
 * @author alfonso
 */
class TurnDTO {

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del turno
     * @var int
     */
    public $Id = 0;

    /**
     * Día de la semana asociado a la configuración
     * @var int
     */
    public $DOW = 0;

    /**
     * Franja horaria del turno
     * @var int
     */
    public $Slot = 0;

    /**
     * Hora de inicio del turno
     * @var string
     */
    public $Start = "";

    /**
     * Hora de finalización del turno
     * @var string
     */
    public $End = "";
}
