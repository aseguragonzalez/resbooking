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

require_once("model/PendingModel.php");
require_once("model/dtos/StateDTO.php");

/**
 * Controlador para la gestión de solicitudes
 *
 * @author manager
 */
class PendingController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Proceso para la listar las solicitudes pendientes
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar el modelo
            $model = new \PendingModel();
            // Cargar la lista de solicitudes
            $model->GetRequestsPending();
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para obtener el número de pedidos pendientes
     * @param int $id Identidad del proyecto
     * @return string Serialización JSON
     */
    public function GetRequestCount($id=0){
        try{
            // Instanciar el modelo
            $model = new \PendingModel();
            // Cargar la lista de solicitudes
            $json = $model->GetRequestCount($id);
            // Retornar contenido JSON
            return $this->ReturnJSON($json);
        }
        catch(Exception $e){
            // Procesado del error
            $json = $this->ProcessError("GetRequestCount", $e);

            return $this->ReturnJSON($json);
        }
    }
}
