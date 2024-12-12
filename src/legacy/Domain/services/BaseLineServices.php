<?php

declare(strict_types=1);

/**
 * Capa de servicio para la configuración de línea base
 *
 * @author manager
 */
class BaseLineServices extends \BaseServices implements \IBaseLineServices{

    /**
     * Referencia
     * @var \IBaseLineServices
     */
    private static $_reference = null;

    /**
     * Referencia al repositorio actual
     * @var \IBaseLineRepository
     */
    protected $repository = null;

    /**
     * Referencia al agregado
     * @var \BaseLineAggregate
     */
    protected $aggregate = null;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \BaseLineAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = null) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->repository = BaseLineRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \CategoriesAggregate Referencia al agregado actual
     * @return \IBaseLineServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null){
        if(BaseLineServices::$_reference == null){
            BaseLineServices::$_reference = new \BaseLineServices($aggregate);
        }
        return BaseLineServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotConfigured $entity Referencia a la entidad
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null){
        if($entity != null){
            $this->ValidateProject($entity->Project);
            $this->ValidateSlot($entity->SlotOfDelivery);
            $this->ValidateDayOfWeek($entity->DayOfWeek);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? true : $this->Result;
    }

    /**
     * Proceso de validación del proyecto asociado
     * @param int $id Identidad del proyecto asociado
     */
    private function ValidateProject($id = 0){
        if(empty($id)){
            $this->Result[] = -4;
        }
        elseif($id < 1){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del slot asociado a la configuración
     * @param int $slot Identidad del slot asociado
     */
    private function ValidateSlot($slot = 0){
        if(empty($slot)){
            $this->Result[] = -6;
        }
        elseif($slot < 1){
            $this->Result[] = -7;
        }
        else{
            $s = $this->GetById(
                    $this->aggregate->AvailableSlotsOfDelivery, $slot);
            if($s == null){
                $this->Result[] = -8;
            }
        }
    }

    /**
     * Proceso de validación del día de la semana seleccionado
     * @param int $dayOfWeek Día de la semana asociado
     */
    private function ValidateDayOfWeek($dayOfWeek = 0){
        if(empty($dayOfWeek)){
            $this->Result[] = -9;
        }
        elseif($dayOfWeek < 1){
            $this->Result[] = -10;
        }
        else{
            $s = $this->GetById(
                    $this->aggregate->DaysOfWeek, $dayOfWeek);
            if($s == null){
                $this->Result[] = -11;
            }
        }
    }
}
