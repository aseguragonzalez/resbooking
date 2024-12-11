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
 * Model para la gestión de Reservas
 *
 * @author alfonso
 */
class RequestsModel extends \ResbookingModel{

    /**
     * Opción del menú principal seleccionado
     * @var string $Activo
     */
    public $Activo = "Pendientes";

    /**
     * Colección de reservas sin estado: Pendientes
     * @var array $Entities
     */
    public $Entities = [];

    /**
     * Número de solicitudes pendientes
     * @var int $Counts Cantidad de solicitudes pendientes
     */
    public $Counts = 0;

    /**
     * Constructor
     */
    public function __construct(){
        // LLamada al constructor
        parent::__construct();
        // Título de la página
        $this->Title = "Pendientes";
    }

    /**
     * Obtiene la colección de reservas pendientes y las
     * configura para su visualización.
     */
    public function GetRequests(){
        $filter = ["Project" => $this->Project, "State" => NULL];
        $this->Entities = $this->Dao->GetByFilter("RequestDTO", $filter);
        foreach($this->Entities as $item){
            $item->Date = $this->SetDate($item->Date);
            $item->sComment = $this->SetText($item->Comment);
            $item->sOfferTitle = $this->SetText($item->OfferTitle);
            $item->TurnStart = substr($item->TurnStart,0,5);
        }
        $this->Counts = count($this->Entities);
    }

    /**
     * Obtiene el número de solicitudes pendientes
     * @return int Número de solicitudes pendientes
     */
    public function GetRequestCount(){
        $filter = ["Project" => $this->Project, "State" => NULL];
        $requests = $this->Dao->GetByFilter("RequestDTO", $filter);
        $this->Counts = count($requests);
        return $this->Counts;
    }

    /**
     * Modificación del estado de la reserva
     * @param int $id Identidad de la reserva
     * @param int $idState Identidad del nuevo estado
     * @param boolean $cancel Tipo de cambio de estado : Aceptar o cancelar
     * @return int código de operación
     */
    public function ChangeState($id = 0, $idState = 0, $cancel = FALSE){
        // Obtener una referencia al management de gestión de reservas
        $management = BookingManagement
                ::GetInstance($this->Project, $this->Service);
        if($cancel == TRUE){
            $management->CancelBooking($id, $idState);
        }
        else{
            $management->SavePropertyBooking($id, "State", $idState);
        }
        return 0;
    }

    /**
     * Acorta el texto en función de la longitud del mismo
     * @param string $comment Texto a recortar
     * @return string Texto
     */
    private function SetText($text  = "", $maxlength = 25){
        if(isset($text) && strlen($text) > $maxlength){
            return substr($text, 0, $maxlength-3 )."...";
        }
        return $text;
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
}
