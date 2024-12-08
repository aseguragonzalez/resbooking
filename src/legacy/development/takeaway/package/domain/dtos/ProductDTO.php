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
 * DTO resumen con la informacion de un producto
 */
class ProductDTO{

    /**
     * Identidad del producto
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al producto
     * @var \Product
     */
    public $Product = NULL;

    /**
     * Coleccion de imagenes asociadas al producto
     * @var array
     */
    public $Gallery = [];

    /**
     *
     * @var type
     */
    public $Likes = [];

    /**
     * Coleccion de registros de actividad
     * @var type
     */
    public $Logs = [];

    /**
     * Coleccion de comentarios asociados al producto
     * @var array
     */
    public $Comments = [];

    /**
     * Constructor de la clase
     * @param int Identidad del producto
     */
    public function __construct($id = 0){
        $this->Id = $id;
    }
}
