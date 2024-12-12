<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicios para la gestión de línea base
 *
 * @author alfonso
 */
interface IBaseLineServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseLineAggregate Referencia al agregado actual
     * @return \IBaseLineServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null);

    /**
     * Proceso de validación de la entidad
     * @param \SlotConfigured $entity Referencia a la entidad
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null);
}
