<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Model para la creación de reservas
 *
 * @author alfonso
 */
class BookModel extends \ResbookingModel{

    /**
     * Mensaje de error en la fecha de reserva
     * @var string
     */
    public $eDate = "";

    /**
     * Mensaje de error en el turno seleccionado
     * @var string
     */
    public $eTurn = "";

    /**
     * Mensaje de error en el número de comensales
     * @var string
     */
    public $eDiners = "";

    /**
     * Mensaje de error en el lugar seleccionado
     * @var string
     */
    public $ePlace = "";

    /**
     * Mensaje de error en la oferta seleccionada
     * @var string
     */
    public $eOffer = "";

    /**
     * Mensaje de error en el nombre de cliente
     * @var string
     */
    public $eClientName = "";

    /**
     * Mensaje de error en la dirección de e-mail
     * @var string
     */
    public $eEmail = "";

    /**
     * Mensaje de error en el teléfono de contacto
     * @var string
     */
    public $ePhone = "";

    /**
     * Mensaje del resultado de la operación
     * @var string
     */
    public $eResult = "";

    /**
     * Clase CSS utilizada en el mensaje de error de la fecha
     * @var string
     */
    public $eDateClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error del turno
     * @var string
     */
    public $eTurnClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error del número de comensales
     * @var string
     */
    public $eDinersClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error del lugar de reserva
     * @var string
     */
    public $ePlaceClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error de la oferta
     * @var string
     */
    public $eOfferClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error del cliente
     * @var string
     */
    public $eClientNameClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error de la dirección de email
     * @var string
     */
    public $eEmailClass = "";

    /**
     * Clase CSS utilizada en el mensaje de error del teléfono
     * @var string
     */
    public $ePhoneClass = "";

    /**
     * Clase CSS utilizada en el mensaje de resultado de operación
     * @var string
     */
    public $eResultClass = "has-success";

    /**
     * Consentimiento para guardar los datos de cliente
     * @var boolean
     */
    public $Legal = FALSE;

    /**
     * Referencia a la entidad
     * @var \Booking
     */
    public $Entity = NULL;

    /**
     * Colección de turnos configurados
     * @var array
     */
    public $Turns = [];

    /**
     * Colección de ofertas registradas
     * @var array
     */
    public $Offers = [];

    /**
     * Colección de espacios disponibles
     * @var array
     */
    public $Places = [];

    /**
     * Serialización de los bloqueos
     * @var string
     */
    public $Blocks = "[]";

    /**
     * Serialización de los eventos de ofertas
     * @var string
     */
    public $OffersEvents= "[]";

    /**
     * Serialización de cuotas de ofertas
     * @var string
     */
    public $OffersShare = "[]";

    /**
     * Serialización de cuotas de turno
     * @var string
     */
    public $TurnsShare = "[]";

    /**
     * Colección de opciones para el combo de selección de comensales
     * @var array
     */
    public $DinersLst = [];

    /**
     * Flag para indicar si el formulario contiene publicidad
     * @var int
     */
    public $Advertising = 0;

    /**
     * Flag para indicar si el formulario contiene pre-pedido
     * @var int
     */
    public $Preorder = 0;

    /**
     * Referencia al servicio actual
     * @var int
     */
    public $Service = 0;

    /**
     * Referencia al objeto Management de reservas
     * @var \BookingManagement
     */
    protected $Management = NULL;

    /**
     * Referencia al agregado de reservas
     * @var \BookingAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Array de codigos de resultado
     * @var array
     */
    protected $Codes = [];

    /**
     * Constructor
     * @param int $project ID del proyecto actual
     */
    public function __construct($project = 0){
        // Cargar constructor padre
        parent::__construct();
        // Asignar id de proyecto
        $this->Project = $project;
        // Cargar el management
        $this->Management = BookingManagement
                ::GetInstance($this->Project, $this->Service);
        // Configurar los códigos de error
        $this->SetCodes();
        // Cargar toda la información del modelo
        $this->SetModelProperties();
    }

