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
 * Implementación de la interfaz para el repositorio de productos
 *
 * @author alfonso
 */
class ProductsRepository extends \BaseRepository implements \IProductsRepository{

    /**
     * Referencia a la clase base
     * @var \IProductsRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IProductsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(ProductsRepository::$_reference == NULL){
            ProductsRepository::$_reference =
                    new \ProductsRepository($project, $service);
        }
        return ProductsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \ProductsAggregate
     */
    public function GetAggregate() {

        $agg = new \ProductsAggregate($this->IdProject, $this->IdService);

        $filter = ["Project" => $this->IdProject, "State"  => 1];

        $products = $this->Dao->GetByFilter( "Product", $filter );

        foreach($products as $item){
            $agg->Products[$item->Id] = $item;
        }

        $agg->Categories = $this->Dao->GetByFilter( "Category", $filter );

        return $agg;
    }
}
