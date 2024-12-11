<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz para la realización de pedidos
 *
 * @author alfonso
 */
class OrderRepository extends \BaseRepository implements \IOrderRepository{

    /**
     * Referencia a la clase base
     * @var \IOrderRepository
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IOrderRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(OrderRepository::$_reference == NULL){
            OrderRepository::$_reference =
                    new \OrderRepository($project, $service);
        }
        return OrderRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \OrderAggregate
     */
    public function GetAggregate($project = 0, $service = 0) {
        // Instanciar agregado
        $agg = new \OrderAggregate($project, $service);
        // Tablas maestras
        $agg->HoursOfDay = $this->Dao->Get("HourOfDay");
        // Información del proyecto
        $agg->Project = $this->Dao->Read($project, "Project");
        // filtrado por proyecto y servicio
        $filter = ["Project" => $project, "Service" => $service];
        $agg->PaymentMethods = $this->Dao->GetByFilter("PaymentMethodDTO", $filter);
        $agg->DeliveryMethods = $this->Dao->GetByFilter("DeliveryMethodDTO", $filter);
        $agg->Slots = $this->Dao->GetByFilter("SlotDTO", $filter);
        $agg->PostCodes = $this->Dao->GetByFilter("PostCodeDTO", $filter);
        // filtrado por proyecto
        $filterP = ["Project" => $project, "State" => 1 ];
        $agg->Events = $this->Dao->GetByFilter("SlotEvent", $filterP);
        $agg->SlotsOfDelivery = $this->Dao->GetByFilter( "SlotOfDelivery", $filterP );
        $agg->Categories = $this->Dao->GetByFilter("Category", $filterP);
        $agg->Products = $this->Dao->GetByFilter("Product", $filterP);
        foreach($agg->Products as $item){
            $item->Images = $this->Dao->GetByFilter("Image", ["Product" => $item->Id]);
        }
        $agg->Discounts = $this->Dao->GetByFilter("DiscountOn", $filterP);
        foreach($agg->Discounts as $item){
            $item->Configuration = $this->Dao->GetByFilter(
                    "DiscountOnConfiguration", ["DiscountOn" => $item->Id]);
        }
        return $agg;
    }

    /**
     * Proceso de registro de la información de un pedido
     * @param \Request $request Referencia a la información de pedido
     * @param array $items Referencia a la colección de productos seleccionados
     * @return int Código de operación
     */
    public function CreateOrder($request = NULL, $items = NULL){
        // Validación de los parámetros
        if($request == NULL || $request instanceof \Request == FALSE){
            return -101;
        }
        if(!is_array($items) || $items == NULL ){
            return -102;
        }
        // Registrar la solicitud y productos
        if(($r = $this->Create($request)) != FALSE){
            $r instanceof \Request;
            foreach($items as $item){
                $item instanceof \RequestItem;
                $item->Request = $r->Id;
                $this->Create($item);
            }
            return $r->Id;
        }
        return -103;
    }

    /**
     * Genera el registro de notificación de un pedido
     * @param int $id Identidad del pedido
     * @param string $subject Asunto de la notificación
     * @return int Código de operación
     */
    public function CreateNotification($id = 0, $subject = ""){
        // Obtener la información del pedido
        $dto = $this->Dao->Read($id, "RequestNotificationDTO");
        // Comprobar datos leídos
        if($dto instanceof \RequestNotificationDTO != FALSE){
            // Obtener los productos asociados al pedido
            $dto->Items = $this->Dao->GetByFilter(
                    "RequestItemNotificationDTO", ["Request"=>$id]);
            // Establecer el formato de fecha
            $date = new DateTime($dto->DeliveryDate);
            $dto->DeliveryDate = strftime(
                    "%A %d de %B del %Y", $date->getTimestamp());
            // Comprobar descuento
            if(empty($dto->Discount)){
                $dto->Discount = "Sin descuento";
            }

            return $this->RegisterNotification($dto, $subject);
        }
        return -104;
    }

    /**
     * Crea el registro de la notificación con la información de
     * la reserva y la tipología indicada.
     * @param \RequestNotificationDTO $entity Referencia a la notificación
     * @param string $subject Asunto de la notificación
     * @return int Código de operación
     */
    private function RegisterNotification($entity = NULL, $subject = ""){
       if($entity != NULL && is_object($entity)){
           $date = new \DateTime( "NOW" );
           $dto = new \Notification();
           $dto->Project = $this->IdProject;
           $dto->Service = $this->IdService;
           $dto->To = $entity->Email;
           $dto->Subject = $subject;
           $dto->Content = json_encode($entity);
           $dto->Date = $date->format( "y-m-d h:i:s" );
           $this->Create( $dto );
           $dto->To = "";
           $this->Create( $dto );
           return 0;
       }
       return -105;
    }
}
