<?php

declare(strict_types=1);

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
    public $Info = null;

    /**
     * Referencia al agregado
     * @var \ConfigurationAggregate
     */
    public $Aggregate = null;

    /**
     * Referencia al gestor del agregado
     * @var \IConfigurationManagement
     */
    protected $Management = null;

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
        $this->aggregate = $this->Management->GetAggregate();
        foreach($this->aggregate->DeliveryMethods as $method){
            $filter = array_filter($this->aggregate->AvailableDeliveryMethods,
                    function ($item) use($method){
                        return $item->Id == $method->Id;
                    });
            $method->Checked = count($filter) > 0 ? 1 : 0;
        }
        $this->DeliveryMethods = $this->aggregate->DeliveryMethods;
        foreach($this->aggregate->PaymentMethods as $method){
            $filter = array_filter($this->aggregate->AvailablePaymentMethods,
                    function ($item) use($method){
                        return $item->Id == $method->Id;
                    });
            $method->Checked = count($filter) > 0 ? 1 : 0;
        }
        $this->PaymentMethods = $this->aggregate->PaymentMethods;
        foreach($this->aggregate->PostCodes as $method){
            $filter = array_filter($this->aggregate->AvailablePostCodes,
                    function ($item) use($method){
                        return $item->Code == $method->Id;
                    });
            $method->Checked = count($filter) > 0 ? 1 : 0;
        }
        $this->PostCodes = $this->aggregate->PostCodes;
        $this->Info = $this->aggregate->ProjectInfo;

    }

    /**
     * Procedimiento para actualizar la información del proyecto utilizada
     * en la generación de tickets
     * @param \ProjectInformation $info Referencia a la entidad
     * @return \JsonResultDTO Resultado de la operación
     */
    public function SetProjectInformation($info = null){
        $result = $this->Management->SetProjectInfo($info);

        $json = new \JsonResultDTO();

        if(is_array($result) == false){
            $json->Result = false;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }
        elseif(count($result) >  0){
            $json->Result = false;
            $json->Codes = $result;
        }
        else{
            $json->Result = true;
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

        if(is_numeric($result) == false){
            $json->Result = false;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }

        if($result < 0){
            $json->Result = false;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = true;
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

        if(is_numeric($result) == false){
            $json->Result = false;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }

        if($result < 0){
            $json->Result = false;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = true;
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

        if(is_numeric($result) == false){
            $json->Result = false;
            $json->Code = 500;
            $json->Exception = new \Exception("Códigos de operación inválidos");
        }

        if($result < 0){
            $json->Result = false;
            $json->Error = $this->GetResultMessage(_OP_DELETE_, $result);
        }
        else{
            $json->Result = true;
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
