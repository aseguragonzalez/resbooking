<?php

declare(strict_types=1);

/**
 * Modelo para la gestión de Turnos de reparto
 *
 * @author manager
 */
class DeliveryTimeModel extends \TakeawayModel{

    /**
     * Indica si se ha producido un error durante la última operación
     * @var boolean
     */
    public $Error = FALSE;

    /**
     * Referencia al turno de reparto en edición
     * @var \SlotOfDelivery
     */
    public $Entity = NULL;

    /**
     * Colección de turnos de reparto activos
     * @var array
     */
    public $Entities = [];

    /**
     * Horas para el inicio/fin del turno
     * @var type
     */
    public $Hours = [];

    /**
     * Mensaje de error en el nombre del turno de reparto
     * @var String
     */
    public $eName = "";

    /**
     * Clase CSS a utilizar en el mensaje de error del nombre de
     * turno de reparto
     * @var String
     */
    public $eNameClass = "";

    /**
     * Mensaje de error en la hora de inicio del turno de reparto
     * @var String
     */
    public $eStart = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de la hora
     * de inicio del turno de reparto
     * @var String
     */
    public $eStartClass = "";

    /**
     * Mensaje de error en la hora de fin del turno de reparto
     * @var String
     */
    public $eEnd = "";

    /**
     * Clase CSS a utilizar en el mensaje de error de la hora
     * de fin del turno de reparto
     * @var String
     */
    public $eEndClass = "";

    /**
     * Mensaje de error en el icono utilizado
     * @var String
     */
    public $eIcoName = "";

    /**
     * Clase CSS a utilizar en el mensaje de error del nombre de icono
     * @var String
     */
    public $eIcoNameClass = "";

    /**
     * @ignore
     * Constructor
     */
    public function __construct(){
        parent::__construct(
                "Turnos",
                "Configuración",
                "SlotsOfDeliveryManagement");
        $this->SetModel();
    }

    /**
     * Configura el modelo con la información de los turnos de reparto
     */
    public function GetDeliveryTimes(){
        foreach($this->Entities as $item){
            $item->StartT = $this->GetHourText($item->Start);
            $item->EndT = $this->GetHourText($item->End);
        }
    }

    /**
     * Proceso de almacenamiento de un turno de reparto y configuración
     * del modelo para visualizar los resultados de la operación
     * @param \SlotOfDelivery $entity Referencia al turno de reparto
     */
    public function Save($entity = NULL){

        $this->Error = TRUE;

        $result = $this->Management->SetSlot($entity);

        if(is_array($result) == FALSE){
            throw new Exception("Save: SetSlot: "
                    . "Códigos de operación inválidos");
        }

        if(count($result) != 1 || $result[0] != 0){
            $this->TranslateResultCodes(_OP_CREATE_, $result);
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
            $this->Entities[$entity->Id] = $entity;
            $this->Error = FALSE;
        }
        $this->Entity = $entity;
    }

    /**
     * Proceso de eliminación de un turno de reparto y configuración
     * del modelo para visualizar los resultados de la operación
     * @param int $id Identidad del turno
     */
    public function Delete($id = 0){

        $result = $this->Management->RemoveSlot($id);

        if($result != 0){
            $this->TranslateResultCodes(_OP_DELETE_, [$result]);
        }
        else{
            $this->eResult = "La operación se ha realizado satisfactoriamente.";
            $this->eResultClass="has-success";
            unset($this->Entities[$id]);
        }
    }

    /**
     * Configuración estándar del modelo
     */
    protected function SetModel() {
        $this->Entity = new \SlotOfDelivery();
        $this->Entities = $this->Aggregate->AvailableSlots;
        $this->Hours = $this->Aggregate->HoursOfDay;
    }

    /**
     * Configuración de los códigos de resultado
     */
    protected function SetResultCodes() {
        $this->Codes = [
            _OP_CREATE_ => $this->GetSaveMessages(),
            _OP_READ_ => $this->GetReadMessages(),
            _OP_UPDATE_ => $this->GetSaveMessages(),
            _OP_DELETE_ => $this->GetDeleteMessages()];
    }

    /**
     * Obtiene el texto correspondiente a la hora indicada por su Id
     * @param int $id Identidad del registro hora
     * @return string Texto asociado
     */
    private function GetHourText($id = 0){
        $hour = array_filter($this->Hours, function($item)
                use ($id){
            return $item->Id == $id;
        });

        if(!empty($hour)){
            return current($hour)->Text;
        }
        return $id;
    }

    /**
     * Obtiene los mensajes de error al "leer" un turno desde
     * el repositorio principal
     * @return array
     */
    private function GetReadMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha encontrado el turno solicitado"]
            ];
    }

    /**
     * Obtiene los mensajes de error al "eliminar" un turno
     * en el repositorio principal
     * @return array
     */
    private function GetDeleteMessages(){
        return [
                -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
                -2 => ["name" => "eResult",
                    "msg" => "El turno no ha sido encontrado" ]
                ];
    }

    /**
     * Obtiene los mensajes de error al "guardar" la información
     * de un turno de reparto en el repositorio principal
     * @return array
     */
    private function GetSaveMessages(){
        return [
            -1 => ["name" => "eResult",
                    "msg" => "No se ha podido realizar la eliminación" ],
            -2 => ["name" => "eResult",
                "msg" => "El turno no ha sido encontrado" ],
            -3 => ["name" => "eResult",
                "msg" => "El turno no ha sido encontrado" ],
            -4 => ["name" => "eResult",
                "msg" => "No se ha asignado un proyecto" ],
            -5 => ["name" => "eResult",
                "msg" => "El proyecto asignado no es valido" ],
            -6 => ["name" => "eName",
                    "msg" => "Debe asignar un nombre" ],
            -7 => ["name" => "eName",
                "msg" => "El nombre no puede tener más de 45 caracteres" ],
            -8 => ["name" => "eName",
                "msg" => "Ya existe otro turno con el mismo nombre." ],
            -9 => ["name" => "eStart",
                "msg" => "Debe seleccionar una hora de inicio" ],
            -10 => ["name" => "eStart",
                "msg" => "La hora de inicio no es válida" ],
            -11 => ["name" => "eStart",
                    "msg" => "El dato introducido no tiene el formato correcto" ],
            -12 => ["name" => "eStart",
                "msg" => "Ya existe un turno con la misma hora de inicio" ],
            -13 => ["name" => "eEnd",
                "msg" => "Debe seleccionar una hora de finalización" ],
            -14 => ["name" => "eEnd",
                "msg" => "La hora de finalización no es válida" ],
            -15 => ["name" => "eEnd",
                "msg" => "El dato introducido no tiene el formato correcto" ],
            -16 => ["name" => "eEnd",
                "msg" => "Ya existe un turno con la misma hora de finalización." ],
            -17 => ["name" => "eFormResult",
                "msg" => "Debe seleccionar hora de inicio y fin" ],
            -18 => ["name" => "eFormResult",
                "msg" => "La hora de finalización no puede ser mayor que la de fin" ],
            ];
    }
}
