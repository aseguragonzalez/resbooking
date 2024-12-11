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

// Cargar las dependencias del modelo
require_once "model/ClientsModel.php";

/**
 * Controlador para la gestión de clientes
 *
 * @author alfonso
 */
class ClientsController extends \ResbookingController{

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct(TRUE);
    }

    /**
     * Acción para listar todos los clientes registrados en un proyecto
     * @return string Vista renderizada
     */
    public function Index(){
        try{
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Cargar los clientes registrados
            $model->GetClients();
            // Procesar la vista
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Acción para obtener la información de una ficha de cliente por su id
     * @param int $id Identidad del registro de cliente
     * @return string resultado de la operación serializado
     */
    public function GetClient($id = 0){
        try{
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->GetClient($id);
            // Objeto a retornar
            $resultDTO = [
                "Error" => ($result == NULL) ,
                "Result" => $result,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetClient" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para actualizar el registro de datos de un cliente
     * @return string Vista renderizada
     */
    public function Save(){
        try{
            // Obtener datos de la entidad
            $client = $this->GetEntity("Client");


            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $model->Save($client);
            // Cargar la lista de clientes
            $model->GetClients();
            // Procesar la vista
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error actual
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Acción para ejecutar el proceso de eliminación del registro de un cliente
     * @param int $id Identidad del cliente
     * @return string resultado de la operación serializado
     */
    public function Delete($id = 0){
        try{
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->Delete($id);
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
            $obj = $this->ProcessJSONError("Delete" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para establecer la categoría VIP de un cliente
     * @param int $id Identidad del cliente
     * @return string resultado de la operación serializado
     */
    public function SetVip($id = 0){
        try{
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->SetVip($id);
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
            $obj = $this->ProcessJSONError("SetVip" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para actualizar los comentarios asociados a un cliente
     * @param int $id Identidad del cliente
     * @return string resultado de la operación serializado
     */
    public function SetNotes($id = 0){
        try{
            // Obtener parámetros de la llamada
            $notes = filter_input(INPUT_POST, "notes");
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->SetNotes($id, $notes);
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
            $obj = $this->ProcessJSONError("SetNotes" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para buscar la ficha de un cliente por su teléfono
     * @param string $phone Teléfono del cliente
     * @return string resultado de la operación serializado
     */
    public function FindClientByPhone($phone = ""){
        try{
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->GetClientByPhone($phone);
            // Objeto a retornar
            $resultDTO = [
                "Error" => !is_array($result),
                "Result" => $result,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("FindClientByPhone" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para buscar la ficha de un cliente por su nombre
     * @param string $name Nombre del cliente
     * @return string resultado de la operación serializado
     */
    public function FindClientByName($name = ""){
        try{
            $name = str_replace("%20", " ", $name);
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->GetClientByName($name);
            // Objeto a retornar
            $resultDTO = [
                "Error" => !is_array($result),
                "Result" => $result,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("FindClientByName" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Acción para buscar la ficha de un cliente por su email
     * @param string $email Email del cliente
     * @return string resultado de la operación serializado
     */
    public function FindClientByEmail($email = ""){
        try{
            // Instanciar modelo de datos
            $model = new \ClientsModel();
            // Ejecutar operación
            $result = $model->GetClientByEmail($email);
            // Objeto a retornar
            $resultDTO = [
                "Error" => !is_array($result),
                "Result" => $result,
                "Message" => ""
            ];
            // Serializar el resultado
            return $this->ReturnJSON($resultDTO);
        }
        catch(Exception $e){
            // Procesado del error
            $obj = $this->ProcessJSONError("GetClientByEmail" , $e);
            // Retornar serialización
            return $this->ReturnJSON($obj);
        }
    }

    /**
     * Override para obtener una instancia de la entidad con los datos de
     * la peticion actual
     * @param string $entityName Nombre de la entidad
     * @return Object Referencia a la instancia
     */
    public function GetEntity($entityName = "") {
        $entity = parent::GetEntity($entityName);
        if($entityName == "Client"){
            // comprobar estado del vip
            $entity->Vip = filter_input(INPUT_POST,"Vip",
                    FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            $entity->Advertising = FALSE;
        }
        return $entity;
    }
}
