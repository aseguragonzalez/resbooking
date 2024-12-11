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

require_once "model/DeliveryTimeModel.php";

/**
 * Description of DeliveryTimeController
 *
 * @author alfonso
 */
class DeliveryTimeController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Obtiene la lista de turnos de reparto disponibles
     * @return String Vista renderizada
     */
    public function Index(){
        try{
            $model = new \DeliveryTimeModel();

            $model->GetDeliveryTimes();

            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso de almacenamiento de la información del turno de reparto
     * @return String Vista renderizada
     */
    public function Save(){
        try{
            $entity = $this->GetEntity("SlotOfDelivery");

            $model = new \DeliveryTimeModel();

            $model->Save($entity);

            $model->GetDeliveryTimes();

            return $this->Partial("Index", $model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Proceso de eliminación del turno de reparto
     * @param Int $id Identidad del turno a eliminar
     * @return String
     */
    public function Delete($id = 0){
        try{
            $model = new \DeliveryTimeModel();

            $model->Delete($id);

            $model->GetDeliveryTimes();

            return $this->Partial("Index", $model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Delete", $e);
        }
    }
}
