<?php

declare(strict_types=1);

/**
 * Interfaz para el validador de entidades
 *
 * @author alfonso
 */
interface IValidatorClient{

    /**
     * Validación de la entidad
     * @param object $entity Referencia a la entidad a validar
     */
    public function Validate($entity = null);

    /**
     * Configuración del validador con el xml de base de datos
     * @param string $fileName Ruta al fichero de configuración
     */
    public function Configure($fileName = "");

    /**
     * Obtiene una referencia al validador de entidades
     */
    public static function GetInstance();
}
