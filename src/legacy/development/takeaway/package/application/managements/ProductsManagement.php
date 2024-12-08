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
 * de aplicación para productos
 */
class ProductsManagement extends \BaseManagement implements \IProductsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IProductsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IProductsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IBaseLineManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = ProductsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = ProductsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IProductsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(ProductsManagement::$_reference == NULL){
            ProductsManagement::$_reference =
                   new \ProductsManagement($project, $service);
        }
        return ProductsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \ProductsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual el producto
     * indicado mediante su identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function GetProduct($id = 0) {
        $product = $this->Services->GetById(
                $this->Aggregate->Products, $id);
        if($product != NULL){
            $this->Aggregate->Product = $product;

            $this->GetImagesByProduct($id);

            return 0;
        }
        return -1;
    }

    /**
     * Proceso de registro o actualización de un producto
     * @param \Product $product Referencia al producto
     * @return array Códigos de operación
     */
    public function SetProduct($product = NULL) {

        $product->Project = $this->IdProject;

        $result = $this->Services->Validate($product);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($product->Id == 0){
                $res = $this->Repository->Create($product);
                $result[] = ($res != FALSE) ? 0 : -1;
                $product->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($product);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Products[$product->Id] = $product;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de un producto mediante su Identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function RemoveProduct($id = 0) {
        // Obtener referencia
        $product = $this->Services->GetById(
                $this->Aggregate->Products, $id);
        if($product != NULL){
            if($this->RemoveImages($id) == 0){

                $product->State = 0;

                $res = ($this->Repository->Update($product) != FALSE);

                if($res){
                    unset($this->Aggregate->Products[$id]);
                }

                return $res ? 0 : -1;
            }
        }
        return -2;
    }

    /**
     * Proceso de eliminación de una imagen asociada a un producto
     * @param int $id Identidad de la imagen
     * @return int Código de operación
     */
    public function RemoveImage($id = 0) {
        $image = NULL;

        if(count($this->Aggregate->Images) == 0){
            $filter = [ "Id" => $id, "State"  => 1];
            $images = $this->Repository->GetByFilter( "Image", $filter );
        }
        else{
            $images = $this->Services->GetById(
                    $this->Aggregate->Images, $id);
        }

        $image = current($images);

        if($image != NULL){
            $image->State = 0;
            return ($this->Repository->Update($image) != FALSE)
                    ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso de registro o actualización de la información de una imagen
     * @param \Image $image Referencia a la imagen
     * @return array Códigos de operación
     */
    public function SetImage($image = NULL) {
        $date = new \DateTime("NOW");
        $image->Date = $date->format("Y-m-d h:i:s");
        $result = $this->Services->ValidateImage($image);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($image->Id == 0){
                $res = $this->Repository->Create($image);
                $result[] = ($res != FALSE) ? 0 : -1;
            }
            else{
                $res = $this->Repository->Update($image);
                $result[] = ($res != FALSE) ? 0 : -2;
            }
        }
        return $result;
    }

    /**
     * Carga en el agregado la colección de imágenes asociadas a un producto
     * @param int $id Identidad del producto
     */
    private function GetImagesByProduct($id = 0){
        $filter = ["Product" => $id, "State"  => 1];
        $this->Aggregate->Images =
                $this->Repository->GetByFilter( "Image", $filter );
    }

    /**
     * Eliminar todas las imágenes asociadas a un producto
     * @param int $id Identidad del producto
     */
    private function RemoveImages($id = 0){
        $results = [];
        $this->GetImagesByProduct($id);
        foreach($this->Aggregate->Images as $image){
            $results[] = $this->RemoveImage($image->Id);
        }
        $err = array_filter($results, function($item){ return $item != 0; });
        return (count($err) != 0) ? -1 : 0;
    }
}
