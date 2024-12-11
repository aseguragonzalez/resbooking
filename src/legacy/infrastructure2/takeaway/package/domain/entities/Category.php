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
 * Entidad categoría
 */
class Category{

    /**
     * Identidad de la categoría
     * @var int
     */
    public $Id=0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Identidad de la categoría padre si existe
     * @var int
     */
    public $Parent = NULL;

    /**
     * Código asociado a la categoría
     * @var string
     */
    public $Code = "";

    /**
     * Nombre o denominación de la categoría
     * @var string
     */
    public $Name = "";

    /**
     * Descripción informativa de la categoría
     * @var string
     */
    public $Description = "";

    /**
     * Definición de los atributos que caracterizan a una categoría
     * @var xml
     */
    public $Xml = "";

    /**
     * Estado lógico de la categoría
     * @var boolean
     */
    public $State = 1;

    /**
     * Link de búsqueda para tener un URL friendly
     * @var string
     */
    public $Link = "";
}
