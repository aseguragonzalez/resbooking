<?php

declare(strict_types=1);

/**
 * Interfaz de la capa de servicios para la gestión de la configuración
 * del proyecto
 *
 * @author alfonso
 */
interface IConfigurationServices {

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \ConfigurationAggregate Referencia al agregado actual
     * @return \IConfigurationServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null);

    /**
     * Proceso de validación de la información del proyecto para la impresión
     * de tickets
     * @param \ProjectInfo $dto Referencia a la información del proyecto
     * @return true|array Colección de códigos de validación
     */
    public function ValidateInfo($dto = null);
}
