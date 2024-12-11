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
 * Entidad para gestionar la información del proyecto relativa a la
 * impresión de tickets de venta
 *
 * @author alfonso
 */
class ProjectInformation {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Título a utilizar en los ticket
     * @var string
     */
    public $Title = "";

    /**
     * Código de identificación fiscal
     * @var string
     */
    public $CIF = "";

    /**
     * Dirección física del proyecto
     * @var string
     */
    public $Address = "";

    /**
     * Número de teléfono del proyecto
     * @var string
     */
    public $Phone = "";

    /**
     * Email de contacto del proyecto
     * @var string
     */
    public $Email = "";
}
