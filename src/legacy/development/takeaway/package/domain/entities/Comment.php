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
 * Entidad comentario. Representa el comentario de un usuario
 * respecto al producto con el que está relacionado
 */
class Comment{

    /**
     * Identidad del comentario
     * @var int
     */
    public $Id=0;

    /**
     * Referencia al producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Título del comentario
     * @var string
     */
    public $Title="";

    /**
     * Texto del comentario
     * @var string
     */
    public $Text="";

    /**
     * Autor / Usuario que realiza el comentario
     * @var string
     */
    public $Author="";

    /**
     * Fecha en la que se realiza el comentario
     * @var string
     */
    public $Date=null;

    /**
     * Cantidad de votos positivos
     * @var int
     */
    public $Likes=0;

    /**
     * Cantidad de votos negativos
     * @var int
     */
    public $Unlikes=0;

    /**
     * Estado del comentario
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct() {
        $date = new DateTime();
        $this->Date = $date->format( 'Y-m-d H:i:s' );
    }
}
