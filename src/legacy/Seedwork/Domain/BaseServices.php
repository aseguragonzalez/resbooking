<?php

declare(strict_types=1);

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
    protected $Aggregate = NULL;

    /**
     * Referencia al respositorio
     * @var \BaseRepository
     */
    protected $Repository = NULL;

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
    public function __construct($aggregate = NULL){
        if($aggregate != NULL){
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
    public static function GetInstance($aggregate = NULL){

    }

    /**
     * Obtiene una entidad desde un array filtrado por id
     * @param array $array Colección de entidades
     * @param int $id Id de la entidad buscada
     * @return object|NULL Referencia encontrada
     */
    public function GetById($array = NULL, $id = 0){
        $items = array_filter($array, function($item) use ($id){
           return $item->Id == $id;
        });
        return (count($items) > 0) ? current($items) : NULL;
    }

    /**
     * Filtra una colección los criterios del filtro indicado
     * @param array $array Colección original
     * @param array $filter Colección de criterios para el filtro
     * @return array Colección de elementos que cumplen el filtro
     */
    public function GetListByFilter($array = NULL, $filter = NULL){
        $result = [];
        if($array != NULL && $filter != NULL){
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
    private function CompareObject($item = NULL, $filter = NULL){
        foreach($filter as $key => $value){
            $val = $item->{$key};
            $nok = (is_numeric($value) && $val != $value)
                || (is_string($value) && strpos($val, $value) === FALSE);
            if($nok){
                return FALSE;
            }
        }
        return TRUE;
    }
}
