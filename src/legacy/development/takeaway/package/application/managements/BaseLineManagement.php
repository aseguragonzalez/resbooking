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
 * de aplicación para configuraciones de línea base
 *
 * @author manager
 */
class BaseLineManagement extends \BaseManagement implements \IBaseLineManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IBaseLineServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IBaseLineRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IBaseLineManagement
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
        $this->Repository = BaseLineRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = BaseLineServices::GetInstance($this->Aggregate);
    }

    /**
     * Proceso para cargar en el agregado la información del Slot
     * de configuración indicado mediante su identidad
     * @param int $id Identidad del registro de configuración
     * @return int Código de operación
     */
    public function GetSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById(
                $this->Aggregate->Slots, $id);
        if($slot != NULL){

            $this->Aggregate->Slot = $slot;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información de un registro de configuración
     * @param \SlotConfiguration $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = NULL) {
        $slot->Project = $this->IdProject;
        $result = $this->Services->Validate($slot);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($slot->Id == 0){
                $res = $this->Repository->Create($slot);

                $result[] = ($res != FALSE) ? 0 : -1;

                $slot->Id = ($res != FALSE)? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($slot);

                $result[] = ($res != FALSE) ? 0 : -2;
            }
            // Actualizar la colección de slots
            if($res != FALSE){
                $this->Aggregate->Slots[$slot->Id] = $slot;
            }
        }
        return $result;
    }

    /**
     * Proceso para eliminar un registro de configuración
     * @param int $id Identidad del slot
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0) {

        $slot = $this->Services->GetById($this->Aggregate->Slots, $id);

        if($slot != NULL ){
            $result = $this->Repository->Delete("SlotConfigured", $id);
            if($result == 0){
                unset($slot);
                return 0;
            }
            return -1;
        }
        return -2;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(BaseLineManagement::$_reference == NULL){
            BaseLineManagement::$_reference =
                   new \BaseLineManagement($project, $service);
        }
        return BaseLineManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }
}
