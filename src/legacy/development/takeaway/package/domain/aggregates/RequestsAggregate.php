<?php

/*
 * Copyright (C) 2015 manager
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
 * Agregado para la gestión de solicitudes/pedidos
 */
class RequestsAggregate extends \BaseAggregate{

    /**
     * Colección de horas del día
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Colección de descuentos disponibles
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de estados del flujo de trabajo registrados
     * @var array
     */
    public $States = [];

    /**
     * Colección de las categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de los productos referenciados en la solicitud
     * @var array
     */
    public $Products = [];

    /**
     * Referencia a la solicitud cargada
     * @var \Request
     */
    public $Request = NULL;

    /**
     * Referencia a la información del proyecto para impresión
     * @var \ProjectInformation
     */
    public $ProjectInformation = NULL;

    /**
     * Colección de Productos parametrizados en la solicitud
     * @var array
     */
    public $Items = [];

    /**
     * Colección de solicitudes disponibles
     * @var array
     */
    public $Requests = [];

    /**
     * Constructor
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        $this->IdProject = $idProject;
        $this->IdService = $idService;
        $this->Request = new \Request();
        $this->ProjectInformation = new \ProjectInformation();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }
}
