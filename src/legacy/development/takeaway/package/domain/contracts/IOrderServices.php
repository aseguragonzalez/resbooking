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
 * Interfaz de la capa de servicios para la gestión de solicitudes
 *
 * @author manager
 */
interface IOrderServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \OrderAggregate Referencia al agregado actual
     * @return \IOrderServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la solicitud
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL);

    /**
     * Proceso para el cálculo del importe total(Aplicado descuento si procede)
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe Total
     */
    public function GetTotal($entity = NULL);

    /**
     * Proceso para el cálculo del importe sin descuentos
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe
     */
    public function GetAmount($entity = NULL);

    /**
     * Proceso para la generación del Ticket de pedido
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return string Ticket del pedido
     */
    public function GetTicket($entity = NULL);
}
