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
 * Entidad para el registro de eventos asociados a una oferta
 *
 * @author alfonso
 */
class OfferEvent {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Referencia al proyecto padre
     * @var int
     */
    public $Project = 0;

    /**
     * Identidad del turno asociado
     * @var int
     */
    public $Turn = 0;

    /**
     * Identidad de la oferta asociada
     * @var int
     */
    public $Offer = 0;

    /**
     * Identidad de la configuración de línea base(si existe)
     * @var int
     */
    public $Config = "";

    /**
     * Anyo del evento
     * @var int
     */
    public $Year = 0;

    /**
     * Semana del anyo en que es válida
     * @var int
     */
    public $Week = 0;

    /**
     * Día de la semana
     * @var int
     */
    public $DayOfWeek = 0;

    /**
     * Fecha del evento
     * @var string
     */
    public $Date = "";

    /**
     * Tipo de evento de oferta: válida o no
     * @var boolean
     */
    public $State = FALSE;

}
