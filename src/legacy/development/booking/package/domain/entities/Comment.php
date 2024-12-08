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
 * Comentario sobre la reserva
 *
 * @author alfonso
 */
class Comment{

    /**
     * Identidad del comentario
     * @var int Identidad del comentario
     */
    public $Id = 0;

    /**
     * Referencia a la reserva
     * @var int Identidad de la reserva asociada
     */
    public $Booking = 0;

    /**
     * Comentario
     * @var string Comentario
     */
    public $Text = "";

    /**
     * Fecha del comentario
     * @var string Fecha en la que se realiza el comentario
     */
    public $Date = "";

    /**
     * Usuario que crea el comentario
     * @var string Nombre de usuario
     */
    public $UserName = "";

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime( "NOW" );
        $this->Date = $date->format( "Y-m-d H:i:s" );
    }
}
