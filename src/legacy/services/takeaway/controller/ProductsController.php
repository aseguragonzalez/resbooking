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
require_once("model/ProductsModel.php");

/**
 * Controllador para la gestión de productos
 *
 * @author manager
 */
class ProductsController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Proceso para la lista de productos
     * @return string Vista renderizada
     */
    public function Index(){
        try{
           // Instanciar el modelo
           $model = new \ProductsModel();
           // Cargar la lista de productos
           $model->GetProducts();
           // Retornar la vista renderizada
           return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso para editar un producto
     * @param int? $id Identidad del producto
     * @return string Vista renderizada
     */
    public function Edit($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ProductsModel();
            // Cargar categorías
            $model->GetProduct($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Edit", $e);
        }
    }

    /**
     * Proceso para almacenar un producto
     * @return string Vista renderizada
     */
    public function Save(){
        try{
            // Obtener registro de reserva
            $entity = $this->GetEntity( "Product" );
            // Instanciar el modelo
            $model = new \ProductsModel();
            // Cargar categorías
            $model->Save($entity);
            // Retornar la vista renderizada
            return $this->Partial("Edit", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Procedimiento de eliminación de un producto
     * @param int $id Identidad del producto
     * @return string Vista renderizada
     */
    public function Delete($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ProductsModel();
            // Cargar categorías
            $model->Delete($id);
            // Cargar la lista de productos
            $model->GetProducts();
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Delete", $e);
        }
    }
}
