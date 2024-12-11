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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para categorías
 */
class CategoriesManagement extends \BaseManagement
    implements \ICategoriesManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \ICategoriesServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \ICategoriesRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \ICategoriesManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = CategoriesRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = CategoriesServices::GetInstance($this->Aggregate);
    }

    /**
     * Proceso para cargar en el agregado actual la categoría
     * indicada mediante su identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function GetCategory($id = 0) {
        // Obtener referencia
        $category = $this->Services->GetById(
                $this->Aggregate->Categories, $id);
        if($category != NULL){

            $this->Aggregate->Category = $category;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso de registro o actualización de la categoría
     * @param \Category $category Referencia a la categoría
     * @return array Códigos de operación
     */
    public function SetCategory($category = NULL) {
        $category->Project = $this->IdProject;
        $result = $this->Services->Validate($category);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($category->Id == 0){
                $res = $this->Repository->Create($category);
                $result[] = ($res != FALSE) ? 0 : -1;
                $category->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($category);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Categories[$category->Id] = $category;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveCategory($id = 0) {
        // Obtener referencia
        $category = $this->Services->GetById(
                $this->Aggregate->Categories, $id);
        if($category != NULL){
            // Eliminar todas las referencias asociadas a la categoría
            $this->RemoveReferences($id);
            // Establecer el estado
            $category->State = 0;
            // Actualizar
            $res = ($this->Repository->Update($category) != FALSE);

            if($res == TRUE){
                unset($this->Aggregate->Categories[$id]);
            }

            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \ICategoriesManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(CategoriesManagement::$_reference == NULL){
            CategoriesManagement::$_reference =
                   new \CategoriesManagement($project, $service);
        }
        return CategoriesManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \CategoriesAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso de baja de todas las referencias asociadas a una categoría
     * @param int $id Identidad de la categoría
     */
    private function RemoveReferences($id = 0){
        // Buscar las subcategorías
        $filter = ["Parent" => $id, "State" => 1];
        // Obtener todas las subcategorias
        $categories = $this->Services->GetListByFilter(
                $this->Aggregate->Categories, $filter);
        // Proces de eliminación de subcategorías
        foreach($categories as $item){
            // Actualizar la categoría actual
            $item->State = 0;
            // Actualizar el estado en bbdd
            $this->Repository->Update($item);
            // Actualizar los productos relacionados
            $products = $this->Repository->GetByFilter("Product",
                ["Category" => $item->Id, "State" => 1]);
            // Actualizar el estado en bbdd
            foreach($products as $prod){
                $prod->State = 0;
                $this->Repository->Update($prod);
            }
        }
    }
}
