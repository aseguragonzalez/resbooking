<?php

declare(strict_types=1);

/**
 * Excepción en la gestión de errores de la factoría de contraseñas
 *
 * @author alfonso
 */
class PasswordFactoryException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de excepción
     * @param int $code Código de excepción
     * @param Exception $previous Referencia a la excepción original
     */
    public function __construct($message, $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
