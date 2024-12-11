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
 * Description of ConfigurationModel
 *
 * @author manager
 */
class ConfigurationModel extends \TakeawayModel{

    /**
     * Colección de métodos de entrega
     * @var array
     */
    public $DeliveryMethods = [];

    /**
     * Colección de formas de pago
     * @var array
     */
    public $PaymentMethods = [];

    /**
     * Colección de códigos postales
     * @var array
     */
    public $PostCodes = [];

    /**
     * Referencia a la información del proyecto
     * @var \ProjectInformation
     */
    public $Info = NULL;

    /**
     * Referencia al agregado
     * @var \ConfigurationAggregate
     */
    public $Aggregate = NULL;

    /**
     * Referencia al gestor del agregado
     * @var \IConfigurationManagement
     */
    protected $Management = NULL;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
                "Configuraciones",
                "Configuracion",
                "ConfigurationManagement");
        $this->Info = new \ProjectInformation();
    }

    /**
     * Procedimiento para cargar el modelo con la información de configuración
     */
    public function LoadModel(){
        $this->Management->GetConfiguration();
        $this->Aggregate = $this->Management->GetAggregate();
        foreach($this->Aggregate->DeliveryMethods as $method){
            $filter = array_filter($this->Aggregate->AvailableDeliveryMethods,
                    function ($item) use($method){
                        return $item->Id == $method->Id;
                    });
            $method->Checked = count($filter) > 0 ? 1 : 0;
        }
        $this->DeliveryMethods = $this->Aggregate->DeliveryMethods;
        foreach($this->Aggregate->PaymentMethods as $method){
            $filter = array_filter($this->Aggregate->AvailablePaymentMethods,
                    function ($item) use($method){
                        return $item->Id == $method->Id;
                    });
            $method->Checked = count($filter) > 0 ? 1 : 0;
        }
        $this->PaymentMethods = $this->Aggregate->PaymentMethods;
        foreach($this->Aggregate->PostCodes as $method){
            $filter = array_filter($this->Aggregate->AvailablePostCodes,
                    function ($item) use($method){
                        return $item->Code == $method->Id;
                    });
            $method->Checked = count($filter) > 0 ? 1 : 0;
        }
        $this->PostCodes = $this->Aggregate->PostCodes;
        $this->Info = $this->Aggregate->ProjectInfo;

    }

    /**
     * Procedimiento para actualizar la información del proyecto utilizada
     * en la generación de tickets
     * @param \ProjectInformation $info Referencia a la entidad
     * @return \JsonResultDTO Resultado de la operación
     */
    public function SetProjectInformation($info = NULL){
        $result = $this->Management->SetProjectInfo($info);

        $json = new \JsonResultDTO();

        if(is_array($result) == FALSE){
            $json->Result = FALSE;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }
        else if(count($result) >  0){
            $json->Result = FALSE;
            $json->Codes = $result;
        }
        else{
            $json->Result = TRUE;
            $json->Message = "La operación se ha realizado correctamente.";
        }

        return $json;
    }

    /**
     * Actualiza la relación del método de entrega con el proyecto
     * @param int $id Identidad del método de entrega
     * @return \JsonResultDTO Resultado de la operación
     */
    public function SetDeliveryMethod($id = 0){
        // Configurar evento
        $result = $this->Management->SetDeliveryMethod($id);

        $json = new \JsonResultDTO();

        if(is_numeric($result) == FALSE){
            $json->Result = FALSE;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }

        if($result < 0){
            $json->Result = FALSE;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = TRUE;
            $json->Message = "La operación se ha realizado correctamente.";
        }

        return $json;
    }

    /**
     * Actualiza la relación de la forma de pago con el proyecto
     * @param int $id Identidad de la forma de pago
     * @return \JsonResultDTO
     */
    public function SetPaymentMethod($id = 0){
        // Configurar evento
        $result = $this->Management->SetPaymentMethod($id);

        $json = new \JsonResultDTO();

        if(is_numeric($result) == FALSE){
            $json->Result = FALSE;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }

        if($result < 0){
            $json->Result = FALSE;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = TRUE;
            $json->Message = "La operación se ha realizado correctamente.";
        }

        return $json;
    }

    /**
     * Actualiza la relación del código postal con el proyecto
     * @param int $id Identidad del código postal
     * @return \JsonResultDTO
     */
    public function SetPostCode($id = 0){
        // Configurar evento
        $result = $this->Management->SetPostCode($id);

        $json = new \JsonResultDTO();

        if(is_numeric($result) == FALSE){
            $json->Result = FALSE;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }

        if($result < 0){
            $json->Result = FALSE;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = TRUE;
            $json->Message = "La operación se ha realizado correctamente.";
        }

        return $json;
    }

    /**
     * Configuración estándar del modelo
     */
    protected function SetModel() {

    }

    /**
     * Configuración de los códigos de resultado
     */
    protected function SetResultCodes() {

    }
}
