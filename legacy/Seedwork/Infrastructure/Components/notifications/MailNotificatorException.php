<?php

declare(strict_types=1);

/**
 * Excepciones específica en el envío de emails de la implementacion
 * MailNotificator
 *
 * @author alfonso
 */
class MailNotificatorException extends \BaseException{
    // Redefinir la excepción, por lo que el mensaje no es opcional
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
