<?php

declare(strict_types=1);

/**
 *
 * @author manager
 */
interface ISlotsOfDeliveryManagement {

    /**
     * Proceso para cargar la información del turno de reparto indicado
     * mediante su identidad en el agregado.
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function GetSlot($id = 0);

    /**
     * Proceso para almacenar la información de un turno de reparto
     * @param \SlotOfDelivery $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = null);

    /**
     * Proceso para eliminar el registro de un turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de turnos de reparto
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \ISlotsOfDeliveryManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
