<?php

/*
 * Copyright (C) 2015 manager
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
 * Suscriptor a la lista de noticias de la web
 */
class Suscriber{

    /**
     * Identidad del suscriptor
     * @var int
     */
    public $Id=0;

    /**
     * Nombre del suscriptor
     * @var string
     */
    public $SuscriberName="";

    /**
     * Dirección de email del suscriptor
     * @var string
     */
    public $Email="";

    /**
     * Dirección IP desde donde se genera el registro
     * @var string
     */
    public $IP="";

    /**
     * Fecha de creación del registro
     * @var string
     */
    public $CreateDate = NULL;

    /**
     * Estado de la suscripción
     * @var boolean
     */
    public $Active=0;

    /**
     * Fecha de baja del registro
     * @var string
     */
    public $DeleteDate=NULL;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime("NOW");
        $this->CreateDate = $date->format( 'Y-m-d H:i:s' );
    }

}
