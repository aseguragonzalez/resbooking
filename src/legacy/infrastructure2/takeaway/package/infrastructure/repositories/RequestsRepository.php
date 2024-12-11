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
 * Implementación de la interfaz para el repositorio de solicitudes
 *
 * @author alfonso
 */
class RequestsRepository extends \BaseRepository implements \IRequestsRepository{

    /**
     * Referencia a la clase base
     * @var \IRequestsRepository
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
     * @return \IRequestsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(RequestsRepository::$_reference == NULL){
            RequestsRepository::$_reference =
                    new \RequestsRepository($project, $service);
        }
        return RequestsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \RequestsAggregate
     */
    public function GetAggregate($projec = 0, $service = 0) {
        // Instanciar agregado de solicitudes
        $agg = new \RequestsAggregate($this->IdProject, $this->IdService);

        $agg->HoursOfDay = $this->Dao->Get("HourOfDay");

        $agg->States = $this->Dao->GetByFilter( "WorkFlow", ["State" => 1]);

        $agg->Discounts = $this->Dao->GetByFilter("DiscountOn",
                    ["Project" => $this->IdProject, "State" => 1]);

        $agg->Requests = $this->GetRequestsByDate();

        $projectsInfo = $this->Dao->GetByFilter("ProjectInformation",
            ["Project" => $this->IdProject]);

        if(count($projectsInfo)>0){
            $info = $projectsInfo[0] ;
            $info instanceof \ProjectInformation;
            $agg->ProjectInformation = $info;
        }

        return $agg;
    }

    /**
     * Carga en el agregado la colección de solicitudes filtradas por fecha.
     * Si no se especifica una fecha, se utiliza la actual
     * @param \DateTime $date Referencia a un objeto de tipo datetime
     * @return array
     */
    public function GetRequestsByDate($date = NULL){
        $array = [];
        if($date == NULL || !($date instanceof DateTime)){
            $date = new \DateTime("NOW");
        }
        $sDate = $date->format("Y-m-d");
        $filter = ["Project" => $this->IdProject, "DeliveryDate" => $sDate ];
        $requests = $this->Dao->GetByFilter( "Request", $filter );
        foreach ($requests as $item){
            $array[$item->Id] = $item;
        }
        return $array;
    }
}
