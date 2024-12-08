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

// Cargar dependencias
require_once "model/dto/DateNavDTO.php";
require_once "model/BookingModel.php";

/**
 * Controlador para la gestión de reservas
 *
 * @author alfonso
 */
class BookingController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Acción por defecto : carga la lista de reservas registradas
     * @param string $d Fecha de consulta
     * @return string Vista renderizada
     */
    public function Index($d = ""){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Cargar las reservas registradas
            $model->GetBookings($d);
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para modificar el estado de una reserva
     * @return string resultado de la operación serializada
     */
    public function SetState(){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Obtener datos de la petición
            $id = filter_input(INPUT_POST, "id");
            $state = filter_input(INPUT_POST, "state");
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Ejecutar operación
            $result = $model->ChangeState($id, $state);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result < 0) ,
                "Result" => $result ,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetState" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para actualizar el número de comensales
     * @return string resultado de la operación serializada
     */
    public function SetDiners(){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Obtener datos de la petición
            $id = filter_input(INPUT_POST, "id");
            $value = filter_input(INPUT_POST, "value");
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Ejecutar operación
            $result = $model->ChangeDiners($id, $value);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result < 0) ,
                "Result" => $result ,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetDiners" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para actualizar la información de la mesa
     * @return string resultado de la operación serializada
     */
    public function SetTable(){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Obtener datos de la petición
            $id = filter_input(INPUT_POST, "id");
            $value = filter_input(INPUT_POST, "table");
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Ejecutar operación
            $result = $model->UpdateTableInfo($id, $value);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result < 0) ,
                "Result" => $result ,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetTable" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para agrega una anotación a la reserva
     * @return string resultado de la operación serializado
     */
    public function SetNotes(){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Obtener datos de la petición
            $id = filter_input(INPUT_POST, "id");
            $notes = filter_input(INPUT_POST, "notes");
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Ejecutar operación
            $result = $model->AddNotes($id, $notes);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result < 0),
                "Result" => $result ,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SetNotes" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para enviar una notificación de tipo mensaje
     * @return string resultado de la operación serializado
     */
    public function SendMessage(){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Obtener los datos de la petición
            $to = filter_input(INPUT_POST, "to");
            $message = filter_input(INPUT_POST, "message");
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Ejecutar operación
            $result = $model->SendMessage($to, $message);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result < 0),
                "Result" => $result ,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SendMessage" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para lanzar un recordatorio
     * @param int $id Identidad de la reserva asociada
     * @return string resultado de la operación serializado
     */
    public function SendReminder($id = 0){
        try{
            // Validar proyecto del contexto
            $this->ValidateProject();
            // Instanciar modelo de datos
            $model = new \BookingModel();
            // Ejecutar operación
            $result = $model->SendReminder($id);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result < 0),
                "Result" => $result ,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("SendReminder" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para modificar el estado de una reserva
     * @return string vista renderizada
     */
    public function Cancel($ticket = ""){
        try{
            // Decodificar el ticket
            $arr = json_decode(base64_decode($ticket));

            settype($arr, "array");

            // Validación del ticket
            if($arr == NULL || !is_array($arr) || !isset($arr["Project"])){
                $this->Log->LogError( "Cancel - Error en el ticket "
                        . "de cancelación: ". $ticket);
                $model = new \BookingModel(0);

                return $this->Partial("SetFechaPasada", $model);
            }
            // Instanciar modelo de datos
            $model = new \BookingModel($arr["Project"]);
            // Ejecutar operación
            $cancel = $model->GetTicket($ticket);

            if($cancel == 1){
                return $this->Partial("SetCancel", $model);
            }

            return ($cancel == 0)
                ? $this->PartialView($model)
                :$this->Partial("SetFechaPasada", $model);
        }
        catch(Exception $e){
            // Crear traza de error
            $this->Log->LogErrorTrace( "Cancel" , $e);
            // Instanciar Modelo
            $model = new \SaasModel();
            // Renderizado de la vista de error
            return $this->Partial("error", $model);
        }
    }

    /**
     * Acción para modificar el estado de una reserva
     * @return string vista renderizada
     */
    public function SetCancel(){
        try{
            // Id de anulación
            $idAnulado = ConfigurationManager::GetKey( "anulado" );
            // Obtener ticket actual
            $ticket = strip_tags($_POST["Ticket"]);
            // Obtener el proyecto actual
            $project = strip_tags($_POST["Project"]);
            // Instanciar modelo de datos
            $model = new \BookingModel($project);
            // Ejecutar operación
            $cancel = $model->GetTicket($ticket);

            if($cancel == 0){
                // Generar log
                $this->Log->LogInfo("Reserva ".$model->Entity->Id
                        ." anulada por: ".$model->From);
                $model->ChangeState($model->Entity->Id, $idAnulado);
            }

            if($cancel == -1){
                return $this->Partial("SetFechaPasada", $model);
            }

            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Crear traza de error
            $this->Log->LogErrorTrace( "SetCancel" , $e);
            // Instanciar Modelo
            $model = new \SaasModel();
            // Renderizado de la vista de error
            return $this->Partial("error", $model);
        }
    }
}
