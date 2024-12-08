<?php

/*
 * Copyright (C) 2015 alfonso
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
 * Interfaz de la capa de infraestructura para reservas
 *
 * @author alfonso
 */
interface IBookingRepository {

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingRepository
     */
    public static function GetInstance($project = 0, $service = 0);

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseAggregate
     */
    public function GetAggregate($project = 0, $service = 0);

    /**
     * Obtiene la referencia a la entidad cliente de la reserva
     * @param \Booking $entity Referencia a la reserva actual
     * @param boolean $advertising Flag para indicar si el cliente quiere publicidad
     * @return int Identidad del cliente
     */
    public function GetClient($entity = NULL, $advertising = FALSE);

    /**
     * Genera el registro de notificación de una reserva
     * @param \Booking $entity Referencia a la reserva
     * @param string $subject Asunto de la notificación
     * @return boolean Resultado del registro
     */
    public function CreateNotification($entity = NULL, $subject = "");
}
