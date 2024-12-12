<?php

declare(strict_types=1);

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
