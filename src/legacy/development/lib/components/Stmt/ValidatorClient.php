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
 * Implementación de la interfaz IValidatorClient
 *
 * @author alfonso
 */
class ValidatorClient implements \IValidatorClient{

    /**
     * Validación de la entidad
     * @var \IValidatorClient $_singleton
     * Referencia al cliente de validación
     */
    private static $_singleton = null;

    /**
     * Validación de la entidad
     * @var boolean $_isConfigure Estado de configuración
     */
    private $_isConfigure = false;

    /**
     * Constructor por defecto
     */
    private function __construct(){

    }

    /**
     * Validación de la entidad
     * @var object $entity Referencia a la entidad a validar
     */
    public function Validate($entity = null){
        return array();
    }

    /**
     * Configuración del validador con el xml de base de datos
     * @var string $fileName Nombre del fichero de configuración
     */
    public function Configure($fileName = ""){
        $this->_isConfigure = true;
    }

    /**
     * Obtiene una referencia al validador de entidades
     * @var string $fileName Nombre del fichero de configuración
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya hay una instancia
        if(ValidatorClient::$_singleton == null){
            ValidatorClient::$_singleton = new \ValidatorClient();
        }

        if($fileName != ""){
            ValidatorClient::$_singleton->Configure($fileName);
        }

        return ValidatorClient::$_singleton;
    }
}
