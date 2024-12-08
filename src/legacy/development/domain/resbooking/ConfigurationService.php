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
 * Entidad con los parámetros de configuración del proyecto
 *
 * @author alfonso
 */
class ConfigurationService {

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
     * Mínimo número de comensales
     * @var int
     */
    public $MinDinners = 1;

    /**
     * Máximo número de comensales
     * @var int
     */
    public $MaxDinners = 25;

    /**
     * Flag para indicar si están activados los recordatorios
     * @var boolean
     */
    public $Reminders = FALSE;

    /**
     * Ventana de tiempo previa para el envío de recordatorio [en horas]
     * @var int
     */
    public $TimeSpan = 1;

    /**
     * Mínimo número de comensales para enviar un recordatorio
     * @var int
     */
    public $Dinners = 1;

    /**
     * Flag para indicar la suscripción al servicio de publicidad
     * en el formulario de reservas
     * @var boolean
     */
    public $Advertising = FALSE;

    /**
     * Flag para indicar la suscripción al servicio de pre-pedidos
     * @var boolean
     */
    public $PreOrder = FALSE;
}
