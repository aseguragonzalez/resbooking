<?php

declare(strict_types=1);

/**
 * Excepciones específica en la ejecución de INSERT, UPDATE, DELETE...
 *
 * @author alfonso
 */
class StmtClientExecuteException extends \BaseException{

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
