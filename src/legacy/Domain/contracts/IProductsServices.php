<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicios para la gestión de productos
 */
interface IProductsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ProductsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Obtiene el link de un producto a partir de su nombre
     * @param string $name Nombre del producto
     * @return string
     * @throws Exception Excepción generada si el nombre
     * de producto no es válido
     */
    public function GetLinkProduct($name = "");

    /**
     * Proceso de validación del producto
     * @param \Product $entity
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

    /**
     * Proceso de validacion de la imagen
     * @param \Image $image Referencia al objeto imagen a crear
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function ValidateImage($image = NULL);
}
