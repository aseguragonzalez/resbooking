<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para descuentos
 */
class DiscountsManagement extends \BaseManagement
    implements \IDiscountsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IDiscountsServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IDiscountsRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado actual
     * @var \DiscountsAggregate
     */
    public $Aggregate = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IDiscountsManagement
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
        $this->Repository = DiscountsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = DiscountsServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project Identidad del proyecto del contexto
     * @param int $service Identidad del servicio solicitado
     * @return \IDiscountsManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(DiscountsManagement::$_reference == NULL){
            DiscountsManagement::$_reference =
                   new \DiscountsManagement($project, $service);
        }
        return DiscountsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \DiscountsAggregate
     */
    public function GetAggregate() {

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
    }

    /**
     * Proceso para cargar en el agregado actual el descuento
     * identificado por su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function GetDiscount($id = 0) {
        $result = -1;
        // Obtener referencia al descuento
        $dto = $this->Services->GetById(
                $this->Aggregate->Discounts, $id);

        if($dto == NULL){
            $dto = $this->Repository->GetDiscountById($id);
        }

        // Validar la referencia obtenida
        if($dto != NULL){
            // Asignamos el dto encontrado
            $this->Aggregate->Discount = $dto;
            // código de operación
            $result = 0;
        }

        return $result;
    }

    /**
     * Proceso para obtener los descuentos activos
     * @return array Colección de descuentos activos
     */
    public function GetDiscounts() {
        // Cargar en el agregado los descuentos
        $this->Aggregate->Discounts = $this->Repository->GetDiscounts();
        // Retornar la colección
        return $this->Aggregate->Discounts;
    }

    /**
     * Proceso para guardar la información del descuento en el repositorio
     * @param \DiscountDTO $dto Referencia al descuento
     * @return array Códigos de operación
     */
    public function SetDiscount($dto = NULL) {
        // Asignar el proyecto
        $dto->Project = $this->IdProject;
        // Asignar el servicio
        $dto->Service = $this->IdService;
        // Validar la información del descuento
        $result = $this->Services->Validate($dto);
        if(!is_array($result) && $result == TRUE ){
            // Obtener referencia a la entidad de bbdd
            $entity = $dto->GetDiscountOn();
            $result = [];
            // Registrar|actualizar el descuento
            if($entity->Id == 0){
                // Crear el registro del descuento
                $res = $this->Repository->Create($entity);
                // Establecer el resultado de la operación
                $result[] = ($res != FALSE) ? 0 : -1;
                $dto->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                // Actualizar el regsitro del descuento
                $res = $this->Repository->Update($entity);
                // Establecer el resultado de la operación
                $result[] = ($res != FALSE) ? 0 : -2;
            }
            // Actualizar las configuraciones asociadas
            if($res != FALSE){
                // Actualizar las configuraciones del descuento
                $res = $this->SetConfiguration($res->Id, $dto->Configuration);
                // Establecer el resultado de la operación
                $result = ($res != FALSE) ? [0] : [-3];
                // Actualizar el dto en la colección
                $this->Aggregate->Discounts[$dto->Id] = $dto;
            }
        }
        return $result;
    }

    /**
     * Proceso para dar de baja un descuento mediante su identidad
     * @param int $id Identidad del descuento
     * @return int Código de operación
     */
    public function RemoveDiscount($id = 0) {
        // Obtener referencia al dto del descuento
        $dto = $this->Services->GetById(
                $this->Aggregate->Discounts, $id);

        if($dto == NULL){
            $dto = $this->Repository->GetDiscountById($id);
        }

        if($dto != NULL){
            // Eliminar todas las referencias a configuraciones del descuento
            $this->RemoveReferences($dto->Configuration);
            // Eliminar la referencia
            $dto->Configuration = [];
            // obtener referencia al descuento para actualizar el registro
            $entity = $dto->GetDiscountOn();
            // Establecer el estado
            $entity->State = 0;
            // Actualizar
            return ($this->Repository->Update($entity) != FALSE)
                ? 0 : -1;
        }
        return -2;
    }

    /**
     * Proceso de eliminación de las configuraciones del descuento
     * @param array $configs Colección de configuraciones
     */
    private function RemoveReferences($configs = NULL){
        foreach($configs as $config){
            if($config->Id > 0){
                $this->Repository->Delete(
                        "DiscountOnConfiguration", $config->Id);
            }
        }
    }

    /**
     * Crear o eliminar los registros de configuración indicados
     * @param int $id Identidad del descuento
     * @param array $config Colección de configuraciones
     */
    private function SetConfiguration($id = 0, $config = NULL){
        // filtro para cargar las configuraciones del descuento
        $configFilter = ["DiscountOn" => $id ];
        // Obtener configuraciones
        $configuration = $this->Repository->GetByFilter(
                "DiscountOnConfiguration", $configFilter );
        // Eliminar la configuración actual
        $this->RemoveReferences($configuration);
        // Crear todos los registros nuevos
        foreach($config as $item){
            $item->DiscountOn = $id;
            $item = $this->Repository->Create($item);
        }
        return $id;
    }

    /**
     * Proceso para obtener la colección de eventos asociados a un descuento
     * filtrados por semana y año (opcional) o por estar activos
     * @param int $id Identidad del descuento asociado
     * @return array Colección de eventos registrados
     */
    public function GetDiscountEvents($id = 0, $week = 0, $year = 0){
        // Filtro estándar de búsqueda
        $filter = [
            "Project" => $this->IdProject,
            "Service" => $this->IdService,
            "DiscountOn" => $id
        ];
        // Aplicar el filtro de búsqueda por semana y anyo si se ha especificado
        if($week != 0 && $year != 0){
            $filter["Week"] = $week;
            $filter["Year"] = $year;
        }
        // Búsqueda de eventos
        $events = $this->Repository->GetByFilter("DiscountOnEvent", $filter);
        // Filtrado de eventos por fecha actual
        // (No se han especificado los parametros)
        if($week == 0 || $year == 0){
            $yesterday = new \DateTime("YESTERDAY");
            $events = array_filter($events, function($item) use ($yesterday){
                return (new \DateTime($item->Date)) > $yesterday;
            });
        }
        return $events;
    }

    /**
     * Proceso para actualizar el estado del evento asociado a un descuento
     * @param \DiscountOnEvent $dto Referencia a la información del evento
     * @return int Código de operación
     */
    public function SetDiscountEvent($dto = NULL){
        if($dto == NULL){
            return -1;
        }
        $dto->Project = $this->IdProject;
        $dto->Service = $this->IdService;
        // Filtro para buscar eventos registrados
        $filter = [ "Project" => $dto->Project, "Service" => $dto->Service,
            "DiscountOn" => $dto->DiscountOn, "Date" => "%$dto->Date%",
            "SlotOfDelivery" => $dto->SlotOfDelivery
        ];
        // Resultado de la búsqueda
        $events = $this->Repository->GetByFilter("DiscountOnEvent", $filter);

        if(empty($events)){
            $this->Repository->Create($dto);
        }
        else{
            foreach($events as $event){
                $this->Repository->Delete("DiscountOnEvent", $event->Id);
            }
        }
        return 0;
    }

}
