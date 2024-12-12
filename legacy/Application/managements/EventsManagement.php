<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para configuraciones de eventos
 *
 * @author manager
 */
class EventsManagement extends \BaseManagement implements \IEventsManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \IEventsServices
     */
    protected $Services = null;

    /**
     * Referencia al respositorio de reservas
     * @var \IEventsRepository
     */
    protected $repository = null;

    /**
     * Referencia a la instancia de management
     * @var \IEventsManagement
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
        $this->repository = EventsRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->aggregate = $this->repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = EventsServices::GetInstance($this->aggregate);
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \IEventsManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(EventsManagement::$_reference == null){
            EventsManagement::$_reference =
                   new \EventsManagement($project, $service);
        }
        return EventsManagement::$_reference;
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \EventsAggregate
     */
    public function GetAggregate() {

        $this->aggregate->SetAggregate();

        return $this->aggregate;
    }

    /**
     * Proceso para cargar en el agregado la información del evento
     * indicado mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function GetEvent($id = 0) {
        // Obtener referencia
        $event = $this->Services->GetById(
                $this->aggregate->Events, $id);
        if($event != null){

            $this->aggregate->Category = $event;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información del evento actual
     * @param \SlotEvent $event Referencia a la entidad
     * @return array Códigos de operación
     */
    public function SetEvent($event = null) {
        $event->Project = $this->IdProject;
        $result = $this->Services->Validate($event);
        if(!is_array($result) && $result == true ){
            $result = [];
            if($event->Id == 0){
                $res = $this->repository->Create($event);
                $result[] = ($res != false) ? 0 : -1;
                $event->Id = ($res != false) ? $res->Id : 0;
            }
            else{
                $res = $this->repository->Update($event);
                $result[] = ($res != false) ? 0 : -2;
            }

            if($res != false){
                $this->aggregate->Events[$event->Id] = $event;
            }
        }

        return $result;
    }

    /**
     * Proceso para eliminar un evento del registro
     * mediante su identidad
     * @param int $id Identidad del evento
     * @return int Código de operación
     */
    public function RemoveEvent($id = 0) {
        // Obtener referencia
        $event = $this->Services->GetById(
                $this->aggregate->Events, $id);
        if($event != null){

            $result = $this->repository->Delete("SlotEvent", $id);

            if($result == 0){

                unset($event);

                return 0;
            }
            return -1;
        }
        return -2;
    }
}
