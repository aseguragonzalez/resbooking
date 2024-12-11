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
 * de aplicación para solicitudes / pedidos
 *
 * @author manager
 */
class RequestsManagement extends \BaseManagement
    implements \IRequestsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IRequestsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IRequestsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IRequestsManagement
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
        $this->Repository = RequestsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = RequestsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto para el contexto
     * @param int $service Identidad del servicio
     * @return \RequestsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(RequestsManagement::$_reference == NULL){
            RequestsManagement::$_reference =
                   new \RequestsManagement($project, $service);
        }
        return RequestsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \RequestsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual la solicitud
     * indicada mediante su identidad
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    public function GetRequest($id = 0) {
        // Carga las dependencias: Categorías, productos..
        $this->GetRequestDependences();
        // Cargar el registro de la solicitud
        $result = $this->GetRequestById($id);
        // Si la carga ha sido un éxito
        if($result == 0){
            $this->GetItemsByRequest($id);
        }
        return $result;
    }

    /**
     * Proceso para cargar en el agregado los solicitudes registradas
     * @param string $sDate Filtro opcional por fecha
     */
    public function GetRequests($sDate = ""){
        $date = NULL;
        if($sDate != ""){
            try{
                $date = new \DateTime($sDate);
            } catch (Exception $ex) {
                $date = new \DateTime("NOW");
            }
        }
        $this->Aggregate->Requests =
                $this->Repository->GetRequestsByDate($date);
    }

    /**
     * Proceso para cargar en el agregado las solicitudes pendientes
     */
    public function GetRequestsPending(){
        $filter = ["Project" => $this->IdProject, "WorkFlow" => NULL];
        $this->Aggregate->Requests =
                $this->Repository->GetByFilter("Request", $filter);
    }

    /**
     * Proceso de registro o actualización de la solicitud
     * @param \Request $request Referencia a la solicitud
     * @return array Códigos de operación
     */
    public function SetRequest($request = NULL) {

        $result = $this->Services->Validate($request);

        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($request->Id == 0){
                $res = $this->Repository->Create($request);
                $result[] = ($res != FALSE) ? 0 : -1;
                $request->Id = ($res) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($request);
                $result[] = ($res != FALSE) ? 0 : -2;
            }

            if($res != FALSE){
                $this->Aggregate->Requests[$request->Id] = $request;
            }
        }
        return $result;
    }

    /**
     * Proceso de eliminación de una categoría mediante su Identidad
     * @param int $id Identidad de la categoría
     * @return int Código de operación
     */
    public function RemoveRequest($id = 0) {
        // Cargamos la información de la solicitud y eliminamos los items
        // asociados a la misma
        if($this->GetRequestById($id) == 0
                && $this->RemoveItemsByRequest($id) == 0){
            // Actualizar el estado
            $this->Aggregate->Request->State = 0;
            // Guardar cambios
            $res = ($this->Repository->Update($this->Aggregate->Request) != FALSE);
            // Modificar la información en el agregado
            if($res && isset($this->Aggregate->Requests[$id])){
                unset($this->Aggregate->Requests[$id]);
            }
            $this->Aggregate->Request = NULL;
            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso para actualizar el estado de la solicitud indicada
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del estado de workflow
     * @return int Código de operación
     */
    public function SetState($id = 0, $state = 0) {

        if($this->GetRequestById($id) != 0){
            return -1;
        }

        if(!$this->Services->ValidateChangeState(
                $this->Aggregate->Request->WorkFlow, $state)){
            return -2;
        }

        $this->Aggregate->Request->WorkFlow = $state;

        if($this->Repository->Update($this->Aggregate->Request) == FALSE){
            return -3;
        }

        $this->Aggregate->Requests[$id] = $this->Aggregate->Request;

        return 0;
    }

    /**
     * Carga la información de categorías y productos del proyecto
     */
    private function GetRequestDependences(){
        // Cargamos todos los productos y categorías
        $filter = ["Project" => $this->IdProject ];
        $this->Aggregate->Products =
                $this->Repository->GetByFilter( "Product", $filter );
        $this->Aggregate->Categories =
                $this->Repository->GetByFilter( "Category", $filter );
    }

    /**
     * Carga el registro de la solicitud filtrada por su identidad
     * @param int $id Identidad del registro
     * @return int Código de operación
     */
    private function GetRequestById($id = 0){
        // Buscamos la información en la lista de solicitudes del agregado
        $this->Aggregate->Request =
                $this->Services->GetById($this->Aggregate->Requests, $id);
        // Si no se ha encontrado, buscamos en base de datos
        if($this->Aggregate->Request instanceof \Request == FALSE){
            $this->Aggregate->Request = $this->Repository->Read("Request", $id);
        }
        // Retornamos el código de operación
        return ($this->Aggregate->Request instanceof \Request) ? 0 : -1;
    }

    /**
     * Carga en el agregado la lista de detalles de la
     * solicitud especificada
     * @param int $id Identidad de la solicitud
     */
    private function GetItemsByRequest($id = 0){
        $filter = ["Request" => $id ];

        $this->Aggregate->Items =
                $this->Repository->GetByFilter( "RequestItem", $filter );
    }

    /**
     * Proceso de baja de los registros de detalle del solicitud
     * @param int $id Identidad de la solicitud
     * @return int Código de operación
     */
    private function RemoveItemsByRequest($id = 0){
        $results = [];
        $this->GetItemsByRequest($id);
        foreach($this->Aggregate->Items as $item){
            $results[] = $this->RemoveItemById($item->Id);
        }
        $err = array_filter($results, function($item){ return $item != 0; });
        return (count($err) != 0) ? -1 : 0;
    }

    /**
     * Proceso de baja de un registro de detalle
     * @param int $id Identidad del registro
     * @return int Código de operación
     */
    private function RemoveItemById($id = 0){
        $item = NULL;
        if(count($this->Aggregate->Items) == 0){
            $filter = [ "Id" => $id, "State"  => 1];
            $item = $this->Repository->GetByFilter( "RequestItem", $filter );
        }
        else{
            $item = $this->Services->GetById($this->Aggregate->Items, $id);
        }
        if($item != NULL){
            return ($this->Repository->Update($item) != FALSE)
                    ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso para establecer el estado de una solicitud
     * @param int $id Identidad de la solicitud
     * @param int $state Identidad del nuevo estado
     * @return \Request Referencia a la solicitud
     */
    public function SetRequestState($id = 0, $state = 0){
        $request = $this->GetById($this->Aggregate->Requests, $id);
        if($request != NULL){
            $request->WorkFlow = $state;
            return $request;
        }
        return NULL;
    }
}
