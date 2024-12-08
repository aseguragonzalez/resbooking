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
 * Interfaz para el objeto de acceso a datos
 *
 * @author alfonso
 */
interface IDataAccessObject{

    /**
     * Permite configurar los parámetros de la conexión al
     * sistema de persistencia
     * @param array $connection Array con los parámetros de conexión
     */
    public function Configure($connection = null);

    /**
     * Persiste la entidad en el sistema y la retorna actualizada
     * @param object $entity Referencia a la entidad
     */
    public function Create($entity);

    /**
     * Obtiene una entidad filtrada por su identidad utilizando el nombre
     * del tipo de entidad
     * @param object $identity Identidad de la entidad
     * @param string $entityName Nombre de la entidad
     */
    public function Read($identity, $entityName);

    /**
     * Actualiza la información de la entidad en el sistema de persistencia.
     * @param object $entity Referencia a la entidad
     */
    public function Update($entity);

    /**
     * Elimina la entidad utilizando su identidad y el nombre del
     * tipo de entidad
     * @param object $identity Identidad de la entidad
     * @param string $entityName Nombre de la entidad
     */
    public function Delete($identity, $entityName);

    /**
     * Obtiene el conjunto de entidades existentes del tipo especificado
     * @param string $entityName Nombre de la entidad
     */
    public function Get($entityName);

    /**
     * Obtiene el conjunto de entidades del tipo especificado mediante el
     * filtro especificado. El filtro debe ser un array del tipo:
     * array( "PropertyName" => $propValue, ... )
     * @param string $entityName Nombre de la entidad
     * @param array $filter filtro de búsqueda
     */
    public function GetByFilter($entityName, $filter);

    /**
     * Ejecuta la consulta pasada como parámetro
     * @param string $query Consulta sql libre
     */
    public function ExeQuery($query);

    /**
     * Valida el contenido de una entidad
     * @param object $entity Referencia a la entidad a validar
     */
    public function IsValid($entity);

}
