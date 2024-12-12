<?php

declare(strict_types=1);

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
    protected $repository = null;

    /**
     * Referencia al gestor de servicios de la capa de dominio
     * @var \BaseService
     */
    protected $Service = null;

    /**
     * Referencia al agregado actual
     * @var \BaseAggregate;
     */
    protected $aggregate = null;

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
