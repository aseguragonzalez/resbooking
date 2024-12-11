<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicio para la gestión de solicitudes/pedidos
 *
 * @author manager
 */
interface IRequestsServices{

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IRequestsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL);

    /**
     * Proceso de validación de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return boolean
     */
    public function Validate($request = NULL);

    /**
     * Proceso de validación en el cambio de estado de una solicitud
     * @param int $current Identidad del estado actual
     * @param int $next Identidad del estado próximo
     * @return boolean
     */
    public function ValidateChangeState($current = 0, $next = 0);
}
