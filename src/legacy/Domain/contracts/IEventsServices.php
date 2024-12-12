<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicio para la gestión de eventos
 *
 * @author manager
 */
interface IEventsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \EventsAggregate Referencia al agregado actual
     * @return \IEventsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null);

    /**
     * Proceso de validación de la entidad
     * @param \SlotEvent $entity Referencia a la entidad
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null);
}
