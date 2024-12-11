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
 * Interfaz para el gestor de servicios del dominio
 *
 * @author alfonso
 */
interface IBookingServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Comprobación sobre la existencia de la reserva solicitada
     * @param \Booking $entity Referencia a la reserva a registrar
     * @return boolean Resultado de la comprobación. TRUE si la reserva
     * ya está registrada. FALSE en caso contrario
     */
    public function Exist($entity = NULL);

    /**
     * Obtiene una instancia para el registro de actividad
     * @param \Booking $entity Referencia a la reserva
     * @return \Log
     */
    public function GetActivity($entity = NULL);

    /**
     * Proceso de validación de la entidad Reserva
     * @param \Booking $entity Referencia a los datos de reserva
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

    /**
     * Proceso de validación del estado de la reserva
     * @param int $id Identidad del estado a validar
     * @return boolean Resultado de la validación del estado
     */
    public function ValidateState($id = 0);
}
