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
 * Clase base para los repositorios
 *
 * @author alfonso
 */
abstract class BaseRepository{

    /**
     * Identidad del proyecto del contexto
     * @var int
     */
    protected $IdProject = 0;

    /**
     * Identidad del servicio del contexto
     * @var string
     */
    protected $IdService = 0;

    /**
     * Referencia al objeto de acceso a datos
     * @var \IDataAccessObject
     */
    protected $Dao = NULL;

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager Gestor de trazas
     */
    protected $Log = NULL;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0) {
        $this->IdProject = $project;
        $this->IdService = $service;
        // Obtener nombre de la cadena de conexión
        $connectionString = ConfigurationManager
                ::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString = ConfigurationManager
                ::GetConnectionStr($connectionString);
        // Cargar las referencias
        $injector = Injector::GetInstance();
        // Cargar una instancia del gestor de trazas
        $this->Log = $injector->Resolve( "ILogManager" );
        // Cargar el objeto de acceso a datos
        $this->Dao = $injector->Resolve( "IDataAccessObject" );
        // Configurar el objeto de conexión a datos
        $this->Dao->Configure($oConnString);
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \BaseAggregate
     */
    public abstract function GetAggregate();

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \BaseRepository
     */
    public static function GetInstance($project = 0, $service = 0){

    }

    /**
     * Obtiene una colección con las entidades solicitadas
     * @param string $entityName
     * @return array
     */
    public function Get($entityName = ""){
        return $this->Dao->Get($entityName);
    }

    /**
     * Obtiene una colección filtrada de entidades del tipo solicitado
     * @param string $entityName Nombre del tipo de entidad solicitada
     * @param array $filter Filtro de búsqueda
     * @return array Colección de entidades disponibles
     */
    public function GetByFilter($entityName = "", $filter = NULL){
        return $this->Dao->GetByFilter($entityName, $filter);
    }

    /**
     * Crea un registro de la entidad solicitada
     * @param object $entity Referencia a la entidad a registrar
     * @return object|boolean Referencia a la entidad generada o FALSE
     */
    public function Create($entity = NULL){
        if($entity != NULL){
             $entity->Id = $this->Dao->Create($entity);
            return $entity;
        }
        return FALSE;
    }

    /**
     * Realiza una búsqueda de entidad por su identidad
     * @param string $entityName Nombre de la entidad
     * @param object $identity Identidad de la entidad buscada
     * @return object Referencia a la entidad buscada
     */
    public function Read($entityName = "", $identity = NULL){
        return $this->Dao->Read($identity, $entityName);
    }

    /**
     * Actualización de la entidad pasado como argumento
     * @param object $entity Referencia a la entidad a actualizar
     * @return object|boolean Referencia a la entidad o FALSE
     */
    public function Update($entity = NULL){
        if($entity != NULL){
            $this->Dao->Update($entity);
            return $entity;
        }
        return FALSE;
    }

    /**
     * Eliminación de la entidad por su identidad
     * @param string $entityName Nombre de la entidad
     * @param object $identity Identidad de la entidad
     * @return boolean Resultado de la operacion
     */
    public function Delete($entityName = "", $identity = NULL){
        return $this->Dao->Delete($identity, $entityName);
    }
}
