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
 * Model para la gestión de reservas
 *
 * @author alfonso
 */
class BookingModel extends \ResbookingModel{

    /**
     * Opción del menú principal seleccionado
     * @var string
     */
    public $Activo = "Reservas";

    /**
     * Referencia al dto para navegación por fechas
     * @var \DateNavDTO
     */
    public $DateNavDTO = NULL;

    /**
     * Identidad del estado anulado
     * @var int
     */
    public $IdAnulado = 0;

    /**
     * Identidad del estado reservado
     * @var int
     */
    public $IdReservado = 0;

    /**
     * Número de registros totales
     * @var int
     */
    public $NTotal = 0;

    /**
     * Colección con los Id de estados
     * @var array
     */
    public $States = [];

    /**
     * Flag para indicar errores en la última operación
     * @var int
     */
    public $Error = 0;

    /**
     * Referencia a la reserva seleccionada
     * @var \Booking
     */
    public $Entity = NULL;

    /**
     * Número máximo de comensales
     * @var int
     */
    public $MaxDiners = 25;

    /**
     * Número mínimo de comensales
     * @var int
     */
    public $MinDiners = 1;

    /**
     * Colección de reservas
     * @var array
     */
    public $Entities = [];

    /**
     * Constructor
     */
    public function __construct($project = 0){
        parent::__construct();
        $this->Title = "Reservas";
        $this->DateNavDTO = new \DateNavDTO();
        if($project != 0){
            $this->SetProject($project);
        }
        $this->IdAnulado = ConfigurationManager::GetKey( "anulado" );
    }

    /**
     * Procedimiento para establecer el proyecto en el modelo a partir del id
     * @param int $id Identidad del proyecto
     */
    private function SetProject($id = 0){
        $project = $this->Dao->Read($id, "Project");
        if($project != NULL && $project instanceof \Project){
            $this->Project = $id;
            $this->ProjectName = $project->Name;
            $this->ProjectPath = $project->Path;
        }
    }

    /**
     * Cargar la colección de reservas filtradas por fecha
     * @param string $sDate Fecha utilizada como filtro
     */
    public function GetBookings($sDate = "" ){
        $this->States = $this->Dao->Get("State");
        $this->DateNavDTO->SetDate($sDate);
        $filter = ["Project" => $this->Project,
            "Date" => $this->DateNavDTO->Fecha];
        $registros = $this->Dao->GetByFilter("BookingDTO", $filter);
        $reservas = array_filter($registros, function($item){
           return $item->State != NULL;
        });
        foreach($reservas as $item){
            $item->sClientName = $this->SetText($item->ClientName, 25);
            $item->sComment = $this->SetText($item->Comment);
            $item->TurnStart = substr($item->TurnStart,0,5);
            $item->Date = $this->SetDate($item->Date);
            $item->sOfferTitle = $this->SetText($item->OfferTitle);
            $item->sPlaceName = $this->SetText($item->PlaceName, 20);
            $item->PreOrder = str_replace("\n", ";", $item->PreOrder);
        }
        $this->NTotal = count($reservas);
        $this->Entities = array_reverse($reservas);
    }

    /**
     * Acorta el texto del comentario si es necesario
     * @param string $comment Texto del comentario de la reserva
     * @return string Texto del comentario a visualizar
     */
    private function SetText($comment = "", $length = 15){
        if(isset($comment) && strlen($comment) > $length){
            return substr( $comment, 0, $length - 3 )."...";
        }
        return $comment;
    }

    /**
     * Convertir la fecha a formato texto (largo)
     * @param string $sdate Fecha de base de datos formato Y-m-d
     * @return string Fecha en formato largo
     */
    private function SetDate($sdate = ""){
        if($sdate != ""){
            // Obtener la instancia para la fecha
            $date = new DateTime($sdate);
            // Parsear a formato texto
            $sdate = strftime("%A %d de %B del %Y", $date->getTimestamp());
        }
        return $sdate;
    }

