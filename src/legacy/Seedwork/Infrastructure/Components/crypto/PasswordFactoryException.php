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
 * Excepción en la gestión de errores de la factoría de contraseñas
 *
 * @author alfonso
 */
class PasswordFactoryException extends \BaseException{

    /**
     * Constructor de la clase
     * @param string $message Mensaje de excepción
     * @param int $code Código de excepción
     * @param Exception $previous Referencia a la excepción original
     */
    public function __construct($message, $code = 0,
            \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
