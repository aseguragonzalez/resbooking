<?php

declare(strict_types=1);

/**
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para productos
 */
interface IProductsManagement{

    /**
     * Proceso de registro o actualización de la información de una imagen
     * @param \Image $image Referencia a la imagen
     * @return array Códigos de operación
     */
    public function SetImage($image = null);

    /**
     * Proceso de eliminación de una imagen asociada a un producto
     * @param int $id Identidad de la imagen
     * @return int Código de operación
     */
    public function RemoveImage($id = 0);

    /**
     * Proceso para cargar en el agregado actual el producto
     * indicado mediante su identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function GetProduct($id = 0);

    /**
     * Proceso de registro o actualización de un producto
     * @param \Product $product Referencia al producto
     * @return array Códigos de operación
     */
    public function SetProduct($product = null);

    /**
     * Proceso de eliminación de un producto mediante su Identidad
     * @param int $id Identidad del producto
     * @return int Código de operación
     */
    public function RemoveProduct($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Productos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IProductsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
