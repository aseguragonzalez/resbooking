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
 * de aplicación para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryManagement extends \BaseManagement
    implements \ISlotsOfDeliveryManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \ISlotsOfDeliveryServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \ISlotsOfDeliveryRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \ISlotsOfDeliveryManagement
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
        $this->Repository = SlotsOfDeliveryRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = SlotsOfDeliveryServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \ISlotsOfDeliveryManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(SlotsOfDeliveryManagement::$_reference == NULL){
            SlotsOfDeliveryManagement::$_reference =
                   new \SlotsOfDeliveryManagement($project, $service);
        }
        return SlotsOfDeliveryManagement::$_reference;
    }

    /**
     * Proceso para cargar la información del turno de reparto indicado
     * mediante su identidad en el agregado.
     * @param int $id Identidad del turno de reparto
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
     * Proceso para almacenar la información de un turno de reparto
     * @param \SlotOfDelivery $slot Referencia a la entidad a guardar
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
                $slot->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($slot);
                $result[] = ($res != FALSE) ? 0 : -2;
            }
            if($res != FALSE){
                $this->Aggregate->Slots[$slot->Id] = $slot;
            }
        }
        return $result;
    }

    /**
     * Proceso para eliminar el registro de un turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById($this->Aggregate->Slots, $id);
        if($slot != NULL){
            // Establecer el estado
            $slot->State = 0;
            // Actualizar
            $res = ($this->Repository->Update($slot) != FALSE);

            if($res){
                // Eliminar todas las entidades relacionadas
                $this->RemoveRelations($id);

                unset($this->Aggregate->Slots[$id]);
            }

            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * @ignore
     * Cargar toda la información del agregado  para el
     * proyecto y servicio indicado
     */
    private function LoadAggregate(){
        $agg = new \SlotsOfDeliveryAggregate();
        $agg->IdProject = $this->IdProject;
        $agg->IdService = $this->IdService;
        $this->Aggregate = $this->GetFromRepository($agg);
        $this->Aggregate->SetAggregate();
    }

    /**
     * Proceso de carga de los datos de agregado
     * @param \SlotsOfDeliveryAggregate $agg Referencia al agregado a completar
     * @return \SlotsOfDeliveryAggregate
     */
    private function GetFromRepository($agg = NULL){

        // Cargar las horas disponibles
        $agg->HoursOfDay = $this->Repository->
                GetByFilter( "HourOfDay", ["State" => 1] );

        $filter = ["Project" => $this->IdProject];

        $slots = $this->Repository->GetByFilter( "SlotOfDelivery", $filter );

        foreach($slots as $slot){
            $agg->Slots[$slot->Id] = $slot;
        }

        return $agg;
    }

    /**
     * Elimina todos los registros relacionados con el turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return boolean
     */
    private function RemoveRelations($id = 0){

        $filter = [ "SlotOfDelivery" => $id ];

        $slotsEvents = $this->Repository->GetByFilter( "SlotEvent", $filter );

        foreach($slotsEvents as $item){
            $this->Repository->Delete( "SlotEvent", $item->Id );
        }

        $slotsConfigured =
                $this->Repository->GetByFilter( "SlotConfigured", $filter );

        foreach($slotsConfigured as $item){
            $this->Repository->Delete( "SlotConfigured", $item->Id );
        }

        $discountsOnConfiguration =
                $this->Repository->GetByFilter( "DiscountOnConfiguration",
                        $filter );

        foreach($discountsOnConfiguration as $item){
            $this->Repository->Delete( "DiscountOnConfiguration", $item->Id );
        }

        return TRUE;
    }
}
