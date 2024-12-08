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
 * Clase para los test del management de reservas
 */
class Test_Booking_Management extends \Test_BaseClass{

    /**
     * Constructor de la clase
     * @param int $idProject Identidad del proyecto
     * @param int $idService Identidad del servicio
     */
    public function __construct($idProject = 0, $idService = 0) {
        parent::__construct($idProject, $idService);
    }

    /**
     * Colección de funciones utilizadas para el testeo de la clase
     * BookingManagement a patir de la información establecida en
     * el fichero xml de pruebas.
     *
     * Definición de reserva:
     * <booking project="" source="" clientname="" email="" phone="" comment=""
     *      date="" diners="" offer="" place="" turn="" msg="" code="" />
     *
     */

    /**
     * Obtiene una referencia al agregado de reserva de un proyecto
     * @param int $idProject Id del proyecto
     * @param int $idService Id del servicio actual
     * @return \BookingAggregate Referencia al agregado de reservas
     */
    private function get_BookingAggregate($idProject = 0, $idService = 0){
        // Obtener instacia del agregado desde el repositorio
        $agg = $this->load_from_repository($idProject,
                $idService, BaseRepository::GetInstance());
        // Configurar la instancia
        $agg->SetAggregate();
        // retornar la referencia
        return $agg;
    }

    /**
     * Carga la referencia del agregado desde el repositorio
     * @param int $idProject Id del proyecto
     * @param int $idService Id del servicio actual
     * @param \BookingAggregate $agg Referencia al agregado
     * @param \BaseRepository $repo Referencia al repositorio
     * @return \BookingAggregate
     */
    private function load_from_repository($idProject = 0,
            $idService = 0, $repo = NULL){
        $agg = new BookingAggregate($idProject, $idService );
        $agg->Project = $repo->Read("Project", $agg->IdProject);
        $agg->States = $repo->Get("State");
        $agg->Turns = $repo->Get("Turn");
        $agg->Slots = $repo->Get("Slot");
        $agg->BookingSources = $repo->Get("BookingSource");
        $filter = array( "Project" => $agg->IdProject);
        $agg->Places = $repo->GetByFilter( "Place" , $filter);
        $agg->Blocks = $repo->GetByFilter( "Block" , $filter);
        $agg->Configurations = $repo->GetByFilter( "Configuration", $filter);
        $agg->Offers = $repo->GetByFilter( "Offer" , $filter);
        foreach($agg->Offers as $offer){
            $filtroOferta = array( "Offer" => $offer->Id );
            $offer->Config = $repo->GetByFilter( "OfferConfig", $filtroOferta);
        }
        return $agg;
    }

