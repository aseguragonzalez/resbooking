<?php

declare(strict_types=1);

/**
 * Modelo para la generación de pedidos
 *
 * @author manager
 */
class OrderModel extends \TakeawayModel{

    /**
     * Referencia a la solicitud actual
     * @var \Request
     */
    public $Entity = null;

    /**
     * Indica si se ha producido un error de validación
     * @var boolean
     */
    public $Error = false;

    /**
     * Indica si debe visualizarse el modal de códigos postales
     * @var boolean
     */
    public $ModalEntrega = true;

    /**
     * Cólección de horas de reparto disponibles
     * @var array
     */
    public $HoursOfDay = [];

    /**
     * Colección de códigos postales donde se puede realizar la entrega
     * @var array
     */
    public $PostCodes = [];

    /**
     * Serialización JSON de los códigos postales
     * @var string
     */
    public $JSONPostCodes = "[]";

    /**
     * Métodos de págo configurados
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Métodos de entrega configurados
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Submenu de categorías
     * @var array
     */
    public $SubMenuCategories = [];

    /**
     * Colección de categorías
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de productos
     * @var array
     */
    public $Products = [];

    /**
     * Colección de descuentos
     * @var array
     */
    public $Discounts = [];

    /**
     * Colección de información de descuentos
     * @var array
     */
    public $DiscountsInfo = [];

    /**
     * Colección de eventos registrados
     * @var array
     */
    public $Events = [];

    /**
     * Colección de slot configurados
     * @var array
     */
    public $Slots = [];

    /**
     * Mensaje de error en el campo nombre
     * @var string
     */
    public $eName = "";

    /**
     * Clase CSS para el mensaje de error del campo nombre
     * @var string
     */
    public $eNameClass = "";

    /**
     * Mensaje de error en el campo teléfono
     * @var string
     */
    public $ePhone = "";

    /**
     * Clase CSS para el mensaje de error del campo teléfono
     * @var string
     */
    public $ePhoneClass = "";

    /**
     * Mensaje de error en el campo e-mail
     * @var string
     */
    public $eEmail = "";

    /**
     * Clase CSS para el mensaje de error del campo e-mail
     * @var string
     */
    public $eEmailClass = "";

    /**
     * Mensaje de error en el campo código postal
     * @var string
     */
    public $ePostCode = "";

    /**
     * Clase CSS para el mensaje de error en el campo código postal
     * @var string
     */
    public $ePostCodeClass = "";

    /**
     * Mensaje de error en el campo dirección
     * @var string
     */
    public $eAddress = "";

    /**
     * Clase CSS para el mensaje de error del campo dirección
     * @var string
     */
    public $eAddressClass = "";

    /**
     * Mensaje de error en el campo Hora de entrega
     * @var string
     */
    public $eDeliveryTime = "";

    /**
     * Clase CSS para el mensaje de error del campo hora de entrega
     * @var string
     */
    public $eDeliveryTimeClass = "";

    /**
     * Mensaje de error en el campo Método de entrega
     * @var string
     */
    public $eDeliveryMethods = "";

    /**
     * Clase CSS para el mensaje de error del campo método de entrega
     * @var string
     */
    public $eDeliveryMethodsClass = "";

    /**
     * Mensaje de error en el campo forma de pago
     * @var string
     */
    public $ePaymentMethod = "";

    /**
     * Clase CSS para el mensaje de error del campo forma de pago
     * @var string
     */
    public $ePaymentMethodClass = "";

    /**
     * @ignore
     * Constructor
     */
    public function __construct($project = 0){
        parent::__construct("Pedidos");
        $this->Management =
            OrderManagement::GetInstance($project, $this->Service);
        $this->aggregate = $this->Management->GetAggregate();
        $this->SetProject();
        $this->SetModel();
    }

