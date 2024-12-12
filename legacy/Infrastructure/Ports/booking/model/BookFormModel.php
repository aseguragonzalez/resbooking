<?php

declare(strict_types=1);

/**
 * Implementación de un objeto de negocio para gestionar la información
 * de una reserva. Se utiliza para controlar la información disponible
 * en el formulario y validar las opciones seleccionadas
 *
 * @author alfonso
 */
class BookFormModel extends \ResbookingModel{

    /**
     * Texto de error para el dato Fecha
     * @var string
     */
    public $eDate = "";

    /**
     * Clase CSS a utilizar en el control de error de selección de fecha
     * @var string
     */
    public $eDateClass = "";

    /**
     * Texto de error para el dato Comensales
     * @var string
     */
    public $eDiners = "";

    /**
     * Clase CSS a utilizar en el control de error del número de comensales
     * @var string
     */
    public $eDinersClass = "";

    /**
     * Texto de error para el dato oferta
     * @var string
     */
    public $eOffer = "";

    /**
     * Clase CSS a utilizar en el control de error de oferta
     * @var string
     */
    public $eOfferClass = "";

    /**
     * Texto de error para el dato e-mail
     * @var string
     */
    public $eEmail = "";

    /**
     * Clase CSS a utilizar en el control de error del email
     * @var string
     */
    public $eEmailClass = "";

    /**
     * Texto de error para el dato teléfono
     * @var string
     */
    public $ePhone = "";

    /**
     * Clase CSS a utilizar en el control de error del teléfono
     * @var string
     */
    public $ePhoneClass = "";

    /**
     * Texto de error para el dato Turno
     * @var string
     */
    public $eTurn = "";

    /**
     * Clase CSS a utilizar en el control de error de selección de turno
     * @var string
     */
    public $eTurnClass = "";

    /**
     * Texto de error para el dato Espacio|Lugar
     * @var string
     */
    public $ePlace = "";

    /**
     * Clase CSS a utilizar en el control de error de Espacio/Lugar
     * @var string
     */
    public $ePlaceClass = "";

    /**
     * Texto de error para el dato
     * @var string
     */
    public $eClientName = "";

    /**
     * Clase CSS a utilizar en el control de error del nombre de cliente
     * @var string
     */
    public $eClientNameClass = "";

    /**
     * Colección de turnos configurados
     * @var array
     */
    public $Turns = [];

    /**
     * Colección de ofertas activas
     * @var array
     */
    public $Offers = [];

    /**
     * Colección de espacios activos
     * @var array
     */
    public $Places = [];

    /**
     * Serialización JSON de bloqueos y aperturas disponibles
     * @var string
     */
    public $Blocks = "[]";

    /**
     * Serialización JSON de eventos de ofertas
     * @var string
     */
    public $OffersEvents = "[]";

    /**
     * Combo selección de comensales
     * @var array
     */
    public $DinersLst = [];

    /**
     * Tabla de traducción de códigos de error
     * @var array
     */
    protected $Codes = [];

    /**
     * Referencia al management
     * @var \IBookingManagement
     */
    protected $Management = null;

    /**
     * Referencia al agregado
     * @var \BookingAggregate
     */
    protected $aggregate = null;

    /**
     * Constructor
     */
    public function __construct(){
        parent::__construct();
        $this->Title = "Reservar";
        $this->Management = BookingManagement::
                GetInstance($this->Project, $this->Service);
        $this->aggregate = $this->Management->GetAggregate();

        $diners = ($this->aggregate->MinDiners <= 2)
                ? 2: $this->aggregate->MinDiners;
        $this->Entity = new \Booking();
        $this->Entity->Diners = $diners;
        $this->Entity->Comment = "";

        $this->SetCodes();
    }

    /**
     * Configura la información del formulario
     */
    public function SetForm(){
        $this->Turns = $this->aggregate->Turns;
        foreach($this->Turns as $turn){
            if(isset($turn->Days)){
                $turn->Days = json_encode($turn->Days);
            }
            if(strlen($turn->Start) > 5){
                $turn->Start = substr($turn->Start, 0, 5);
            }
        }

        $this->Offers = $this->aggregate->AvailableOffers;
        foreach($this->Offers as $offer){
            $offer->Config = json_encode($offer->Config);
        }

        for($i = $this->aggregate->MinDiners;
                $i <= $this->aggregate->MaxDiners; $i++){
            $this->DinersLst[] = new SelectControlItem($i, $i);
        }

        $this->Places = $this->aggregate->AvailablePlaces;

        $this->Blocks = [];
        foreach($this->aggregate->AvailableBlocks as $block){
            $this->Blocks[] = $block;
        }
        $this->Blocks = json_encode($this->Blocks);

        $this->OffersEvents = [];
        foreach($this->aggregate->AvailableOffersEvents as $event){
            $this->OffersEvents[] = $event;
        }
        $this->OffersEvents = json_encode($this->OffersEvents);
    }

