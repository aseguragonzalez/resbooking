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
 * Lugar o estancia
 *
 * @author alfonso
 */
class Place{

    /**
     * Identidad del lugar
     * @var int Identidad del lugar
     */
    public $Id = 0;

    /**
     * Proyecto al que pertenece
     * @var int Identidad del proyecto asociado
     */
    public $Project = 0;

    /**
     * Nombre del lugar
     * @var string Nombre del Lugar
     */
    public $Name = 0;

    /**
     * Descripción del lugar
     * @var string Descripción del lugar
     */
    public $Description = "";

    /**
     * Numero de plazas
     * @var int Cantidad de comensales que pueden ser servidos
     */
    public $Size = 0;

    /**
     * Estado lógico del registro
     * @var boolean Estado del lugar
     */
    public $Active = true;

}
