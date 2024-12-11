<?php

declare(strict_types=1);

/**
 * Capa de servicios para la gestión de turnos de reparto
 *
 * @author manager
 */
class SlotsOfDeliveryServices extends \BaseServices
    implements \ISlotsOfDeliveryServices{

    /**
     * Referencia
     * @var \ISlotsOfDeliveryServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \ISlotsOfDeliveryRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \SlotsOfDeliveryAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \SlotsOfDeliveryAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {
        // Constructor de la clase padre
        parent::__construct($aggregate);
        // Obtener instancia del repositorio
        $this->Repository = SlotsOfDeliveryRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \RequestsAggregate Referencia al agregado actual
     * @return \IProductsServices Referencia a la instancia actual
     */
    public static function GetInstance($aggregate = NULL){
        if(SlotsOfDeliveryServices::$_reference == NULL){
            SlotsOfDeliveryServices::$_reference =
                    new \SlotsOfDeliveryServices($aggregate);
        }
        return SlotsOfDeliveryServices::$_reference;
    }

    /**
     * Proceso de validación de la entidad
     * @param \SlotOfDelivery $entity Referencia a la entidad
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        if($entity != NULL){
            $this->ValidateProject($entity->Project);
            $this->ValidateName($entity->Id, $entity->Name);
            $this->ValidateStart($entity->Id, $entity->Start);
            $this->ValidateEnd($entity->Id, $entity->End);
            $this->ValidateStartEnd($entity->Start, $entity->End);
        }
        else{
            $this->Result[] = -3;
        }
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del proyecto asociado
     * @param int $id Identidad del proyecto asociado
     */
    private function ValidateProject($id = 0){
        if(empty($id)){
            $this->Result[] = -4;
        }
        else if($id < 1){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del nombre para el turno de reparto
     * @param int $id Identidad del turno si existe
     * @param string $name Nombre asignado al turno
     */
    private function ValidateName($id = 0, $name = ""){
        if(empty($name)){
            $this->Result[] = -6;
        }
        elseif(strlen($name) > 45){
            $this->Result[] = -7;
        }
        else{
            $this->ValidateExistName($id, $name);
        }
    }

    /**
     * Proceso de validación para comprobar si el nombre ya está registrado
     * @param int $id Identidad del turno de reparto
     * @param string $name Nombre asignado al turno
     */
    private function ValidateExistName($id = 0, $name = ""){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Name" => $name, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter($this->Aggregate->Slots, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && current($items)->Id != $id){
            $this->Result[] = -8;
        }
    }

    /**
     * Proceso de validación de la hora de inicio del turno
     * @param int $id Identidad del turno si ya existe
     * @param int $start Identidad de la hora de inicio del turno
     */
    private function ValidateStart($id = 0, $start = 0){
        if(empty($start)){
            $this->Result[] = -9;
        }
        elseif(!$this->ValidateExistHour($start)){
            $this->Result[] = -10;
        }
        else{
            $this->ValidateExistStart($id, $start);
        }
    }

    /**
     * Proceso de validación para comprobar si ya existe un turno activo con
     * la misma hora de inicio
     * @param int $id Identidad del registro de turno si ya existe
     * @param int $start Identidad de la hora de inicio del turno
     */
    private function ValidateExistStart($id = 0, $start = 0){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "Start" => $start, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter(
                    $this->Aggregate->Slots, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && current($items)->Id != $id){
            $this->Result[] = -12;
        }
    }

    /**
     * Proceso de validación de la hora de finalización del turno de reparto
     * @param int $id Identidad del turno de reparto
     * @param int $end Identidad de la hora de finalización del turno
     */
    private function ValidateEnd($id = 0, $end = 0){
        if(empty($end)){
            $this->Result[] = -13;
        }
        elseif(!$this->ValidateExistHour($end)){
            $this->Result[] = -14;
        }
        else{
            $this->ValidateExistEnd($id, $end);
        }
    }

    /**
     * Proceso de validación para comprobar si ya existe un turno activo con
     * la misma hora de finalización
     * @param int $id Identidad del turno si ya existe
     * @param int $end Identidad de la hora de finalización del turno
     */
    private function ValidateExistEnd($id = 0, $end = 0){
        // filtro de búsqueda
        $filter = [ "Project" => $this->IdProject,
            "End" => $end, "State" => 1 ];
        // buscar algún item con el mismo código
        $items = $this->GetListByFilter(
                    $this->Aggregate->Slots, $filter);
        // Comprobar el resultado de la búsqueda
        if(isset($items) && is_array($items)
                && count($items) != 0 && current($items)->Id != $id){
            $this->Result[] = -16;
        }
    }

    /**
     * Proceso de validación sobre el orden de las horas deliminates del turno
     * @param int $start Identidad de la hora de inicio del turno
     * @param int $end Identidad de la hora de finalización del turno
     */
    private function ValidateStartEnd($start = 0, $end = 0){
        if(empty($start) || empty($end)){
            $this->Result[] = -17;
        }
        else if(!$this->CompareHour($start, $end)){
            $this->Result[] = -18;
        }
    }

    /**
     * Proceso de validación en el que se comprueba que existe
     * el registro de hora identificado
     * @param int $id Identidad del registro de hora
     */
    private function ValidateExistHour($id = 0){
        $hour = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($id){
            return $item->Id == $id;
        });
        return count($hour) == 1;
    }

    /**
     * Proceso de validación para comprobar que la hora de inicio es menor
     * que la hora de finalización
     * @param int $iStart Identidad de la hora de inicio
     * @param int $iEnd Identidad de la hora de finalización
     * @return boolean
     */
    private function CompareHour($iStart = 0, $iEnd = 0){
        $sHours = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($iStart){return $item->Id == $iStart;});
        $start = current($sHours);

        $eHours = array_filter($this->Aggregate->HoursOfDay,
                function($item) use($iEnd){return $item->Id == $iEnd;});
        $end = current($eHours);

        // Partimos las cadenas con format [hh:mm] por ":"
        $aStart = explode(":", $start->Text);
        $aEnd = explode(":", $end->Text);
        // Obtener horas
        $hStart = intval($aStart[0]);
        $hEnd = intval($aEnd[0]);
        // Proceso de comparación
        if($hStart > $hEnd){
            return FALSE;
        }
        else if($hStart == $hEnd){
            // comparar minutos
            if(intval($aStart[1]) >= intval($aEnd[1])){
                return FALSE;
            }
        }
        return TRUE;
    }
}
