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

require_once("model/RequestsModel.php");
require_once("model/dtos/StateDTO.php");

/**
 * Controlador para la gestión de solicitudes
 *
 * @author manager
 */
class RequestsController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(TRUE);
    }

    /**
     * Proceso para la listar las solicitudes o cargar una específica
     * @param string $date Fecha para el filtro de solicitudes
     * @return string Vista renderizada
     */
    public function Index($date = ""){
        try{
            // Instanciar el modelo
            $model = new \RequestsModel();
            // Cargar la lista de solicitudes
            $model->GetRequests($date);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Proceso para la listar las solicitudes pendientes
     * @return string Vista renderizada
     */
    public function Pending(){
        try{
            // Instanciar el modelo
            $model = new \RequestsModel();
            // Cargar la lista de solicitudes
            $model->GetRequestsPending();
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Pending", $e);
        }
    }

    /**
     * Proceso para cargar los detalles de una solicitud
     * @param int $id Identidad de la solicitud
     * @return string Vista renderizada
     */
    public function Details($id = 0){
        try{
            // Instanciar el modelo
            $model = new \RequestsModel();
            // Cargar la lista de solicitudes
            $model->GetRequest($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Details", $e);
        }
    }

    /**
     * Acción para imprimir el ticket de venta del pedido
     * @param int $id Identidad de la solicitud
     * @return string Vista renderizada
     */
    public function Ticket($id = 0){
        try{
            // Instanciar el modelo
            $model = new \RequestsModel();
            // Cargar la lista de solicitudes
            $model->GetRequest($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Ticket", $e);
        }
    }

    /**
     * Proceso para modificar el estado de una solicitud
     * @param int? $id Identidad de la solicitud
     * @return string Vista renderizada
     */
    public function SetState(){
        try{
            $dto = $this->GetEntity("StateDTO");
            // Instanciar el modelo
            $model = new \RequestsModel();
            // Cargar la lista de solicitudes
            $model->GetRequests();
            // Cargar la lista de solicitudes
            $json = $model->SetState($dto);
            // Retornar contenido JSON
            return $this->ReturnJSON($json);
        }
        catch(Exception $e){
            // Procesado del error
            $json = $this->ProcessError("SetState", $e);

            return $this->ReturnJSON($json);
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
            $model = new \RequestsModel();
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
