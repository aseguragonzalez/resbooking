<?php

declare(strict_types=1);

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
    public static function GetInstance($aggregate = null);

    /**
     * Proceso de validación de la solicitud
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null);

    /**
     * Proceso para el cálculo del importe total(Aplicado descuento si procede)
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe Total
     */
    public function GetTotal($entity = null);

    /**
     * Proceso para el cálculo del importe sin descuentos
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe
     */
    public function GetAmount($entity = null);

    /**
     * Proceso para la generación del Ticket de pedido
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return string Ticket del pedido
     */
    public function GetTicket($entity = null);
}
