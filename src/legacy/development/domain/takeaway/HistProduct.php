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
 * Entidad Histórico. Se utiliza para almacenar cualquier cambio
 * realizado sobre un producto
 */
class HistProduct{

    /**
     * Identidad del historico
     * @var int
     */
    public $Id=0;

    /**
     * Identidad del producto modificado
     * @var int
     */
    public $Product=0;

    /**
     * Serialización JSON del producto
     * @var type
     */
    public $Json="";

    /**
     * Fecha en la que se realiza la modificación
     * @var string
     */
    public $Date=null;

    /**
     * Constructor
     * @param \Product $product Referencia al producto modificado
     */
    public function __construct($product = null){
        if($product != null
                && !is_array($product)
                && is_object($product)){
            $date = new DateTime();
            $this->Product = $product->Id;
            $this->Json = json_encode($product);
            $this->Date = $date->format( 'Y-m-d H:i:s' );
        }
    }
}
