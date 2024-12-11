<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicios para la gestión de turnos de reparto
 *
 * @author manager
 */
interface ISlotsOfDeliveryServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \SlotsOfDeliveryAggregate Referencia al agregado actual
     * @return \ISlotsOfDeliveryServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la entidad
     * @param \SlotOfDelivery $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

}
