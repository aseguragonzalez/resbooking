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
 * Capa de servicio para la gestión de eventos
 *
 * @author manager
 */
class EventsServices extends \BaseServices implements \IEventsServices{

    /**
     * Referencia
     * @var \IEventsServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IEventsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \EventsAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \EventsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = EventsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \EventsAggregate Referencia al agregado actual
     * @return \IEventsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(EventsServices::$_reference == NULL){
            EventsServices::$_reference = new \EventsServices($aggregate);
        }
        return EventsServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotEvent $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateSlot($entity->SlotOfDelivery);
            $this->ValidateDate($entity->Date);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del proyecto asociado
     * @param int $id Identidad del proyecto asociado
     */
    private function ValidateProject($id = 0){
        if(empty($id)){
            $this->Result[] = -4;
        }
        else if($id < 1){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del slot
     * @param int $slot Identidad del slot asociado
     */
    private function ValidateSlot($slot = 0){
        if(empty($slot)){
            $this->Result[] = -6;
        }
        else if($slot < 1){
            $this->Result[] = -7;
        }
        else{
            $s = $this->GetById(
                    $this->Aggregate->AvailableSlotsOfDelivery, $slot);
            if($s == NULL){
                $this->Result[] = -8;
            }
        }
    }

    /**
     * Proceso de validación de la fecha asociada al evento
     * @param string $sDate Fecha del evento
     */
    private function ValidateDate($sDate = ""){
        try{
            if(empty($sDate)){
                $this->Result[] = -9;
                return;
            }

            $yesterday = new \DateTime("YESTERDAY");

            $date = new \DateTime($sDate);

            if($date <= $yesterday){
                $this->Result[] = -10;
            }
        }
        catch(Exception $e){
            $this->Result[] = -11;
        }
    }
}
