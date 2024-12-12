<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicios para la gestión de categorías
 */
interface ICategoriesServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \ICategoriesServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null);

    /**
     * Proceso de validación de categorías
     * @param \Category $entity Referencia a la categoría a validar
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null);
}
