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
 * Description of EventsRepository
 *
 * @author alfonso
 */
class EventsRepository extends \BaseRepository implements \IEventsRepository{

    /**
     * Referencia a la clase base
     * @var \IEventsRepository
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
     * @return \IEventsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(EventsRepository::$_reference == NULL){
            EventsRepository::$_reference =
                    new \EventsRepository($project, $service);
        }
        return EventsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \EventsAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \EventsAggregate($this->IdProject, $this->IdService);
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject];
        // Cargar los días de la semana
        $agg->Events = $this->Dao->GetByFilter("SlotEvent", $filter);
        // Cargar los turnos de reparto registrados
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter("SlotOfDelivery", $filter);
        // Cargar turnos configurados
        $agg->BaseLine = $this->Dao->GetByFilter("SlotConfigured", $filter);
        // Cargar los días de la semana disponibles
        $agg->DaysOfWeek = $this->Dao->Get("DayOfWeek");

        return $agg;
    }
}
