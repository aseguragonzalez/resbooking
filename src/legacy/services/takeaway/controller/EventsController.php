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

require_once "model/EventsModel.php";
require_once "model/dtos/WeekNavDTO.php";

/**
 * Description of EventsController
 *
 * @author alfonso
 */
class EventsController extends \TakeawayController {

    /**
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Obtiene el listado de eventos registrados
     * @return String Vista renderizada
     */
    public function Index($week = 0){
        try{
            $year = (isset($_GET["year"]) && is_numeric($_GET["year"]))
                    ? intval($_GET["year"]) : 0;

            $model = new \EventsModel();

            $model->GetEvents($week, $year);

            return $this->PartialView($model);
        }
        catch (Exception $e) {
            // Procesado del error
            return $this->ProcessError("Events", $e);
        }
    }

    /**
     * Alta o baja de un evento asociado al proyecto
     * @return String Json
     */
    public function SetEvent(){
        try{
            $entity = $this->GetEntity("SlotEvent");

            $model = new \EventsModel();

            $json = $model->SetEvent($entity);

            return $this->ReturnJSON($json);
        }
        catch (Exception $e) {
            // Procesado del error
            $json = $this->ProcessError("SetEvent", $e);

            return $this->ReturnJSON($json);
        }
    }
}
