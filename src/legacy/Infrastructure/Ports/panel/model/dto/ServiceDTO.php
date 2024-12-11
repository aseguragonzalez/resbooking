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
 * Description of ServiceDTO
 *
 * @author alfonso
 */
class ServiceDTO {

    /**
     * Identidad de Servicio
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del servicio
     * @var string
     */
    public $Name = "";

    /**
     * Ruta fisica de la aplicacion cliente
     * @var string
     */
    public $Path = "";

    /**
     * Ruta de la plataforma web utilizada
     * @var string
     */
    public $Platform = "";

    /**
     * Descripcion funcional del servicio
     * @var string
     */
    public $Description = "";

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del Usuario
     * @var int
     */
    public $User = 0;

}
