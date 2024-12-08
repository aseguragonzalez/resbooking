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
 * Agregado para la gestión de solicitudes
 * @author manager
 */
class OrderAggregate extends \BaseAggregate{

    /**
     * Colección de categorías disponibles
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de productos disponibles
     * @var array
     */
    public $Products = [];

    /**
     * Colección de Slots
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de eventos registrados
     * @var array
     */
    public $Events = [];

    /**
     * Colección de horas disponibles
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Métodos de pago disponibles
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Métodos de entrega disponibles
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de turnos de reparto existentes
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Colección de códigos postales asociados
     * @var array
     */
    public $PostCodes = [];

    /**
     * Colección de descuentos configurados
     * @var array
     */
    public $Discounts = [];

    /**
     * Constructor
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        $this->IdProject = $idProject;
        $this->IdService = $idService;
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }

}
