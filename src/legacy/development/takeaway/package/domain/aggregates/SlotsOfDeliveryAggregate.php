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
 * Argegado para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryAggregate extends \BaseAggregate{

    /**
     * Colección de horas disponibles en base de datos
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Referencia al Turno de reparto actual
     * @var \SlotOfDelivery
     */
    public $Slot = NULL;

    /**
     * Colección de turnos de reparto registrados
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de turnos activos
     * @var array
     */
    public $AvailableSlots = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Slot = new \SlotOfDelivery();
    }

    /**
     * Configuración de agregados
     */
    public function SetAggregate() {
        $this->AvailableSlots =
                array_filter($this->Slots, function($item){
                   return $item->State == 1;
                });
    }
}
