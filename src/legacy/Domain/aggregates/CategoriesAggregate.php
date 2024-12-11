<?php

declare(strict_types=1);

/**
 * Agregado para la gestión de Categorias
 */
class CategoriesAggregate extends \BaseAggregate{

    /**
     * Referencia a la categoría cargada
     * @var \Category
     */
    public $Category = NULL;

    /**
     * Colección de categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Category = new \Category();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {

    }
}
