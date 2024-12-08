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
 * Entidad Notificación
 *
 * @author alfonso
 */
class Notification{

    /**
     * Identidad de la notificación
     * @var int
     */
    public $Id = 0;

    /**
     * proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Servicio que genera el registro
     * @var int
     */
    public $Service = 0;

    /**
     * Destino de la notificación
     * @var string
     */
    public $To = "";

    /**
     * Asunto de la notificación
     * @var string
     */
    public $Subject = "";

    /**
     * Cabecera del e-mail
     * @var string
     */
    public $Header = "";

    /**
     * Contenido de la notificación
     * @var string
     */
    public $Content = "";

    /**
     * Fecha en la que se genera la notificación
     * @var string
     */
    public $Date = "";

    /**
     * Número de veces que la notificación ha sido enviada
     * @var int
     */
    public $Dispatched = 0;
}
