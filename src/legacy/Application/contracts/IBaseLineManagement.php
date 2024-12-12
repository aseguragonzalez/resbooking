<?php

declare(strict_types=1);

/**
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para configuración de línea base
 * @author manager
 */
interface IBaseLineManagement {

    /**
     * Proceso para cargar en el agregado la información del Slot
     * de configuración indicado mediante su identidad
     * @param int $id Identidad del registro de configuración
     * @return int Código de operación
     */
    public function GetSlot($id = 0);

    /**
     * Proceso para almacenar la información de un registro de configuración
     * @param \SlotConfiguration $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = null);

    /**
     * Proceso para eliminar un registro de configuración
     * @param int $id Identidad del slot
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de gestión de línea base
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
