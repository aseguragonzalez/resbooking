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
 * Description of ReaderXMLTest
 *
 * @author alfonso
 */
class ReaderXMLTest {

    /**
     * Proceso para establecer la opción "guardar datos" del comensal
     * @param string $save
     * @return boolean
     */
    private static function get_save_client($save = ""){
        if(intval($save) == 1){
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Proceso para decodificar los códigos de operación de la prueba
     * @param string $codes Códigos de operación
     * @return array
     */
    private static function get_codes($codes = ""){
        return explode(",", $codes);
    }

    /**
     * Obtiene una instancia de reserva con los datos configurados en el xml
     * de pruebas de reservas. La definición del xml es como
     * se indica a continuación:
     * @param xml $attr
     * @return \Booking
     */
    private static function get_booking_from_xml($attr = NULL){
        $date = new \DateTime("NOW");
        $bkg = new \Booking();
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
     * Obtiene la colección de pruebas de reserva configuradas
     * @param object $xml nodo del xml de pruebas
     * @return array Colección de test a ejecutar
     */
    public static function GetRegisterBookingTest($xml = NULL){
        $test = [];
        if(isset($xml->registers)){
            $bookings = $xml->registers->children();
            foreach($bookings as $book){
                $attrs = $book->attributes();
                $test[] = [
                        "ref" => (string)$attrs->ref,
                        "save" =>  ReaderXMLTest::get_save_client((string)$attrs->save),
                        "entity" => ReaderXMLTest::get_booking_from_xml($attrs),
                        "msg" => (string)$attrs->msg,
                        "codes" => ReaderXMLTest::get_codes((string)$attrs->codes)
                ];
            }
        }
        return $test;
    }

    /**
     * Obtiene la colección de test de actualización de reserva
     * @param object $xml nodo del xml de pruebas
     * @return array Colección de test a ejecutar
     */
    public static function GetUpdateBookingTest($xml  = NULL){
        $test = [];
        if(isset($xml->updates)){
            $updates = $xml->updates->children();
            foreach($updates as $update){
                $attrs = $update->attributes();
                $test[] = [
                    "ref" => (string)$attrs->ref,
                    "id" => (string)$attrs->id,
                    "propName"  => (string)$attrs->propName,
                    "propValue"  => (string)$attrs->propValue,
                    "msg" => (string)$attrs->msg,
                    "codes" => ReaderXMLTest::get_codes((string)$attrs->codes)
                ];
            }
        }
        return $test;
    }

    /**
     * Obtiene la colección de test de cancelación de reserva
     * @param object $xml nodo del xml de pruebas
     * @return array Colección de test a ejecutar
     */
    public static function GetCancelBookingTest($xml = NULL){
        $test = [];
        if(isset($xml->cancels)){
            $cancels = $xml->cancels->children();
            foreach($cancels as $cancel){
                $attrs = $cancel->attributes();
                $test[] = [
                    "ref" => (string)$attrs->ref,
                    "id" => (string)$attrs->id,
                    "msg" => (string)$attrs->msg,
                    "codes" => ReaderXMLTest::get_codes((string)$attrs->codes)
                ];
            }
        }
        return $test;
    }
}