    /**
     * Proceso para la modificación del estado de la reserva
     * @param int $id Identidad de la reserva
     * @param int $idState Identidad del estado que se aplicará
     * @return int Código de la operación
     */
    public function ChangeState($id = 0, $idState = 0){
        $management = BookingManagement::
                GetInstance($this->Project, $this->Service);
        if($idState == $this->IdAnulado){
            $management->CancelBooking($id, $idState);
        }
        else{
            $management->SavePropertyBooking($id, "State" , $idState);
        }
        return 0;
    }

    /**
     * Proceso para la modificación de las notas de la reserva
     * @param int $id Identidad de la reserva
     * @param string $notes Anotaciones sobre la reserva
     * @return int Código de la operación
     */
    public function AddNotes($id = 0, $notes = ""){
        $management = BookingManagement::
                GetInstance($this->Project, $this->Service);
        return $management->SavePropertyBooking(
                $id, "Notes" , strip_tags($notes));
    }

    /**
     * Establece el numero de comensales de la reserva
     * @param int $id Identidad de la reserva
     * @param int $diners Cantidad de comensales
     * @return int Codigo de operación obtenido:
     *   0 : Todo es correcto.
     *  -1 : El nuevo valor es inferior al mínimo
     *  -2 : El nuevo valor es superior al máximo
     *   otros => Error interno de la aplicación
     */
    public function ChangeDiners($id = 0, $diners = 0){
        $management = BookingManagement::
                GetInstance($this->Project, $this->Service);
        $agg = $management->GetAggregate();
        if($diners < $agg->MinDiners ){
            $result = -1;
        }
        else if($diners > $agg->MaxDiners){
            $result = -2;
        }
        else{
            $result = $management->SavePropertyBooking($id, "Diners", $diners);
        }
        return $result;
    }

    public function UpdateTableInfo($id = 0, $info = ""){
        $booking = $this->Dao->Read($id, "Booking");

        if($booking instanceof \Booking){
            $booking->sTable = $info;
            $this->Dao->Update($booking);
            return 0;
        }
        return -1;
    }

    /**
     * Proceso de guardado de reserva manual
     * @param \Booking $entity Referencia a la reserva a generar
     * @return boolean Resultado de la operación
     */
    public function SaveBooking($entity = NULL){
        $management = BookingManagement::
                GetInstance($this->Project, $this->Service);
        return FALSE;
    }

    /**
     * Proceso para el registro de una notificación de tipo mensaje
     * @param string $to Destinatario del mensaje
     * @param string $message Contenido del mensaje
     * @return int Código de operación
     */
    public function SendMessage($to = "", $message = ""){
        $result = 0;
        if(empty($to)){
            $result = -1;
        }
        else if(filter_var($to, FILTER_VALIDATE_EMAIL) == FALSE){
            $result = -2;
        }
        else if(empty($message)){
            $result = -3;
        }
        if($result == 0){
            $result = $this->RegistrarNotificacion($to, "mensaje-resbooking",
                ["To" => $to, "Message" => $message]);
        }
        return $result;
    }

    /**
     * Proceso para el registro de un recordatorio de reserva
     * @param int $id Identidad de los datos de reserva
     * @return int Código de operación
     */
    public function SendReminder($id = 0){
        $result = -1;
        $reserva = $this->Dao->Read($id, "BookingDTO");
        if($reserva != NULL){
            $result = $this->RegistrarNotificacion($reserva->Email,
                    "recordatorio-resbooking", $reserva);
        }
        return $result;
    }

    /**
     * Crea el registro de una notificación con los parámetros pasados
     * @param string $to Destinatario de la notificación
     * @param string $subject Asunto de la notificación [Tipología]
     * @param Object|Array $data Información a serializar para el registro
     * @return int Código de operación
     */
    private function RegistrarNotificacion($to = "", $subject = "", $data = []){
        $date = new \DateTime( "NOW" );
        $dto = new \Notification();
        $dto->Project = $this->Project;
        $dto->Service = $this->Service;
        $dto->To = $to;
        $dto->Subject = $subject;
        $dto->Content = json_encode($data);
        $dto->Date = $date->format( "Y-m-d" );
        $this->Dao->Create($dto);
        return 0;
    }

