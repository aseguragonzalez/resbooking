<?php

declare(strict_types=1);

/**
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para solicitudes / pedidos
 */
interface IRequestsManagement{

    /**
     * Proceso para cargar en el agregado actual la solicitud
     * indicada mediante su identidad
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    public function GetRequest($id = 0);

    /**
     * Proceso para cargar en el agregado los solicitudes registradas
     * @param string $date Filtro opcional por fecha
     */
    public function GetRequests($date = "");

    /**
     * Proceso para cargar en el agregado las solicitudes pendientes
     */
    public function GetRequestsPending();

    /**
     * Proceso de registro o actualización de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetRequest($request = null);

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveRequest($id = 0);

    /**
     * Proceso para actualizar el estado de la solicitud indicada
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del estado de workflow
     * @return int Código de operación
     */
    public function SetState($id = 0, $state = 0);

    /**
     * Obtiene una referencia al agregado del contexto
     * @return \BaseAggregate
     */
    public function GetAggregate();

    /**
     * Obtiene una instancia del Management de Pedidos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
