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
 * Entidad Proyecto
 *
 * @author alfonso
 */
class Project{

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre de proyecto
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del proyecto
     * @var string
     */
    public $Description = "";

    /**
     * Ruta de acceso al proyecto
     * @var string
     */
    public $Path = "";

    /**
     * Fecha de alta del proyecto
     * @var string
     */
    public $Date = NULL;

    /**
     * Estado lógico del proyecto
     * @var boolean
     */
    public $Active = TRUE;
}
