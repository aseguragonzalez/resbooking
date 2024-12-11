<?php

declare(strict_types=1);

/**
 * Capa de servicio para la configuración del proyecto
 *
 * @author alfonso
 */
class ConfigurationServices  extends \BaseServices
    implements \IConfigurationServices{

    /**
     * Referencia
     * @var \IConfigurationServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IConfigurationRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \ConfigurationAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \ConfigurationAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = ConfigurationRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Proceso de validación de la información del proyecto para la impresión
     * de tickets
     * @param \ProjectInformation $dto Referencia a la información del proyecto
     * @return TRUE|array Colección de códigos de validación
     */
    public function ValidateInfo($dto = NULL){
        if($dto != NULL){
            $this->ValidateTitleInfo($dto->Title);
            $this->ValidateCIFInfo($dto->CIF);
            $this->ValidateAddressInfo($dto->Address);
            $this->ValidatePhoneInfo($dto->Phone);
            $this->ValidateEmailInfo($dto->Email);
        }
        else{
            $this->Result[] = -1;
        }

        return count($this->Result) == 0 ? TRUE : $this->Result;
    }

    public function ValidateTitleInfo($title = ""){
        if(empty($title)){
            $this->Result[] = -2;
        }
    }

    public function ValidateCIFInfo($cif = ""){
        if(empty($cif)){
            $this->Result[] = -3;
        }
        else if(strlen($cif) > 15){
            $this->Result[] = -4;
        }
    }

    public function ValidateAddressInfo($address = ""){
        if(empty($address)){
            $this->Result[] = -5;
        }
    }

    public function ValidatePhoneInfo($phone = ""){
        if(empty($phone)){
            $this->Result[] = -6;
        }
        else if(strlen($phone) > 15){
            $this->Result[] = -7;
        }
    }

    public function ValidateEmailInfo($email = ""){
        if(empty($email)){
            $this->Result[] = -8;
        }
        else if(strlen($email) > 200){
            $this->Result[] = -9;
        }
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \IConfigurationServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(ConfigurationServices::$_reference == NULL){
            ConfigurationServices::$_reference = new \ConfigurationServices($aggregate);
        }
        return ConfigurationServices::$_reference;
    }
}
