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

require_once( "model/CategoriesModel.php" );

/**
 * Controlador para la gestión de categorías
 *
 * @author manager
 */
class CategoriesController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Procedimiento para cargar la lista de categorías disponibles
     * @return string
     */
    public function Index(){
       try{
           // Instanciar el modelo
           $model = new \CategoriesModel();
           // Cargar categorías
           $model->GetCategories();
           // Retornar la vista renderizada
           return $this->PartialView($model);
       }
       catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
       }
    }

    /**
     * Procedimiento para editar una categoría
     * @param int $id Identidad de la categoría
     * @return string vista renderizada
     */
    public function Edit($id = 0){
        try{
            // Instanciar el modelo
            $model = new \CategoriesModel();
            // Cargar categorías
            $model->GetCategory($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Edit", $e);
        }
    }

    public function GetEntity($entityName = "") {
        if($entityName != "Category"){
            return parent::GetEntity($entityName);
        }
        $entity = new Category();
        foreach($_POST as $key => $value){
            $entity->{$key} = $value;
        }

        if($entity->Parent == "null"){
            $entity->Parent = NULL;
        }

        return $entity;
    }

    /**
     * Procedimiento para almacenar una categoría
     * @return string vista renderizada
     */
    public function Save(){
        try{
            // Obtener registro de reserva
            $entity = $this->GetEntity( "Category" );
            // Instanciar el modelo
            $model = new \CategoriesModel();
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
     * Procedimiento de eliminación de una categoría
     * @param int $id Identidad de la categoría
     * @return string Vista renderizada
     */
    public function Delete($id = 0){
        try{
            // Instanciar el modelo
            $model = new \CategoriesModel();
            // Eliminación de la categoría
            $model->Delete($id);
            // Cargar la lista de categorías
            $model->GetCategories();
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Delete", $e);
        }
    }
}
