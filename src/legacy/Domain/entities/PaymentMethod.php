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
 * Medios o formas de pago.
 */
class PaymentMethod{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre asignado al medio de pago
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del medio de mago
     * @var string
     */
    public $Description = "";

    /**
     * Abreviatura del nombre asignado
     * @var string
     */
    public $ShortName = "";

    /**
     * Nombre del icono a utilizar (si procede)
     * @var string
     */
    public $IcoName = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}
