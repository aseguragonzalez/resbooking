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
    public function SetEvent($event = NULL);

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
