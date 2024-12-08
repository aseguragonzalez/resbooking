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
 * DTO para el listado de reservas
 *
 * @author alfonso
 */
class BookingDTO {
    // Propiedades de la reserva
    public $Id = 0;
    public $Project = 0;
    public $Turn = 0;
    public $Client = NULL;
    public $Date = NULL;
    public $Diners = 1;
    public $ClientName = "";
    public $Email = "";
    public $Phone = "";
    public $CreateDate = NULL;
    public $State = null;
    public $Offer = null;
    public $Place = null;
    public $Comment = "-";
    public $BookingSource = NULL;
    public $Notes = "";
    public $PreOrder = "";
    // Propiedades del turno
    public $TurnSlot = 0;
    public $TurnStart = "";
    public $TurnEnd = "";
    // Propiedades del "espacio"
    public $PlaceName = 0;
    public $PlaceDescription = "";
    public $PlaceSize = 0;
    // Propiedades de la oferta
    public $OfferTitle = 0;
    public $OfferDescription = "";
    public $OfferTerms = "";
    public $OfferStart = "";
    public $OfferEnd = "";
    // Propiedades origen reserva
    public $SourceName = "";
    public $SourceDescription = "";
    // Campos calculados
    public $ClientCount = 0;
}
