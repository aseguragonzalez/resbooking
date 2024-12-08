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
 * Interfaz de la capa de infrastructura para la gestión de descuentos
 *
 * @author alfonso
 */
interface IDiscountsRepository {

    /**
     * Proceso para obtener la colección de descuentos registrados activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts();

    /**
     * Proceso para obtener la información de un descuento filtrado por su Id
     * @param int $id Identidad del descuento
     * @return \DiscountDTO Referencia al DTO
     */
    public function GetDiscountById($id = 0);
}
