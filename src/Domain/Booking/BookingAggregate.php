<?php

declare(strict_types=1);

namespace App\Domain\Booking;

final class BookingAggregate extends \BaseAggregate{

    /**
     * Colección de fuentes de reserva registradas
     * @var array
     */
    public $BookingSources = [];

    /**
     * Colección de estados posibles de reserva
     * @var array
     */
    public $States = [];

    /**
     * Colección de turnos registrados
     * @var array
     */
    public $Turns = [];

    /**
     * Colección de cupos de turnos
     * @var array
     */
    public $TurnsShare = [];

    /**
     * Colección de Slots|Franjas registradas
     * @var array
     */
    public $Slots = [];

    /**
     * Colección de Espacios registrados en el proyecto
     * @var array
     */
    public $Places = [];

    /**
     * Colección de Ofertas registradas en el proyecto
     * @var array
     */
    public $Offers = [];

    /**
     * Colección de eventos de ofertas actuales
     * @var array
     */
    public $OffersEvents = [];

    /**
     * Colección de cuotas asociadas a ofertas
     * @var array
     */
    public $OffersShare = [];

    /**
     * Colección de Bloqueos registrados en el proyecto
     * @var array
     */
    public $Blocks = [];

    /**
     * Colección de configuraciones de turnos registradas en el proyecto
     * @var array
     */
    public $Configurations = [];

    /**
     * Espacios disponibles para la reserva
     * @var array
     */
    public $AvailablePlaces = [];

    /**
     * Colección de ofertas disponibles
     * @var array
     */
    public $AvailableOffers = [];

    /**
     * Colección de eventos de ofertas disponibles
     * @var array
     */
    public $AvailableOffersEvents = [];

    /**
     * Colección de bloqueos disponibles
     * @var array
     */
    public $AvailableBlocks = [];

    /**
     * Colección de turnos configurados
     * @var array
     */
    public $AvailableTurns = [];

    /**
     * Referencia a la entidad cliente
     * @var \Client
     */
    public $Client = NULL;

    /**
     * Referencia a la entidad reserva
     * @var \Booking
     */
    public $Booking = NULL;

    /**
     * Referencia a la configuración del servicio
     * @var \ConfigurationService
     */
    public $Configuration = NULL;

    /**
     * Cantidad mínima de comensales
     * @var int
     */
    public $MinDiners = 1;

    /**
     * Cantidad máxima de comensales
     * @var int
     */
    public $MaxDiners = 25;

    /**
     * Fecha de solicitud
     * @var \DateTime
     */
    public $Date = NULL;

    /**
     * Constructor
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        $this->IdProject = $idProject;
        $this->IdService = $idService;
        $this->Booking = new \Booking();
        $this->Client = new \Client();
        $this->Configuration = new \ConfigurationService();
    }

    /**
     * Configuración de las propiedades filtrando por fecha
     * @param string $sDate
     */
    public function SetAggregate($sDate = ""){
        $this->Date = ($sDate != "") ?
                new \DateTime($sDate) : new \DateTime("NOW");
        $this->MaxDiners = $this->Configuration->MaxDiners;
        $this->MinDiners = $this->Configuration->MinDiners;
        $this->FilterAvailablePlaces();
        $this->FilterAvailableBlocks();
        $this->FilterAvailableOffersEvents();
        $this->FilterAvailableOffers();
        $this->FilterAvailableTurns();

        $yesterday = new \DateTime("YESTERDAY");
        $arr = [];
        foreach($this->OffersShare as $item){
            $date = new \DateTime($item->Date);
            if($date <= $yesterday){
                continue;
            }
            $arr[] = $item;
        }

        $this->OffersShare = $arr;
        $arr = [];
        foreach($this->TurnsShare as $item){
            $date = new \DateTime($item->Date);
            if($date <= $yesterday){
                continue;
            }
            $arr[] = $item;
        }
        $this->TurnsShare = $arr;
    }

    /**
     * Filtra los espacios activos en la colección de espacios disponibles
     */
    private function FilterAvailablePlaces(){
        $this->AvailablePlaces = array_filter($this->Places, function($item){
           return  $item->Active == TRUE;
        });
    }

    /**
     * Filtra los bloqueos activos desde el día anterior(AYER)
     */
    private function FilterAvailableBlocks(){
        $yesterday = new \DateTime( "YESTERDAY" );
        $this->AvailableBlocks = array_filter($this->Blocks,
                function($item) use ($yesterday){
            $dateBlocked = new \DateTime($item->Date);
            return $dateBlocked >= $yesterday;
        });
    }

    /**
     * Filtra los eventos de ofertas activos desde el día anterior(AYER)
     */
    private function FilterAvailableOffersEvents(){
        $yesterday = new \DateTime( "YESTERDAY" );
        $this->AvailableOffersEvents = array_filter($this->OffersEvents,
                function($item) use ($yesterday){
            $date = new \DateTime($item->Date);
            return $date >= $yesterday;
        });
    }

    /**
     * Filtra las ofertas activas válidas
     */
    private function FilterAvailableOffers(){
        $this->AvailableOffers = [];
        $yesterday = new \DateTime("YESTERDAY");
        foreach($this->Offers as $offer){
            if($offer->Active != 1){
                continue;
            }
            $end = ($offer->End == "0000-00-00 00:00:00" )
                    ? NULL : new \DateTime($offer->End);
            if($end > $yesterday || $end == NULL){
                $this->AvailableOffers[] = $offer;
            }
        }
    }

    /**
     * Filtra y configura los turnos establecidos para el proyecto
     */
    private function FilterAvailableTurns(){
        $this->AvailableTurns = array();
        foreach($this->Turns as $turn){
            $t = $this->SetTurnData($turn);
            if($t != NULL){
                $this->AvailableTurns[] = $t;
            }
        }
    }

    /**
     * Establece los datos del turno para agregarlo a la lista de turnos
     * disponibles.
     * @param \Turn $turn Referencia al turno a configurar
     * @return \Turn Referencia al turno a agregar o NULL si
     * no tiene configuraciones válidas
     */
    private function SetTurnData($turn = NULL){
        $configs = $this->GetConfigByTurn($turn->Id);
        if(!empty($configs)){
            $days = [];
            foreach($configs as $item){
                $days[] = $item->Day;
            }
            $turn->Days = $days;
            $turn->Start = substr($turn->Start, 0, 5);
            $turn->End = substr($turn->End, 0, 5);
            return $turn;
        }
        return NULL;
    }

    /**
     * Obtiene las configuraciones para un turno especificado
     * @param int $id Identidad del turno
     * @return array Colección de configuraciones registradas
     */
    private function GetConfigByTurn($id = 0){
        $configs = array_filter($this->Configurations,
                function ($item) use ($id) {
            return ($item->Turn == $id);
        });
        return (empty($configs)) ? [] : $configs;
    }
}
