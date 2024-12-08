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
 * Capa de servicio para la configuración de línea base
 *
 * @author manager
 */
class BaseLineServices extends \BaseServices implements \IBaseLineServices{

    /**
     * Referencia
     * @var \IBaseLineServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IBaseLineRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \BaseLineAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \BaseLineAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = BaseLineRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \IBaseLineServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(BaseLineServices::$_reference == NULL){
            BaseLineServices::$_reference = new \BaseLineServices($aggregate);
        }
        return BaseLineServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotConfigured $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateSlot($entity->SlotOfDelivery);
            $this->ValidateDayOfWeek($entity->DayOfWeek);
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
     * Proceso de validación del slot asociado a la configuración
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
     * Proceso de validación del día de la semana seleccionado
     * @param int $dayOfWeek Día de la semana asociado
     */
    private function ValidateDayOfWeek($dayOfWeek = 0){
        if(empty($dayOfWeek)){
            $this->Result[] = -9;
        }
        else if($dayOfWeek < 1){
            $this->Result[] = -10;
        }
        else{
            $s = $this->GetById(
                    $this->Aggregate->DaysOfWeek, $dayOfWeek);
            if($s == NULL){
                $this->Result[] = -11;
            }
        }
    }
}
