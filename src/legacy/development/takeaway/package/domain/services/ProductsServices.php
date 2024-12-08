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
 * Implementación de la capa de servicios para la gestión de productos
 */
class ProductsServices extends \BaseServices implements \IProductsServices{

    /**
     * Referencia
     * @var \IProductsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IProductsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \ProductsAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \ProductsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = ProductsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ProductsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(ProductsServices::$_reference == NULL){
            ProductsServices::$_reference = new \ProductsServices($aggregate);
        }
        return ProductsServices::$_reference;
    }

    /**
     * Obtiene el link de un producto a partir de su nombre
     * @param string $name Nombre del producto
     * @return string
     * @throws Exception Excepción generada si el nombre
     * de producto no es válido
     */
    public function GetLinkProduct($name = ""){
        if(empty($name)){
            return str_replace(" ", "-",
                    strtolower(urlencode(trim($name))));
        }
        throw new Exception("GetLinkProduct: El nombre de producto "
                . "no puede ser una cadena vacía");
    }

    /**
     * Proceso de validación del producto
     * @param \Product $entity
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateDesc($entity->Description);
            $this->ValidateKeywords($entity->Keywords);
            $this->ValidateCategory($entity->Category);
            $this->ValidateReference($entity->Id, $entity->Reference);
            $this->ValidatePrice($entity->Price);
            $this->ValidateOrd($entity->Ord);
            $this->ValidateAttr($entity->Attr);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validacion de la imagen
     * @param \Image $image Referencia al objeto imagen a crear
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function ValidateImage($image = NULL){
        if($image != NULL){
            $this->ValidateImageProduct($image->Product);
            $this->ValidateImageName($image->Id, $image->Name);
            $this->ValidateImageDescription($image->Description);
            $this->ValidateImagePath($image->Path);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del nombre de producto
     * @param int $id Identidad del producto
     * @param string $name Nombre del producto
     */
    private function ValidateName($id = 0, $name = ""){
        if(empty($name)){
            $this->Result[] = -4;
        }
        elseif(strlen($name) > 100){
            $this->Result[] = -5;
        }
        else{
            $this->ValidateExistsName($id, $name);
        }
    }

    /**
     * Validación del nombre de producto por si es único o no
     * @param string $name Nombre del producto
     */
    private function ValidateExistsName($id = 0, $name = ""){
        $filter = [ "Project" => $this->IdProject,
            "Name" => $name, "State" => 1 ];

        $items = $this->GetListByFilter(
                    $this->Aggregate->Products, $filter);
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -6;
        }
    }

    /**
     * Proceso de validación de la descripción
     * @param string $description Descripción del producto
     */
    private function ValidateDesc($description = ""){
        if(empty($description)){
            $this->Result[] = -7;
        }
        elseif(strlen($description) > 140){
            $this->Result[] = -8;
        }
    }

    /**
     * Proceso de validación de los keywords de producto
     * @param string $keywords Keywords introducidos
     */
    private function ValidateKeywords($keywords = ""){
        if(empty($keywords)){
            $this->Result[] = -9;
        }
        elseif(strlen($keywords) > 140){
            $this->Result[] = -10;
        }
    }

    /**
     * Proceso de validación de la categoría
     * @param int $category Identidad de la categoría seleccionada
     */
    private function ValidateCategory($category = 0){
        $filter = [ "Project" => $this->IdProject,
            "Id" => $category, "State" => 1 ];
        $items = $this->GetListByFilter(
                    $this->Aggregate->Categories, $filter);
        if(empty($items) || count($items) == 0 ){
            $this->Result[] = -11;
        }
    }

    /**
     * Proceso de validación de la referencia de producto
     * @param int $id Identidad del producto
     * @param string $reference Referencia asignada al producto
     */
    private function ValidateReference($id = 0, $reference = ""){
        if(empty($reference)){
            $this->Result[] = -12;
        }
        elseif(strlen($reference) > 20){
            $this->Result[] = -13;
        }
        else{
            $this->ValidateExistsReference($id, $reference);
        }
    }

    /**
     * Validación clave Unique de la referencia de producto
     * @param int $id Identidad del producto
     * @param string $reference Referencia asignada al producto
     */
    private function ValidateExistsReference($id = 0, $reference = ""){
        $filter = [ "Project" => $this->IdProject,
            "Reference" => $reference, "State" => 1 ];

        $items = $this->GetListByFilter(
                    $this->Aggregate->Products, $filter);
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -14;
        }
    }

    /**
     * Proceso de validación del precio de producto
     * @param string $price Precio asignado al producto
     */
    private function ValidatePrice($price = ""){
        if(empty($price)){
            $this->Result[] = -15;
        }
        elseif(!is_numeric($price)){
            $this->Result[] = -16;
        }
    }

    /**
     * Proceso de validación del orden de producto
     * @param string $ord Criterio de orden asignado al producto
     */
    private function ValidateOrd($ord = ""){
        if(empty($ord)){
            $this->Result[] = -17;
        }
        elseif(!is_numeric($ord)){
            $this->Result[] = -18;
        }
    }

    /**
     * Proceso de validación de los atributos del producto
     * @param string $attr Cadena de atributos (Serialización JSON)
     */
    private function ValidateAttr($attr = ""){
        if(empty($attr)){
            $this->Result[] = -19;
        }
    }

    /**
     * Proceso de validación para el atributo producto
     * @param int $id Identidad del producto
     */
    private function ValidateImageProduct($id = 0){
        $product = $this->GetById($this->Aggregate->Products, $id);
        if($product == NULL){
            $this->Result[] = -10;
        }
    }

    /**
     * Proceso de validación del nombre de la imágen
     * @param int $id Identidad de la imagen
     * @param string $name Nombre de la imagen
     */
    private function ValidateImageName($id = 0, $name = ""){
        if(empty($name)){
            $this->Result[] = -4;
        }
        elseif(strlen($name) > 45){
            $this->Result[] = -5;
        }
        else{
            $this->ValidateImageExistsName($id, $name);
        }
    }

    /**
     * Proceso de validación del nombre. Comprueba si ya
     * existe una imagen con el mismo nombre
     * @param int $id Identidad de la imagen
     * @param string $name Nombre de la imagen
     */
    private function ValidateImageExistsName($id = 0, $name = ""){
        $filter = [ "Name" => $name, "State" => 1 ];
        $items = $this->GetListByFilter(
                    $this->Aggregate->Images, $filter);
        if(isset($items) && is_array($items)
                && count($items) != 0 && $items[0]->Id != $id){
            $this->Result[] = -6;
        }
    }

    /**
     * Proceso de validación de la descripción de la imagen
     * @param type $description
     */
    private function ValidateImageDescription($description = ""){
        if(empty($description)){
            $this->Result[] = -7;
        }
        elseif(strlen($description) > 200){
            $this->Result[] = -8;
        }
    }

    /**
     * Proceso de validación del path de fichero
     * @param type $path
     */
    private function ValidateImagePath($path = ""){
        if(empty($path)){
            $this->Result[] = -9;
        }
    }
}
