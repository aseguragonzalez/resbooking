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
 * Información de la reserva
 *
 * @author alfonso
 */
class Booking{

    /**
     * Identidad
     * @var int Identidad de la reserva
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) al Proyecto
     * @var int Identidad del proyecto
     */
    public $Project = 0;

    /**
     * Referencia externa (fk) al Turno de comida
     * @var int Identidad del turno
     */
    public $Turn = 0;

    /**
     * Referencia externa (fk) al cliente registrado
     * @var int Identidad del cliente (si ha sido registrado)
     */
    public $Client = NULL;

    /**
     * Fecha para la reserva
     * @var string Fecha de la reserva
     */
    public $Date = NULL;

    /**
     * Comensales
     * @var int Comensales para la reserva
     */
    public $Diners = 1;

    /**
     * Usuario que reserva
     * @var string Nombre al que se hace la reserva
     */
    public $ClientName = "";

    /**
     * E-mail de contacto
     * @var string Email de contacto para la reserva
     */
    public $Email = "";

    /**
     * Teléfono de contacto
     * @var string Teléfono de contacto para la reserva
     */
    public $Phone = "";

    /**
     * Fecha de creación del registro
     * @var string Fecha de creación del registro
     */
    public $CreateDate = NULL;

    /**
     * Fecha de creación del registro
     * @var int Estado de workflow de gestión de la reserva
     */
    public $State = null;

    /**
     * Referencia a la oferta seleccionada
     * @var int Identidad de la ofera asociada a la reserva
     */
    public $Offer = null;

    /**
     * Referencia al lugar seleccionado
     * @var int Identidad del "Lugar" asociado a la reserva
     */
    public $Place = null;

    /**
     * Comentarios del cliente sobre la reserva
     * @var string Comentarios de la reserva
     */
    public $Comment = "-";

    /**
     * Identidad del origen de reserva
     * @var int
     */
    public $BookingSource = NULL;

    /**
     * Notas asociadas a la reserva
     * @var string
     */
    public $Notes = "";

    /**
     * Información del pre-pedido
     * @var string
     */
    public $PreOrder = "";

    /**
     * Información para la gestión de mesas
     * @var string
     */
    public $sTable = "";

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime( "NOW" );
        $this->Date = $date->format( "Y-m-d H:i:s" );
        $this->CreateDate = $date->format( "Y-m-d H:i:s" );
    }

}
