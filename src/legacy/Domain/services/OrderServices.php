<?php

declare(strict_types=1);

/**
 * Implementación de la capa de servicios para la gestión de solicitudes
 *
 * @author manager
 */
class OrderServices extends \BaseServices implements \IOrderServices{

    /**
     * Referencia
     * @var \IOrderServices
     */
    private static $_reference = null;

    /**
     * Referencia al repositorio actual
     * @var \IOrderRepository
     */
    protected $repository = null;

    /**
     * Referencia al agregado
     * @var \OrderAggregate
     */
    protected $aggregate = null;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \OrderAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = null) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->repository = OrderRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \OrderAggregate Referencia al agregado actual
     * @return \IOrderServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null){
        if(OrderServices::$_reference == null){
            OrderServices::$_reference = new \OrderServices($aggregate);
        }
        return OrderServices::$_reference;
    }

    /**
     * Proceso para el cálculo del importe sin descuentos
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe
     */
    public function GetAmount($entity = null) {
        // Obtener la colección de productos
        $items = $entity->GetRequestItems();
        // Calcular importe
        return $this->CalculateAmount($items);
    }

    /**
     * Proceso para el cálculo del importe total(Aplicado descuento si procede)
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return float Importe Total
     */
    public function GetTotal($entity = null) {
        // Calcular total sin descuento
        $total = $this->GetAmount($entity);
        // Obtener referencia al descuento
        if(isset($entity->Discount) && $entity->Discount > 0){
            $discounts = $this->GetListByFilter(
                    $this->aggregate->Discounts, ["Id" => $entity->Discount]);
            if(!empty($discounts)){
                $discount = current($discounts);
                $discount instanceof \DiscountOn;
                $total = $total * (1 - ($discount->Value/100));
            }
        }
        return number_format($total, 2);
    }

    /**
     * Proceso para la generación del Ticket de pedido
     * @param \OrderDTO $entity Referencia a la solicitud
     * @return string Ticket del pedido
     */
    public function GetTicket($entity = null) {

        $sProject = "$this->IdProject";
        if($this->IdProject < 10){
            $sProject = "00$this->IdProject-";
        }
        elseif($this->IdProject < 100){
            $sProject = "0$this->IdProject-";
        }
        else{
            $sProject = "$this->IdProject-";
        }

        $requests = $this->repository->GetByFilter(
                "Request", ["Project" => $this->IdProject]);
        $current = count($requests);

        do{
            $current++;

            $ticket = $sProject.$this->SetTicket($current);

            $requests = $this->repository->GetByFilter(
                    "Request", ["Ticket" => $ticket]);
        }while (count($requests) > 0);

        return $ticket;
    }

    private function SetTicket($current){

        if($current < 10){
            $sCurrent = "0000$current";
        }
        elseif($current < 100){
            $sCurrent = "000$current";
        }
        elseif($current < 1000){
            $sCurrent = "00$current";
        }
        elseif($current < 10000){
            $sCurrent = "0$current";
        }
        else{
            $sCurrent = "$current";
        }
        return $sCurrent;
    }

    /**
     * Proceso de validación de la solicitud
     * @param \OrderDTO $entity Referencia al pedido
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null){
        if($entity != null){
            $this->ValidateProject($entity->Project);
            $this->ValidateTicket($entity->Ticket);
            $this->ValidateName($entity->Name);
            $this->ValidateEmail($entity->Email);
            $this->ValidatePhone($entity->Phone);
            $this->ValidateAddress($entity->Address);
            $this->ValidateDiscount($entity->Discount);
            $this->ValidateDeliveryMethod($entity->DeliveryMethod);
            $this->ValidatePaymentMethod($entity->PaymentMethod);
            $this->ValidateDeliveryDate($entity->DeliveryDate);
            $this->ValidateDeliveryTime($entity->DeliveryTime);
            $this->ValidateItems($entity->Items);
            $this->ValidateRequest($entity);
            $this->ValidatePostCode($entity->PostCode,$entity->DeliveryMethod );
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? true : $this->Result;
    }



    /**
     * Calcula el importe neto del pedido utilizando la
     * lista de productos asociados
     * @param array $items Colección de productos asociados
     * @return float
     */
    private function CalculateAmount($items = null){
        $amount = 0;
        foreach($items as $item){
            $product = $this->GetById($this->aggregate->Products,
                    $item->Product);
            if($product != null){
                $amount += $item->Count * $product->Price;
            }
        }
        return number_format($amount, 2);
    }

    /**
     * Validación del proyecto seleccionad
     * @param int $id
     */
    private function ValidateProject($id = 0){
        if(empty($id) || $id == 0){
            $this->Result[] = -4;
        }
    }

    /**
     * Proceso de validación del ticket de solicitud
     * @param string $ticket ticket de solicitud
     */
    private function ValidateTicket($ticket = ""){
        if(empty($ticket)){
            $this->Result[] = -5;
        }
        elseif(strlen($ticket) > 45){
            $this->Result[] = -6;
        }
    }

    /**
     * Validación del nombre de cliente
     * @param string $name Nombre del cliente
     */
    private function ValidateName($name = ""){
        if(empty($name)){
            $this->Result[] = -7;
        }
        elseif(strlen($name) > 200){
            $this->Result[] = -8;
        }
    }

    /**
     * Validación del e-mail de contacto
     * @param string $email Dirección de email de contacto
     */
    private function ValidateEmail($email = ""){
        if(empty($email)){
            $this->Result[] = -9;
        }
        elseif(strlen($email) > 100){
            $this->Result[] = -10;
        }
        elseif(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
            $this->Result[] = -11;
        }
    }

    /**
     * Validación de la dirección de entrega
     * @param string $addr Dirección de entrega del pedido
     */
    private function ValidateAddress($addr = ""){
        if(empty($addr)){
            $this->Result[] = -12;
        }
        elseif(strlen($addr) > 500){
            $this->Result[] = -13;
        }
    }

    /**
     * Validación del descuento asociado (si corresponde)
     * @param int $id Identidad del descuento
     * @return void
     */
    private function ValidateDiscount($id = null){

        if($id == null){
            return;
        }

        if($id == 0){
            $this->Result[] = -14;
        }
        else{
            $discounts = array_filter($this->aggregate->Discounts,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($discounts) == 0){
                $this->Result[] = -15;
            }
        }
    }

    /**
     * Validación del método de entrega seleccionado
     * @param int $id Identidad del método de entrega
     */
    private function ValidateDeliveryMethod($id = 0){
        if($id == 0){
            $this->Result[] = -16;
        }
        else{
            $list = array_filter($this->aggregate->DeliveryMethods,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($list) == 0){
                $this->Result[] = -17;
            }
        }
    }

    /**
     * Validación del método de pago seleccionado
     * @param int $id Identidad del método de pago
     */
    private function ValidatePaymentMethod($id = 0){
        if($id == 0){
            $this->Result[] = -18;
        }
        else{
            $list = array_filter($this->aggregate->PaymentMethods,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($list) == 0){
                $this->Result[] = -19;
            }
        }
    }

    /**
     * Validación de la hora de entrega seleccionada
     * @param int $id Identidad de la hora de entrega
     * @param int $dayOfWeek Identidad del día de la semana
     */
    private function ValidateDeliveryTime($id = 0, $dayOfWeek = 0){
        if($id == 0){
            $this->Result[] = -20;
        }
        else{
            $list = array_filter($this->aggregate->HoursOfDay,
                    function($item) use ($id){ return $item->Id == $id; });
            if(count($list) == 0){
                $this->Result[] = -21;
            }
        }
    }

    /**
     * Proceso de validación del teléfono de contacto
     * @param string $phone Teléfono asociado
     */
    private function ValidatePhone($phone = ""){
        if(empty($phone)){
            $this->Result[] = -22;
        }
        elseif(strlen($phone) > 15){
            $this->Result[] = -23;
        }
    }

    /**
     * Validación de los productos seleccionados
     * @param array $items colección de productos asociados al pedido
     */
    private function ValidateItems($items = null){
        if($items == null || !is_array($items) || count($items)==0){
            $this->Result[] = -24;
        }
        else{
            $error = false;
            foreach($items as $item){
                $product = $this->GetById($this->aggregate->Products,
                    $item->Product);
                if($product == null){
                    $error = true;
                }
            }
            if($error == true){
                $this->Result[] = -31;
            }
        }
    }

    /**
     * Validación si ya existe un registro de pedido
     * @param \OrderDTO $request Referencia al DTO de pedido
     */
    private function ValidateRequest($request = null){
        $filter = [
            "DeliveryDate" => $request->DeliveryDate,
            "DeliveryTime" => $request->DeliveryTime,
            "Email" => $request->Email,
            "Name" => $request->Name,
            "Phone" => $request->Phone,
            "Project" => $request->Project
        ];

        $requests = $this->repository->GetByFilter("Request", $filter);

        if(is_array($requests) && count($requests) != 0){
            $this->Result[] = -25;
        }
    }

    /**
     * Proceso de validación del código postal asociado
     * @param string $postcode Código postal para el pedido
     * @param int $delivery Tipo de método de entrega
     */
    private function ValidatePostCode($postcode = "", $delivery = 0){
        // Comprobación si el método de entrega no es a domicilio
        if($delivery != 2){
            return;
        }

        if(empty($postcode)){
            $this->Result[] = -26;
        }
        elseif(strlen($postcode) > 6){
            $this->Result[] = -27;
        }
        elseif(!is_numeric($postcode)){
            $this->Result[] = -28;
        }
        else{
            $postcodes = array_filter($this->aggregate->PostCodes,
                    function($item) use ($postcode){
                        return $item->Code == $postcode;
                    });
            if(count($postcodes) != 1){
                $this->Result[] = -35;
            }
        }
    }

    /**
     * Proceso de validación del importe neto del pedido
     * @param float $amount Importe del pedido
     * @param array $items Colección de productos asociados
     */
    private function ValidateAmount($amount = 0, $items = null){
        if($amount <= 0){
            $this->Result[] = -29;
        }
        elseif($items != null && is_array($items) && count($items) !=0){
            $temp = $this->CalculateAmount($items);
            if($temp != $amount){
                $this->Result[] = -30;
            }
        }


    }

    /**
     * Proceso de validación del importe TOTAL del pedido
     * @param float $total Importe total del pedido
     * @param float $amount Importe neto del pedido
     * @param int $discount Referencia al descuento aplicable
     */
    private function ValidateTotal($total = 0, $amount = 0, $discount = null){
        if($total == 0){
            $this->Result[] = -32;
        }
        elseif(($discount == null || $discount == 0)
                && ($total != $amount)){
            $this->Result[] = -33;
        }
        else {
            $disc = $this->GetById($this->aggregate->Discounts, $discount);
            $temp = 0;
            if($disc != null){
                $temp = $amount * (1 - ($disc->Value/100));
            }
            if($temp != $total){
                $this->Result[] = -34;
            }
        }
    }

    /**
     * Validación de la fecha de entrega
     * @param \DateTime $date Referencia a la fecha de entrega
     */
    private function ValidateDeliveryDate($date = null){

    }
}