    /**
     * Configura el modelo para cargar el formulario de peticiones
     */
    public function GetOrderForm(){

        foreach ($this->Products as $item){
            $item->PName = $item->Name;
            $item->PDesc = $item->Description;
            $item->PId = $item->Id;
            $price = floatval(str_replace(",",".",$item->Price ));
            $item->PPrice = number_format($price, 2);
        }

        foreach($this->aggregate->Categories as $cat){

            $prods = array_filter($this->Products, function($prod) use($cat){
                return $prod->Category == $cat->Id;
            });

            if(count($prods) > 0){
                $this->{"Products".$cat->Id } = $prods;
                $this->Categories[] = $cat;
                $this->SubMenuCategories[] = $cat;
            }
        }
    }

    /**
     * Proceso de registro de un pedido
     * @param \Request $entity Referencia con la información del pedido
     * @return boolean Resultado del registro
     */
    public function Save($entity = null){
        $this->Error = true;
        // Generar ticket
        $entity->Ticket = $this->GenerateTicket();
        // Extraer lista de items
        $entity->Items = $this->GetRequestItems($entity);
        // Procedimiento para almacenar el descuento
        $result = $this->Management->SetOrder($entity);

        if(is_array($result) == false){
            throw new Exception("Save: SetOrder: "
                    . "Códigos de operación inválidos");
        }

        $this->Entity = $entity;

        if(count($result) == 1){
            if($result[0] == 0 || $result[0] == -25){
                $this->Error = false;
                $this->eResult = "La operación se ha realizado satisfactoriamente.";
                $this->eResultClass="has-success";
                return true;
            }
        }

        $this->TranslateResultCodes(_OP_CREATE_, $result);
        $this->Entity->Items = json_encode($entity->Items);

        return false;
    }

    /**
     * Configuración estándar del modelo
     */
    protected function SetModel() {
        $this->Entity = new \Request();
        $this->Entity->Items = "[]";
        $this->Products = $this->aggregate->Products;
        foreach($this->Products as $item){
            $item->Price = number_format($item->Price, 2);
        }
        $this->PostCodes = $this->aggregate->PostCodes;
        $this->DeliveryMethods = $this->aggregate->DeliveryMethods;
        $this->PaymentMethods = $this->aggregate->PaymentMethods;
        $this->JSONPostCodes = json_encode($this->PostCodes);
        $this->FilterByDate();
    }

    /**
     * Establecimiento de los códigos de resultado
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()];
    }

    /**
     * Establece los datos del proyecto seleccionado
     */
    private function SetProject(){
        if($this->aggregate->Project != null){
            $this->Project = $this->aggregate->Project->Id;
            $this->ProjectName = $this->aggregate->Project->Name;
            $this->ProjectPath = $this->aggregate->Project->Path;
        }
    }

    /**
     * Proceso de filtrado de la configuración mediante fecha
     * @param string $sDate Fecha de filtrado
     */
    private function FilterByDate($sDate = ""){
        // Obtener el día de la semana
        $dow = $this->GetDayOfWeek($sDate);
        // Filtrar slots por día de la semana
        $this->FilterSlotsByDayOfWeek($dow);
        // filtrar slots por eventos
        $this->FilterSlotsByEvents($sDate);
        // filtrar los descuentos válidos
        $this->FilterDiscounts($dow);
        // Establecer las horas de entrega válidas
        $this->SetHoursOfDay($dow);
    }

    /**
     * Obtiene el dia de la semana para los filtros
     * @param string $sDate
     * @return int
     */
    private function GetDayOfWeek($sDate = ""){
        if($sDate == ""){
            $sDate = "NOW";
        }

        $date = new \DateTime($sDate);

        $dow = $date->format("w");

        if($dow == 0){
            $dow = 7;
        }
        return $dow;
    }

    /**
     * Filtrado de los descuentos
     * @param int $dow Dia de la semana
     */
    private function FilterDiscounts($dow = 0){
        foreach($this->aggregate->Discounts as $discount){
            $items = array_filter($discount->Configuration,
                    function($item) use($dow){
                return $item->DayOfWeek == $dow;
            });

            if(count($items)>0){
                $discount->Value = number_format($discount->Value, 2);
                $this->Discounts[] = $discount;
                $this->DiscountsInfo[] = $discount;
            }
        }
    }

