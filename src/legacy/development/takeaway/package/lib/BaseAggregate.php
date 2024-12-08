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
 * Clase base para los agregados
 *
 * @author alfonso
 */
abstract class BaseAggregate{
    /**
     * Referencia al proyecto actual
     * @var \Project
     */
    public $Project = NULL;

    /**
     * Identidad del proyecto actual
     * @var int
     */
    public $IdProject = 0;

    /**
     * Identidad del servicio en ejecución
     * @var int
     */
    public $IdService = 0;

    /**
     * Establecimiento de todas las entidades del agregado
     */
    abstract public function SetAggregate();
}
