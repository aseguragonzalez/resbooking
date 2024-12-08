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
 * Entidad Log. Representa la cantidad de visitas que recibe un mismo
 * producto.
 */
class Log{

    /**
     * Identidad del log en base de datos
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad de visitas recibidas
     * @var int
     */
    public $Count=0;

}
