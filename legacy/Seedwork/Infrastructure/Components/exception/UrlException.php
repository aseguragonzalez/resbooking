<?php

declare(strict_types=1);

/**
 * Excepcion para la gestion de errores relacionados con la url
 * de la solicitud actual
 *
 * @author alfonso
 */
class UrlException extends \BaseException {

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
