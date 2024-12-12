<?php

declare(strict_types=1);

/**
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación de eventos
 *
 * @author manager
 */
interface IEventsManagement {

    /**
     * Proceso para cargar en el agregado la información del evento
     * indicado mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function GetEvent($id = 0);

    /**
     * Proceso para almacenar la información del evento actual
     * @param \SlotEvent $event Referencia a la entidad
     * @return array Códigos de operación
     */
    public function SetEvent($event = null);

    /**
     * Proceso para eliminar un evento del registro
     * mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function RemoveEvent($id = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de eventos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IEventsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
