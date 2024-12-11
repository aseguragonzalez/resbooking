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

require_once "model/dtos/DateNavDTO.php";

/**
 * Modelo para la gestión de solicitudes
 *
 * @author manager
 */
class RequestsModel extends \TakeawayModel{

    /**
     * Referencia al dto de navegación por fechas
     * @var \DateNavDTO
     */
    public $DateNavDTO = NULL;

    /**
     * Colección de estados
     * @var array
     */
    public $Workflow = [];

    /**
     * Referencia a la solicitud en edición
     * @var \Request
     */
    public $Entity = NULL;

    /**
     * Referencia a la información del proyecto para impresión
     * @var \ProjectInformation
     */
    public $Information = NULL;

    /**
     * Colección de solicitudes disponibles
     * @var array
     */
    public $Entities = [];

    /**
     * Colección de items asociados a la solicitud
     * @var array
     */
    public $Items = [];

    /**
     * Importe total sin descuento
     * @var float
     */
    public $Amount = 0;

    /**
     * Valor del descuento aplicado
     * @var string
     */
    public $DiscountValue = "Sin descuento";

    /**
     * Referencia al descuento
     * @var \DiscountOn
     */
    protected $Discount = NULL;

    /**
     * Importe total
     * @var float
     */
    public $Total = 0;

    /**
     * Colección de productos del proyecto
     * @var array
     */
    protected $Products = [];

    /**
     *
     * @var \RequestsAggregate
     */
    public $Aggergate = NULL;


    /**
     * Constructor
     */
    public function __construct(){
       parent::__construct(
               "Pedidos",
               "Pedidos",
               "RequestsManagement");
       $this->SetModel();
    }

    /**
     * Carga las solicitudes filtradas por fecha
     * @param string $date Fecha utilizada en el filtro
     */
    public function GetRequests($date = ""){
        // Iniciar DTO de navegacion
        $this->DateNavDTO = new \DateNavDTO($date);
        // Proceso para carga las solicitudes
        $this->Management->GetRequests($date);
        // Cargar la lista de entidades
        $this->SetEntities();
    }

    /**
     * Cargar las solicitudes pendientes en el modelo
     */
    public function GetRequestsPending(){
        // Proceso para carga las solicitudes
        $this->Management->GetRequestsPending();
        // Cargar la lista de entidades
        $this->SetEntities();
    }

    /**
     * Carga el modelo con la información de una solicitud específica
     * @param int $id
     */
    public function GetRequest($id = 0){
        $result = $this->Management->GetRequest($id);
        // Proceso para carga la información de una solicitud
        if($result == 0){
            // configurar el modelo para la vista de detalles
            $this->SetEntity();
            // Configuración de los productos asociados
            $this->SetItems();
        }
        else{
            $this->GetResultMessage(_OP_UPDATE_, [$result]);
        }
    }

    /**
     *
     * @param type $id
     * @return \JsonResultDTO
     */
    public function GetRequestCount($id = 0){
        $filter = ["Project" => $id, "WorkFlow" => NULL];
        $requests = $this->Dao->GetByFilter("Request", $filter);
        $json = new \JsonResultDTO();
        $json->Result = TRUE;
        $json->Data = count($requests);
        $json->Error = "";
        $json->Code = 200;
        $json->Exception = NULL;
        return $json;
    }

    /**
     * Procedimiento para actualizar el estado del pedido
     * @param type $dto
     */
    public function SetState($dto = NULL){
        if($dto != NULL){
            $json = $this->UpdateState($dto);
        }
        else{
            $json = new \JsonResultDTO();
            $json->Result = FALSE;
            $json->Error = ["La entidad no es válida."];
            $json->Code = 200;
            $json->Exception = NULL;
        }
        return $json;
    }

    /**
     * Actualización del estado de la entidad y configuración del objeto
     * a serializar en la petición ajax
     * @param \Request $dto Referencia a la solicitud
     * @return \JsonResultDTO
     */
    private function UpdateState($dto = NULL){
        $json = new \JsonResultDTO();

        $result = $this->Management->SetState($dto->Id, $dto->State);

        if(is_array($result) == FALSE){
            $json->Result = FALSE;
            $json->Code = 500;
            $json->Exception = new Exception("Códigos de operación inválidos");
        }

        if($result != 0){
            $json->Result = FALSE;
            $json->Error = $this->GetResultMessage(_OP_UPDATE_, [$result]);
        }
        else{
            $json->Data = $dto;
            $json->Code = 200;
            $json->Result = TRUE;
            $json->Message = "La operación se ha realizado correctamente.";
        }
        return $json;
    }

