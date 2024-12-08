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
 * Implementación de la interfaz para el repositorio de turnos de reparto
 *
 * @author alfonso
 */
class SlotsOfDeliveryRepository extends \BaseRepository
    implements \ISlotsOfDeliveryRepository {

    /**
     * Referencia a la clase base
     * @var \ISlotsOfDeliveryRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \ISlotsOfDeliveryRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(SlotsOfDeliveryRepository::$_reference == NULL){
            SlotsOfDeliveryRepository::$_reference =
                    new \SlotsOfDeliveryRepository($project, $service);
        }
        return SlotsOfDeliveryRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \SlotsOfDeliveryAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \SlotsOfDeliveryAggregate($this->IdProject, $this->IdService);
        // Cargar las horas disponibles
        $agg->HoursOfDay = $this->Dao->GetByFilter( "HourOfDay", ["State" => 1] );
        // filtro por proyecto
        $filter = ["Project" => $this->IdProject];
        $slots = $this->Dao->GetByFilter( "SlotOfDelivery", $filter );
        foreach($slots as $slot){
            $agg->Slots[$slot->Id] = $slot;
        }
        return $agg;
    }
}
