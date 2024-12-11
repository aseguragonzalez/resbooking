<?php

declare(strict_types=1);

/**
 * Implementación de la excepción tipo para el inyector
 *
 * @author alfonso
 */
class InjectorException extends \BaseException {

    /**
     * Constructor de la clase
     * @param string $message Mensaje de error
     * @param int $code Código de error
     * @param \Exception $previous Excepción previa
     */
    public function InjectorException($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
