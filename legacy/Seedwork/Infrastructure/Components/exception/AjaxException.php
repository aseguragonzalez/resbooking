<?php

declare(strict_types=1);

/**
 * Excepción para gestión de errores en comunicaciones Asíncronas (AJAX)
 *
 * @author alfonso
 */
class AjaxException extends \BaseException {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param Exception $previous Excepción original
     */
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
