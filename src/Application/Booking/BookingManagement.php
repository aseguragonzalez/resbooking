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
 * Implementación del gestor de la capa de aplicación para Reservas
 *
 * @author alfonso
 */
class BookingManagement extends \BaseManagement implements \IBookingManagement{

    /**
     * Referencia al gestor de servicio de reservas
     * @var \BookingServices
     */
    protected $Services = NULL;

    /**
     * Referencia al respositorio de reservas
     * @var \IBookingRepository
     */
    protected $Repository = NULL;

    /**
     * Referencia al Management de reservas
     * @var \IBookingManagement
     */
    private static $_reference = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        // Cargar Constructor padre
        parent::__construct($project, $service);
        // Obtener referencia al repositorio
        $this->Repository = BookingRepository::GetInstance($project, $service);
        // Cargar el agregado
        $this->Aggregate = $this->Repository->GetAggregate($project, $service);
        // Cargar el gestor de servicios
        $this->Services = BookingServices::GetInstance($this->Aggregate);
    }

    /**
     * Obtiene una referencia al agregado del proyecto actual
     * @param string $sDate Fecha para la que se solicita la información
     * @return \BookingAgregate
     */
    public function GetAggregate($sDate = ""){

        $this->Aggregate->SetAggregate($sDate);

        return $this->Aggregate;
    }

    /**
     * Obtiene la instancia actual del Management de reservas
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BookingManagement
     */
    public static function GetInstance($project = 0, $service = 0){
        if(BookingManagement::$_reference == NULL){
           BookingManagement::$_reference =
                   new \BookingManagement($project, $service);
        }
        return BookingManagement::$_reference;
    }

    /**
     * Registro de una reserva
     * @param \Booking $entity Referencia a la entidad
     * @param boolean $saveClient Validación registrar los datos del cliente
     * @param boolean $sendNotification Flag para indicar si se envía notificación
     * @return array Resultado de la operación
     */
    public function RegisterBooking($entity = NULL,
            $saveClient = FALSE, $sendNotification = TRUE){
        $entity->Project = $this->IdProject;
        $result = $this->Services->Validate($entity);
        if(!is_array($result) && $result == TRUE ){
            $exist = $this->Services->Exist($entity);
            if($exist == FALSE){
                $result = $this->CreateBooking($entity,
                        $saveClient, $sendNotification);
            }
            else{
                $result = [1];
            }
        }
        return $result;
    }

    /**
     * Actualización de la información de una reserva
     * @param int $id Identida de la reserva a modificar
     * @param string $propName Nombre de la propiedad
     * que se desea actualizar
     * @return int Código de operación :
     *   0 : La operación de ha ejecutado correctamente.
     *  -1 : No se ha encontrado la reserva por su Id
     *  -2 : Se ha producido un error durante la actualización
     */
    public function SavePropertyBooking($id = 0,
            $propName = "", $propValue = NULL){
        // Código de operación
        $result = -1;
        // Obtener referencia a la entidad buscada
        $entity = $this->Repository->Read( "Booking", $id);
        // Comprobar que se ha encontrad
        if($entity != NULL){
            // Proceso de actualización de la reserva
            $result = $this->UpdatePropertyBooking($entity, $propName, $propValue);
        }
        return $result;
    }

    /**
     * Proceso de anulación de la reserva
     * @param int $id Identidad de la reserva
     * @param int $state Identidad del estado de cancelación
     * @return int Código de operación :
     *   0 => La operación se ha ejecutado correctamente
     *  -1 => La reserva no ha sido encontrada
     *  -2 => La reserva no se ha podido actualizar
     *  -3 => La notificación no se ha podido generar
     *  -4 => No se ha encontrado el estado identificado por id
     */
    public function CancelBooking($id = 0, $state = 0){
        // Código de operación
        $result = -1;
        // Proceso de validación del estado
        $valState = $this->Services->ValidateState($state);
        // Obtener referencia a la entidad buscada
        $entity = $this->Repository->Read( "Booking", $id);
        // Comprobar que se ha encontrad
        if($entity != NULL && $valState == TRUE){
            // Validar el estado actual de la reserva
            if($entity->State == $state){ return 0; }
            // Actualización
            if($this->UpdatePropertyBooking($entity, "State", $state) == 0){
                // Obtener el asunto de la notificación
                $subject = ConfigurationManager::GetKey("mailCancel");
                // Generar la notificación
                $not = $this->Repository->CreateNotification($entity, $subject);
                // Asignar el resultado de la operación
                $result = ($not == FALSE) ? -3 : 0;
            }
            else{
                $result = -2;
            }
        }
        else if($valState == FALSE){
            $result = -4;
        }
        return $result;
    }

    /**
     * Obtiene la informción de una reserva a partir de la identidad
     * de la misma
     * @param int $id Identidad de la reserva
     * @return \Booking Referencia encontrada
     */
    public function GetBookingById($id = 0){
        return $this->Repository->Read( "Booking", $id);
    }

    /**
     * Obtiene la colección de reservas filtradas por fecha
     * @param string $sDate Fecha de las reservas
     * @return array Colección de reservas disponibles
     */
    public function GetBookingsByDate($sDate = ""){
        // Establecer el filtro de búsqueda
        $filter = [ "Project" => $this->IdProject, "Date" => $sDate ];
        // Obtener todas las reservas
        return $this->Repository->GetByFilter( "Booking", $filter);
    }

    /**
     * Obtiene la colección de reservas utilizando el filtro especificado
     * @param array $filter Filtro de búsqueda
     * @return array Colección de reservas encontradas
     */
    public function GetBookingsByFilter($filter = NULL){
        // Si el filtro no se ha definido, lo definimos
        // y establecemos el proyecto sobre el que se buscan las
        // reservas
        if($filter == NULL){
            $filter = [ "Project" => $this->IdProject ];
        }
        else{
            $filter["Project"] = $this->IdProject;
        }
        // Obtener todas las reservas
        return $this->Repository->GetByFilter( "Booking", $filter);
    }

    /**
     * Generar el registro de la reserva y de la actividad en base de datos
     * @param \Booking $entity Referencia a la reserva
     * @param boolean $saveClient Indica si se deben guardar los datos de cliente
     * @param boolean $sendNotification Flag para indicar si se envía notificación
     * @return array Códigos de operación
     */
    private function CreateBooking($entity = NULL,
            $saveClient = FALSE, $sendNotification = TRUE){
        $log = $this->Services->GetActivity($entity);
        $this->Repository->Create($log);
        $entity->Client = $this->Repository->GetClient($entity, $saveClient);
        $booking = $this->Repository->Create($entity);
        if($booking != FALSE ){
            $this->Aggregate->Booking = $booking;

            $subject = ConfigurationManager::GetKey("mailSubject");

            if(class_exists("ZapperDAL")){
                $zapper = new \ZapperDAL();
                $required = $zapper->RequiredPrePay($booking->Project,
                        ($booking->Offer == null) ? 0 : $booking->Offer,
                        $booking->Diners, $booking->Date);
                if($required){
                    $booking = $zapper->RegisterZapperBooking($booking);
                    $this->Aggregate->Booking = $booking;
                    $subject = "zapper-booking";
                }
            }

            if($sendNotification && $entity->BookingSource == 1){
                $this->Repository->CreateNotification($booking, $subject);
            }

            return [1];
        }
        else{
            return [0];
        }
    }

    /**
     * Procedimiento para actualizar una propiedad una reserva creando
     * un registro de histórico
     * @param \Booking $entity Referencia a la reserva a actualizar
     * @param string $propertyName Nombre de la propiedad que se actualiza
     * @param object $propertyValue Valor asignado a la propiedad
     * @return int Código de operación :
     *   0 => La operación de ha ejecutado correctamente
     *  -1 => La referencia a la reserva es null
     *  -2 => No se ha definido la propiedad a actualizar
     */
    private function UpdatePropertyBooking($entity = NULL,
            $propertyName = "", $propertyValue = NULL){
        $return = -1;
        if($entity != NULL){
            if($propertyName != ""){
                // Crear Histórico
                $hist = new \BookingBck($entity);
                // Guardar el histórico
                $this->Repository->Create($hist);
                // Actualizar la propiedad
                $entity->{$propertyName} = $propertyValue;
                // Actualización de la reserva
                $this->Repository->Update($entity);
                // Resultado de la operación
                $return = 0;
            }
            else{
                $return = -2;
            }
        }
        return $return;
    }
}
