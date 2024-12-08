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
 * Configuración de días bloqueados
 *
 * @author alfonso
 */
class Block{

    /**
     * Identidad
     * @var int Identidad del bloqueo
     */
    public $Id = 0;

    /**
     * Referencia externa (fk) al Proyecto
     * @var int Identidad del proyecto asociado
     */
    public $Project = 0;

    /**
     * Referencia externa(fk) al Turno
     * @var int Identidad del turno asociado
     */
    public $Turn = 0;

    /**
     * Fecha del día que se bloquea
     * @var string Fecha del bloqueo
     */
    public $Date = NULL;

    /**
     * Tipo de registro: Bloqueo = TRUE| Apertura = FALSE
     * @var boolean
     */
    public $Block = TRUE;

    /**
     * Anyo del bloqueo
     * @var int
     */
    public $Year = 0;

    /**
     * Semana del bloqueo
     * @var int
     */
    public $Week = 0;
}
