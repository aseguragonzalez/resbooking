<?php

/*
 * Copyright (C) 2015 manager
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
    public function SetRequest($request = NULL);

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