    /**
     * Filtrado de las horas de reparto
     * @param int $dow Día de la semana
     */
    private function SetHoursOfDay($dow = 0){
        // Filtrar horas por slots
        $this->FilterHoursOfDayBySlots($dow);
        // Filtrar horas no válidas
        $this->FilterHoursOfDayByCurrentHour();
    }

    /**
     * Proceso para filtrar los Slots de reparto válidos en la fecha dada
     * @param string $sDate Fecha para filtrar
     */
    private function FilterSlotsByEvents($sDate = ""){
        if($sDate == ""){
            $sDate = "NOW";
        }
        $date = new \DateTime($sDate);
        $dateOfEvent = $date->format("Y-m-d");
        $this->Events = array_filter($this->aggregate->Events,
                function($event) use($dateOfEvent){
            return strpos($event->Date, $dateOfEvent) !== false ;
        });
        // Agregar y Quitar los slot bloqueados para la fecha
        foreach($this->Events as $event){
            $blocks = array_filter($this->Slots, function($slot) use($event){
               return $event->Open == false
                       && $slot->Id == $event->SlotOfDelivery;
            });
            if(count($blocks) > 0){
                $this->Slots = array_diff($this->Slots, $blocks);
            }
            $opened = array_filter($this->aggregate->SlotsOfDelivery, function($slot) use($event){
               return $event->Open == true
                       && $slot->Id == $event->SlotOfDelivery;
            });
            if(count($opened) > 0){
                $this->Slots = array_merge($this->Slots, $opened);
            }
        }
    }

    /**
     * Filtra los slot configurados por el día de la semana
     * @param int $dow Día de la semana
     */
    private function FilterSlotsByDayOfWeek($dow = 0){
        $this->Slots = array_filter($this->aggregate->Slots,
            function($item) use($dow){
                return $item->DayOfWeek == $dow;
        });
    }

    /**
     * Filtrar las horas configuradas
     */
    private function FilterHoursOfDayBySlots(){
        foreach($this->Slots as $slot){
            $hours = array_filter($this->aggregate->HoursOfDay,
                function($item) use($slot){
                    return $slot->Start <= $item->Id
                            && $slot->End >= $item->Id;
            });
            $this->HoursOfDay = array_merge($this->HoursOfDay, $hours);
        }
    }

    /**
     * Filtrar las horas de reparto con la hora actual
     */
    private function FilterHoursOfDayByCurrentHour(){
        $minutes_to_add = 30;
        $date = new \DateTime("NOW");
        $date->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $hour = $date->format("H:i");

        foreach($this->HoursOfDay as $key => $item){
            if($this->CompareHour($item->Text, $hour)){
                unset($this->HoursOfDay[$key]);
            }
        }
    }

