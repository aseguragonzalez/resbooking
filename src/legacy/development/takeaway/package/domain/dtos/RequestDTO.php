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
 * DTO para la información de solicitudes
 */
class RequestDTO{

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia a la solicitud
     * @var \Request
     */
    public $Request = null;

    /**
     * Colección de productos asociados a la solicitud
     * @var array
     */
    public $Items = [];

}
