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
 * Modelo para la configuración de turnos de reparto
 *
 * @author manager
 */
class SlotModel extends \TakeawayModel{

    /**
     * Colección de días de la semana
     * @var array
     */
    public $DaysOfWeek = [];

    /**
     * Colección de Turnos de reparto registrados
     * @var array
     */
    public $SlotsOfDelivery = [];

    /**
     * Número de columnas de la tabla de configuraciónes
     * @var int
     */
    public $Columns = 0;

    /**
     * Serialización Json de las configuraciones
     * @var String
     */
    public $JSONSlots = "[]";

    /**
     * Colección de Turnos de reparto configurados
     * @var array
     */
    protected $SlotsConfigured = [];

    /**
     * Línea base de configuraciones (combinaciones posibles)
     * @var array
     */
    protected $BaseLine = [];

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Disponibilidad",
                "Configuración",
                "BaseLineManagement");

        $this->SetModel();
    }

    /**
     * Configura el modelo para liscar la colección de turnos
     * de reparto disponibles
     */
    public function GetSlots(){
        // Generar la línea base
        $this->CreateBaseLine();
        // Setear los slot ya configurados
        $this->SetSlotState();
        // Parsear a json
        $this->SetJson();
    }

    /**
     * Proceso de almacenamiento de un turno de reparto
     * @param \SlotConfigured $entity Referencia a la entidad e configuración
     * @return \JSonResultDTO Referencia a un dto de resultados
     */
    public function SetSlot($entity = NULL){
        $dto = NULL;
        if($entity != NULL){
            if($entity->Id == 0){
                $dto = $this->CreateSlot($entity);
            }
            else{
                $dto = $this->DeleteSlot($entity->Id);
            }
        }
        else{
            $dto = new \JsonResultDTO();
            $dto->Result = FALSE;
            $dto->Message = "La entidad no es válida.";
            $dto->Code = 200;
            $dto->Exception = NULL;
        }
        return $dto;
    }

    /**
     * Configuración estándar del modelo
     */
    protected function SetModel() {
        $this->DaysOfWeek = $this->Aggregate->DaysOfWeek;
        $this->SlotsOfDelivery = $this->Aggregate->AvailableSlotsOfDelivery;
        $this->SlotsConfigured = $this->Aggregate->Slots;
        $this->Columns = count($this->DaysOfWeek) + 1;
    }

    /**
     * Configuración de los códigos de operación
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()];
    }

    /**
     * Obtiene los mensajes de error al "leer" un turno desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado el turno solicitado"]
            ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" un turno
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "El turno no ha sido encontrado" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de un turno de reparto
     * @return array
     */
    private function GetSaveMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
            -2 => ["name" => "eResult",
                "msg" => "El turno no ha sido encontrado" ],
            -3 => ["name" => "eResult",
                "msg" => "El turno no ha sido encontrado" ]
            ];
    }

    /**
     * Configuración de un turno de reparto
     * @param \SlotConfigured $slot
     * @return \JsonResultDTO
     */
    private function CreateSlot($slot = NULL){

        $dto = new \JsonResultDTO();

        $result = $this->Management->SetSlot($slot);

        if(is_array($result) == FALSE){
            $dto->Result = FALSE;
            $dto->Code = 500;
            $dto->Exception = new Exception("Códigos de operación inválidos");
            $dto->Message = "Códigos de operación inválidos";
        }

        if(count($result) != 1 || $result[0] != 0){
            $dto->Result = FALSE;
            $dto->Error = $this->GetResultMessage(_OP_CREATE_, $result);
            $dto->Message = $dto->Error[$result];
        }
        else{
            $dto->Code = 200;
            $dto->Result = TRUE;
            $dto->Data = $slot->Id;
            $dto->Message = "La operación se ha realizado correctamente.";
        }

        return $dto;
    }

    /**
     * Proceso de eliminación de una configuración
     * @param int $id Identidad del slot a eliminar
     * @return \JsonResultDTO
     */
    private function DeleteSlot($id = 0){

        $dto = new \JsonResultDTO();

        $result = $this->Management->RemoveSlot($id);

        if(is_numeric($result) == FALSE){
            $dto->Result = FALSE;
            $dto->Code = 500;
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
     * Construye la tabla de configuraciónes
     */
    private function CreateBaseLine(){
        $this->BaseLine = [];
        foreach($this->DaysOfWeek as $day){
            foreach($this->SlotsOfDelivery as $deliveryTime){
                $o = new \SlotConfigured();
                $o->Id = 0;
                $o->Project = $this->Project;
                $o->DayOfWeek = $day->Id;
                $o->SlotOfDelivery = $deliveryTime->Id;
                $this->BaseLine[] = $o;
            }
        }
    }

    /**
     * Configura los estados de los slot para su visualización
     */
    private function SetSlotState(){
        foreach($this->SlotsConfigured as $slot){
            $base = array_filter($this->BaseLine, function ($item) use ($slot){
               return $item->DayOfWeek == $slot->DayOfWeek
                       && $item->SlotOfDelivery == $slot->SlotOfDelivery
                       && $item->Project == $slot->Project;
            });
            if(count($base) > 0){
                $item = current($base);
                $item->Id = $slot->Id;
            }
        }
    }

    /**
     * Serialización de la línea base a json
     */
    private function SetJson(){
        $this->JSONSlots = json_encode($this->BaseLine);
    }
}
