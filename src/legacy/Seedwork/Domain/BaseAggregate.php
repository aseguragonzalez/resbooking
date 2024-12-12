<?php

declare(strict_types=1);

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
    public int $projectId = 0;

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
