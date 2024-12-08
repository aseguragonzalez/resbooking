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
 * Clase para la ejecución de test sobre la interfaz IBookingManagement
 *
 * @author alfonso
 */
class IBookingManagementTest {

    /**
     * Referencia a la instancia actual
     * @var \IBookingManagementTest
     */
    private static $_reference = NULL;

    /**
     * Referencia a la instancia DAO actual
     * @var \IDataAccessObject
     */
    private $_DAO = NULL;

    /**
     * Referencia a la instancia IBookingManagement activa
     * @var \IBookingManagement
     */
    private $_management = NULL;

    /**
     * Identidad del proyecto sobre el que se ejecutan las pruebas
     * @var int
     */
    private $_project = 0;

    /**
     * Identidad del servicio sobre el que se ejecutan las pruebas
     * @var int
     */
    private $_service = 0;

    /**
     * Flag para indicar si se ha producido un error en la ejecución
     * @var boolean
     */
    private $_error  = FALSE;

    /**
     * Colección de mensajes de error generados
     * @var array
     */
    private $_errorInfo = [];

    /**
     * Colección con los mensajes de los test ejecutados
     * @var array
     */
    private $_testInfo = [];

    /**
     * Colección de pruebas ejecutadas
     * @var array
     */
    public $Test = [];

    /**
     * Constructor
     * @param string $instanceName Nombre de la instancia para la interfaz
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return void
     */
    private function __construct($instanceName = "", $project = 0,
            $service = 0){
        $this->initialize();
        $this->_project = $project;
        $this->_service = $service;
        if(!empty($instanceName)){
            $ref = $instanceName::GetInstance($project, $service);
            if($ref instanceof \IBookingManagement){
                $this->_management = $ref;
                return;
            }
            $this->_error = TRUE;
            $this->_errorInfo[] =
                "$instanceName No implementa la interfaz IBookingManagement";
        }
        else{
            $this->_error = TRUE;
            $this->_errorInfo[] = "No se ha especificado un nombre para "
                    . "la implementación de la interfaz IBookingManagement";
        }
    }

    /**
     * Proceso de configuración de la referencia de acceso a datos
     */
    private function initialize(){
        // Obtener nombre de la cadena de conexión
        $connectionString = ConfigurationManager
                ::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString = ConfigurationManager
                ::GetConnectionStr($connectionString);
        // Cargar las referencias
        $injector = Injector::GetInstance();
        // Cargar el objeto de acceso a datos
        $this->_DAO = $injector->Resolve( "IDataAccessObject" );
        // Configurar el objeto de conexión a datos
        $this->_DAO->Configure($oConnString);
    }

    /**
     * Proceso de ejecución de pruebas unitarias
     * @param xmlNode $xml Nodo xml para las pruebas unitaras
     */
    public function RunTest($xml = NULL){
        if($xml != NULL){
            $registers = ReaderXMLTest::GetRegisterBookingTest($xml);
            foreach($registers as $r){
                $result = $this->RegisterBookingTest($r["ref"], $r["entity"],
                        $r["save"], $r["codes"]);
                $this->Test[] = [ "Referencia" => $r["ref"],
                    "Mensaje" => $r["msg"], "Resultado" => $result];
            }

            $updates = ReaderXMLTest::GetUpdateBookingTest($xml);
            foreach($updates as $r){
                $result = $this->SavePropertyBookingTest($r["ref"], $r["id"],
                        $r["propName"], $r["propValue"] ,$r["codes"]);
                $this->Test[] = $r["msg"]. " : " . (($result) ? "Ok" : "kO");
            }

            $cancels = ReaderXMLTest::GetCancelBookingTest($xml);
            foreach($cancels as $r){
                $result = $this->CancelBookingTest($r["ref"], $r["id"],
                        $r["state"], $r["codes"]);
                $this->Test[] = $r["msg"]. " : " . (($result) ? "Ok" : "kO");
            }
        }
    }

    /**
     * Proceso para comprobar el correcto funcionamiento del método
     * RegisterBooking de la interfaz IBookingManagement
     * @param string $ref Referencia a la prueba
     * @param \Booking $entity Referencia a la entidad
     * @param boolean $saveClient Indica si los datos del cliente se registran
     * @param array $resultCodes Colección de códigos de operación esperados
     * @return boolean Resultado de la prueba
     */
    private function RegisterBookingTest($ref = "-", $entity = NULL,
            $saveClient = "", $resultCodes = []){
        $error = FALSE;
        $results = $this->_management->RegisterBooking($entity, $saveClient);
        // Comprobar que se reciben el mismo número de códigos
        if(count($results) != count($resultCodes)){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref, "Mensaje" => "La cantidad de "
                . "códigos de operación esperados no coincide"];
        }
        // Comprobar todos los códigos esperados
        foreach($resultCodes as $codigo){
            $codes = array_filter($results, function($code) use($codigo) {
                return intval($code) == intval($codigo);
            });
            if(empty($codes)){
                $error = TRUE;
                $this->_testInfo[] = ["Referencia" => $ref,
                    "Mensaje" => "Código $codigo esperado y no obtenido"];
            }
        }
        // Comprobar todos los códigos obtenidos
        foreach($results as $codigo){
            $codes = array_filter($resultCodes, function($code) use($codigo) {
                return $code == $codigo;
            });
            if(empty($codes)){
                $error = TRUE;
                $this->_testInfo[] = ["Referencia" => $ref,
                    "Mensaje" => "Código $codigo obtenido y no esperado"];
            }
        }
        $resultados = count($results);
        if( $resultados != 1 || ($resultados == 1 && $results[0] != 0)){
            $this->_testInfo[] = ["Referencia" => $ref,
                    "Mensaje" => "Códigos obtenidos: " . implode(",", $results)];
            return !$error;
        }

