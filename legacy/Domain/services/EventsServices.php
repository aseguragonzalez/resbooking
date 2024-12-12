<?php

declare(strict_types=1);

/**
 * Capa de servicio para la gestión de eventos
 *
 * @author manager
 */
class EventsServices extends \BaseServices implements \IEventsServices{

    /**
     * Referencia
     * @var \IEventsServices
     */
    private static $_reference = null;

    /**
     * Referencia al repositorio actual
     * @var \IEventsRepository
     */
    protected $repository = null;

    /**
     * Referencia al agregado
     * @var \EventsAggregate
     */
    protected $aggregate = null;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \EventsAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = null) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->repository = EventsRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \EventsAggregate Referencia al agregado actual
     * @return \IEventsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = null){
        if(EventsServices::$_reference == null){
            EventsServices::$_reference = new \EventsServices($aggregate);
        }
        return EventsServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotEvent $entity Referencia a la entidad
     * @return boolean|array Devuelve true si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = null){
        if($entity != null){
            $this->ValidateProject($entity->Project);
            $this->ValidateSlot($entity->SlotOfDelivery);
            $this->ValidateDate($entity->Date);
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
     * Proceso de validación del slot
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
     * Proceso de validación de la fecha asociada al evento
     * @param string $sDate Fecha del evento
     */
    private function ValidateDate($sDate = ""){
        try{
            if(empty($sDate)){
                $this->Result[] = -9;
                return;
            }

            $yesterday = new \DateTime("YESTERDAY");

            $date = new \DateTime($sDate);

            if($date <= $yesterday){
                $this->Result[] = -10;
            }
        }
        catch(Exception $e){
            $this->Result[] = -11;
        }
    }
}
