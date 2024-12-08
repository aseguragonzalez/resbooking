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
 * Description of DeliveryMethodDTO
 *
 * @author manager
 */
class DeliveryMethodDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del método de entrega
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del método de entrega
     * @var string
     */
    public $Description = "";

    /**
     * Términos generales (opcional)
     * @var string
     */
    public $Terms = "";

    /**
     * Nombre del icono a utilizar(si procede)
     * @var type
     */
    public $IcoName = "";

    /**
     * Identidad del proyecto padre
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio actual
     * @var int
     */
    public $Service = 0;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;

}
