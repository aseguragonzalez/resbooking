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
 * DTO para visualizar la información del registro de clientes
 *
 * @author alfonso
 */
class ClientDTO {

    /**
     * Identidad del cliente
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Nombre del cliente
     * @var string
     */
    public $Name = "";

    /**
     * E-mail del cliente
     * @var string
     */
    public $Email = "";

    /**
     * Teléfono del cliente
     * @var string
     */
    public $Phone = "";

    /**
     * Fecha del regsitro
     * @var string
     */
    public $CreateDate = NULL;

    /**
     * Fecha de última actualización
     * @var string
     */
    public $UpdateDate = NULL;

    /**
     * Estado del cliente
     * @var boolean
     */
    public $State = 1;

    /**
     * Tipificación del cliente como VIP
     * @var boolean
     */
    public $Vip = FALSE;

    /**
     * Comentarios asociados al regsitro de cliente
     * @var string
     */
    public $Comments = "";

    public $Total = 0;

    public $Estado_0 = 0;

    public $Estado_1 = 0;

    public $Estado_2 = 0;

    public $Estado_3 = 0;

    public $Estado_4 = 0;

    public $Estado_5 = 0;

    public $Estado_6 = 0;

    public $Estado_7 = 0;

    public $UltimaFecha = "";

}
