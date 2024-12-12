<?php

declare(strict_types=1);

/**
 * Description of OrderDTO
 *
 * @author manager
 */
class OrderDTO extends \Request {

    /**
     * Colección de productos asociados al pedido
     * @var array
     */
    public $Items = [];

    /**
     * Constructor
     * @param \Request $request
     */
    public function __construct($request = null) {

        parent::__construct();

        $this->SetRequest($request);
    }

    /**
     * Obtiene una instancia de la solicitud con la información del DTO
     * @return \Request
     */
    public function GetRequest(){
        $request = new \Request();
        $request->Address = $this->Address;
        $request->Advertising = ($this->Advertising == true);
        $request->Date = $this->Date;
        $request->DeliveryDate = $this->DeliveryDate;
        $request->DeliveryMethod = $this->DeliveryMethod;
        $request->DeliveryTime = $this->DeliveryTime;
        $request->Discount = $this->Discount;
        $request->Email = $this->Email;
        $request->IP = $this->IP;
        $request->Name = $this->Name;
        $request->PaymentMethod = $this->PaymentMethod;
        $request->Phone = $this->Phone;
        $request->Project = $this->Project;
        $request->Ticket = $this->Ticket;
        $request->State = $this->State;
        $request->WorkFlow = $this->WorkFlow;
        $request->Id = $this->Id;
        $request->Total = $this->Total;
        $request->Amount = $this->Amount;
        $request->PostCode = $this->PostCode;
        return $request;
    }

    /**
     * Obtiene la colección de productos asociados a la solicitud
     * @param int $id Identidad de la solicitud
     * @return \RequestItem
     */
    public function GetRequestItems($id = 0){
        $items = [];
        if(is_array($this->Items)){
            foreach($this->Items as $item){
                $o = new \RequestItem();
                $o->Id = $item->Id;
                $o->Request = $item->Request;
                $o->Product = $item->Product;
                $o->Data = $item->Data;
                $o->Count = $item->Count;
                if($id > 0){
                    $o->Request = $id;
                }
                $items[] = $o;
            }
        }
        return $items;
    }

    /**
     * Establece las propiedades heredadas de una solicitud
     * @param \Request $request
     */
    public function SetRequest($request = null){
        if($request != null && is_object($request)){
            $this->Address = $request->Address;
            $this->Advertising = ($request->Advertising == true);
            $this->Date = $request->Date;
            $this->DeliveryDate = $request->DeliveryDate;
            $this->DeliveryMethod = $request->DeliveryMethod;
            $this->DeliveryTime = $request->DeliveryTime;
            $this->Discount = $request->Discount;
            $this->Email = $request->Email;
            $this->IP = $request->IP;
            $this->Name = $request->Name;
            $this->PaymentMethod = $request->PaymentMethod;
            $this->Phone = $request->Phone;
            $this->Project = $request->Project;
            $this->Ticket = $request->Ticket;
            $this->State = $request->State;
            $this->WorkFlow = $request->WorkFlow;
            $this->Id = $request->Id;
            $this->Amount= $request->Amount;
            $this->Total = $request->Total;
            $this->PostCode = $request->PostCode;
        }
    }

}
