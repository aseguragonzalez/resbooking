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
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para configuraciones de eventos
 *
 * @author manager
 */
class EventsManagement extends \BaseManagement implements \IEventsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IEventsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IEventsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IEventsManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = EventsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = EventsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IEventsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(EventsManagement::$_reference == NULL){
            EventsManagement::$_reference =
                   new \EventsManagement($project, $service);
        }
        return EventsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \EventsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado la información del evento
     * indicado mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function GetEvent($id = 0) {
        // Obtener referencia
        $event = $this->Services->GetById(
                $this->Aggregate->Events, $id);
        if($event != NULL){

            $this->Aggregate->Category = $event;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información del evento actual
     * @param \SlotEvent $event Referencia a la entidad
     * @return array Códigos de operación
     */
    public function SetEvent($event = NULL) {
        $event->Project = $this->IdProject;
        $result = $this->Services->Validate($event);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($event->Id == 0){
                $res = $this->Repository->Create($event);
                $result[] = ($res != FALSE) ? 0 : -1;
                $event->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($event);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Events[$event->Id] = $event;
            }
        }

        return $result;
    }

    /**
     * Proceso para eliminar un evento del registro
     * mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function RemoveEvent($id = 0) {
        // Obtener referencia
        $event = $this->Services->GetById(
                $this->Aggregate->Events, $id);
        if($event != NULL){

            $result = $this->Repository->Delete("SlotEvent", $id);

            if($result == 0){

                unset($event);

                return 0;
            }
            return -1;
        }
        return -2;
    }
}
