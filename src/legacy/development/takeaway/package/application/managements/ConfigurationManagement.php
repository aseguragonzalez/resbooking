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
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IConfigurationRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia a la instancia de management
     * @var \IConfigurationManagement
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
        $this->Repository = ConfigurationRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = new \ConfigurationAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = ConfigurationServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IBaseLineManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(ConfigurationManagement::$_reference == NULL){
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

        $this->Aggregate->SetAggregate();

        return $this->Aggregate;
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
                $this->Repository->GetByFilter("ServiceDeliveryMethod", $filter);
        if(empty($register)){
            $entity = new \ServiceDeliveryMethod();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->DeliveryMethod = $id;
            $nEntity = $this->Repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->Repository->Delete("ServiceDeliveryMethod", $reg->Id);
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
                $this->Repository->GetByFilter("ServicePaymentMethod", $filter);
        if(empty($register)){
            $entity = new \ServicePaymentMethod();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->PaymentMethod = $id;
            $nEntity = $this->Repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->Repository->Delete("ServicePaymentMethod", $reg->Id);
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
                $this->Repository->GetByFilter("ServicePostCode", $filter);
        if(empty($register)){
            $entity = new \ServicePostCode();
            $entity->Project = $this->IdProject;
            $entity->Service = $this->IdService;
            $entity->Code = $id;
            $nEntity = $this->Repository->Create($entity);
            $result = $nEntity->Id;
        }
        else{
            foreach($register as $reg){
                $this->Repository->Delete("ServicePostCode", $reg->Id);
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
    public function SetProjectInfo($info = NULL){
        $info->Project = $this->IdProject;
        $result = $this->Services->ValidateInfo($info);
        if(!is_array($result) && $result == TRUE ){
            $result = [];
            if($info->Id == 0){
                $res = $this->Repository->Create($info);
                $result = ($res != FALSE) ? [] : [-1];
                $info->Id = ($res != FALSE) ? $res->Id : 0;
            }
            else{
                $res = $this->Repository->Update($info);
                $result = ($res != FALSE) ? [] : [-2];
            }

            if($res != FALSE){
                $this->Aggregate->ProjectInfo = $info;
            }
        }
        return $result;
    }

    /**
     * Procedimiento para cargar en el agregado la información de configuración
     */
    public function GetConfiguration() {
        // Cargar el agregado
        $this->Aggregate =
                $this->Repository->GetAggregate($this->IdProject, $this->IdService);

        $this->Aggregate->SetAggregate();
    }

}
