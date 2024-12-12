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
 * Clase base de la capa de aplicación
 *
 * @author alfonso
 */
abstract class BaseManagement{

    /**
     * Identidad del proyecto actual
     * @var int
     */
    protected $IdProject = 0;

    /**
     * Identidad del servicio en ejecución
     * @var int
     */
    protected $IdService = 0;

    /**
     * Referencia al respositorio de entidades
     * @var \BaseRepository
     */
    protected $Repository = null;

    /**
     * Referencia al gestor de servicios de la capa de dominio
     * @var \BaseService
     */
    protected $Service = null;

    /**
     * Referencia al agregado actual
     * @var \BaseAggregate;
     */
    protected $Aggregate = null;

    /**
     * Constructor de la clase
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        $this->IdProject = $project;
        $this->IdService = $service;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseAggregate
     */
    public abstract function GetAggregate();

    /**
     * Obtiene la instancia actual del Management del contexto
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseManagement
     */
    public static function GetInstance($project = 0, $service = 0){

    }
}
