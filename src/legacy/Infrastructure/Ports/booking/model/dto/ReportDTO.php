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
 * DTO Resumen del informe de reservas
 */
class ReportDTO{

    /**
     * Mes
     * @var string
     */
    public $Mes = "";

    /**
     * Anyo
     * @var string
     */
    public $Anyo = "";

    /**
     * Cantidad de reservas cursadas
     * @var int
     */
    public $Cursadas = 0;

    /**
     * Cantidad de reservas perdidas
     * @var int
     */
    public $Perdidas = 0;

    /**
     * Mes del informe
     * @var int
     */
    public $Month = 0;

    /**
     * Año del informe
     * @var int
     */
    public $Year = 0;
}
