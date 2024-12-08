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

class Test_BaseClass{

    /**
     * Identidad del servicio
     * @var int
     */
    public $IdService = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $IdProject = 0;

    /**
     * Coleccion de test realizados
     * @var array
     */
    public $Tests = [];

    /**
     * @ignore
     * Constructor de la clase
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        $this->IdProject = $idProject;
        $this->IdService = $idService;
    }

    /**
     * Ejecucion de todos los test definidos
     * @param object $xml Nodo xml para el test del Management
     * @return array Coleccion de pruebas realizadas
     */
    public function Test($xml = NULL){
        return $this->Tests;
    }
}
