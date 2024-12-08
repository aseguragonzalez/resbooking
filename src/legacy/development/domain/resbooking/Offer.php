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
 * Oferta disponible para las reservas
 *
 * @author alfonso
 */
class Offer{

    /**
     * Propiedad Id
     * @var int Identidad de la oferta
     */
    public $Id = 0;

    /**
     * Propiedad Project
     * @var int Identidad del proyecto asociado
     */
    public $Project = 0;

    /**
     * Propiedad Título
     * @var string Título de la oferta
     */
    public $Title = 0;

    /**
     * Propiedad Description
     * @var string Descripción de la oferta
     */
    public $Description = "";

    /**
     * Propiedad Terms
     * @var string Términos de la oferta
     */
    public $Terms = "";

    /**
     * Propiedad Start
     * @var string Fecha de inicio de validez de la oferta
     */
    public $Start = "";

    /**
     * Propiedad End
     * @var string Fecha de fin de validez de la oferta
     */
    public $End = "";

    /**
     * Propiedad Active
     * @var boolean Estado de la oferta
     */
    public $Active = true;
}