    /**
     * Establece las propiedades del modelo con la información del
     * agregado correspondiente
     */
    private function SetModelProperties(){
        $this->Aggregate = $this->Management->GetAggregate();
        $this->Advertising = ($this->Aggregate->Configuration->Advertising) ? 1: 0;
        $this->Preorder = ($this->Aggregate->Configuration->PreOrder) ? 1: 0;
        $this->Places = $this->Aggregate->AvailablePlaces;

        $this->OffersShare = json_encode($this->Aggregate->OffersShare);
        $this->TurnsShare = json_encode($this->Aggregate->TurnsShare);

        $this->OffersEvents = [];
        foreach($this->Aggregate->AvailableOffersEvents as $item){
            $this->OffersEvents[] = $item;
        }
        $this->OffersEvents = json_encode($this->OffersEvents);

        $this->Blocks = [];
        foreach($this->Aggregate->AvailableBlocks as $item){
            $this->Blocks[] = $item;
        }
        $this->Blocks = json_encode($this->Blocks);

        $max = $this->Aggregate->MaxDiners;
        $min = $this->Aggregate->MinDiners;
        for($i = $min; $i <= $max; $i++){
            $this->DinersLst[] = new \SelectControlItem($i, $i);
        }
        $this->Offers = $this->Aggregate->AvailableOffers;
        foreach($this->Offers as $item){
            $item->Config = json_encode($item->Config);
        }
        $diners = ($this->Aggregate->MinDiners <= 2)
                    ? 2: $this->Aggregate->MinDiners;
        $this->Entity = new \Booking();
        $this->Entity->Diners = $diners;
        $this->Entity->Comment = "";
        $this->Turns = $this->Aggregate->Turns;
        foreach($this->Turns as $turn){
            if(isset($turn->Days)){
                $turn->Days = json_encode($turn->Days);
            }
            if(strlen($turn->Start) > 5){
                $turn->Start = substr($turn->Start, 0, 5);
            }
        }
        if(count($this->Turns) > 0){
            $this->Entity->Turn = $this->Turns[0]->Id;
        }
    }

    /**
     * Establecer en el model los datos de la entidad
     * @param \Booking $entity
     */
    private function SetEntity($entity = NULL){
        if($entity != NULL){
            $date = new \DateTime( $entity->Date );
            $entity->Date = $date->format( "d-m-Y" );
            $this->Entity = $entity;
            $this->Entity->Project = $this->Project;
        }
        else{
            $diners = ($this->Aggregate->MinDiners <= 2)
                    ? 2: $this->Aggregate->MinDiners;
            $this->Entity = new \Booking();
            $this->Entity->Project = $this->Project;
            $this->Entity->Diners = $diners;
            $this->Entity->Comment = "";
        }
    }

    /**
     * Proceso para guardar el registro de la reserva
     * @param \Booking $entity Referencia a los datos de la reserva
     * @param boolean $legal Flag para indicar el registro del cliente
     * @return boolean Resultado de la operación
     */
    public function Save($entity = NULL, $legal = FALSE){
        $this->Legal = $legal;

        $ars = [" ", "-", "(", ")"];
        $arr = ["", "", "", ""];
        $entity->Phone = trim(str_replace($ars, $arr, $entity->Phone));

        // Establecer el valor de la oferta seleccionada
        $entity->Offer = (empty($entity->Offer) || $entity->Offer == "-1" )
                ? NULL: intval($entity->Offer);
        // Formatear la fecha
        $datetime = new \DateTime($entity->Date);
        $entity->Date = $datetime->format("Y-m-d");
        // Guardamos la fecha de la solicitud
        $date = $entity->Date;
        // Establecemos el origen de la reserva
        $entity->BookingSource = 1;
        // realigar el guardado
        $result = $this->Management->RegisterBooking($entity, $this->Legal);
        // Establecer los mensajes de error
        $this->TranslateResultCodes($result);
        // Resultado de la operacion
        $saved = (count($result) == 1 && $result[0] >= 0);
        // Establecer el mensaje general
        if($saved){
            $this->eResult = "La reserva se ha realizado correctamente.";
            $this->eResultClass = "has-success";
        }
        else{
            $this->eResult = "Por favor revise los errores del formulario";
            $this->eResultClass = "has-error";
        }
        // Reasignamos la fecha
        $entity->Date = $date;
        // Establecer la entidad en el modelo
        $this->SetEntity($entity);
        // Retornar el resultado de la operación
        return $saved;
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
                "msg" => "El turno no está disponible."),
            -28 => array( "name" => "eTurn",
                "msg" => "El turno no está disponible, el cupo ha sido superado"),
            -29 => array( "name" => "eTurn",
                "msg" => "La oferta no está disponible, el cupo ha sido superado"),
        );
    }

    /**
     * Establece los mensajes de error a partir de los codigos obtenidos
     * en la operacion anterior.
     * @param array $codes Coleccion de codigos de error obtenidos
     */
    private function TranslateResultCodes($codes = NULL){
        if($codes != NULL && is_array($codes)){
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