        // Comprobar que existe una reserva con los mismo parámetros
        $filter = [ "Project" => $entity->Project, "Turn" => $entity->Turn,
                "Date" => $entity->Date, "Diners" => $entity->Diners,
                "Email" => $entity->Email, "Phone" => $entity->Phone,
                "Offer" => $entity->Offer, "Place" => $entity->Place ];
        $reservas = $this->_DAO->GetByFilter("Booking", $filter);
        if(empty($reservas)){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "No se ha encontrado ninguna reserva con los parámetros"];
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "No se ha podido buscar la notificación correspondiente"];
            return FALSE;
        }

        $reserva = $reservas[0];
        // Comprobación de las notificaciones
        $filter2 = ["Project" => $this->_project, "Service" => $this->_service,
            "Subject" => "reserva-resbooking", "Content" => json_encode($reserva)];
        $nots = $this->_DAO->GetByFilter("Notification", $filter2);
        if(empty($nots)){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "No se ha encontrado la "
                . "notificación de reserva: $reserva->Id"];
        }
        // Comprobar la validez del registro de cliente
        if($saveClient == TRUE && $reserva->Client == NULL
                || $saveClient == FALSE && $reserva->Client != NULL){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "El registro de cliente no es correcto, "
                . "(guardar): $saveClient => Id: $reserva->Client"];
        }
        return !$error;
    }

    /**
     * Proceso para comprobar el correcto funcionamiento del método
     * SavePropertyBooking de la interfaz IBookingManagement
     * @param string $ref Referencia de la prueba
     * @param int $id Identidad de la reserva a modificar
     * @param string $propName Nombre de la propiedad que se modificará
     * @param mixed $propValue Valor para la asignación
     * @param int $resultCode Código de operación esperado
     * @return boolean Resultado del test
     */
    private function SavePropertyBookingTest($ref="-", $id=0, $propName="",
            $propValue = NULL, $resultCode = 0){
        $error = FALSE;
        $result = $this->_management->SavePropertyBooking($id, $propName, $propValue);
        // Comprobar el código de operación
        if($result != $resultCode){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "Resultado obtenido: $result, esperado: $resultCode"];
        }
        // Comprobar si se ha registrado el cambio
        $booking = $this->_DAO->Read($id, "Booking");

        if($booking == NULL){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "No se ha podido recuperar la entidad de bbdd: $id" ];
        }
        // Validar los cambios en base de datos
        if($booking->{$propName} != $propValue){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "La propiedad no se ha actualizado: "
                . "$propValue != ".$booking->{$propName} ];
        }
        return !$error;
    }

    /**
     * Proceso para comprobar el correcto funcionamiento del método
     * CancelBooking de la interfaz IBookingManagement
     * @param string $ref Referencia a la prueba
     * @param int $id Identidad de la reserva
     * @param int $state Identidad del estado de cancelación
     * @param int $resultCode Código de operación esperado
     * @return boolean
     */
    private function CancelBookingTest($ref = "-", $id = 0, $state = 0,
            $resultCode = 0){
        $error = FALSE;
        $result = $this->_management->CancelBooking($id, $state);
        // Comprobar el código de operación
        if($result != $resultCode){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "Resultado obtenido: $result, esperado: $resultCode"];
        }
        // Comprobar si se ha registrado el cambio
        $booking = $this->_DAO->Read($id, "Booking");
        if($booking == NULL){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "No se ha podido recuperar la entidad de bbdd: $id" ];
        }
        // Validar los cambios en base de datos
        if($booking->State != $state){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "La propiedad no se ha actualizado: "
                . "$state != ".$booking->State ];
        }

        // Comprobación de las notificaciones
        $filter = ["Project" => $this->_project, "Service" => $this->_service,
            "Subject" => "cancelar-resbooking", "Content" => json_encode($booking)];
        $nots = $this->_DAO->GetByFilter("Notification", $filter);
        if(empty($nots)){
            $error = TRUE;
            $this->_testInfo[] = ["Referencia" => $ref,
                "Mensaje" => "No se ha encontrado la "
                . "notificación de cancelación: $id"];
        }

        return !$error;
    }

    private function GetAggregateTest($ref = "-"){

    }

    public function GetError(){
        return $this->_testInfo;
    }

    public static function GetInstance($instanceName = "", $project = 0, $service = 0){
        if(IBookingManagementTest::$_reference == NULL){
            IBookingManagementTest::$_reference =
                    new \IBookingManagementTest($instanceName,$project, $service);
        }
        return IBookingManagementTest::$_reference;
    }
}