    /**
     * Proceso de validación para comprobar que la hora de inicio es menor
     * que la hora de finalización
     * @param int $start Identidad de la hora de inicio
     * @param int $end Identidad de la hora de finalización
     * @return boolean
     */
    private function CompareHour($start = null, $end = null){
        if($start != null && $end != null){
            // Partimos las cadenas con format [hh:mm] por ":"
            $aStart = explode(":", $start);
            $aEnd = explode(":", $end);
            // Obtener horas
            $hStart = intval($aStart[0]);
            $hEnd = intval($aEnd[0]);
            // Proceso de comparación
            if($hStart > $hEnd){
                return false;
            }
            elseif($hStart == $hEnd){
                // comparar minutos
                if(intval($aStart[1]) >= intval($aEnd[1])){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Proceso de generación de tickets
     * @var int $length Longitud del ticket generado
     */
    private function GenerateTicket($length = 15){
        $date = new \DateTime("NOW");

        return $date->format("YmdHis");
    }

    /**
     * Proceso para extraer la colección de productos asociados al pedido
     * @param \Request $entity Referencia a la solicitud
     * @return array
     */
    private function GetRequestItems($entity = null){
        if($entity != null && isset($entity->Items)
                && is_string($entity->Items)){
            return json_decode($entity->Items);
        }
        return [];
    }

    /**
     * Obtiene los mensajes de error al "leer"
     * @return array
     */
    private function GetReadMessages(){
        return [];
    }

    /**
     * Obtiene los mensajes de error al "eliminar"
     * @return array
     */
    private function GetDeleteMessages(){
        return [];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de un pedido
     * @return array
     */
    private function GetSaveMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se han podido leer los datos" ],
            -2 => ["name" => "eResult",
                    "msg" => "No se han podido recuperar los datos" ],
            -3 => ["name" => "eResult",
                    "msg" => "Los datos no han sido recuperados correctamente" ],
            -4 => ["name" => "eResult",
                    "msg" => "Proyecto no válido" ],
            -5 => ["name" => "eResult",
                    "msg" => "El ticket no ha sido generado" ],
            -6 => ["name" => "eResult",
                    "msg" => "El ticket generado no es válido" ],
            -7 => ["name" => "eName",
                    "msg" => "Debe indicar nombre y apellidos" ],
            -8 => ["name" => "eName",
                    "msg" => "No debe superar los 200 caracteres" ],
            -9 => ["name" => "eEmail",
                    "msg" => "Debe especificar una dirección de e-mail" ],
            -10 => ["name" => "eEmail",
                    "msg" => "No puede superar los 100 caracteres" ],
            -11 => ["name" => "eEmail",
                    "msg" => "El formato de e-mail es incorrecto" ],
            -12 => ["name" => "eAddress",
                    "msg" => "Debe indicar una dirección postal" ],
            -13 => ["name" => "eAddress",
                    "msg" => "La dirección postal no puede tener más de 500 caracteres" ],
            -14 => ["name" => "eResult",
                    "msg" => "El descuento aplicado no es correcto : 0" ],
            -15 => ["name" => "eResult",
                    "msg" => "El descuento asociado es incorrecto." ],
            -16 => ["name" => "eDeliveryMethod",
                    "msg" => "Debe seleccionar el método de entrega" ],
            -17 => ["name" => "eDeliveryMethod",
                    "msg" => "El método de reparto no es válido" ],
            -18 => ["name" => "ePaymentMethod",
                    "msg" => "Debe seleccionar una forma de pago" ],
            -19 => ["name" => "ePaymentMethod",
                    "msg" => "La forma de pago no es válida" ],
            -20 => ["name" => "eDeliveryTime",
                    "msg" => "Debe seleccionar una hora de entrega" ],
            -21 => ["name" => "eDeliveryTime",
                    "msg" => "La hora de entrega no es válida" ],
            -22 => ["name" => "ePhone",
                    "msg" => "Debe indicar un teléfono de contacto" ],
            -23 => ["name" => "ePhone",
                    "msg" => "El teléfono de contacto no puede tener más de 15 caracteres" ],
            -24 => ["name" => "eResult",
                    "msg" => "No ha seleccionado ningún producto." ],
            -25 => ["name" => "eResult",
                    "msg" => "Existe un pedido en curso. Por favor,"
                . " póngase en contacto con el restaurante." ],
            -26 => ["name" => "eResult",
                    "msg" => "Debe especificar un código postal" ],
            -27 => ["name" => "eResult",
                    "msg" => "El código postal no puede tener más de 6 dígitos" ],
            -28 => ["name" => "eResult",
                    "msg" => "El código postal no tiene un formato correcto" ],

            -29 => ["name" => "eResult",
                    "msg" => "El importe del pedido no puede ser "
                . "menor igual que 0" ],
            -30 => ["name" => "eResult",
                    "msg" => "El importe neto no coincide con "
                . "el de los productos solicitados" ],
            -31 => ["name" => "eResult",
                    "msg" => "Alguno de los productos solicitados no es válido" ],
            -32 => ["name" => "eResult",
                    "msg" => "El importe total no puede ser menor o igual que 0" ],
            -33 => ["name" => "eResult",
                    "msg" => "El importe total no coincide." ],
            -34 => ["name" => "eResult",
                    "msg" => "El importe total no coincide." ],
            -35 => ["name" => "eResult",
                    "msg" => "El código postal no es válido." ]
        ];
    }
}