    /**
     * Proceso de guardado de reserva manual
     * @param \Booking $entity Referencia a la reserva
     * @return boolean Resultado de la operación
     */
    public function Save($entity = null){
        $ars = [" ", "-", "(", ")"];
        $arr = ["", "", "", ""];
        if($entity != null){
            $entity->Project = $this->Project;
            $entity->State = ConfigurationManager::GetKey("reservado");
            $entity->Phone = trim(str_replace($ars, $arr, $entity->Phone));
            $entity->BookingSource = 2;
        }
        // Asignar la oferta
        $value = intval($entity->Offer);
        $entity->Offer = (is_numeric($value) && $value > 0) ? $value : null;
        // Guardamos la fecha de la solicitud
        $date = $entity->Date;
        // realigar el guardado
        $result = $this->Management->RegisterBooking($entity, true, false);
        // Establecer los mensajes de error
        $this->TranslateResultCodes($result);
        // Reasignamos la fecha
        $entity->Date = $date;
        // Establecer la entidad en el modelo
        $this->Entity = $entity;
        // Retornar el resultado de la operación
        return (count($result) == 1 && $result[0] >= 0);
    }

    /**
     * Establece el array de "traducción" de códigos de error
     */
    private function SetCodes(){
        $this->Codes = array(
            0 => array( "name" => "eResult",
                "msg" => "La reserva se ha realizado correctamente"),
            -1 => array( "name" => "eClientName",
                "msg" => "Debe especificar un nombre."),
            -2 => array( "name" => "eClientName",
                "msg" => "El tipo de dato no es correcto."),
            -3 => array( "name" => "eClientName",
                "msg" => "La longitud del nombre de cliente supera "
                . "el máximo de caracteres (100)."),
            -4 => array( "name" => "eEmail",
                "msg" => "Debe especificar una dirección de email."),
            -5 => array( "name" => "eEmail",
                "msg" => "El email proporcionado no tiene "
                . "el formato correcto."),
            -6 => array( "name" => "eEmail",
                "msg" => "La longitud de e-mail supera el "
                . "máximo de caracteres (100)."),
            -7 => array( "name" => "ePhone",
                "msg" => "Debe especificar un número de teléfono."),
            -8 => array( "name" => "ePhone",
                "msg" => "El tipo de dato no es correcto."),
            -9 => array( "name" => "ePhone",
                "msg" => "La longitud del teléfono de contacto es"
                . " superior a 15 caracteres"),
            -10 => array( "name" => "eDiners",
                "msg" => "Debe seleccionar un número de comensales."),
            -11 => array( "name" => "eDiners",
                "msg" => "El tipo de dato no es correcto."),
            -12 => array( "name" => "eDiners",
                "msg" => "El número de comensales es superior al máximo."),
            -13 => array( "name" => "eDiners",
                "msg" => "El número de comensales es inferior al mínimo."),
            -14 => array( "name" => "eDate",
                "msg" => "Debe seleccionar una fecha para la reserva"),
            -15 => array( "name" => "eDate",
                "msg" => "La fecha seleccionada no es válida"),
            -16 => array( "name" => "eDate",
                "msg" => "La fecha seleccionada es anterior al día de hoy"),
            -17 => array( "name" => "ePlace",
                "msg" => "Debe seleccionar un lugar"),
            -18 => array( "name" => "ePlace",
                "msg" => "El lugar seleccionado no es válido."),
            -19 => array( "name" => "ePlace",
                "msg" => "El lugar seleccionado no está disponible."),
            -20 => array( "name" => "eTurn",
                "msg" => "Debe seleccionar un turno."),
            -21 => array( "name" => "eTurn",
                "msg" => "El turno seleccionado no es válido."),
            -22 => array( "name" => "eTurn",
                "msg" => "El turno no está disponible en "
                . "la fecha seleccionada."),
            -23 => array( "name" => "eTurn",
                "msg" => "El turno no está disponible en "
                . "la fecha seleccionada"),
            -24 => array( "name" => "eOffer",
                "msg" => "La oferta seleccionada no está disponible."),
            -25 => array( "name" => "eOffer",
                "msg" => "La oferta no es válida "
                . "en la fecha seleccionada."),
            -26 => array( "name" => "eOffer",
                "msg" => "La oferta no está disponible en "
                . "el turno y fecha seleccionados."),
            -27 => array( "name" => "eTurn",
                "msg" => "El turno seleccionado no es válido")
        );
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
