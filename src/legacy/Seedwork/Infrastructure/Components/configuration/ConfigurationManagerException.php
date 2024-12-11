<?php

declare(strict_types=1);

/**
 * Excepción para la gestión del fichero de configuración
 *
 * @author alfonso
 */
class ConfigurationManagerException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param Exception $previous Excepción previa
     */
    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
