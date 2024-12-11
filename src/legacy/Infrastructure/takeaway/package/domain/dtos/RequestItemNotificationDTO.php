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
 * Description of RequestItemNotificationDTO
 *
 * @author alfonso
 */
class RequestItemNotificationDTO {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id=0;

    /**
     * Identidad de la solicitud
     * @var int
     */
    public $Request=0;

    /**
     * Identidad del producto seleccionado
     * @var int
     */
    public $Product=0;

    /**
     * Cantidad solicitada
     * @var int
     */
    public $Count=0;

    /**
     * Observaciones/Opciones del producto
     * @var string
     */
    public $Data = "";

    /**
     * Nombre del producto
     * @var string
     */
    public $Name = "";

    /**
     * Precio del producto asociado
     * @var float
     */
    public $Price = 0;

}
