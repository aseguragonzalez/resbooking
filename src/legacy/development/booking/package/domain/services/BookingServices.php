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
 * Capa de servicios para la gestión de entidades reserva
 *
 * @author alfonso
 */
class BookingServices extends \BaseServices implements \IBookingServices{

    /**
     * Referencia
     * @var \BookingServices
     */
    private static $_reference = NULL;

    /**
     * Referencia al repositorio actual
     * @var \IBookingRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al agregado
     * @var \BookingAggregate
     */
    protected $Aggregate = NULL;

    /**
     * Colección de códigos de operación
     * @var array
     */
    protected $Result = [];

    /**
     * Constructor
     * @param \BaseLineAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = NULL) {

        parent::__construct($aggregate);

        $this->Repository = BookingRepository
                ::GetInstance($this->IdProject, $this->IdService);
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     */
    public static function GetInstance($aggregate = NULL) {
        if(BookingServices::$_reference == NULL){
            BookingServices::$_reference =
                    new \BookingServices($aggregate);
        }
        return BookingServices::$_reference;
    }

    /**
     * Comprobación sobre la existencia de la reserva solicitada
     * @param \Booking $entity Referencia a la reserva a registrar
     * @return boolean Resultado de la comprobación. TRUE si la reserva
     * ya está registrada. FALSE en caso contrario
     */
    public function Exist($entity = NULL){
        $filter = [ "Project" => $entity->Project, "Turn" => $entity->Turn,
                "Date" => $entity->Date, "Diners" => $entity->Diners,
                "Email" => "%".$entity->Email."%", "Phone" => "%".$entity->Phone."%",
                "Offer" => $entity->Offer, "Place" => $entity->Place ];
        $reservas = $this->Repository->GetByFilter( "Booking" , $filter );
        return !empty($reservas);
    }

    /**
     * Obtiene una instancia para el registro de actividad
     * @param \Booking $entity Referencia a la reserva
     * @return \Log
     */
    public function GetActivity($entity = NULL){
        $info = [ "REQUEST" => $_REQUEST, "Entity" => $entity];
        $date = new \DateTime( "NOW" );
        $log = new \Log();
        $log->Booking = $entity->Id;
        $log->Address = $_SERVER["REMOTE_ADDR"];
        $log->Information = json_encode($info);
        $log->Date = $date->format( "Y-m-d" );
        return $log;
    }

    /**
     * Proceso de validación de la entidad Reserva
     * @param \Booking $entity Referencia a los datos de reserva
     * @return boolean|array Devuelve TRUE si la validación es correcta
     * o la colección de códigos de operación si no supera el proceso
     */
    public function Validate($entity = NULL){
        $this->Result = [];
        $this->ValidateClientName($entity->ClientName);
        $this->ValidateDate($entity->Date);
        $this->ValidateDiners($entity->Diners);
        $this->ValidateEmail($entity->Email);
        $this->ValidatePhone($entity->Phone);
        $this->ValidatePlace($entity->Place);
        $this->ValidateTurn($entity->Turn, $entity->Date);
        $this->ValidateOffer($entity->Offer,
                $entity->Turn, $entity->Date);
        return empty($this->Result) ? TRUE : $this->Result;
    }

    /**
     * Proceso de validación del estado de la reserva
     * @param int $id Identidad del estado a validar
     * @return boolean Resultado de la validación del estado
     */
    public function ValidateState($id = 0){
        // Referencia al estado de reserva
        $state = $this->GetById($this->Aggregate->States, $id);
        // Validación
        return ($state != NULL);
    }

    /**
     * Validación del nombre del cliente
     * @param string $name Nombre del cliente
     */
    private function ValidateClientName($name=""){
        if(empty($name)){
            $this->Result[] = -1;
        }
        else if(!is_string($name)){
            $this->Result[] = -2;
        }
        else if(strlen($name) > 100){
            $this->Result[] = -3;
        }
    }

