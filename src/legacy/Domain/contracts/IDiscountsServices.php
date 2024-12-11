<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicios para la gestion de descuentos
 *
 * @author manager
 */
interface IDiscountsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \DiscountsAggregate Referencia al agregado actual
     * @return \IDiscountsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la información del descuento
     * contenida en el DTO
     * @param \DiscountDTO $dto Referencia a la información de descuento
     * @return TRUE|array Colección de códigos de validación
     */
    public function Validate($dto = NULL);

}
