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
 * Description of EventsOffer
 *
 * @author alfonso
 */
class OffersEventsModel extends \ResbookingModel{

    /**
     * Identidad de la oferta
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia a la oferta
     * @var \Offer
     */
    public $Entity = NULL;

    /**
     * DTO de navegación
     * @var \WeekNavDTO
     */
    public $WeekNavDTO = NULL;

    /**
     * Colección de días de la semana disponibles
     * @var array
     */
    public $Days = [];

    /**
     * Colección de días de la semana disponibles
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Colección de turnos disponibles
     * @var array
     */
    public $Turns = [];

    /**
     * Línea base de configuraciones
     * @var array
     */
    public $BaseLine = [];

    /**
     * Colección de eventos configurados
     * @var array
     */
    public $Events = [];

    /**
     * Serialización de los eventos existentes
     * @var string
     */
    public $JSONEvents = "[]";

    /**
     * Constructor
     */
    public function __construct(){

        parent::__construct();

        $this->Title = "Gestión de ofertas";

        $this->WeekNavDTO = new \WeekNavDTO();
    }

    /**
     * Carga el modelo con la información de los eventos asociados a la oferta
     * indicada para el anyo y semana solicitados
     * @param int $id Identidad de la oferta
     * @param int $year Año solicitado
     * @param int $week Semana del año
     */
    public function GetEvents($id= 0, $year = 0, $week = 0){
        $this->Id = $id;

        $this->Entity = $this->Dao->Read($id, "Offer");

        $this->CargarDiasDeLaSemana();
        $this->CargarTurnosConfigurados();
        $this->WeekNavDTO->SetWeekInfo($this->Days, $year, $week);
        $this->Days = $this->WeekNavDTO->DaysOfWeek;
        $this->DaysOfWeek = $this->WeekNavDTO->DaysOfWeek;
        $base = $this->Dao->GetByFilter("OfferConfig", ["Offer" => $id]);
        $this->BaseLine = json_encode($base);
        $filtro = ["Offer" => $id, "Week" => $this->WeekNavDTO->Current,
            "Year" => $this->WeekNavDTO->CurrentYear];
        $events = $this->Dao->GetByFilter("OfferEvent", $filtro);
        $this->Events = json_encode($events);
    }

    /**
     * Proceso de almacenamiento del estado de un evento
     * @param \OfferEvent $entity Referencia a la información del evento
     * @return int Código de la operación ejecutada
     */
    public function SetEvent($entity = NULL){
        $result = -1;
        if($entity == NULL){
            return $result;
        }

        $entity->Project = $this->Project;

         // Buscar los registros ya existentes
        $filtro = ["Project" => $entity->Project,
            "Offer" => $entity->Offer,
            "Turn" => $entity->Turn,
            "Date" => $entity->Date];

        $eventos = $this->Dao->GetByFilter("OfferEvent", $filtro);

        if(count($eventos) == 0){
            $result = $this->Dao->Create($entity);
        }
        else{
             // Eliminar los registros existentes
            foreach($eventos as $item){
                $this->Dao->Delete($item->Id, "OfferEvent");
            }
            $result = 0;
        }

        return $result;
    }

    /**
     * Método para cargar la información de los días de la semana
     */
    private function CargarDiasDeLaSemana(){
        $this->Days = $this->Dao->Get("Day");
        foreach($this->Days as $day){
            $day->ShortName = substr($day->Name, 0, 2);
            unset($day->Id);
        }
        $this->DaysH = $this->Days;
    }

    /**
     * Metodo para cargar la colección de turnos configurados en el proyecto
     */
    private function CargarTurnosConfigurados(){
        $filter = ["Project" => $this->Project];
        $dtos = $this->Dao->GetByFilter("TurnDTO", $filter);
        foreach($dtos as $dto){
            if(isset($this->Turns[$dto->Id])){
                $this->Turns[$dto->Id]->Days[] = $dto->DOW;
            }
            else{
                $dto->Days = [];
                $dto->Days[] = $dto->DOW;
                $this->Turns[$dto->Id] = $dto;
            }
        }
        foreach ($this->Turns as $turn){
            $turn->Start = substr($turn->Start,0,5);
            $turn->Days = json_encode($turn->Days);
        }
    }
}
