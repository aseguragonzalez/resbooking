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
 * Descuento especificado sobre el precio de un producto para el proyecto y
 * servicio especificado. El descuento es aplicable cuando un precio
 * está entre el valor mínimo ( x >= MinValue ) y el valor máximo ( x < MaxValue)
 */
class DiscountOn{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    public $Service = 0;

    /**
     * Porcentaje de descuento
     * @var int
     */
    public $Value = 0;

    /**
     * Valor mínimo aplicable
     * @var int
     */
    public $Min = 0;

    /**
     * Valor máximo aplicable
     * @var int
     */
    public $Max = 0;

    /**
     * Fecha de inicio del descuento. Formato : yyyy-mm-dd
     * @var string
     */
    public $Start = "";

    /**
     * Fecha de fin del descuento. Formato : yyyy-mm-dd
     * @var string
     */
    public $End = "";

    /**
     * Estado lógico del registro
     * @var boolean
     */
    public $State = 1;
}