    /**
     * @ignore
     * Establecimiento de los códigos de operación
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetUpdateMessage(),
            _OP_DELETE_ => $this->GetDeleteMessages()];
    }

    /**
     * Obtiene los mensajes de error al "leer" una solicitud desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado la solicitud"]
            ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" una solicitud
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "La solicitud no ha sido encontrado" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de una solicitud
     * @return array
     */
    private function GetSaveMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
            -2 => ["name" => "eResult",
                "msg" => "El evento no ha sido encontrado" ],
            -3 => ["name" => "eResult",
                "msg" => "El evento no es válido" ]
            ];
    }

    /**
     * Obtiene los codigos de operacion de actualizar una solicitud
     * @return type
     */
    private function GetUpdateMessage(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado la solicitud" ],
            -2 => ["name" => "eResult",
                "msg" => "No se ha actualizado el estado" ]
            ];
    }

    /**
     * Configuración estándar del modelo
     */
    protected function SetModel() {
        $this->Entity = new \Request();
        $this->Workflow = $this->Aggregate->States;
    }

    /**
     * Carga el array de solicitudes y lo configura
     */
    private function SetEntities(){
        $this->Entities = $this->Aggregate->Requests;
        foreach($this->Entities as $item){
            $item instanceof \Request;
            $item->sTicket = $this->GetCutText($item->Ticket);
            $item->sName = $this->GetCutText($item->Name);
            $item->sEmail = $this->GetCutText($item->Email);
            $item->sAddress = $this->GetCutText($item->Address);
            $item->sDeliveryTime = $this->GetHourOfDay($item->DeliveryTime);
            $item->sDiscount = $this->GetDiscountText($item->Discount);
            $item->Amount = number_format($item->Amount, 2);
            $item->Total = number_format($item->Total, 2);
        }
        usort($this->Entities, "RequestsModel::CompareRequestByDeliveryTime");
    }

    /**
     * Procedimiento para obtener la hora del día a partir de su Identidad
     * @param int $id Identidad del registro de hora
     * @return string
     */
    private function GetHourOfDay($id = 0){
        $hour = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($id){
            return $item->Id == $id;
        });

        if(count($hour)>0){
            return current($hour)->Text;
        }
        return "-";
    }

    /**
     * Configuración del modelo para la vista de detalles de la solicitud
     */
    private function SetEntity(){
        $this->Aggregate = $this->Management->GetAggregate();
        $this->Entity = $this->Aggregate->Request;
        $this->Information = $this->Aggregate->ProjectInformation;

        // Instanciar las fechas
        $date = new \DateTime($this->Entity->DeliveryDate);

        $this->Entity->sDeliveryDate =
                strftime("%A %e de %B del %Y", $date->getTimestamp());

        if($this->Entity->DeliveryMethod == 1){
            $this->Entity->sDeliveryMethod = "Recogida en local";
        }
        else{
            $this->Entity->sDeliveryMethod = "A domicilio";
        }

        if(empty($this->Entity->PostCode)){
            $this->Entity->PostCode = "No Procede";
        }

        $this->Entity->Amount = number_format($this->Entity->Amount, 2);
        $this->Entity->Total = number_format($this->Entity->Total, 2);

        $this->Items = $this->Aggregate->Items;
        $this->Products = $this->Aggregate->Products;
        $this->SetEntityState();
        $this->SetDiscount();
    }

    /**
     * Establece el valor del descuento si procede
     */
    private function SetDiscount(){
        if($this->Entity->Discount != NULL){
            $discount = $this->Entity->Discount;
            $discounts = array_filter($this->Aggregate->Discounts,
                    function($item) use($discount){
                return $item->Id == $discount;
            });
            if(count($discounts) > 0){
                $this->Discount = current($discounts);
            }
        }
        if($this->Discount != NULL){
            $this->DiscountValue = $this->Discount->Value."%";
        }
    }

    /**
     * Obtiene el valor del descuento a la solicitud
     * @param int $id
     */
    private function GetDiscountText($id = 0){
        $discounts = array_filter($this->Aggregate->Discounts,
                function($item) use ($id){
            return $item->Id == $id;
        });
        if(count($discounts) > 0){
            $value = current($discounts)->Value;
            return number_format($value, 2)." %";
        }
        else{
            return "-";
        }
    }


    /**
     * Establece el estado de la entidad
     */
    private function SetEntityState(){
        if($this->Entity->WorkFlow != NULL){
            $entity = $this->Entity;
            $states = array_filter($this->Workflow,
                    function($item) use($entity){
                return $item->Id == $entity->WorkFlow;
            });
            if(count($states) > 0){
                $this->Entity->WorkFlow = current($states)->Name;
            }
            else{
                $this->Entity->WorkFlow = "-";
            }
        }
        else{
            $this->Entity->WorkFlow = "Pendiente";
        }
    }

    /**
     * Configuración de los productos asociados al pedido actual
     */
    private function SetItems(){
        foreach($this->Items as $item){
            $products = array_filter($this->Products,
                    function($product) use($item){
                return $product->Id == $item->Product;
            });

            if(is_array($products) && count($products) > 0){
                $current = current($products);
                $item->Reference =$current->Reference;
                $item->Price = number_format($current->Price, 2);
                $item->Name = $current->Name;
            }
        }
    }

    /**
     * Metodo para comparar solicitudes mediante el tiempo de entrega
     * @param \Request $a Referencia a la solicitud A
     * @param \Request $b Referencia a la solicitud B
     * @return int
     */
    public static function CompareRequestByDeliveryTime($a, $b){
        if(!($a instanceof \Request) || !($b instanceof \Request)){
            return 0;
        }

        if($a->DeliveryTime == $b->DeliveryTime){
            return 0;
        }

        return ($a->DeliveryTime < $b->DeliveryTime) ? -1:1;
    }
}
