<?php

declare(strict_types=1);

/**
 * Agregado para la gestión de productos
 */
class ProductsAggregate extends \BaseAggregate{

    /**
     * Colección de las categorías registradas
     * @var array
     */
    public $Categories = [];

    /**
     * Colección de las categorías disponibles
     * @var array
     */
    public $AvailableCategories = [];

    /**
     * Referencia al producto cargado
     * @var \Product
     */
    public $Product = null;

    /**
     * Colección de imagenes del producto seleccionado
     * @var array
     */
    public $Images = [];

    /**
     * Colección de los productos referenciados en la solicitud
     * @var array
     */
    public $Products = [];

    /**
     * Constructor
     * @param int $projectId Identidad del proyecto
     * @param int $serviceId Identidad del servicio
     */
    public function __construct($projectId = 0, $serviceId = 0) {
        $this->IdProject = $projectId;
        $this->IdService = $serviceId;
        $this->Product = new \Product();
    }

    /**
     * Configuración del agregado
     */
    public function SetAggregate() {
        $this->AvailableCategories = array_filter($this->Categories,
                function ($item) {
                    return $item->State == 1;
            });
    }
}
