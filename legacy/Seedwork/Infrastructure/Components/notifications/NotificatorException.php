<?php

declare(strict_types=1);

/**
 * Excepciones específica en el envío de emails
 *
 * @author alfonso
 */
class NotificatorException extends \BaseException{

    public function __construct($message = "", $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
