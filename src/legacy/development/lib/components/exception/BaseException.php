<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