    /**
     * Proceso de validación de e-mail
     * @param string $email email del cliente
     */
    private function ValidateEmail($email = ""){
        if(empty($email)){
            //$this->Result[] = -4;
        }
        else if(strlen($email) > 100){
            $this->Result[] = -6;
        }
        else if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE){
            $this->Result[] = -5;
        }
    }

    /**
     * Proceso de validación del número de teléfono
     * @param string $phone Teléfono del cliente
     */
    private function ValidatePhone($phone = ""){
        if(empty($phone)){
            $this->Result[] = -7;
        }
        else if(!is_string($phone)){
            $this->Result[] = -8;
        }
        else if(strlen($phone) > 15){
            $this->Result[] = -9;
        }
    }

    /**
     * Proceso de validación del número de comensales
     * @param int $diners Cantidad de comensales
     */
    private function ValidateDiners($diners = 0){
        if(empty($diners)){
            $this->Result[] = -10;
        }
        else if(is_numeric($diners)=== FALSE){
            $this->Result[] = -11;
        }
        else if($diners > $this->Aggregate->MaxDiners){
            $this->Result[] = -12;
        }
        else if($diners < $this->Aggregate->MinDiners){
            $this->Result[] = -13;
        }
    }

    /**
     * Proceso de validación de la fecha de reserva
     * @param string $sDate Fecha de la validación
     */
    private function ValidateDate($sDate = ""){
        // formato de fecha yyyy-mm-dd
        $regex = "((19|20)[0-9]{2}[-]"
                . "(0[1-9]|1[012])[-]0[1-9]|[12][0-9]|3[01])";

        if(empty($sDate)){
            $this->Result[] = -14;
        }
        else if(preg_match($regex, $sDate) != 1){
            $this->Result[] = -15;
        }
        else{
            try{
                $date = new \DateTime($sDate);
                $yesterday = new \DateTime( "YESTERDAY" );
                if($date <=$yesterday){
                    $this->Result[] = -16;
                }
            }catch(Exception $e){
                $this->Result[] = -15;
            }
        }
    }

    /**
     * Validación del Espacio. Comprueba que el espacio está
     * asociado al proyecto actual
     * @param int $place Identidad del Espacio|Lugar
     */
    private function ValidatePlace($place = 0){
        if(empty($place)){
            $this->Result[] = -17;
        }
        else if(!is_numeric($place)){
            $this->Result[] = -18;
        }
        else{
            $filter = ["Project" =>
                $this->Aggregate->IdProject,"Id" => $place ];
            $places = $this->GetListByFilter(
                    $this->Aggregate->Places, $filter);
            if(empty($places)){
                $this->Result[] = -19;
            }
        }
    }

    /**
     * Validación del Turno. Comprueba que el turno está asociado
     * al proyecto actual para la fecha dada(date)
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     */
    private function ValidateTurn($turn = 0, $sDate = ""){
        // formato de fecha yyyy-mm-dd
        $regex = "((19|20)[0-9]{2}[-]" . "(0[1-9]|1[012])[-]0[1-9]|[12][0-9]|3[01])";
        if(empty($turn)){
            $this->Result[] = -20;
        }
        else if(!is_numeric($turn)){
            $this->Result[] = -21;
        }
        else if(empty($sDate)){
            $this->Result[] = -14;
        }
        else if(preg_match($regex, $sDate) != 1){
            $this->Result[] = -15;
        }
        else if($this->TurnIsBlock($turn, $sDate)){
            $this->Result[] = -22;
        }
        else if($this->TurnIsOpen($turn, $sDate) || $this->TurnIsConfig($turn, $sDate)){
            if(!$this->TurnIsAlive($turn, $sDate)){
                $this->Result[] = -27;
            }
            else if(!$this->ValidateTurnShare($turn, $sDate)){
                $this->Result[] = -28;
            }
        }
        else{
            $this->Result[] = -23;
        }
    }

    /**
     * Filtro para validar los turnos activos por la hora de reserva
     * @param int $id Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean resultado de la validación
     */
    private function TurnIsAlive($id = 0, $sDate = ""){
        // Comprobar si es necesario validar el turno
        $date = new \DateTime($sDate);
        $current = new \DateTime("NOW");
        $validar = (intval($date->format("d")) == intval($current->format("d")))
                && (intval($date->format("m")) == intval($current->format("m")))
                && (intval($date->format("Y")) == intval($current->format("Y")));
        if($validar == FALSE){ return TRUE;}
        // Proceso de validación del turno
        $turn = $this->GetById($this->Aggregate->Turns, $id);
        if($turn != NULL && $turn instanceof \Turn){
            $start = substr($turn->Start, 0, 5);
            $startParts = explode(":", $start);
            $H = intval($current->format("H"));
            $h = intval($startParts[0]);
            if( $H < $h){
                return TRUE;
            }
            else if($H == $h){
                $M = intval($current->format("i")) + 20;
                $m = intval($startParts[1]);
                return $M < $m;
            }
        }
        return FALSE;
    }

    /**
     * Comprueba si el turno está bloqueado en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsBlock($turn = 0, $sDate =""){
        $blocksFilter = ["Project" => $this->IdProject,
                "Turn" => $turn, "Date" => $sDate, "Block" => 0];
        $blocks = $this->GetListByFilter(
                $this->Aggregate->Blocks, $blocksFilter);
        return !empty($blocks);
    }

    /**
     * Comprueba si el turno está "abierto" en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsOpen($turn = 0, $sDate =""){
        $blocksFilter = [ "Project" => $this->IdProject,
                "Turn" => $turn, "Date" => $sDate, "Block" => 1];
        $blocks = $this->GetListByFilter(
                $this->Aggregate->Blocks, $blocksFilter);
        return !empty($blocks);
    }

    /**
     * Comprueba si el turno está configurado en la fecha indicada
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function TurnIsConfig($turn = 0, $sDate =""){
        $date = new \DateTime($sDate);
        $dayOfWeek = $date->format( "N" );
        $filter = ["Project" => $this->IdProject,
            "Day" => $dayOfWeek, "Turn" => $turn ];
        $configs = $this->GetListByFilter(
                $this->Aggregate->Configurations, $filter);
        return !empty($configs);
    }

    /**
     * Proceso para validar la cuota del turno
     * @param int $id Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la validación
     */
    private function ValidateTurnShare($id = 0, $sDate = ""){
        $filter = [ "Project" => $this->IdProject,
            "Turn" => $id, "Date" => $sDate ];
        $shares = $this->GetListByFilter($this->Aggregate->TurnsShare, $filter);
        $filterShares = array_filter($shares, function($item){
           return $item->DinersFree <= 0;
        });
        return empty($filterShares);
    }

    /**
     * Proceso de validación de la oferta seleccionada
     * @param int $offer Identidad de la oferta seleccionada
     * @param int $turn Identidad del turno seleccionado
     * @param string $sDate Fecha de la reserva
     */
    private function ValidateOffer($offer = 0, $turn = 0, $sDate = "" ){
        if($offer > 0){
            $off = $this->GetById($this->Aggregate->Offers, $offer);
            if($off == NULL){
                $this->Result[] = -24;
            }
            // Comprobamos si la oferta está abierta
            else if($this->OfferIsOpen($offer, $turn, $sDate) == TRUE){
                return;
            }
            // Comprobamos si la oferta está cerrada
            else if($this->OfferIsClose($offer, $turn, $sDate) == TRUE){
                $this->Result[] = -26;
            }
            else if(!$this->ValidateOfferDates($off, $sDate)){
                $this->Result[] = -25;
            }
            else if(!$this->ValidateOfferConfig($off, $turn, $sDate)){
                $this->Result[] = -26;
            }
            else if(!$this->ValidateOfferShare($off, $turn, $sDate)){
                $this->Result[] = -29;
            }
        }
    }

    /**
     * Proceso de validación de oferta de configuración
     * @param \Offer $offer Referencia a la oferta seleccionada
     * @param int $idturn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean
     */
    private function ValidateOfferConfig($offer = NULL, $idturn = 0, $sDate = ""){
        if($offer != NULL) {
            $date = new \DateTime($sDate);
            $dayOfWeek = $date->format("N");
            $filter = [ "Turn" => $idturn, "Day" => $dayOfWeek ];
            $configs = json_decode($offer->Config);
            if($configs == FALSE){
                $configs = [];
            }
            return !empty($this->GetListByFilter($configs, $filter));
        }
        return FALSE;
    }

    /**
     * Validación de las fechas de la oferta
     * @param \Offer $offer Referencia al objeto oferta
     * @param string $sDate Referencia a la fecha
     * @return boolean Resultado de la comprobación
     */
    private function ValidateOfferDates($offer = NULL, $sDate = ""){
        // Instanciar fecha
        $date = new \DateTime($sDate);

        $start = (isset($offer->Start)
                && $offer->Start != ""
                && $offer->Start != "0000-00-00 00:00:00")
                ? new DateTime($offer->Start) : NULL;

        $end = (isset($offer->End)
                && $offer->End != ""
                && $offer->End != "0000-00-00 00:00:00")
                ? new DateTime($offer->End) : NULL;

        $cmp_ok_1 = ($start == NULL
                || ($start != NULL && $date >= $start));

        $cmp_ok_2 = ($end == NULL
                || ($end != NULL && $date <= $end));

        return ($cmp_ok_1 && $cmp_ok_2);
    }

    /**
     * Comprobación si la oferta tiene una configuración de evento "Abierta"
     * para los parámetros de la reserva
     * @param int $id Identidad de la oferta
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function OfferIsOpen($id = 0, $turn = 0, $sDate = ""){
        $filter = ["Project" => $this->IdProject, "Offer" => $id,
                "Turn" => $turn, "Date" => $sDate, "State" => 1];
        $events = $this->GetListByFilter(
                $this->Aggregate->AvailableOffersEvents, $filter);
        return !empty($events);
    }

    /**
     * Comprobación si la oferta tiene una configuración de evento "Cerrada"
     * para los parámetros de la reserva
     * @param int $id Identidad de la oferta
     * @param int $turn Identidad del turno
     * @param string $sDate Fecha de la reserva
     * @return boolean Resultado de la comprobación
     */
    private function OfferIsClose($id = 0, $turn = 0, $sDate = ""){
       $filter = ["Project" => $this->IdProject, "Offer" => $id,
                "Turn" => $turn, "Date" => $sDate, "State" => 0];
        $events = $this->GetListByFilter(
                $this->Aggregate->AvailableOffersEvents, $filter);
        return !empty($events);
    }

    /**
     * Proceso de validación del cupo de oferta
     * @param int $id Identidad de la oferta
     * @param int $idTurn Identidad del turno
     * @param string $sDate Fecha de reserva
     * @return boolean Resultado de la validación
     */
    private function ValidateOfferShare($id = 0, $idTurn = 0, $sDate = ""){
        $turn = $this->GetById($this->Aggregate->Turns, $idTurn);
        $filterShares = [];
        if($turn != NULL){
            $filter = [ "Project" => $this->IdProject, "Offer" => $id,
                "Slot" => $turn->Slot, "Date" => $sDate ];
            $shares = $this->GetListByFilter($this->Aggregate->OffersShare, $filter);
            $filterShares = array_filter($shares, function($item){
               return $item->DinersFree <= 0;
            });
        }
        return empty($filterShares);
    }
}