    /**
     * Proceso de validación de los datos de un agregado
     * @param \BookingAggregate $agg Referencia al agregado a validar
     * @return boolean
     */
    private function validate_BookingAggregate($agg = NULL){

        if($agg == NULL){
            return FALSE;
        }

        $vagg = $this->get_BookingAggregate($agg->IdProject, $agg->IdService);

        if(compare_objects_list($agg->Blocks, $vagg->Blocks, "Id") != 0
            || compare_objects_list($agg->BookingSources, $vagg->BookingSources, "Id") != 0
            || compare_objects_list($agg->Configurations, $vagg->Blocks, "Id") != 0
            || compare_objects_list($agg->Offers, $vagg->Offers, "Id") != 0
            || compare_objects_list($agg->Places, $vagg->Places, "Id") != 0
            || compare_objects_list($agg->Slots, $vagg->Slots, "Id") != 0
            || compare_objects_list($agg->States, $vagg->States, "Id") != 0
            || compare_objects_list($agg->Turns, $vagg->Turns, "Id") != 0
            || compare_objects_list($agg->AvailableBlocks, $vagg->AvailableBlocks, "Id") != 0
            || compare_objects_list($agg->AvailableOffers, $vagg->AvailableOffers, "Id") != 0
            || compare_objects_list($agg->AvailablePlaces, $vagg->AvailablePlaces, "Id") != 0
            || compare_objects_list($agg->AvailableTurns, $vagg->AvailableTurns, "Id") != 0){
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Obtiene una instancia de reserva con los datos configurados en el xml
     * de pruebas de reservas. La definición del xml es como
     * se indica a continuación:
     * @param xml $attr
     * @return \Booking
     */
    private function get_xml_booking($attr = NULL){
        $date = new DateTime("NOW");
        $bkg = new Booking();
        $bkg->Project = (string)$attr->project;
        $bkg->BookingSource = (string)$attr->source;
        $bkg->ClientName = (string)$attr->clientname;
        $bkg->Email = (string)$attr->email;
        $bkg->Phone = (string)$attr->phone;
        $bkg->Comment = (string)$attr->comment;
        $bkg->CreateDate = $date->format("Y-m-d h:i:s");
        $bkg->Date = (string)$attr->date;
        $bkg->Diners = (string)$attr->diners;
        $bkg->Offer = (string)$attr->offer;
        $bkg->Place = (string)$attr->place;
        $bkg->Turn = (string)$attr->turn;
        return $bkg;
    }

    /**
     * Obtiene una instancia del objeto reserva con datos configurados
     * utilizando el instante de instanciación.
     * @param int $project Id del proyecto en pruebas
     * @return \Booking Referencia al objeto reserva
     */
    private function get_time_booking($project = 0){
        $date = new DateTime("NOW");
        $sdate = $date->format("Ymdhis");
        $bkg = new Booking();
        $bkg->Project = $project;
        $bkg->ClientName = "Usuario-$sdate";
        $bkg->BookingSource = 1;
        $bkg->Email = "usuario-$sdate@domain.com";
        $bkg->Phone = "$sdate";
        $bkg->Comment = "";
        $bkg->CreateDate = $date->format("Y-m-d h:i:s");
        $bkg->Date = $date->format("Y-m-d");
        $bkg->Diners = 2;
        $bkg->Offer = NULL;
        $bkg->Place = 1;
        $bkg->Turn = 1;
        return $bkg;
    }

    /**
     * Obtiene la colección de pruebas de reserva configuradas
     * @param object $xml nodo del xml de pruebas
     * @return array Colección de test a ejecutar
     */
    private function read_xml_booking_test($xml = NULL){
        $test = array();
        if(isset($xml->register)){
            $bookings = $xml->register->children();
            foreach($bookings as $book){
                $attrs = $book->attributes();
                $test[] = array(
                        "book" => $this->get_xml_booking($attrs),
                        "msg" => (string)$attrs->msg,
                        "code" => (string)$attrs->code
                    );
            }
        }
        return $test;
    }

    /**
     * Obtiene la colección de test de actualización de reserva
     * @param object $xml nodo del xml de pruebas
     * @return array Colección de test a ejecutar
     */
    private function read_xml_update_booking($xml  = NULL){
        $test = array();
        if(isset($xml->update)){
            $bookings = $xml->update->children();
            foreach($bookings as $book){
                $attrs = $book->attributes();
                $test[] = array(
                        "propName"  => (string)$attr->propName,
                        "propValue"  => (string)$attr->propValue,
                        "msg" => (string)$attrs->msg,
                        "code" => get_codes($attrs->code)
                    );
            }
        }
        return $test;
    }

    /**
     * Obtiene la colección de test de cancelación de reserva
     * @param object $xml nodo del xml de pruebas
     * @return array Colección de test a ejecutar
     */
    private function read_xml_cancel_booking($xml = NULL){
        $test = array();
        if(isset($xml->cancel)){
            $bookings = $xml->cancel->children();
            foreach($bookings as $book){
                $attrs = $book->attributes();
                $test[] = array(
                        "id" => (string)$attrs->id,
                        "msg" => (string)$attrs->msg,
                        "code" => get_codes($attrs->code)
                    );
            }
        }
        return $test;
    }

/*******************************************************************************
* Funciones para la validación de las firmas del contrado de BookingManagement*
******************************************************************************/

    /**
     * Procedimiento para validación de la instanciación del
     * objeto BookingManagement
     */
    protected function validate_GetInstance(){
        $bm = BookingManagement
                ::GetInstance($this->IdProject, $this->IdService);

        $res = $bm != NULL;

        $this->Tests["GetInstance"] = array(
            "msg" => "Validación obtener instancia BookingManagement",
            "code" => "-",
            "result" => $res
        );
    }

    /**
     * Procedimiento de validación de la información cargada en el agregado
     * mediante el Management instanciado
     */
    protected function validate_GetAggregate(){
        $bm = BookingManagement
                ::GetInstance($this->IdProject, $this->IdService);

        $agg = $bm->GetAggregate();

        $res = $this->validate_BookingAggregate($agg);

        $this->Tests["GetAggregate"] = array(
            "msg" => "Validación del agregado obtenido con GetAggregate",
            "code" => "-",
            "result" => $res
        );
    }

    /**
     * Ejecución de las pruebas definidas para el registro de reservas
     * @param object $xml Nodo xml donde se definen las pruebas a seguir
     * @return void
     */
    protected function validate_RegisterBooking($xml = NULL){
        $tests = $this->read_xml_booking_test($xml);
        $bm = BookingManagement
                ::GetInstance($this->IdProject, $this->IdService);
        foreach($tests as $test){
            $test["result"] = $bm->RegisterBooking($test["book"], TRUE);
        }
        $this->Tests["RegisterBooking"] = $tests;
    }

    /**
     * Ejecución de las pruebas definidas para la actualización de reservas
     * @param object $xml Nodo xml donde se definen las pruebas a seguir
     * @return void
     */
    protected function validate_SavePropertyBooking($xml = NULL){
        $tests = $this->read_xml_update_booking($xml);
        $bm = BookingManagement
                ::GetInstance($this->IdProject, $this->IdService);
        foreach($tests as $test){
            // Obtener un registro aleatorio
            $book = $this->get_time_booking($this->IdProject);
            // Generar registro
            $res = $bm->RegisterBooking($book, TRUE);

            if($res >= 0){
                // Obtener id
                $id = $bm->Aggregate->Booking->Id;
                // Probar la modificación
                $test["result"] =
                        $bm->SavePropertyBooking($id,
                                $test["propName"], $test["propValue"]);
            }
            else{
                $test["result"] = -333;
            }
        }
        $this->Tests["SavePropertyBooking"] = $tests;
    }

    /**
     * Ejecución de las pruebas definidas para la cancelación de reservas
     * @param object $xml Nodo xml donde se definen las pruebas a seguir
     * @return void
     */
    protected function validate_CancelBooking($xml = NULL){
        $tests = $this->read_xml_cancel_booking($xml);
        $bm = BookingManagement
                ::GetInstance($this->IdProject, $this->IdService);
        foreach($tests as $test){
            // Obtener un registro aleatorio
            $book = $this->get_time_booking($this->IdProject);
            // Generar registro
            $res = $bm->RegisterBooking($book, TRUE);

            if($res >= 0){
                // Obtener id
                $id = $bm->Aggregate->Booking->Id;
                // Probar la modificación
                $test["result"] = $bm->CancelBooking($id, $test["id"]);
            }
            else{
                $test["result"] = -333;
            }
        }
        $this->Tests["CancelBooking"] = $tests;
    }

    /**
     * Ejecucion de las pruebas definidas para el listado de reservas por
     * fechas.
     * @param object $xml Nodo xml donde se definen las pruebas a seguir
     * @return void
     */
    protected function validate_GetBookingsByDate($xml = NULL){
        // GetBookingsByDate($sDate = "")
    }

    /**
     * Ejecucion de las pruebas definidas para el listado de reservas segun
     * el filtro definido
     * @param object $xml Nodo xml donde se definen las pruebas a seguir
     * @return void
     */
    protected function validate_GetBookingsByFilter($xml = NULL){
        // GetBookingsByFilter($filter = NULL)
    }

    /**
     * Ejecucion de todos los test definidos
     * @param object $xml Nodo xml para el test del Management
     * @return array Coleccion de pruebas realizadas
     */
    public function Test($xml = NULL){
        if($xml != NULL){
            $this->validate_GetInstance();
            $this->validate_GetAggregate();
            $this->validate_RegisterBooking($xml);
            $this->validate_SavePropertyBooking($xml);
            $this->validate_CancelBooking($xml);
            $this->validate_GetBookingsByDate($xml);
            $this->validate_GetBookingsByFilter($xml);
        }
        return $this->Tests;
    }

}
