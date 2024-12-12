<?php

declare(strict_types=1);

/**
 * Implementación del contrato(Interface) para el gestor de la capa
 * de aplicación para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryManagement extends \BaseManagement
    implements \ISlotsOfDeliveryManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \ISlotsOfDeliveryServices
     */
    protected $Services = null;

    /**
     * Referencia al respositorio de reservas
     * @var \ISlotsOfDeliveryRepository
     */
    protected $repository = null;

    /**
     * Referencia a la instancia de management
     * @var \ISlotsOfDeliveryManagement
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
        $this->repository = SlotsOfDeliveryRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->aggregate = $this->repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = SlotsOfDeliveryServices::GetInstance($this->aggregate);
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @return \BaseLineAgregate
     */
    public function GetAggregate() {

        $this->aggregate->SetAggregate();

        return $this->aggregate;
    }

    /**
     * Obtiene una instancia del Management
     * @param int $project identidad del proyecto del contexto
     * @param int $service identidad del servicio
     * @return \ISlotsOfDeliveryManagement
     */
    public static function GetInstance($project = 0, $service = 0) {
        if(SlotsOfDeliveryManagement::$_reference == null){
            SlotsOfDeliveryManagement::$_reference =
                   new \SlotsOfDeliveryManagement($project, $service);
        }
        return SlotsOfDeliveryManagement::$_reference;
    }

    /**
     * Proceso para cargar la información del turno de reparto indicado
     * mediante su identidad en el agregado.
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function GetSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById(
                $this->aggregate->Slots, $id);
        if($slot != null){

            $this->aggregate->Slot = $slot;

            return 0;
        }
        return -1;
    }

    /**
     * Proceso para almacenar la información de un turno de reparto
     * @param \SlotOfDelivery $slot Referencia a la entidad a guardar
     * @return array Códigos de operación
     */
    public function SetSlot($slot = null) {
        $slot->Project = $this->IdProject;
        $result = $this->Services->Validate($slot);
        if(!is_array($result) && $result == true ){
            $result = [];
            if($slot->Id == 0){
                $res = $this->repository->Create($slot);
                $result[] = ($res != false) ? 0 : -1;
                $slot->Id = ($res != false) ? $res->Id : 0;
            }
            else{
                $res = $this->repository->Update($slot);
                $result[] = ($res != false) ? 0 : -2;
            }
            if($res != false){
                $this->aggregate->Slots[$slot->Id] = $slot;
            }
        }
        return $result;
    }

    /**
     * Proceso para eliminar el registro de un turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return int Código de operación
     */
    public function RemoveSlot($id = 0) {
        // Obtener referencia
        $slot = $this->Services->GetById($this->aggregate->Slots, $id);
        if($slot != null){
            // Establecer el estado
            $slot->State = 0;
            // Actualizar
            $res = ($this->repository->Update($slot) != false);

            if($res){
                // Eliminar todas las entidades relacionadas
                $this->RemoveRelations($id);

                unset($this->aggregate->Slots[$id]);
            }

            return  $res ? 0 : -1;
        }
        return -2;
    }

    /**
     * @ignore
     * Cargar toda la información del agregado  para el
     * proyecto y servicio indicado
     */
    private function LoadAggregate(){
        $agg = new \SlotsOfDeliveryAggregate();
        $agg->IdProject = $this->IdProject;
        $agg->IdService = $this->IdService;
        $this->aggregate = $this->GetFromRepository($agg);
        $this->aggregate->SetAggregate();
    }

    /**
     * Proceso de carga de los datos de agregado
     * @param \SlotsOfDeliveryAggregate $agg Referencia al agregado a completar
     * @return \SlotsOfDeliveryAggregate
     */
    private function GetFromRepository($agg = null){

        // Cargar las horas disponibles
        $agg->HoursOfDay = $this->repository->
                GetByFilter( "HourOfDay", ["State" => 1] );

        $filter = ["Project" => $this->IdProject];

        $slots = $this->repository->GetByFilter( "SlotOfDelivery", $filter );

        foreach($slots as $slot){
            $agg->Slots[$slot->Id] = $slot;
        }

        return $agg;
    }

    /**
     * Elimina todos los registros relacionados con el turno de reparto
     * @param int $id Identidad del turno de reparto
     * @return boolean
     */
    private function RemoveRelations($id = 0){

        $filter = [ "SlotOfDelivery" => $id ];

        $slotsEvents = $this->repository->GetByFilter( "SlotEvent", $filter );

        foreach($slotsEvents as $item){
            $this->repository->Delete( "SlotEvent", $item->Id );
        }

        $slotsConfigured =
                $this->repository->GetByFilter( "SlotConfigured", $filter );

        foreach($slotsConfigured as $item){
            $this->repository->Delete( "SlotConfigured", $item->Id );
        }

        $discountsOnConfiguration =
                $this->repository->GetByFilter( "DiscountOnConfiguration",
                        $filter );

        foreach($discountsOnConfiguration as $item){
            $this->repository->Delete( "DiscountOnConfiguration", $item->Id );
        }

        return true;
    }
}
