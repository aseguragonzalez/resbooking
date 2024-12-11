<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para configuraciones de eventos
 *
 * @author manager
 */
class OrderManagement extends \BaseManagement implements \IOrderManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IOrderServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IOrderRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IOrderManagement
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
        $this->Repository = OrderRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = OrderServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management de Pedidos
     * @param int $project Referencia al proyecto
     * @param int $service Referencia al servicio
     * @return \IOrderManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(OrderManagement::$_reference == NULL){
            OrderManagement::$_reference =
                   new \OrderManagement($project, $service);
        }
        return OrderManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso de registro de la solicitud
     * @param \OrderDTO $dto Referencia al DTO de la solicitud
     * @return array Códigos de operación
     */
    public function SetOrder($dto = NULL) {
        $subject = "Pedido";
        // Asignar el proyecto
        $dto->Project = $this->IdProject;
        // Asignar campos calculados
        $dto->Total = $this->Services->GetTotal($dto);
        $dto->Amount = $this->Services->GetAmount($dto);
        $dto->Ticket = $this->Services->GetTicket($dto);
        // Validación de los datos
        $result = $this->Services->Validate($dto);

        if(!is_array($result) && $result == TRUE ){
            $result = [];
            // Obtener la referencia a la solicitud
            $request = $dto->GetRequest();
            // Obtener la colección de productos solicitados
            $items = $dto->GetRequestItems();
            // Generar el registro
            $id = $this->Repository->CreateOrder($request, $items);
            // Validar registro del pedido
            if($id > 0){
                $result[] = $this->Repository->CreateNotification($id, $subject);
            }
            else{
                $result[] = $id;
            }
        }
        return $result;
    }
}
