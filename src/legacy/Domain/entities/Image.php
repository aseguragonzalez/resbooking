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
 * Entidad Imagen. Representa una imagen asociada a un producto
 */
class Image{

    /**
     * Identidad de la imagen
     * @var int Id
     */
    public $Id = 0;

    /**
     * Identidad del producto padre
     * @var int Id  del producto al que está asociado
     */
    public $Product = 0;

    /**
     * Nombre asignado a la imagen
     * @var string Nombre de producto
     */
    public $Name = "";

    /**
     * Descripción de la imagen
     * @var string Descripción
     */
    public $Description = "";

    /**
     * Ruta de acceso al fichero de imagen
     * @var string Ruta física
     */
    public $Path = "";

    /**
     * Fecha asociada a la imagen
     * @var string Fecha de imagen
     */
    public $Date = null;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}
