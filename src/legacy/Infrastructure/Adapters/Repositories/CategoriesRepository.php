<?php

declare(strict_types=1);

/**
 * Implementación de la interfaz para el repositorio de categorías
 *
 * @author alfonso
 */
class CategoriesRepository extends \BaseRepository
    implements \ICategoriesRepository{

    /**
     * Referencia a la clase base
     * @var \ICategoriesRepository
     */
    private static $_reference = NULL;

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
     * @return \ICategoriesRepository
     */
    public static function GetInstance($project = 0, $service = 0){
        if(CategoriesRepository::$_reference == NULL){
            CategoriesRepository::$_reference =
                    new \CategoriesRepository($project, $service);
        }
        return CategoriesRepository::$_reference;
    }

    /**
     * Obtiene una referencia al agregado actual
     * @return \CategoriesAggregate
     */
    public function GetAggregate() {
        $agg = new \CategoriesAggregate($this->IdProject, $this->IdService);
        // Cargar referencia al proyecto
        $agg->Project = $this->Dao->Read($this->IdProject, "Project");

        $filter = ["Project" => $this->IdProject, "State"  => 1];
        $categories = $this->Dao->GetByFilter( "Category", $filter );
        foreach($categories as $cat){
            $agg->Categories[$cat->Id] = $cat;
        }
        return $agg;
    }
}
