<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de configuraciones
 * de la capa de aplicación
 *
 * @author alfonso
 */
class ConfigurationManagement extends \BaseManagement
    implements \IConfigurationManagement {

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IConfigurationServices
     */
    protected $Services = null;

    /**
     * Referencia al respositorio de reservas
     * @var \IConfigurationRepository
     */
    protected $repository = null;

    /**
     * Referencia a la instancia de management
     * @var \IConfigurationManagement
     */
    private static $_reference = null;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Constructor de la clase padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->repository = ConfigurationRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->aggregate = new \ConfigurationAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = ConfigurationServices::GetInstance($this->aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(ConfigurationManagement::$_reference == null){
            ConfigurationManagement::$_reference =
                   new \ConfigurationManagement($project, $service);
        }
        return ConfigurationManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \ConfigurationAgregate
     */
    public function GetAggregate() {

        $this->aggregate->SetAggregate();

        return $this->aggregate;
    }

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de entrega seleccionado
     * @param int $id Identidad del método de entrega
     * @return int Código de operación
     */
    public function SetDeliveryMethod($id = 0) {
        $result = ($id < 1) ? -1 : -2;
        $filter = [ "Project" => $this->IdProject, "DeliveryMethod" => $id,
            "Service" => $this->IdService];
        $register =
                $this->repository->GetByFilter("ServiceDeliveryMethod", $filter);
        if(empty($register)){
            $entity = new \ServiceDeliveryMethod();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->DeliveryMethod = $id;
            $nEntity = $this->repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->repository->Delete("ServiceDeliveryMethod", $reg->Id);
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el método de pago seleccionado
     * @param int $id Identidad del método de pago
     * @return int Código de operación
     */
    public function SetPaymentMethod($id = 0) {
        $result = ($id < 1) ? -1 : -2;
        $filter = [ "Project" => $this->IdProject, "PaymentMethod" => $id,
            "Service" => $this->IdService];
        $register =
                $this->repository->GetByFilter("ServicePaymentMethod", $filter);
        if(empty($register)){
            $entity = new \ServicePaymentMethod();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->PaymentMethod = $id;
            $nEntity = $this->repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->repository->Delete("ServicePaymentMethod", $reg->Id);
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para establecer la relación del proyecto
     * con el código postal seleccionado
     * @param int $id Identidad del código postal
     * @return int Código de operación
     */
    public function SetPostCode($id = 0) {
        $result = ($id < 1) ? -1 : -2;
        $filter = [ "Project" => $this->IdProject, "Code" => $id,
            "Service" => $this->IdService];
        $register =
                $this->repository->GetByFilter("ServicePostCode", $filter);
        if(empty($register)){
            $entity = new \ServicePostCode();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->Code = $id;
            $nEntity = $this->repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->repository->Delete("ServicePostCode", $reg->Id);
                $result = 0;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para establecer la información de proyecto relativa
     * a la impresión de tickets
     * @param \ProjectInfo $info Referencia a la entidad a registrar
     * @return array Códigos de operación
     */
    public function SetProjectInfo($info = null){
        $info->Project = $this->IdProject;
        $result = $this->Services->ValidateInfo($info);
        if(!is_array($result) && $result == true ){
            $result = [];
            if($info->Id == 0){
                $res = $this->repository->Create($info);
                $result = ($res != false) ? [] : [-1];
                $info->Id = ($res != false) ? $res->Id : 0;
            }
            else{
                $res = $this->repository->Update($info);
                $result = ($res != false) ? [] : [-2];
            }

            if($res != false){
                $this->aggregate->ProjectInfo = $info;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para cargar en el agregado la información de configuración
     */
    public function GetConfiguration() {
        // Cargar el agregado
        $this->aggregate =
                $this->repository->GetAggregate($this->IdProject, $this->IdService);

        $this->aggregate->SetAggregate();
    }

}
