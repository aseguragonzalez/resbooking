<?php

declare(strict_types=1);

/**
 * Excepción para la gestión de errores debidos a procesos de autenticación
 *
 * @author alfonso
 */
class UnAuthenticateException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message="", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
