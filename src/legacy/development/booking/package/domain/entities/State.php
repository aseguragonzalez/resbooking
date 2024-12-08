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
 * Estado de la reserva (WORKFLOW)
 *
 * @author alfonso
 */
class State{

    /**
     * Identidad del estado
     * @var int Identidad del estado
     */
    public $Id = 0;

    /**
     * Nombre del estado
     * @var string Nombre del estado
     */
    public $Name = 0;

    /**
     * Descripción del estado
     * @var string Descripción del estado
     */
    public $Description = "";

    /**
     * Nivel en el workflow
     * @var int Nivel o profundidad del estado
     */
    public $Level = 0;

}
