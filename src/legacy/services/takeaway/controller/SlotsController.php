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

require_once "model/SlotModel.php";

/**
 * Description of SlotsController
 *
 * @author alfonso
 */
class SlotsController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Obtiene la línea base de turnos configurados
     * @return String Vista renderizada
     */
    public function Index(){
        try{
            $model = new \SlotModel();

            $model->GetSlots();

            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Configuración o eliminación de la Franja de reparto
     * @return String Json
     */
    public function Set(){
        try{
            $dto = $this->GetEntity("SlotConfigured");

            $model = new SlotModel();

            $result = $model->SetSlot($dto);

            return $this->ReturnJSON($result);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Set", $e);
        }
    }

}
