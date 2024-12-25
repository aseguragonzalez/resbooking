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
 * Declaración del contrato(Interface) para el gestor de la capa
 * de aplicación para Reservas
 *
 * @author alfonso
 */
interface IBookingManagement
{
    /**
     * Registro de la reserva
     * @param \Booking $entity Referencia a la entidad
     * @param boolean $saveClient Validación registrar los datos del cliente
     * @param boolean $sendNotification Flag para indicar si se envía notificación
     * @return array Resultado de la operación
     */
    public function RegisterBooking(
        $entity = null,
        $saveClient = false,
        $sendNotification = true
    );

    /**
     * Actualización de la información de una reserva
     * @param int $id Identida de la reserva a modificar
     * @param string $propName Nombre de la propiedad
     * que se desea actualizar
     * @return int Código de operación :
     *   0 : La operación de ha ejecutado correctamente.
     *  -1 : No se ha encontrado la reserva por su Id
     *  -2 : Se ha producido un error durante la actualización
     */
    public function SavePropertyBooking(
        $id = 0,
        $propName = "",
        $propValue = null
    );

    /**
     * Proceso de anulación de la reserva
     * @param int $id Identidad de la reserva
     * @param int $state Identidad del estado de cancelación
     * @return int Código de operación :
     *   0 => La operación se ha ejecutado correctamente
     *  -1 => La reserva no ha sido encontrada
     *  -2 => La reserva no se ha podido actualizar
     *  -3 => La notificación no se ha podido generar
     *  -4 => No se ha encontrado el estado identificado por id
     */
    public function CancelBooking($id = 0, $state = 0);

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @param string $sDate Fecha para la que se solicita la información
     * @return \BookingAgregate
     */
    public function GetAggregate($sDate = "");

    /**
     * Obtiene la informción de una reserva a partir de su identidad
     * @param int $id Identidad de la reserva
     * @return \Booking Referencia encontrada
     */
    public function GetBookingById($id = 0);

    /**
     * Obtiene la colección de reservas filtradas por fecha
     * @param string $sDate Fecha de las reservas
     * @return array Colección de reservas disponibles
     */
    public function GetBookingsByDate($sDate = "");

    /**
     * Obtiene la colección de reservas utilizando el filtro
     * pasado como argumento
     * @param array $filter Filtro de búsqueda
     * @return array Colección de reservas encontradas
     */
    public function GetBookingsByFilter($filter = null);

    /**
     * Obtiene una instancia del Management de reservas
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingManagement
     */
    public static function GetInstance($project = 0, $service = 0);
}
