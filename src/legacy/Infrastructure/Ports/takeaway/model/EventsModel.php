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
 * Modelo para la gestión de eventos
 *
 * @author manager
 */
class EventsModel extends \TakeawayModel{

    /**
     * DTO para la navegación semanal
     * @var \WeekNavDTO
     */
    public $WeekNavDTO = NULL;

    /**
     * Colección de días de la semana disponibles
     * @var array
     */
    public $DaysOfWeek = [];

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
     * Coleccion de turnos de reparto disponibles
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Coleccion de turnos configurados
     * @var array
     */
    public $SlotsConfigured = [];

    /**
     * Serialización de los slot configurados
     * @var string
     */
    public $JSONSlots = "[]";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Eventos",
                "Configuración",
                "EventsManagement");
        $this->SetModel();
    }

    /**
     * Cargar la colección de bloqueos y configuraciones para la semana
     * y años solicitado
     * @param int $week Semana del año
     * @param int $year Año solicitado
     */
    public function GetEvents($week = 0, $year = 0){
        $this->WeekNavDTO->SetWeekInfo($this->DaysOfWeek, $year, $week);
        $this->FilterEvents();
    }

    /**
     * Proceso de almacenamiento del estado de un evento
     * @param \SlotEvent $entity Referencia a la información del evento
     * @return \JsonResultDTO Dto con el resultado de la operación
     */
    public function SetEvent($entity = NULL){
        $dto = NULL;
        if($entity != NULL){
            if($entity->Id == 0){
                $dto = $this->CreateEvent($entity);
            }
            else{
                $dto = $this->DeleteEvent($entity->Id);
            }
        }
        else{
            $dto = new \JsonResultDTO();
            $dto->Result = FALSE;
            $dto->Error = ["La entidad no es válida."];
            $dto->Message = "La entidad no es válida.";
            $dto->Code = 200;
            $dto->Exception = NULL;
        }
        return $dto;
    }

    /**
     * Configuración standard del modelo
     */
    protected function SetModel(){
        $this->WeekNavDTO = new \WeekNavDTO();
        $this->DaysOfWeek = $this->Aggregate->DaysOfWeek;
        $this->SlotsOfDelivery = $this->Aggregate->AvailableSlotsOfDelivery;
        $this->SlotsConfigured = $this->Aggregate->BaseLine;
        $this->JSONSlots = json_encode($this->SlotsConfigured);
    }

    /**
     * Configuración de los códigos de error
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()];
    }

    /**
     * Obtiene los mensajes de error al "leer" un evento desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado el evento solicitado"]
            ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" un evento
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "El evento no ha sido encontrado" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de un evento
     * @return array
     */
    private function GetSaveMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar el registro" ],
            -2 => ["name" => "eResult",
                "msg" => "El evento no ha sido encontrado" ],
            -3 => ["name" => "eResult",
                "msg" => "El evento no es válido" ]
            ];
    }

    /**
     * Configuración de un evento
     * @param \SlotEvent $slot
     * @return \JsonResultDTO
     */
    private function CreateEvent($slot = NULL){

        $dto = new \JsonResultDTO();

        $result = $this->Management->SetEvent($slot);

        if(is_array($result) == FALSE){
            $dto->Result = FALSE;
            $dto->Code = 500;
            $dto->Exception = new Exception("Códigos de operación inválidos");
            $dto->Error = ["Códigos de operación inválidos"];
            $dto->Message = "Códigos de operación inválidos";
        }

        if(count($result) != 1 || $result[0] != 0){
            $dto->Result = FALSE;
            $dto->Error = $this->GetResultMessage(_OP_CREATE_, $result);
            $dto->Message = $dto->Error[$result];
        }
        else{
            $dto->Data = $slot->Id;
            $dto->Code = 200;
            $dto->Result = TRUE;
            $dto->Message = "La operación se ha realizado correctamente.";
        }

        return $dto;
    }

    /**
     * Proceso de eliminación de un evento
     * @param int $id Identidad del evento a eliminar
     * @return \JsonResultDTO
     */
    private function DeleteEvent($id = 0){

        $dto = new \JsonResultDTO();

        $result = $this->Management->RemoveEvent($id);

        if(is_numeric($result) == FALSE){
            $dto->Result = FALSE;
            $dto->Code = 500;
            $dto->Exception = new Exception("Códigos de operación inválidos");
            $dto->Message = "Códigos de operación inválidos";
        }

        if($result!= 0){
            $dto->Result = FALSE;
            $dto->Error = $this->GetResultMessage(_OP_DELETE_, $result);
            $dto->Message = $dto->Error[$result];
        }
        else{
            $dto->Result = TRUE;
            $dto->Data = 0;
            $dto->Message = "La operación se ha realizado correctamente.";
        }

        return $dto;
    }

    /**
     * Filtrar los eventos activos en la semana correspondiente
     */
    private function FilterEvents(){
        $base = $this->Aggregate->Events;
        foreach($base as $item){
            $date = new \DateTime($item->Date);
            $item->Date = $date->format("Y-m-d");
            $item->DayOfWeek = $date->format("w");

            if($item->DayOfWeek == 0){
                $item->DayOfWeek = 7;
            }
        }
        $events = [];
        foreach($this->DaysOfWeek as $day){
            $es = array_filter($base,
                    function($item) use($day){
                        return  $item->Date == $day->Date;
            });
            if(count($es) > 0){
                $events = array_merge($events, $es);
            }
        }
        $this->JSONEvents = json_encode($events);
    }
}
