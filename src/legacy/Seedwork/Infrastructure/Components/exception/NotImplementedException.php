<?php

declare(strict_types=1);

/**
 * Excepción para la gestión de errores por la no implementación de
 * [métodos|funciones]
 *
 * @author alfonso
 */
class NotImplementedException extends \BaseException {

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