    /**
     * Procesado del ticket recibido
     * @param string $ticket ticket de la solicitud
     */
    public function GetTicket($ticket = ""){
        // descifrar el ticket
        $sTicket = $this->fnDecrypt($ticket, "resbooking2015");
        // decodificar
        $arr = json_decode($sTicket);
        settype($arr, "array");
        // validación
        if($arr != NULL && is_array($arr)){
            $management = BookingManagement::
                GetInstance($this->Project, $this->Service);
            $entity = $management->GetBookingById($arr["Id"]);
            $dto = $this->Dao->Read($arr["Id"], "BookingNotificationDTO");
            if($entity != NULL && $entity->State != $this->IdAnulado
                    && $this->ValidateDate($entity->Date, $dto->Start)){
                $this->Entity = $dto;
                $this->Entity->Date = $this->SetDate($entity->Date);
                $this->Entity->Ticket = $ticket;
                $this->From = ($arr["User"] != "")
                        ? $arr["User"] : "email-admin";
                return 0;
            }
            if($entity != NULL && $entity->State == $this->IdAnulado){
                return 1;
            }
        }
        return -1;
    }

    /**
     * Método para descifrar el ticket de la reserva
     * @param string $sValue Ticket de reserva
     * @param string $sSecretKey Contraseña
     * @return string ticket descrifrado
     */
    private function fnDecrypt($sValue, $sSecretKey){
        return base64_decode($sValue);
    }

    /**
     * Validación de la fecha de anulación
     * @param string $sDate Fecha de la reserva
     * @return boolean
     */
    private function ValidateDate($sDate = "", $sTurn = ""){
        try{
            $date = new \DateTime($sDate);

            $today = new \DateTime("TODAY");

            return $this->CompareDate($date, $today, $sTurn);
        }
        catch(Exception $e){
            return FALSE;
        }
    }

    /**
     * Proceso de comparación de fechas
     * @param DateTime $date Fecha a comparar
     * @param DateTime $today Fecha actual
     * @param string $sTurn Turno del día
     * @return boolean
     */
    private function CompareDate($date = NULL, $today = NULL, $sTurn = ""){
        $dateY = intval($date->format("Y"));
        $todayY = intval($today->format("Y"));

        if($dateY < $todayY){
            return FALSE;
        }
        else if($dateY > $todayY){
            return TRUE;
        }

        $dateM = intval($date->format("m"));
        $todayM = intval($today->format("m"));

        if($dateM < $todayM){
            return FALSE;
        }
        else if($dateM > $todayM){
            return TRUE;
        }

        $dateD = intval($date->format("d"));
        $todayD = intval($today->format("d"));

        if($dateD < $todayD){
            return FALSE;
        }
        else if($dateD > $todayD){
            return TRUE;
        }

        return $this->ValidateTurn($sTurn);
    }

    /**
     * Validación de la hora de anulación. Se comprueba que no
     * se intente anular una reserva cuando su turno ya ha pasado
     * @param string $sTurn Turno de la reserva
     * @return boolean
     */
    private function ValidateTurn($sTurn = ""){
        $data = explode(":", $sTurn);
        if(count($data)==2){
            $date = new \DateTime("NOW");
            $currentHour = $date->format("H");
            $currentMinutes = $date->format("i");
            $hour = intval($data[0]);

            if($currentHour > $hour){
                return FALSE;
            }
            else if($currentHour < $hour){
                return TRUE;
            }

            $minutes = intval($data[1]);
            if($currentMinutes >= $minutes){
                return FALSE;
            }

            return TRUE;

        }
        return FALSE;
    }
}
