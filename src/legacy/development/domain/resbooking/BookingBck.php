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
 * Entidad Backup de las reservas para cuando se modifican datos
 *
 * @author alfonso
 */
class BookingBck{

    /**
     * Propiedad Id del objeto copia
     * @var int Identidad del registro de copia
     */
    public $Id = 0;

    /**
     * Propiedad Ref del objeto copia
     * @var int Identidad de la reserva asociada
     */
    public $Ref = null;

    /**
     * Propiedad Data del objeto copia
     * @var string Serialización json de la información de reserva original
     */
    public $Data = "[]";

    /**
     * Propiedad Date del objeto copia
     * @var string Fecha en la que se realiza la copia
     */
    public $Date = null;

    /**
     * Constructor de la clase
     */
    public function __construct($o = null){
        $dt = new DateTime();
        $this->Date = $dt->format('Y-m-d H:i:s');
        if($o != null){
            $this->Ref = $o->Id;
            $this->Data = json_encode($o);
        }
    }

}
