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
 * Agregado para la configuración de línea base
 *
 * @author manager
 */
class BaseLineAggregate extends \BaseAggregate{

    /**
     * Referencia al Slot actual
     * @var \SlotConfigured
     */
    public $Slot = NULL;

    /**
     * Coleccion de Slots configurados
     * @var array
     */
    public $Slots = [];

    /**
     * Coleccion de turnos de reparto
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Coleccion de turnos de reparto activos
     * @var array
     */
    public $AvailableSlotsOfDelivery = [];

    /**
     * Coleccion de dias de la semana
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Slot = new \SlotConfigured();
    }

    /**
     * Configuracion del agregado
     */
    public function SetAggregate() {
        $this->AvailableSlotsOfDelivery =
                array_filter($this->SlotsOfDelivery,function($item){
                   return $item->State == 1;
                });
    }
}
