<?php

declare(strict_types=1);

/**
 * Declaración del contrato(Interface) para la gestión del catálogo y pedidos
 * @author manager
 */
interface IOrderManagement {

    /**
     * Proceso de registro de la solicitud
     * @param \OrderDTO $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetOrder($request = NULL);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Pedidos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IOrderManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
