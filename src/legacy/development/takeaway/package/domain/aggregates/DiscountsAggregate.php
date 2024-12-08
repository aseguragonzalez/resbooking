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
 * Agregado para la gestion de descuentos
 *
 * @author manager
 */
class DiscountsAggregate extends \BaseAggregate{

    /**
     * Referencia al descuento en edición
     * @var \DiscountDTO
     */
    public $Discount = NULL;

    /**
     * Colección de DTOs de descuentos activos
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de días de la semana registrados
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Colección de turnos de reparto establecidos
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Constructor
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        $this->IdProject = $idProject;
        $this->IdService = $idService;
        $this->Discount = new \DiscountDTO();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }

}
