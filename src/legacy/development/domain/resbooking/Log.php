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
 * Registro de actividad
 *
 * @author alfonso
 */
class Log{

    /**
     * Propiedad Id
     * @var int Identidad del registro
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) a la reserva
     * @var int Identidad de la reserva
     */
    public $Booking = 0;

    /**
     * Dirección Ip de la petición
     * @var string Dirección de red del cliente
     */
    public $Address = "";

    /**
     * Fecha en la que se realiza el registro
     * @var string Fecha en la que se realiza el registro
     */
    public $Date = NULL;

    /**
     * Información sobre la petición serializada en json
     * @var string Serialización JSON de la información a loguear
     */
    public $Information = "";

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime("NOW");
        $this->Date = $date->format( "Y-m-d H:i:s" );
    }

}
