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
 * Entidad Producto
 */
class Product{

    /**
     * Identidad del producto
     * @var int Id
     */
    public $Id = 0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = NULL;

    /**
     * Referencia a la categoría
     * @var int Id de la categoría
     */
    public $Category = 0;

    /**
     * Referencia de catalogación del producto
     * @var string Referencia
     */
    public $Reference = "";

    /**
     * Nompre del producto
     * @var string Nombre
     */
    public $Name = "";

    /**
     * Texto del enlace utilizado al cargar la ficha de producto
     * @var string Url friendly
     */
    public $Link = "";

    /**
     * Descripción del producto utilizada en la ficha
     * @var string Descripción
     */
    public $Description = "";

    /**
     * Terminos clave asociados a caracterizar el producto
     * @var string keywords
     */
    public $Keywords = "";

    /**
     * Precio del producto
     * @var float Precio
     */
    public $Price = 0;

    /**
     * Serialización de los atributos que caracterizan el producto
     * @var string Atributos jSon
     */
    public $Attr = "";

    /**
     * Valoración para la ordenación de los productos
     * @var int Orden
     */
    public $Ord = 0;

    /**
     * Estado lógico del producto
     * @var boolean Estado actual
     */
    public $State = 1;

    /**
     * Estado de visibilidad del producto en el catálogo
     * @var boolean
     */
    public $Visible = 1;
}
