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
 * Entidad Turno
 *
 * @author alfonso
 */
class Turn{

    /**
     * Propiedad Id
     * @var int Identidad del turno
     */
    public $Id = 0;

    /**
     * Referencia externa a la franja horaria
     * @var int Identidad del Slot asociado
     */
    public $Slot = 0;

    /**
     * Hora de inicio del turno
     * @var string Hora de inicio del turno
     */
    public $Start = "";

    /**
     * Hora de fin del turno
     * @var string Hora de finalización del turno
     */
    public $End = "";

}
