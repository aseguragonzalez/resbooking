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
 * Clase base para la capa de servicios de dominio
 *
 * @author alfonso
 */
abstract class BaseServices{

    /**
     * Referencia al agregado actual
     * @var \BaseAggregate
     */
    protected $Aggregate = null;

    /**
     * Referencia al respositorio
     * @var \BaseRepository
     */
    protected $Repository = null;

    /**
     * Identidad del proyecto
     * @var int
     */
    protected $IdProject = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    protected $IdService = 0;

    /**
     * Constructor
     * @param \BaseAggregate $aggregate Referencia al agregado
     */
    public function __construct($aggregate = null){
        if($aggregate != null){
            // Asignar el agregado
            $this->Aggregate = $aggregate;
            // Asignar identidad del proyecto
            $this->IdProject = $aggregate->IdProject;
            // Asignar la identidad del servicio
            $this->IdService = $aggregate->IdService;
        }
    }

    /**
     * Obtiene la referencia actual al gestor de servicios
     * @param \BaseAggregate Referencia al agregado actual
     */
    public static function GetInstance($aggregate = null){

    }

    /**
     * Obtiene una entidad desde un array filtrado por id
     * @param array $array Colección de entidades
     * @param int $id Id de la entidad buscada
     * @return object|null Referencia encontrada
     */
    public function GetById($array = null, $id = 0){
        $items = array_filter($array, function($item) use ($id){
           return $item->Id == $id;
        });
        return (count($items) > 0) ? current($items) : null;
    }

    /**
     * Filtra una colección los criterios del filtro indicado
     * @param array $array Colección original
     * @param array $filter Colección de criterios para el filtro
     * @return array Colección de elementos que cumplen el filtro
     */
    public function GetListByFilter($array = null, $filter = null){
        $result = [];
        if($array != null && $filter != null){
            foreach($array as $item){
                if($this->CompareObject($item, $filter)){
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * Función para filtrar objetos por un array de parámetros
     * @param object $item Referencia al objeto a filtrar
     * @param array $filter Array con los criterios de filtro
     * @return boolean
     */
    private function CompareObject($item = null, $filter = null){
        foreach($filter as $key => $value){
            $val = $item->{$key};
            $nok = (is_numeric($value) && $val != $value)
                || (is_string($value) && strpos($val, $value) === false);
            if($nok){
                return false;
            }
        }
        return true;
    }
}
