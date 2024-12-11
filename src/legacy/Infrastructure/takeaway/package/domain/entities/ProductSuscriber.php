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
 * Registro de suscripción de usuario a las noticias
 * de un producto
 */
class ProductSuscriber{

     /**
     * Identidad del registro de suscripción
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto al que se asocia el usuario
     * @var int
     */
    public $Product=0;

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
     * Dirección IP desde donde se genera la suscripción
     * @var type
     */
    public $IP="";

    /**
     * Fecha en la que se genera la suscripción
     * @var string
     */
    public $CreateDate = null;

    /**
     * Fecha en la que se solicita la baja
     * @var string
     */
    public $DeleteDate = null;

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct(){
        $date = new DateTime();
        $this->CreateDate = $date->format( 'Y-m-d H:i:s' );
        $this->DeleteDate = $date->format( 'Y-m-d H:i:s' );
    }

}
