<?php

declare(strict_types=1);

/**
 * Model para la gestión de configuraciones
 *
 * @author alfonso
 */
class ConfigurationModel extends \ResbookingModel{

    /**
     * Indica la opción de menú activa
     * @var string
     */
    public $Activo = "Configuración";

    /**
     * Referencia a la entidad de configuración del servicio
     * @var \ConfigurationService
     */
    public $Entity = null;

    /**
     * Mensaje de error en el mínimo número de comensales
     * @var string
     */
    public $eMinDiners = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de MinDinners
     * @var string
     */
    public $eMinDinersClass = "";

    /**
     * Mensaje de error en el máximo número de comensales
     * @var string
     */
    public $eMaxDiners = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de MaxDinners
     * @var string
     */
    public $eMaxDinersClass = "";

    /**
     * Mensaje de error en el TimeSpan
     * @var string
     */
    public $eTimeSpan = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de TimeSpan
     * @var string
     */
    public $eTimeSpanClass = "";

    /**
     * Mensaje de error en el filtro de tiempo
     * @var string
     */
    public $eTimeFilter = "";

    /**
     * Clase CSS a utilizar en el mensaje de error del filtro de tiempo
     * @var string
     */
    public $eTimeFilterClass = "";

    /**
     * Mensaje de error en el número de comensales
     * @var string
     */
    public $eDiners = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de Dinners
     * @var string
     */
    public $eDinersClass = "";

    /**
     * Mensaje para el resultado de la operación
     * @var string
     */
    public $eResult = "";

    /**
     * Clase CSS a aplicar en el mensaje de resultado
     * @var string
     */
    public $eResultClass = "";

    /**
     * Colección de códigos de error obtenidos en la validación
     * @var array
     */
    public $Codigos = [];

    /**
     * Tabla de traducción de códigos de error
     * @var array
     */
    public $Codes = [];

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        $this->Title = "Configuración";
        $filter = ["Project" => $this->Project ];
        $configs = $this->Dao->GetByFilter("ConfigurationService", $filter);
        $this->Entity = (empty($configs))
                ? new \ConfigurationService() : $configs[0];
        $this->SetCodes();
    }

    /**
     * Proceso para almacenar la información de configuración del servicio
     * @param \ConfigurationService $entity Referencia a la entidad a guardar
     */
    public function Save($entity = null){
        if($this->Validate($entity)){
            $entity->Project = $this->Project;
            $entity->Service = $this->Service;
            if($this->Entity->Id == 0){
                $entity->Id = $this->Dao->Create($entity);
            }
            else{
                $entity->Id = $this->Entity->Id;
                $this->Dao->Update($entity);
            }
            $this->Entity = $entity;
            $this->eResult = "La información se ha guardado correctamente.";
            $this->eResultClass = "has-success";
        }
        else{
            $this->eResult = "Por favor, revise los campos del formulario.";
            $this->eResultClass = "has-error";
            $this->TranslateResultCodes($this->Codigos);
        }
    }

    /**
     * Proceso de validación de la entidad
     * @param \ConfigurationService $entity Referencia a la entidad
     * @return type
     */
    public function Validate($entity = null){
        if($entity == null){
            $this->Codigos[] = -1;
        }
        else{
            $this->ValidateDiners($entity);
            $this->ValidateReminders($entity);
        }

        return empty($this->Codigos);
    }

    /**
     * Proceso de validación de el número de comensales
     * @param \ConfigurationService $entity Referencia a la entidad
     */
    private function ValidateDiners($entity = null){
        if(empty($entity->MaxDiners)){
            $this->Codigos[] = -2;
        }
        elseif(!is_numeric($entity->MaxDiners)){
            $this->Codigos[] = -3;
        }
        elseif($entity->MaxDiners < 1){
            $this->Codigos[] = -4;
        }
        if(empty($entity->MinDiners)){
            $this->Codigos[] = -5;
        }
        elseif(!is_numeric($entity->MinDiners)){
            $this->Codigos[] = -6;
        }
        elseif($entity->MinDiners < 1){
            $this->Codigos[] = -7;
        }
        if($entity->MaxDiners <= $entity->MinDiners){
            $this->Codigos[] = -8;
        }
    }

    /**
     * Proceso de validación de la información para recordatorios
     * @param \ConfigurationService $entity Referencia a la entidad
     */
    private function ValidateReminders($entity = null){
        if($entity->Reminders == true){
            if(empty($entity->TimeSpan)){
                $this->Codigos[] = -9;
            }
            elseif(!is_numeric($entity->TimeSpan)){
                $this->Codigos[] = -10;
            }
            elseif($entity->TimeSpan < 1){
                $this->Codigos[] = -11;
            }

            if(empty($entity->Diners)){
                $this->Codigos[] = -12;
            }
            elseif(!is_numeric($entity->Diners)){
                $this->Codigos[] = -13;
            }
            elseif($entity->Diners < 1){
                $this->Codigos[] = -14;
            }
        }
    }

    /**
     * Establece el array de "traducción" de códigos de error
     */
    private function SetCodes(){
        $this->Codes = [
            -1 => [ "name" => "eResult", "msg" => "Se ha producido un error"],
            -2 => [ "name" => "eMaxDiners", "msg" => "Debe especificar el "
                . "máximo de comensales"],
            -3 => [ "name" => "eMaxDiners", "msg" => "El tipo de dato no es correcto"],
            -4 => [ "name" => "eMaxDiners", "msg" => "El máximo de comensales "
                . "no puede ser menor que 1"],
            -5 => [ "name" => "eMinDiners", "msg" => "Debe especificar el "
                . "mínimo de comensales"],
            -6 => [ "name" => "eMinDiners", "msg" => "El tipo de dato no es correcto"],
            -7 => [ "name" => "eMinDiners", "msg" => "El mínimo de comensales "
                . "no puede ser menor que 1"],
            -8 => [ "name" => "eMinDiners", "msg" => "El mínimo de comensales "
                . "no puede ser mayor que el maximo"],
            -9 => [ "name" => "eTimeSpan", "msg" => "Debe especificar el timespan"],
            -10 => [ "name" => "eTimeSpan", "msg" => "El tipo de dato no es correcto"],
            -11 => [ "name" => "eTimeSpan", "msg" => "El timespan no puede ser "
                . "menor que 1"],
            -12 => [ "name" => "eDiners", "msg" => "Debe especificar el número "
                . "de comensales"],
            -13 => [ "name" => "eDiners", "msg" => "El tipo de dato no es correcto"],
            -14 => [ "name" => "eDiners", "msg" => "El número de comensales no "
                . "puede ser menor que 1"]
        ];
    }

    /**
     * Establece los mensajes de error a partir de los codigos obtenidos
     * en la operacion anterior.
     * @param array $codes Coleccion de codigos de error obtenidos
     */
    private function TranslateResultCodes($codes = null){
        if($codes != null && is_array($codes)){
            foreach ($codes as $code){
                if(!isset($this->Codes[$code])){
                    continue;
                }
                $codeInfo = $this->Codes[$code];
                $class = ($code == 0) ? "has-success" : "has-error";
                $this->{$codeInfo["name"]} = $codeInfo["msg"];
                $this->{$codeInfo["name"]."Class"} = $class;
            }
        }
    }
}
