<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz para el repositorio de productos
 *
 * @author alfonso
 */
class ProductsRepository extends \BaseRepository implements \IProductsRepository{

    /**
     * Referencia a la clase base
     * @var \IProductsRepository
     */
    private static $_reference = null;

    /**
     * Constructor
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     */
    public function __construct($project = 0, $service = 0){
        parent::__construct($project, $service);
    }

    /**
     * Obtiene la referencia actual al repositorio
     * @param int $project Identidad del proyecto
     * @param int $service Identidad del servicio
     * @return \IProductsRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(ProductsRepository::$_reference == null){
            ProductsRepository::$_reference =
                    new \ProductsRepository($project, $service);
        }
        return ProductsRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \ProductsAggregate
     */
    public function GetAggregate() {

        $agg = new \ProductsAggregate($this->IdProject, $this->IdService);

        $filter = ["Project" => $this->IdProject, "State"  => 1];

        $products = $this->Dao->GetByFilter( "Product", $filter );

        foreach($products as $item){
            $agg->Products[$item->Id] = $item;
        }

        $agg->Categories = $this->Dao->GetByFilter( "Category", $filter );

        return $agg;
    }
}
