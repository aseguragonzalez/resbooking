<?php

declare(strict_types=1);

/**
 * Clase para el uso de excepciones genéricas en la clase de
 * acceso a datos StmtClient
 *
 * @author alfonso
 */
class StmtClientException extends \BaseException{

    /**
     * Redefinir la excepción, por lo que el mensaje no es opcional
     * @param string $message Mensaje de error
     * @param int $code Código de excepción
     * @param Exception $previous Excepción previa
     */
    public function __construct($message = "" , $code = 0,
            \Exception $previous = null) {
       parent::__construct($message, $code, $previous);
    }
}
