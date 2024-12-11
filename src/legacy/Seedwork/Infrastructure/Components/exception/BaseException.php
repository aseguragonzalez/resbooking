<?php

declare(strict_types=1);

/**
 * Clase base para las excepciones
 *
 * @author alfonso
 */
class BaseException extends \Exception {

    /**
     * Constructor
     * @param string $message Mensaje de error
     * @param int $code Código del error
     * @param \Exception $previous Excepción original
     */
    public function __construct($message = "",
            $code = 0, \Exception $previous = null) {

        if(($code != 0)&&($previous != null)){
            parent::__construct($message, $code, $previous);
        }

        if(($code == 0)&&($previous != null)){
            parent::__construct($message, $code, $previous);
        }

        if(($code != 0)&&($previous == null)){
            parent::__construct($message, $code);
        }

        if(($code == 0)&&($previous == null)){
            parent::__construct($message);
        }
    }

    /**
     * Representación de cadena personalizada del objeto
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
