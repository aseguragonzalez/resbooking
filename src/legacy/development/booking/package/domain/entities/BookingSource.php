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
 * Entidad origen de datos
 *
 * @author alfonso
 */
class BookingSource{

    /**
     * Identidad del origen de reserva
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre del origen de reserva
     * @var string
     */
    public $SourceName = "";

    /**
     * Descripción del origen de datos
     * @var string
     */
    public $Description = "";
}
