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
 * Agregado para la gestión de productos
 */
class ProductsAggregate extends \BaseAggregate{

    /**
     * Colección de las categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de las categorías disponibles
     * @var array
     */
    public $AvailableCategories = [];

    /**
     * Referencia al producto cargado
     * @var \Product
     */
    public $Product = NULL;

    /**
     * Colección de imagenes del producto seleccionado
     * @var array
     */
    public $Images = [];

    /**
     * Colección de los productos referenciados en la solicitud
     * @var array
     */
    public $Products = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Product = new \Product();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {
        $this->AvailableCategories = array_filter($this->Categories,
                function ($item) {
                    return $item->State == 1;
            });
    }
}
