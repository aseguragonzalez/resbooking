<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz IValidatorClient
 *
 * @author alfonso
 */
class ValidatorClient implements \IValidatorClient{

    /**
     * Validación de la entidad
     * @var \IValidatorClient $_singleton
     * Referencia al cliente de validación
     */
    private static $_singleton = null;

    /**
     * Validación de la entidad
     * @var boolean $_isConfigure Estado de configuración
     */
    private $_isConfigure = false;

    /**
     * Constructor por defecto
     */
    private function __construct(){

    }

    /**
     * Validación de la entidad
     * @var object $entity Referencia a la entidad a validar
     */
    public function Validate($entity = null){
        return array();
    }

    /**
     * Configuración del validador con el xml de base de datos
     * @var string $fileName Nombre del fichero de configuración
     */
    public function Configure($fileName = ""){
        $this->_isConfigure = true;
    }

    /**
     * Obtiene una referencia al validador de entidades
     * @var string $fileName Nombre del fichero de configuración
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya hay una instancia
        if(ValidatorClient::$_singleton == null){
            ValidatorClient::$_singleton = new \ValidatorClient();
        }

        if($fileName != ""){
            ValidatorClient::$_singleton->Configure($fileName);
        }

        return ValidatorClient::$_singleton;
    }
}
