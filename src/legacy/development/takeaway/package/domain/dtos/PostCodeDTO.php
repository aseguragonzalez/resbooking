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
 * DTO para obtener la información del código postal asociado
 *
 * @author alfonso
 */
class PostCodeDTO{

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Referencia al servicio asociado
     * @var int
     */
    public $Service = 0;

    /**
     * Referencia al código postal
     * @var int
     */
    public $Code = 0;

    /**
     * Código postal
     * @var string
     */
    public $PostCode = "";

    /**
     * Flag indicación si incluye el código postal completo
     * @var boolean
     */
    public $Full = FALSE;

    /**
     * Descripción
     * @var string
     */
    public $Description;
}
