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
 * Implementación del repositorio para la gestión de línea base
 *
 * @author alfonso
 */
class BaseLineRepository extends \BaseRepository implements \IBaseLineRepository{

    /**
     * Referencia a la clase base
     * @var \IBaseLineRepository
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
     * @return \IBaseLineRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(BaseLineRepository::$_reference == NULL){
            BaseLineRepository::$_reference =
                    new \BaseLineRepository($project, $service);
        }
        return BaseLineRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseLineAggregate
     */
    public function GetAggregate() {
        // Instanciar agregado
        $agg = new \BaseLineAggregate($this->IdProject, $this->IdService);
        // filtro de proyecto
        $filter = ["Project" => $this->IdProject];
        // Cargar los días de la semana
        $agg->DaysOfWeek = $this->Dao->Get("DayOfWeek");
        // Cargar los turnos de reparto registrados
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter("SlotOfDelivery", $filter);
        // Obtener turnos configurados
        $slots = $this->Dao->GetByFilter("SlotConfigured", $filter);
        // Cargar los turnos de reparto configurados
        foreach($slots as $item){
            $agg->Slots[$item->Id] = $item;
        }
        return $agg;
    }

}
